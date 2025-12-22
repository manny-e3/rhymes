<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\RevSyncLog;

class RevService
{
    private $baseUrl;
    private $apiKey;
    private $apiSecret;
    private $enabled;

    public function __construct()
    {
        $accountUrl = config('services.erprev.account_url');
        Log::info('ERPREV Service Constructor - Config Values', [
            'raw_account_url' => $accountUrl,
            'api_key_set' => !empty(config('services.erprev.api_key')),
            'api_secret_set' => !empty(config('services.erprev.api_secret')),
            'enabled' => config('services.erprev.enabled', false),
        ]);
        
        // Remove any protocol prefix if present, then construct the base URL
        $accountUrl = preg_replace('#^https?://#', '', $accountUrl);
        $this->baseUrl = "https://{$accountUrl}/api/1.0";
        $this->apiKey = config('services.erprev.api_key');
        $this->apiSecret = config('services.erprev.api_secret');
        $this->enabled = config('services.erprev.enabled', false);
        
        Log::info('ERPREV Service Initialized', [
            'base_url' => $this->baseUrl,
            'enabled' => $this->enabled,
        ]);
    }

    /**
     * Get authorization header
     */
    private function getAuthHeader()
    {
        $credentials = base64_encode($this->apiKey . ':' . $this->apiSecret);
        return 'Basic ' . $credentials;
    }

