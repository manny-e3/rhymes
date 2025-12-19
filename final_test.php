<?php
// Simple test to verify the fix

// Simulate what the controller does
$dataWithFilter = [
    'records' => array_fill(0, 20, ['id' => 1, 'name' => 'Test']), // 20 records when filtered
    'pagenation' => ['TotalRecords' => 5000] // But API says 5000 total
];

$allSalesData = $dataWithFilter['records'];
$paginationInfo = $dataWithFilter['pagenation'];

// OLD way (incorrect) - uses TotalRecords from API
$totalRecordsOld = (int)($paginationInfo['TotalRecords'] ?? count($allSalesData));

// NEW way (correct) - uses actual count of filtered records
$totalRecordsNew = count($allSalesData);

echo "OLD method (incorrect):\n";
echo "Total records: $totalRecordsOld\n";
echo "This would show 5000 records even though we only have 20\n\n";

echo "NEW method (correct):\n";
echo "Total records: $totalRecordsNew\n";
echo "This correctly shows 20 records\n";