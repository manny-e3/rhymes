<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get ERPREV configuration
$accountUrl = config('services.erprev.account_url');
$apiKey = config('services.erprev.api_key');
$apiSecret = config('services.erprev.api_secret');

// Construct base URL
$accountUrl = preg_replace('#^https?://#', '', $accountUrl);
$baseUrl = "https://{$accountUrl}/api/1.0";

// Create authorization header
$credentials = base64_encode($apiKey . ':' . $apiSecret);
$authHeader = 'Basic ' . $credentials;

echo "Testing ERPREV API with lastupdated parameter...\n";
echo "Base URL: $baseUrl\n";

// Test without filter
echo "\n1. Testing without filter:\n";
$response = Http::withHeaders([
    'Authorization' => $authHeader,
    'Accept' => 'application/json',
])->timeout(120)->get($baseUrl . '/get-salesitems/json/');

if ($response->successful()) {
    $data = $response->json();
    echo "Success! Records count: " . count($data['records'] ?? []) . "\n";
} else {
    echo "Error: " . $response->body() . "\n";
}

// Test with lastupdated filter
echo "\n2. Testing with lastupdated=7d filter:\n";
$response = Http::withHeaders([
    'Authorization' => $authHeader,
    'Accept' => 'application/json',
])->timeout(120)->get($baseUrl . '/get-salesitems/json/lastupdated/7d');

if ($response->successful()) {
    $data = $response->json();
    echo "Success! Records count: " . count($data['records'] ?? []) . "\n";
} else {
    echo "Error: " . $response->body() . "\n";
}

echo "\nTest completed.\n";