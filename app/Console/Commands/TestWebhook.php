<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:test-webhook {event=sale.created} {--data=} {--debug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending a webhook to the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing webhook...');
        
        // Get the webhook URL
        $webhookUrl = route('webhook.erprev');
        $this->line("Webhook URL: {$webhookUrl}");
        
        // Prepare test data
        $event = $this->argument('event');
        $customData = $this->option('data') ? json_decode($this->option('data'), true) : null;
        
        $testData = $customData ?? $this->getTestData($event);
        
        if ($this->option('debug')) {
            $this->line("Sending data: " . json_encode($testData, JSON_PRETTY_PRINT));
        }
        
        // Generate signature
        $webhookSecret = config('services.erprev.webhook_secret');
        $payload = json_encode($testData);
        $signature = hash_hmac('sha256', $payload, $webhookSecret);
        
        // Send the webhook
        try {
            $response = Http::withHeaders([
                'X-ERPREV-Event' => $event,
                'X-ERPREV-Signature' => $signature,
                'Content-Type' => 'application/json',
            ])->post($webhookUrl, $testData);
            
            $this->info("Webhook sent successfully!");
            $this->line("Status: " . $response->status());
            $this->line("Response: " . $response->body());
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to send webhook: " . $e->getMessage());
            Log::error('Webhook test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }
    
    /**
     * Get test data for different event types
     */
    protected function getTestData($event)
    {
        switch ($event) {
            case 'sale.created':
                return [
                    'event' => 'sale.created',
                    'data' => [
                        'sale_id' => 'TEST_SALE_' . time(),
                        'product_id' => 'TEST_PRODUCT_123',
                        'quantity_sold' => 2,
                        'unit_price' => 1500.00,
                        'total_amount' => 3000.00,
                        'sale_date' => now()->toIso8601String(),
                        'invoice_id' => 'INV-' . time(),
                        'location' => 'Online Store'
                    ]
                ];
                
            case 'inventory.updated':
                return [
                    'event' => 'inventory.updated',
                    'data' => [
                        'product_id' => 'TEST_PRODUCT_123',
                        'quantity_on_hand' => 15,
                        'warehouse_id' => 'WH001',
                        'last_updated' => now()->toIso8601String()
                    ]
                ];
                
            case 'product.created':
                return [
                    'event' => 'product.created',
                    'data' => [
                        'product_id' => 'TEST_PRODUCT_' . time(),
                        'name' => 'Test Book Title',
                        'description' => 'A test book for webhook testing',
                        'price' => 2500.00,
                        'category' => 'Fiction',
                        'created_at' => now()->toIso8601String()
                    ]
                ];
                
            default:
                return [
                    'event' => $event,
                    'data' => [
                        'message' => 'Test webhook for event: ' . $event,
                        'timestamp' => now()->toIso8601String()
                    ]
                ];
        }
    }
}