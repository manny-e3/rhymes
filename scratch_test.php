<?php

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RevService;

$revService = app(RevService::class);

echo "Testing ERPREV API with different search formats...\n\n";

// Test 1: ID search in path
echo "1. Testing ID search in path (/ID/15526/):\n";
$res1 = $revService->getProductsList(['ID' => '15526']);
if ($res1['success']) {
    $records = $res1['data']['records'] ?? $res1['data']['data'] ?? [];
    echo "   Success! Found " . count($records) . " records. First record name: " . ($records[0]['Name'] ?? 'N/A') . "\n";
} else {
    echo "   Failed: " . $res1['message'] . "\n";
}

// Test 2: Name search in path (with space and comma: "Little Alfie, How Are You?")
echo "\n2. Testing Name search in path (/Name/Little Alfie, How Are You?/):\n";
$res2 = $revService->getProductsList(['Name' => 'Little Alfie, How Are You?']);
if ($res2['success']) {
    $records = $res2['data']['records'] ?? $res2['data']['data'] ?? [];
    echo "   Success! Found " . count($records) . " records. First record ID: " . ($records[0]['ID'] ?? 'N/A') . "\n";
} else {
    echo "   Failed: " . $res2['message'] . "\n";
}

// Test 3: Name search as query parameter (without appending to path)
echo "\n3. Testing Name search as query parameter:\n";
// Temporarily mock the URL building logic
$url = config('services.erprev.account_url');
$url = preg_replace('#^https?://#', '', $url);
$baseUrl = "http://{$url}/api/1.0/get-products-list/json";

try {
    $credentials = base64_encode(config('services.erprev.api_key') . ':' . config('services.erprev.api_secret'));
    $response = Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'Basic ' . $credentials,
        'Accept' => 'application/json',
    ])->get($baseUrl, ['Name' => 'Little Alfie, How Are You?']);
    
    if ($response->successful()) {
        $data = $response->json();
        $records = $data['records'] ?? $data['data'] ?? [];
        echo "   Success! Found " . count($records) . " records. First record ID: " . ($records[0]['ID'] ?? 'N/A') . "\n";
    } else {
        echo "   Failed HTTP: " . $response->status() . " - " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

// Test 4: POST request with Name in body
echo "\n4. Testing POST request with Name in body:\n";
try {
    $response = Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'Basic ' . $credentials,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->post($baseUrl, ['Name' => 'Little Alfie, How Are You?']);
    
    if ($response->successful()) {
        $data = $response->json();
        $records = $data['records'] ?? $data['data'] ?? [];
        echo "   Success! Found " . count($records) . " records. First record ID: " . ($records[0]['ID'] ?? 'N/A') . "\n";
    } else {
        echo "   Failed HTTP: " . $response->status() . " - " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}
