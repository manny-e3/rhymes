<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Models\WalletTransaction;
use App\Models\User;
use App\Services\PayoutService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SyncStockedSalesWallets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erprev:sync-stocked-wallets {--file=} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync historical sales records from deep search JSON for stocked books into author wallets';

    /**
     * Execute the console command.
     */
    public function handle(PayoutService $payoutService)
    {
        $this->info('Starting historical sales sync for stocked books...');
        
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('DRY RUN MODE ENABLED. No database changes will be committed.');
        }
        
        // 1. Locate the sales JSON file
        $filePath = $this->option('file');
        if (empty($filePath)) {
            // Check default locations
            $loc1 = base_path('stocked_april2026_sales_results.json');
            $loc2 = 'C:\\Users\\aboajah.emmanuel\\.gemini\\antigravity-ide\\scratch\\stocked_april2026_sales_results.json';
            
            if (file_exists($loc1)) {
                $filePath = $loc1;
            } elseif (file_exists($loc2)) {
                $filePath = $loc2;
            } else {
                $this->error('Error: Could not find sales results JSON file at default locations.');
                $this->line("Checked:\n - {$loc1}\n - {$loc2}");
                return 1;
            }
        }
        
        if (!file_exists($filePath)) {
            $this->error("Error: Specified file does not exist: {$filePath}");
            return 1;
        }
        
        $this->info("Loading sales records from: {$filePath}");
        $salesData = json_decode(file_get_contents($filePath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Error: Invalid JSON format in sales file: ' . json_last_error_msg());
            return 1;
        }
        
        $totalSales = count($salesData);
        $this->info("Successfully loaded {$totalSales} sales records.");
        
        // 2. Fetch stocked books
        $stockedBooks = Book::where('status', 'stocked')->get();
        
        $idMap = [];
        $titleMap = [];
        foreach ($stockedBooks as $book) {
            if (!empty($book->rev_book_id)) {
                $idMap[(string)$book->rev_book_id] = $book;
            }
            $titleMap[strtolower(trim($book->title))] = $book;
        }
        
        $processedCount = 0;
        $duplicateCount = 0;
        $skippedCount = 0;
        $totalAuthorEarnings = 0.0;
        $totalPlatformFees = 0.0;
        
        // Determine platform user ID (following SyncRevSalesJob pattern)
        $platformUserId = 1;
        try {
            $admin = User::role('admin')->first();
            if ($admin) {
                $platformUserId = $admin->id;
            } else {
                $firstUser = User::first();
                $platformUserId = $firstUser ? $firstUser->id : 1;
            }
        } catch (\Exception $e) {
            $firstUser = User::first();
            $platformUserId = $firstUser ? $firstUser->id : 1;
        }
        
        foreach ($salesData as $sale) {
            try {
                $saleId = (string)($sale['ID'] ?? $sale['id'] ?? '');
                if (empty($saleId)) {
                    $this->error('Skipping sale with missing ID: ' . json_encode($sale));
                    $skippedCount++;
                    continue;
                }
                
                // Check if duplicate transaction already exists in the database
                $existingTransaction = WalletTransaction::where('type', 'sale')
                    ->where(function($query) use ($saleId) {
                        $query->where('meta->erprev_sale_id', $saleId)
                              ->orWhere('meta->erprev_sid', $saleId)
                              ->orWhere('meta->erprev_unique_id', $saleId);
                    })->first();
                    
                if ($existingTransaction) {
                    $duplicateCount++;
                    continue;
                }
                
                // Map the sale to a book
                $productId = (string)($sale['ProductID'] ?? '');
                $productName = trim($sale['Product'] ?? '');
                $normProdName = strtolower($productName);
                
                $book = null;
                if (isset($idMap[$productId])) {
                    $book = $idMap[$productId];
                } elseif (isset($titleMap[$normProdName])) {
                    $book = $titleMap[$normProdName];
                }
                
                if (!$book) {
                    $this->warn("Warning: Book not found in database for Product ID '{$productId}', Product '{$productName}'");
                    $skippedCount++;
                    continue;
                }
                
                // Extract quantities and prices
                $quantity = (int)str_replace(',', '', $sale['Qty'] ?? '1');
                $amountStr = $sale['Amount'] ?? '0';
                $amountStr = str_replace(['₦', ',', ' '], '', $amountStr);
                $totalAmount = (float)$amountStr;
                
                $unitPriceStr = $sale['UnitPrice'] ?? '0';
                $unitPriceStr = str_replace(['₦', ',', ' '], '', $unitPriceStr);
                $unitPrice = (float)$unitPriceStr;
                
                if ($totalAmount <= 0 && $quantity > 0 && $unitPrice > 0) {
                    $totalAmount = $quantity * $unitPrice;
                }
                
                $saleDate = Carbon::parse($sale['DateTime'] ?? now());
                
                // Use UnitPrice * Qty as the main and only price (no splitting)
                $authorEarnings = $unitPrice * $quantity;
                $platformFee = 0.0;
                
                $totalAuthorEarnings += $authorEarnings;
                $totalPlatformFees += $platformFee;
                
                if (!$dryRun) {
                    // Create wallet transaction for the author
                    WalletTransaction::create([
                        'user_id' => $book->user_id,
                        'book_id' => $book->id,
                        'type' => 'sale',
                        'amount' => $authorEarnings,
                        'meta' => [
                            'erprev_sale_id' => $saleId,
                            'invoice_id' => $sale['InvoiceID'] ?? $sale['invoice_id'] ?? null,
                            'quantity_sold' => $quantity,
                            'unit_price' => $unitPrice,
                            'total_amount' => $totalAmount,
                            'platform_fee' => $platformFee,
                            'author_earnings' => $authorEarnings,
                            'sale_date' => $saleDate->toDateTimeString(),
                            'location' => $sale['WareHouse'] ?? $sale['location'] ?? null,
                            'description' => "Sale of {$quantity} copies of '{$book->title}' (Imported via Deep Search)",
                        ],
                    ]);
                    
                    // Update book quantity in database
                    if ($book->quantity !== null) {
                        $book->update(['quantity' => max(0, $book->quantity - $quantity)]);
                    }
                }
                
                $processedCount++;
            } catch (\Exception $e) {
                $this->error("Exception while processing sale ID " . ($sale['ID'] ?? 'Unknown') . ": " . $e->getMessage());
                Log::error('Wallet Sync Exception', [
                    'sale' => $sale,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $skippedCount++;
            }
        }
        
        $this->line("\n=== SYNC OPERATION SUMMARY ===");
        $this->info("Total Processed/Imported: {$processedCount}");
        $this->info("Duplicates Skipped:       {$duplicateCount}");
        $this->info("Other Skips/Errors:       {$skippedCount}");
        $this->info("Total Author Earnings:    ₦" . number_format($totalAuthorEarnings, 2));
        $this->info("Total Platform Fees:       ₦" . number_format($totalPlatformFees, 2));
        
        Log::info('ERPREV Historical Wallet Sync Completed', [
            'dry_run' => $dryRun,
            'processed' => $processedCount,
            'duplicates' => $duplicateCount,
            'skipped' => $skippedCount,
            'total_author_earnings' => $totalAuthorEarnings,
            'total_platform_fees' => $totalPlatformFees
        ]);
        
        return 0;
    }
}
