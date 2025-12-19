<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RevService;
use Illuminate\Support\Facades\Log;

class RegisterWebhooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:register-webhooks {--unregister} {--list} {--debug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register webhooks with ERPREV for real-time updates';

    /**
     * Execute the console command.
     */
    public function handle(RevService $revService)
    {
        if ($this->option('list')) {
            return $this->listWebhooks($revService);
        }
        
        if ($this->option('unregister')) {
            return $this->unregisterWebhooks($revService);
        }
        
        return $this->registerWebhooks($revService);
    }

    /**
     * Register webhooks with ERPREV
     */
    protected function registerWebhooks(RevService $revService)
    {
        $this->info('Registering webhooks with ERPREV...');
        
        // Get the webhook URL
        $webhookUrl = route('webhook.erprev');
        $this->line("Webhook URL: {$webhookUrl}");
        
        // Events to register
        $events = [
            'sale.created',
            'inventory.updated',
            'product.created',
            'product.updated',
            'order.created',
        ];
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($events as $event) {
            $this->line("Registering webhook for event: {$event}");
            
            $result = $revService->registerWebhook([
                'url' => $webhookUrl,
                'event' => $event,
                'secret' => config('services.erprev.webhook_secret')
            ]);
            
            if ($result['success']) {
                $this->info("Successfully registered webhook for {$event}");
                $successCount++;
            } else {
                $this->error("Failed to register webhook for {$event}: {$result['message']}");
                $errorCount++;
            }
        }
        
        $this->info("Webhook registration completed. Success: {$successCount}, Errors: {$errorCount}");
        
        return $errorCount > 0 ? 1 : 0;
    }

    /**
     * Unregister webhooks with ERPREV
     */
    protected function unregisterWebhooks(RevService $revService)
    {
        $this->info('Unregistering webhooks with ERPREV...');
        
        // In a real implementation, you would need to know the webhook IDs to unregister them
        // For now, we'll just show how it would work
        
        $this->line('Note: In a real implementation, you would need to provide specific webhook IDs to unregister.');
        $this->line('This would typically involve first listing webhooks to get their IDs.');
        
        return 0;
    }

    /**
     * List registered webhooks with ERPREV
     */
    protected function listWebhooks(RevService $revService)
    {
        $this->info('Listing registered webhooks with ERPREV...');
        
        $result = $revService->listWebhooks();
        
        if ($result['success']) {
            $webhooks = $result['data']['data'] ?? $result['data']['webhooks'] ?? [];
            
            if (empty($webhooks)) {
                $this->line('No webhooks registered.');
                return 0;
            }
            
            $this->line('Registered webhooks:');
            foreach ($webhooks as $webhook) {
                $id = $webhook['id'] ?? 'N/A';
                $url = $webhook['url'] ?? 'N/A';
                $event = $webhook['event'] ?? 'N/A';
                $created = $webhook['created_at'] ?? 'N/A';
                
                $this->line("- ID: {$id}");
                $this->line("  URL: {$url}");
                $this->line("  Event: {$event}");
                $this->line("  Created: {$created}");
                $this->line("");
            }
            
            $this->line("Total webhooks: " . count($webhooks));
        } else {
            $this->error("Failed to list webhooks: {$result['message']}");
            return 1;
        }
        
        return 0;
    }
}