    /**
     * Register a book as a product in ERPREV
     */
      public function registerProduct($book)
    {
        if (!$this->enabled) {
            Log::warning('ERPREV registerProduct - Sync is disabled', [
                'book_id' => $book->id,
                'book_title' => $book->title,
            ]);
            return ['success' => false, 'message' => 'ERPREV sync is disabled'];
        }

        try {
            // ERPREV expects data wrapped in "parameters" object with specific field names
            $payload = [
                'parameters' => [
                    'Name' => $book->title,
                    'Description' => $book->description,
                    'Taxable' => "1",
                    'Price' => (string)number_format($book->price, 2, '.', ''),
                    'Measure' => 'PCS',
                    'Barcode' => $book->isbn,
                    'Category' => $book->genre ?? 'Books',
                    'CategoryID' => $book->genre_id ?? '1',
                    'Class' => 'N/A',
                    'ClassID' => '1'
                ]
            ];

            Log::info('ERPREV registerProduct - Sending request', [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'url' => $this->baseUrl . '/register-product/json/',
                'payload' => $payload,
                'method' => 'POST (JSON with parameters wrapper)',
            ]);

            // Send as JSON POST request
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->post($this->baseUrl . '/register-product/json/', $payload);

            Log::info('ERPREV registerProduct - Response received', [
                'book_id' => $book->id,
                'status_code' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
                'headers' => $response->headers(),
            ]);

            if ($response->successful()) {
                $body = $response->body();
                
                // Check if response is valid JSON
                $data = json_decode($body, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('ERPREV registerProduct - Invalid JSON response', [
                        'book_id' => $book->id,
                        'body' => $body,
                        'json_error' => json_last_error_msg(),
                    ]);
                    throw new \Exception('Invalid JSON response from ERPREV: ' . json_last_error_msg());
                }
                
                // Check if ERPREV returned an error despite successful HTTP status
                if (isset($data['status']) && $data['status'] === '0') {
                    $errorMessage = $data['error'] ?? 'Unknown ERPREV error';
                    Log::error('ERPREV registerProduct - API returned error status', [
                        'book_id' => $book->id,
                        'error' => $errorMessage,
                        'response' => $data,
                    ]);
                    throw new \Exception('ERPREV API error: ' . $errorMessage);
                }
                
                // Try different possible field names for product ID
                // According to API docs, ERPREV returns TransactionID
                $productId = $data['TransactionID']
                    ?? $data['product_id'] 
                    ?? $data['id'] 
                    ?? $data['productId'] 
                    ?? $data['ProductID']
                    ?? $data['product']['id'] 
                    ?? $data['data']['product_id'] 
                    ?? $data['data']['id']
                    ?? $data['data']['ProductID']
                    ?? $data['data']['TransactionID']
                    ?? null;
                
                Log::info('ERPREV registerProduct - Parsed response', [
                    'book_id' => $book->id,
                    'full_response' => $data,
                    'extracted_product_id' => $productId,
                    'response_keys' => array_keys($data),
                    'status' => $data['status'] ?? 'not set',
                ]);
                
                $this->logSync('products', 'success', 'Product registered successfully', [
                    'book_id' => $book->id,
                    'product_id' => $productId,
                    'payload' => $payload,
                    'response' => $data,
                ]);

                return [
                    'success' => true,
                    'product_id' => $productId,
                    'message' => 'Product registered successfully',
                    'raw_response' => $data,
                ];
            } else {
                $errorBody = $response->body();
                Log::error('ERPREV registerProduct - HTTP error', [
                    'book_id' => $book->id,
                    'status_code' => $response->status(),
                    'error_body' => $errorBody,
                ]);
                throw new \Exception('ERPREV HTTP error: ' . $errorBody);
            }
        } catch (\Exception $e) {
            Log::error('ERPREV registerProduct - Exception', [
                'book_id' => $book->id,
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            
            $this->logSync('products', 'error', $e->getMessage(), [
                'book_id' => $book->id,
                'payload' => $payload ?? null,
                'exception_class' => get_class($e),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /** 
     * Get products list from ERPREV
     */
    public function getProductsList($filters = [])
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'ERPREV sync is disabled'];
        }

        try {
            // Handle special parameters that need to be in the URL path
            $url = $this->baseUrl . '/get-products-list/json/';
            
            // Extract Name filter if present (lastupdated doesn't work for products)
            $nameFilter = '';
            if (isset($filters['Name']) && !empty($filters['Name'])) {
                $nameFilter = $filters['Name'];
                // Remove from filters as it goes in the URL path
                unset($filters['Name']);
            }
            
            // Add Name to URL path if specified
            if (!empty($nameFilter)) {
                $url .= 'Name/' . urlencode($nameFilter) . '/';
            }
            
            // Remove trailing slash if present
            $url = rtrim($url, '/');
            
            // Log the actual URL being called for debugging
            Log::info('ERPREV Service - getProductsList - Making API call', [
                'url' => $url,
                'filters' => $filters,
                'name_filter' => $nameFilter,
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Accept' => 'application/json',
            ])->timeout(300)->get($url, $filters);

            if ($response->successful()) {
                $data = $response->json();
                
               $this->logSync('products', 'success', 'Products list fetched', [
                    'count' => count($data['data'] ?? []),
                    'filters' => $filters,
                    'url' => $url,
                ]);


                return ['success' => true, 'data' => $data];
            } else {
                throw new \Exception('ERPREV API error: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->logSync('products', 'error', $e->getMessage(), $filters);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get stock list from ERPREV
     */
    public function getStockList($filters = [])
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'ERPREV sync is disabled'];
        }

        try {
            // Handle special parameters that need to be in the URL path
            $url = $this->baseUrl . '/get-stock-list/json/';
            
            // Extract lastupdated filter if present
            $lastUpdated = '';
            if (isset($filters['lastupdated']) && !empty($filters['lastupdated'])) {
                $lastUpdated = $filters['lastupdated'];
                // Remove from filters as it goes in the URL path
                unset($filters['lastupdated']);
            }
            
            // Extract Product filter if present
            $productFilter = '';
            if (isset($filters['Product']) && !empty($filters['Product'])) {
                $productFilter = $filters['Product'];
                // Remove from filters as it goes in the URL path
                unset($filters['Product']);
            }
            
            // Add lastupdated to URL path if specified
            if (!empty($lastUpdated)) {
                $url .= 'lastupdated/' . $lastUpdated . '/';
            }
            
            // Add Product to URL path if specified
            if (!empty($productFilter)) {
                $url .= 'Product/' . urlencode($productFilter) . '/';
            }
            
            // Remove trailing slash if present
            $url = rtrim($url, '/');
            
            // Log the actual URL being called for debugging
            Log::info('ERPREV Service - getStockList - Making API call', [
                'url' => $url,
                'filters' => $filters,
                'last_updated' => $lastUpdated,
                'product_filter' => $productFilter,
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Accept' => 'application/json',
            ])->timeout(300)->get($url, $filters);

            if ($response->successful()) {
                $data = $response->json();
                
                $this->logSync('inventory', 'success', 'Stock list fetched', [
                    'count' => count($data['data'] ?? []),
                    'filters' => $filters,
                    'url' => $url,
                ]);

                return ['success' => true, 'data' => $data];
            } else {
                throw new \Exception('ERPREV API error: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->logSync('inventory', 'error', $e->getMessage(), $filters);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
 
    /**
     * Get sales items from ERPREV
     */
    public function getSalesItems($filters = [])
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'ERPREV sync is disabled'];
        }

        try {
            // Handle special parameters that need to be in the URL path
            $url = $this->baseUrl . '/get-salesitems/json/';
            
            // Extract lastupdated filter if present
            $lastUpdated = '';
            if (isset($filters['lastupdated']) && !empty($filters['lastupdated'])) {
                $lastUpdated = $filters['lastupdated'];
                // Remove from filters as it goes in the URL path
                unset($filters['lastupdated']);
            }
            
            // Extract Name filter if present
            $nameFilter = '';
            if (isset($filters['Name']) && !empty($filters['Name'])) {
                $nameFilter = $filters['Name'];
                // Remove from filters as it goes in the URL path
                unset($filters['Name']);
            }
            
            // Add lastupdated to URL path if specified
            if (!empty($lastUpdated)) {
                $url .= 'lastupdated/' . $lastUpdated . '/';
            }
            
            // Add Name to URL path if specified
            if (!empty($nameFilter)) {
                $url .= 'Name/' . urlencode($nameFilter) . '/';
            }
            
            // Remove trailing slash if present
            $url = rtrim($url, '/');
            
            // Log the actual URL being called for debugging
            Log::info('ERPREV Service - getSalesItems - Making API call', [
                'url' => $url,
                'filters' => $filters,
                'last_updated' => $lastUpdated,
                'name_filter' => $nameFilter,
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Accept' => 'application/json',
            ])->timeout(120)->get($url, $filters);

            if ($response->successful()) {
                $data = $response->json();
                
                $this->logSync('sales', 'success', 'Sales items fetched', [
                    'count' => count($data['data'] ?? []),
                    'filters' => $filters,
                    'url' => $url,
                ]);

                return ['success' => true, 'data' => $data];
            } else {
                throw new \Exception('ERPREV API error: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->logSync('sales', 'error', $e->getMessage(), $filters);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get sold products summary
     */
    public function getSoldProductsSummary($filters = [])
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'ERPREV sync is disabled'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/sold-products-summary/json/', $filters);

            if ($response->successful()) {
                $data = $response->json();
                
                $this->logSync('sales', 'success', 'Sales summary fetched', [
                    'filters' => $filters,
                ]);

                return ['success' => true, 'data' => $data];
            } else {
                throw new \Exception('ERPREV API error: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->logSync('sales', 'error', $e->getMessage(), $filters);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get item categories from ERPREV for use as book genres
     */
    public function getItemCategories()
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'ERPREV sync is disabled'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Accept' => 'application/json',
            ])->timeout(30)->get($this->baseUrl . '/item-category-view/json/');

            if ($response->successful()) {
                $data = $response->json();
                
                // Process the categories to extract both name and ID
                $processedCategories = [];
                if (isset($data['records']) && is_array($data['records'])) {
                    foreach ($data['records'] as $record) {
                        $processedCategories[] = [
                            'id' => $record['CategoryID'] ?? $record['category_id'] ?? null,
                            'name' => $record['Name'] ?? $record['name'] ?? null
                        ];
                    }
                }
                
                $this->logSync('categories', 'success', 'Item categories fetched successfully', [
                    'count' => count($processedCategories)
                ]);

                return ['success' => true, 'data' => $data, 'categories' => $processedCategories];
            } else {
                throw new \Exception('ERPREV API error: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->logSync('categories', 'error', $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Log sync operations
     */
    private function logSync($area, $status, $message, $payload = null)
    {
        RevSyncLog::create([
            'area' => $area,
            'status' => $status,
            'message' => $message,
            'payload' => $payload,
        ]);

        Log::info("ERPREV Sync [{$area}] {$status}: {$message}", $payload ?? []);
    }

    /**
     * Test ERPREV connection
     */
    public function testConnection()
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'ERPREV sync is disabled'];
        }

        try {
            // Log the request for debugging
            Log::info('ERPREV API Request - testConnection', [
                'url' => $this->baseUrl . '/get-products-list/json',
                'headers' => [
                    'Authorization' => 'Basic ' . substr($this->getAuthHeader(), 6, 10) . '...', // Log only part of the token
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Use products endpoint with limit 1 to test connection instead of about endpoint
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json',
            ])->timeout(30)->retry(3, 100)->post($this->baseUrl . '/get-products-list/json', ['limit' => 1]);

            // Log the response for debugging
            Log::info('ERPREV API Response - testConnection', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'headers' => $response->headers(),
                'body_length' => strlen($response->body()),
            ]);

            // Check if response is valid
            $body = $response->body();
            
            // Handle empty or invalid responses
            if (empty($body)) {
                return ['success' => false, 'message' => 'Empty response from ERPREV API'];
            }
            
            // Check if response is HTML (indicates authentication failure or server error)
            if (stripos($body, '<html') !== false) {
                Log::error('ERPREV testConnection - Received HTML response instead of JSON', [
                    'body_sample' => substr($body, 0, 500),
                ]);
                return ['success' => false, 'message' => 'Received HTML response - likely authentication failure'];
            }
            
            $decoded = json_decode($body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('ERPREV testConnection - Invalid JSON response', [
                    'body' => $body,
                    'json_error' => json_last_error_msg(),
                ]);
                
                return ['success' => false, 'message' => 'Invalid JSON response from ERPREV API: ' . json_last_error_msg()];
            }

            // Check if we got data - this is more reliable than status field for ERPREV
            $hasRecords = isset($decoded['records']) && is_array($decoded['records']);
            
            // Even if status is 0, if we have records, consider it successful
            $apiSuccess = $hasRecords || (isset($decoded['status']) && $decoded['status'] === '1');
            
            if (!$apiSuccess && isset($decoded['status']) && $decoded['status'] === '0') {
                $errorMessage = $decoded['error'] ?? 'Unknown API error';
                Log::warning('ERPREV testConnection - API returned status 0 but has data', [
                    'status' => $decoded['status'],
                    'error' => $errorMessage,
                    'has_records' => $hasRecords,
                ]);
                
                // If we have records despite status 0, treat as success
                if ($hasRecords) {
                    $apiSuccess = true;
                }
            }
            
            return [
                'success' => $response->successful() && ($apiSuccess || $hasRecords),
                'message' => ($apiSuccess || $hasRecords) ? 'Connection successful' : 'API returned error status: ' . ($decoded['error'] ?? 'Unknown error'),
                'status_code' => $response->status(),
                'data' => $decoded,
            ];
        } catch (\Exception $e) {
            Log::error('ERPREV testConnection error', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Register webhook with ERPREV
     */
    public function registerWebhook($webhookData)
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'ERPREV sync is disabled'];
        }

        try {
            $payload = [
                'url' => $webhookData['url'],
                'event' => $webhookData['event'],
                'secret' => $webhookData['secret'] ?? null,
            ];

            Log::info('ERPREV registerWebhook - Sending request', [
                'url' => $this->baseUrl . '/webhooks/register',
                'payload' => $payload,
            ]);

            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->post($this->baseUrl . '/webhooks/register', $payload);

            Log::info('ERPREV registerWebhook - Response received', [
                'status_code' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $this->logSync('webhooks', 'success', 'Webhook registered successfully', [
                    'event' => $webhookData['event'],
                    'url' => $webhookData['url'],
                    'response' => $data,
                ]);

                return [
                    'success' => true,
                    'message' => 'Webhook registered successfully',
                    'data' => $data,
                ];
            } else {
                $errorBody = $response->body();
                Log::error('ERPREV registerWebhook - HTTP error', [
                    'status_code' => $response->status(),
                    'error_body' => $errorBody,
                ]);
                throw new \Exception('ERPREV HTTP error: ' . $errorBody);
            }
        } catch (\Exception $e) {
            Log::error('ERPREV registerWebhook - Exception', [
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            
            $this->logSync('webhooks', 'error', $e->getMessage(), [
                'payload' => $payload ?? null,
                'exception_class' => get_class($e),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Unregister webhook with ERPREV
     */
    public function unregisterWebhook($webhookId)
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'ERPREV sync is disabled'];
        }

        try {
            Log::info('ERPREV unregisterWebhook - Sending request', [
                'url' => $this->baseUrl . '/webhooks/unregister/' . $webhookId,
            ]);

            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Accept' => 'application/json',
            ])->timeout(30)->delete($this->baseUrl . '/webhooks/unregister/' . $webhookId);

            Log::info('ERPREV unregisterWebhook - Response received', [
                'status_code' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $this->logSync('webhooks', 'success', 'Webhook unregistered successfully', [
                    'webhook_id' => $webhookId,
                    'response' => $data,
                ]);

                return [
                    'success' => true,
                    'message' => 'Webhook unregistered successfully',
                    'data' => $data,
                ];
            } else {
                $errorBody = $response->body();
                Log::error('ERPREV unregisterWebhook - HTTP error', [
                    'status_code' => $response->status(),
                    'error_body' => $errorBody,
                ]);
                throw new \Exception('ERPREV HTTP error: ' . $errorBody);
            }
        } catch (\Exception $e) {
            Log::error('ERPREV unregisterWebhook - Exception', [
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            
            $this->logSync('webhooks', 'error', $e->getMessage(), [
                'webhook_id' => $webhookId,
                'exception_class' => get_class($e),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * List registered webhooks from ERPREV
     */
    public function listWebhooks()
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'ERPREV sync is disabled'];
        }

        try {
            Log::info('ERPREV listWebhooks - Sending request', [
                'url' => $this->baseUrl . '/webhooks/list',
            ]);

            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Accept' => 'application/json',
            ])->timeout(30)->get($this->baseUrl . '/webhooks/list');

            Log::info('ERPREV listWebhooks - Response received', [
                'status_code' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $this->logSync('webhooks', 'success', 'Webhooks list fetched', [
                    'count' => count($data['data'] ?? []),
                ]);

                return [
                    'success' => true,
                    'data' => $data,
                ];
            } else {
                $errorBody = $response->body();
                Log::error('ERPREV listWebhooks - HTTP error', [
                    'status_code' => $response->status(),
                    'error_body' => $errorBody,
                ]);
                throw new \Exception('ERPREV HTTP error: ' . $errorBody);
            }
        } catch (\Exception $e) {
            Log::error('ERPREV listWebhooks - Exception', [
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            
            $this->logSync('webhooks', 'error', $e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}