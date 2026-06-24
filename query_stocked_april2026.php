<?php

ini_set('memory_limit', '256M');
set_time_limit(180);

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;
use Carbon\Carbon;

$startTime = microtime(true);

// 1. Fetch all stocked books
$stockedBooks = Book::where('status', 'stocked')->get();
echo "Loaded " . count($stockedBooks) . " stocked books from database.\n";

// 2. Build lookup maps
$idMap = [];
$titleMap = [];

foreach ($stockedBooks as $book) {
    if (!empty($book->rev_book_id)) {
        $idMap[(string)$book->rev_book_id] = $book;
    }
    // Normalize title: lowercase, trim
    $normalizedTitle = strtolower(trim($book->title));
    $titleMap[$normalizedTitle] = $book;
}

echo "Lookup maps built: " . count($idMap) . " IDs, " . count($titleMap) . " titles.\n";

// 3. Scan the local JSON file
$filePath = 'C:\Users\aboajah.emmanuel\.gemini\antigravity-ide\scratch\all_sold_products.json';
if (!file_exists($filePath)) {
    echo "Error: JSON file not found at {$filePath}\n";
    exit(1);
}

$handle = fopen($filePath, 'r');
if (!$handle) {
    echo "Error: Could not open the file.\n";
    exit(1);
}

$matchingSales = [];
$processedCount = 0;
$buffer = '';
$thresholdDate = Carbon::parse('2026-04-01 00:00:00');

echo "Scanning file chunk-by-chunk for sales from April 2026 onwards...\n";

while (!feof($handle)) {
    $chunk = fread($handle, 4096000); // 4MB chunks
    if ($chunk === false) {
        break;
    }
    $buffer .= $chunk;
    
    preg_match_all('/\{[^{}]+?\}/s', $buffer, $matches, PREG_OFFSET_CAPTURE);
    
    $lastIndex = 0;
    if (!empty($matches[0])) {
        foreach ($matches[0] as $match) {
            $json = $match[0];
            $offset = $match[1];
            
            $cleanJson = str_replace(["\r", "\n", "\t"], "", $json);
            
            // Reconstruct and decode
            $record = json_decode($cleanJson, true);
            if ($record) {
                // Check date
                $dateTimeStr = $record['DateTime'] ?? $record['DateTimeWritten'] ?? '';
                if ($dateTimeStr) {
                    try {
                        $saleDate = Carbon::parse($dateTimeStr);
                        if ($saleDate->greaterThanOrEqualTo($thresholdDate)) {
                            // Check match
                            $productId = (string)($record['ProductID'] ?? '');
                            $productName = trim($record['Product'] ?? '');
                            $normProdName = strtolower($productName);
                            
                            $matchedBook = null;
                            if (isset($idMap[$productId])) {
                                $matchedBook = $idMap[$productId];
                            } elseif (isset($titleMap[$normProdName])) {
                                $matchedBook = $titleMap[$normProdName];
                            }
                            
                            if ($matchedBook) {
                                $record['MatchedBookID'] = $matchedBook->id;
                                $record['MatchedBookTitle'] = $matchedBook->title;
                                $matchingSales[] = $record;
                            }
                        }
                    } catch (\Exception $e) {
                        // Date parsing issue, skip or log
                    }
                }
            }
            
            $lastIndex = $offset + strlen($json);
            $processedCount++;
        }
    }
    
    if ($lastIndex > 0) {
        $buffer = substr($buffer, $lastIndex);
    }
}
fclose($handle);

$endTime = microtime(true);
$duration = round($endTime - $startTime, 2);

echo "Finished scanning {$processedCount} records in {$duration} seconds.\n";
$matchCount = count($matchingSales);
echo "Found {$matchCount} matching sales records for stocked books since April 2026.\n\n";

if ($matchCount > 0) {
    // Sort matching sales chronologically
    usort($matchingSales, function($a, $b) {
        return strtotime($a['DateTime']) <=> strtotime($b['DateTime']);
    });
    
    $outputPath = __DIR__ . '/stocked_april2026_sales_results.json';
    file_put_contents($outputPath, json_encode($matchingSales, JSON_PRETTY_PRINT));
    echo "Saved results to {$outputPath}\n\n";
    
    // Group sales by product/book to show summary
    $summary = [];
    foreach ($matchingSales as $sale) {
        $title = $sale['MatchedBookTitle'] ?? $sale['Product'] ?? 'Unknown';
        if (!isset($summary[$title])) {
            $summary[$title] = [
                'ProductID' => $sale['ProductID'] ?? 'N/A',
                'Qty' => 0,
                'Amount' => 0,
                'TxCount' => 0
            ];
        }
        $qty = (int)str_replace(',', '', $sale['Qty'] ?? '0');
        $amount = (float)str_replace(['₦', ',', ' '], '', $sale['Amount'] ?? '0');
        
        $summary[$title]['Qty'] += $qty;
        $summary[$title]['Amount'] += $amount;
        $summary[$title]['TxCount'] += 1;
    }
    
    echo "=== SALES SUMMARY BY BOOK ===\n";
    echo "| Book Title | Product ID | Tx Count | Total Qty | Total Value |\n";
    echo "| :--- | :---: | :---: | :---: | :--- |\n";
    foreach ($summary as $title => $stats) {
        printf(
            "| %s | `%s` | %d | %d | ₦%s |\n",
            $title,
            $stats['ProductID'],
            $stats['TxCount'],
            $stats['Qty'],
            number_format($stats['Amount'], 2)
        );
    }
    echo "\n";
    
    echo "=== DETAILED TRANSACTIONS (APRIL 2026 - PRESENT) ===\n";
    echo "| SN | Sale ID | Date/Time | Product ID | Book Title | Qty | Unit Price | Amount | Invoice ID | Warehouse | Staff | Customer |\n";
    echo "|---:|:---:|:---|:---:|:---|:---:|:---|:---|:---:|:---|:---|:---|\n";
    foreach ($matchingSales as $index => $sale) {
        printf(
            "| %d | `%s` | %s | `%s` | %s | %s | ₦%s | ₦%s | `%s` | %s | %s | %s |\n",
            $index + 1,
            $sale['ID'] ?? '',
            $sale['DateTime'] ?? '',
            $sale['ProductID'] ?? '',
            $sale['MatchedBookTitle'] ?? $sale['Product'] ?? 'N/A',
            $sale['Qty'] ?? '0',
            number_format((float)str_replace(['₦', ',', ' '], '', $sale['UnitPrice'] ?? '0'), 0),
            number_format((float)str_replace(['₦', ',', ' '], '', $sale['Amount'] ?? '0'), 0),
            $sale['InvoiceID'] ?? '',
            $sale['WareHouse'] ?? '',
            $sale['UserStaffName'] ?? '',
            trim($sale['CustomerName'] ?? 'Walk in')
        );
    }
} else {
    echo "No sales found for any stocked books since April 2026.\n";
}
