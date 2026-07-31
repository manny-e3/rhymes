<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Services\RevService;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RevStockedBooksSyncController extends Controller
{
    private $revService;

    public function __construct(RevService $revService)
    {
       // $this->middleware(['auth', 'role:admin']);
        $this->revService = $revService;
    }

    /**
     * Securely sync sales data for stocked books from ERPREV to local wallets.
     * Streams progress output line-by-line.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function syncSalesToWallet()
    {
        $response = new StreamedResponse(function () {
            // Disable execution time limit
            @set_time_limit(0);
            @ini_set('max_execution_time', 0);
            @ini_set('memory_limit', '512M');

            // Disable buffering to show live output in the browser
            @ini_set('output_buffering', 'Off');
            @ini_set('zlib.output_compression', 0);
            if (ob_get_level() > 0) {
                @ob_end_clean();
            }
            ob_implicit_flush(true);

            $startDate = '2026-04-01';
            $updateLocalIds = true; // Automatically correct mismatched rev_book_ids in local database

            echo "=== 1. Loading Existing Wallet Transactions ===\n";
            flush();

            $existingSaleIds = [];
            WalletTransaction::where('type', 'sale')
                ->chunk(500, function ($txns) use (&$existingSaleIds) {
                    foreach ($txns as $tx) {
                        $meta = $tx->meta;
                        if (isset($meta['erprev_sale_id'])) {
                            $existingSaleIds[(string)$meta['erprev_sale_id']] = true;
                        }
                    }
                });
            echo "Loaded " . count($existingSaleIds) . " existing sale transaction IDs in-memory.\n";
            flush();

            echo "\n=== 2. Fetching Local Books with ISBNs (Stocked Only) ===\n";
            flush();
            $books = Book::whereNotNull('isbn')->where('isbn', '!=', '')->where('status', 'stocked')->get();
            $totalBooks = count($books);
            echo "Found {$totalBooks} stocked books in local database with a valid ISBN.\n";
            flush();

            $mismatchCount = 0;
            $salesFoundCount = 0;
            $processedCount = 0;
            $insertedTxCount = 0;
            $updatedBooks = [];
            $booksWithSales = [];

            echo "\n=== 3. Querying ERP by Barcode & Syncing Sales to Wallet (Since {$startDate}) ===\n";
            flush();

            foreach ($books as $book) {
                $isbn = trim($book->isbn);
                $processedCount++;
                
                // Print progress//////
                if ($processedCount % 10 === 0 || $processedCount === $totalBooks) {
                    echo "Progress: {$processedCount}/{$totalBooks} books processed...\n";
                    flush();
                }
                
                // Call getProductsList with specific Barcode filter
                $res = $this->revService->getProductsList(['Barcode' => $isbn]);
                if (!$res['success']) {
                    continue;
                }
                
                $products = $res['data']['records'] ?? $res['data']['data'] ?? [];
                if (empty($products)) {
                    continue;
                }
                
                // Use the first matching product from the catalog
                $erpProd = $products[0];
                $erpId = $erpProd['SID'] ?? $erpProd['ID'] ?? $erpProd['TransactionID'] ?? null;
                $erpName = $erpProd['Name'] ?? $erpProd['Product'] ?? '';
                
                if (!$erpId) {
                    continue;
                }
                
                $hasMismatch = ((string)$book->rev_book_id !== (string)$erpId);
                if ($hasMismatch) {
                    $mismatchCount++;
                    echo "⚠️ Mismatch Found: '{$book->title}' (ISBN: {$isbn}) | DB ID: '" . ($book->rev_book_id ?? 'NULL') . "' -> Correct ERP ID: '{$erpId}'\n";
                    flush();
                    
                    if ($updateLocalIds) {
                        $oldId = $book->rev_book_id;
                        $book->update(['rev_book_id' => $erpId]);
                        $updatedBooks[] = [
                            'title' => $book->title,
                            'isbn' => $isbn,
                            'old_id' => $oldId,
                            'new_id' => $erpId
                        ];
                    }
                }
                
                // Fetch sales records starting from April 1, 2026 using the correct ERP Product ID
                $salesRes = $this->revService->getSoldProductsView([
                    'startDate' => $startDate,
                    'ProductID' => (string)$erpId,
                    'TotalRecords' => '100'
                ]);
                
                if ($salesRes['success']) {
                    $salesRecords = $salesRes['data']['records'] ?? $salesRes['data']['data'] ?? [];
                    $salesCount = count($salesRecords);
                    if ($salesCount > 0) {
                        $salesFoundCount++;
                        
                        $newSales = [];
                        foreach ($salesRecords as $sale) {
                            $saleId = (string)$sale['ID'];
                            
                            // Skip if already in database to prevent duplicates
                            if (isset($existingSaleIds[$saleId])) {
                                continue;
                            }
                            
                            // Parse date
                            $saleDate = Carbon::parse($sale['DateTime'] ?? now());
                            
                            // Clean and parse amounts
                            $quantity  = max(1, (int)str_replace(',', '', $sale['Qty'] ?? '1'));
                            $unitPrice = (float)str_replace(['₦', ',', ' ', '&amp;#x20A6;', '&#x20A6;'], '', $sale['UnitPrice'] ?? '0');
                            $amount    = (float)str_replace(['₦', ',', ' ', '&amp;#x20A6;', '&#x20A6;'], '', $sale['Amount']    ?? '0');
                            if ($amount <= 0 && $unitPrice > 0) {
                                $amount = $unitPrice * $quantity;
                            }
                            $authorEarnings = $amount;
                            
                            // Construct unique ID to prevent database constraints issues
                            $uniqueId = md5($isbn . $erpId . $saleId . ($sale['InvoiceID'] ?? ''));

                            $meta = [
                                'erprev_unique_id' => $uniqueId,
                                'erprev_sale_id' => $saleId,
                                'erprev_sid'     => $saleId,
                                'invoice_id'     => $sale['InvoiceID'] ?? null,
                                'product_id'     => $erpId,
                                'quantity_sold'  => $quantity,
                                'unit_price'     => $unitPrice,
                                'total_amount'   => $amount,
                                'platform_fee'   => 0.0,
                                'author_earnings' => $amount,
                                'sale_date'      => $saleDate->toDateTimeString(),
                                'barcode'        => $isbn,
                                'location'       => $sale['WareHouse'] ?? $sale['location'] ?? null,
                                'description'    => "Sale of {$quantity} × '{$book->title}' via ERPREV",
                            ];
                            
                            // Insert transaction
                            $tx = new WalletTransaction([
                                'user_id' => $book->user_id,
                                'book_id' => $book->id,
                                'type'    => 'sale',
                                'amount'  => $authorEarnings,
                                'meta'    => $meta,
                            ]);
                            $tx->timestamps = false;
                            $tx->created_at = $saleDate;
                            $tx->updated_at = $saleDate;
                            $tx->save();
                            
                            // Mark in memory
                            $existingSaleIds[$saleId] = true;
                            $insertedTxCount++;
                            $newSales[] = $sale;
                            
                            // Decrement book stock — guard against UNSIGNED underflow
                            if ($book->quantity !== null) {
                                DB::table('books')
                                    ->where('id', $book->id)
                                    ->update([
                                        'quantity' => DB::raw("CASE WHEN `quantity` >= {$quantity} THEN `quantity` - {$quantity} ELSE 0 END"),
                                    ]);
                            }
                        }
                        
                        $booksWithSales[] = [
                            'title' => $book->title,
                            'isbn' => $isbn,
                            'erp_id' => $erpId,
                            'total_sales_count' => $salesCount,
                            'new_sales_inserted' => count($newSales),
                            'sales' => $salesRecords
                        ];
                        
                        if (count($newSales) > 0) {
                            echo "   💰 Synced " . count($newSales) . " NEW sales records (out of {$salesCount} total) for '{$book->title}'!\n";
                            flush();
                        }
                    }
                }
            }

            echo "\n========================================\n";
            echo "=== FINAL SUMMARY REPORT ===\n";
            echo "Total Books Scanned: {$totalBooks}\n";
            echo "Total Mismatched Product IDs Corrected: {$mismatchCount}\n";
            echo "Total Books with Sales Records Found: {$salesFoundCount}\n";
            echo "Total New Wallet Transactions Inserted: {$insertedTxCount}\n";
            echo "========================================\n";
            flush();

            if (count($updatedBooks) > 0) {
                echo "\n=== Corrected Books Details ===\n";
                foreach ($updatedBooks as $ub) {
                    echo "- '{$ub['title']}' (ISBN: {$ub['isbn']}) | DB ID was: '{$ub['old_id']}' -> Now: '{$ub['new_id']}'\n";
                }
                flush();
            }

            if (count($booksWithSales) > 0) {
                echo "\n=== Sales Found / Synced Details ===\n";
                foreach ($booksWithSales as $bws) {
                    if ($bws['new_sales_inserted'] > 0) {
                        echo "- '{$bws['title']}' (ERP ID: {$bws['erp_id']}) -> Synced {$bws['new_sales_inserted']} new sales (out of {$bws['total_sales_count']} total)\n";
                        foreach ($bws['sales'] as $sale) {
                            echo "  * Date: {$sale['DateTime']} | Qty: {$sale['Qty']} | Price: {$sale['UnitPrice']} | Cust: {$sale['CustomerName']} | Wh: {$sale['WareHouse']}\n";
                        }
                        flush();
                    }
                }
            }
        });

        $response->headers->set('Content-Type', 'text/plain; charset=utf-8');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }
}
