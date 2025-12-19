<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\RevService;
use App\Models\Book;
use App\Models\WalletTransaction;

class ERPRevWebhookController extends Controller
{
    protected $revService;

    public function __construct(RevService $revService)
    {
        $this->revService = $revService;
    }

    /**
     * Handle ERPREV webhooks
     */
    public function handleWebhook(Request $request)
    {
        // Log the incoming webhook
        Log::info('ERPREV Webhook Received', [
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
        ]);

        // Verify webhook signature
        if (!$this->verifySignature($request)) {
            Log::warning('ERPREV Webhook Signature Verification Failed');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Process the webhook based on event type
        $eventType = $request->header('X-ERPREV-Event') ?? $request->input('event');
        $payload = $request->all();

        try {
            switch ($eventType) {
                case 'sale.created':
                    return $this->handleSaleCreated($payload);
                case 'inventory.updated':
                    return $this->handleInventoryUpdated($payload);
                case 'product.created':
                    return $this->handleProductCreated($payload);
                default:
                    Log::info('ERPREV Webhook - Unhandled Event Type', ['event' => $eventType]);
                    return response()->json(['message' => 'Event processed'], 200);
            }
        } catch (\Exception $e) {
            Log::error('ERPREV Webhook Processing Error', [
                'event' => $eventType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Handle sale created webhook
     */
    protected function handleSaleCreated($payload)
    {
        Log::info('Processing Sale Created Webhook', ['payload' => $payload]);
        
        $saleData = $payload['data'] ?? $payload;
        
        // Extract required fields
        $productId = $saleData['product_id'] ?? $saleData['ProductID'] ?? null;
        $saleId = $saleData['sale_id'] ?? $saleData['SaleID'] ?? null;
        $quantity = $saleData['quantity_sold'] ?? $saleData['QuantitySold'] ?? 1;
        $unitPrice = $saleData['unit_price'] ?? $saleData['UnitPrice'] ?? 0;
        $totalAmount = $saleData['total_amount'] ?? $saleData['TotalAmount'] ?? ($quantity * $unitPrice);
        $saleDate = $saleData['sale_date'] ?? $saleData['SaleDate'] ?? now();
        
        if (!$productId) {
            Log::error('Missing product ID in sale webhook', ['payload' => $saleData]);
            return response()->json(['error' => 'Missing product ID'], 400);
        }
        
        // Find the corresponding book
        $book = Book::where('rev_book_id', $productId)->first();
        
        if (!$book) {
            Log::warning('Book not found for ERPREV product ID', ['product_id' => $productId]);
            return response()->json(['error' => 'Book not found'], 404);
        }
        
        // Check if this sale has already been processed
        $existingTransaction = WalletTransaction::where('meta->erprev_sale_id', $saleId)->first();
        
        if ($existingTransaction) {
            Log::info('Sale already processed', ['sale_id' => $saleId]);
            return response()->json(['message' => 'Sale already processed'], 200);
        }
        
        // Calculate author earnings (assuming 70% goes to author)
        $authorEarnings = $totalAmount * 0.7;
        
        // Create wallet transaction for the author
        WalletTransaction::create([
            'user_id' => $book->user_id,
            'book_id' => $book->id,
            'type' => 'sale',
            'amount' => $authorEarnings,
            'meta' => [
                'erprev_sale_id' => $saleId,
                'invoice_id' => $saleData['invoice_id'] ?? null,
                'quantity_sold' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'sale_date' => $saleDate,
                'location' => $saleData['location'] ?? null,
                'description' => "Sale of {$quantity} copies of '{$book->title}' (Webhook)",
            ],
        ]);
        
        Log::info('Processed sale webhook for book', [
            'book_id' => $book->id,
            'sale_id' => $saleId,
            'author_earnings' => $authorEarnings
        ]);
        
        return response()->json(['message' => 'Sale processed successfully'], 200);
    }

    /**
     * Handle inventory updated webhook
     */
    protected function handleInventoryUpdated($payload)
    {
        Log::info('Processing Inventory Updated Webhook', ['payload' => $payload]);
        
        $inventoryData = $payload['data'] ?? $payload;
        
        // Extract required fields
        $productId = $inventoryData['product_id'] ?? $inventoryData['ProductID'] ?? null;
        $quantityOnHand = $inventoryData['quantity_on_hand'] ?? $inventoryData['UnitsInStock'] ?? 0;
        
        if (!$productId) {
            Log::error('Missing product ID in inventory webhook', ['payload' => $inventoryData]);
            return response()->json(['error' => 'Missing product ID'], 400);
        }
        
        // Find the corresponding book
        $book = Book::where('rev_book_id', $productId)->first();
        
        if (!$book) {
            Log::warning('Book not found for ERPREV product ID', ['product_id' => $productId]);
            return response()->json(['error' => 'Book not found'], 404);
        }
        
        // Update book status based on inventory levels
        if ($book->status === 'accepted' && $quantityOnHand > 0) {
            $book->update(['status' => 'stocked']);
            Log::info('Updated book status to stocked', [
                'book_id' => $book->id,
                'quantity_on_hand' => $quantityOnHand
            ]);
        } elseif ($book->status === 'stocked' && $quantityOnHand <= 0) {
            Log::info('Book is out of stock', [
                'book_id' => $book->id,
                'quantity_on_hand' => $quantityOnHand
            ]);
        }
        
        return response()->json(['message' => 'Inventory updated successfully'], 200);
    }

    /**
     * Handle product created webhook
     */
    protected function handleProductCreated($payload)
    {
        Log::info('Processing Product Created Webhook', ['payload' => $payload]);
        
        $productData = $payload['data'] ?? $payload;
        
        // Extract required fields
        $productId = $productData['product_id'] ?? $productData['ProductID'] ?? $productData['TransactionID'] ?? null;
        $productName = $productData['name'] ?? $productData['Name'] ?? 'Unknown Product';
        
        if (!$productId) {
            Log::error('Missing product ID in product webhook', ['payload' => $productData]);
            return response()->json(['error' => 'Missing product ID'], 400);
        }
        
        // Find books that might be waiting for this product ID
        $books = Book::whereNull('rev_book_id')
            ->where('title', 'LIKE', "%{$productName}%")
            ->get();
        
        foreach ($books as $book) {
            // Update the book with the ERPREV product ID
            $book->update(['rev_book_id' => $productId]);
            Log::info('Linked book to ERPREV product', [
                'book_id' => $book->id,
                'product_id' => $productId
            ]);
        }
        
        return response()->json(['message' => 'Product linked successfully'], 200);
    }

    /**
     * Verify webhook signature
     */
    protected function verifySignature(Request $request)
    {
        $webhookSecret = config('services.erprev.webhook_secret');
        
        // If no secret is configured, skip verification
        if (empty($webhookSecret)) {
            Log::warning('ERPREV webhook secret not configured, skipping verification');
            return true;
        }
        
        // Get signature from header
        $signature = $request->header('X-ERPREV-Signature');
        
        if (!$signature) {
            Log::warning('Missing X-ERPREV-Signature header');
            return false;
        }
        
        // Generate expected signature
        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        
        // Compare signatures
        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('Webhook signature mismatch', [
                'received_signature' => $signature,
                'expected_signature' => $expectedSignature
            ]);
            return false;
        }
        
        return true;
    }
}