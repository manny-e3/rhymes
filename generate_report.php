<?php

$jsonPath = __DIR__ . '/stocked_april2026_sales_results.json';
$artifactPath = 'C:\Users\aboajah.emmanuel\.gemini\antigravity-ide\brain\52c003c2-6b32-4f9e-943f-628f5ab09d45\stocked_books_april2026_report.md';

if (!file_exists($jsonPath)) {
    echo "Error: JSON file not found at {$jsonPath}\n";
    exit(1);
}

$sales = json_decode(file_get_contents($jsonPath), true);
$totalSales = count($sales);

$totalQty = 0;
$totalValue = 0.0;
$bookSummaries = [];

foreach ($sales as $sale) {
    $title = $sale['MatchedBookTitle'] ?? $sale['Product'] ?? 'Unknown Book';
    $productId = $sale['ProductID'] ?? 'N/A';
    $qty = (int)str_replace(',', '', $sale['Qty'] ?? '0');
    $amount = (float)str_replace(['₦', ',', ' '], '', $sale['Amount'] ?? '0');
    
    $totalQty += $qty;
    $totalValue += $amount;
    
    if (!isset($bookSummaries[$title])) {
        $bookSummaries[$title] = [
            'ProductID' => $productId,
            'TxCount' => 0,
            'Qty' => 0,
            'Value' => 0.0
        ];
    }
    
    $bookSummaries[$title]['TxCount']++;
    $bookSummaries[$title]['Qty'] += $qty;
    $bookSummaries[$title]['Value'] += $amount;
}

// Sort books by total value descending
uasort($bookSummaries, function($a, $b) {
    return $b['Value'] <=> $a['Value'];
});

$md = "# stocked Books Sales Audit (April 2026 - Present)\n\n";
$md .= "This report presents the findings of a deep search audit conducted across all books with status **`stocked`** in the Rhymes application database. The audit scanned all **588,891** historical sales records in the ERPRev system and filtered for active transactions dated from **April 1, 2026, to the present**.\n\n";

$md .= "## 1. Executive Summary\n\n";
$md .= "* **Audit Timeline**: April 1, 2026 - June 23, 2026 (Local Time)\n";
$md .= "* **Stocked Books Monitored**: 309 books\n";
$md .= "* **Total Matching Transactions**: " . number_format($totalSales) . " sales\n";
$md .= "* **Total Copies Sold**: " . number_format($totalQty) . " copies\n";
$md .= "* **Total Sales Revenue**: ₦" . number_format($totalValue, 2) . "\n\n";

$md .= "## 2. Sales Summary by Book\n\n";
$md .= "The following table lists all stocked books that had active sales transactions from April 2026 onwards, sorted by total sales value:\n\n";
$md .= "| SN | Book Title | Product ID | Tx Count | Total Qty | Total Value |\n";
$md .= "|---:|:---|:---:|:---:|:---:|:---|\n";

$sn = 1;
foreach ($bookSummaries as $title => $stats) {
    $md .= sprintf(
        "| %d | %s | `%s` | %d | %d | ₦%s |\n",
        $sn++,
        $title,
        $stats['ProductID'],
        $stats['TxCount'],
        $stats['Qty'],
        number_format($stats['Value'], 2)
    );
}
$md .= "\n";

$md .= "## 3. Detailed Sales Transactions (Most Recent 100)\n\n";
$md .= "Below are the 100 most recent sales transactions (the full list of " . number_format($totalSales) . " records has been saved as a JSON artifact at [stocked_april2026_sales_results.json](file:///" . str_replace('\\', '/', $jsonPath) . ")):\n\n";

$md .= "| SN | Sale ID | Date/Time | Product ID | Book Title | Qty | Unit Price | Amount | Invoice ID | Warehouse | Staff | Customer |\n";
$md .= "|---:|:---:|:---|:---:|:---|:---:|:---|:---|:---:|:---|:---|:---|\n";

// Show the most recent sales first
$recentSales = array_reverse($sales);
$displayCount = min(100, count($recentSales));

for ($i = 0; $i < $displayCount; $i++) {
    $sale = $recentSales[$i];
    $md .= sprintf(
        "| %d | `%s` | %s | `%s` | %s | %s | ₦%s | ₦%s | `%s` | %s | %s | %s |\n",
        $i + 1,
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

file_put_contents($artifactPath, $md);
echo "Report written successfully to {$artifactPath}.\n";
echo "Parsed " . count($bookSummaries) . " distinct books with sales.\n";
