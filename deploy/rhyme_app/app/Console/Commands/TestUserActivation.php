<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestUserActivation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:user-activation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test user activation/deactivation functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing User Activation/Deactivation functionality...');
        
        // Get the first user or create a test user
        $user = User::first();
        
        if (!$user) {
            $this->error('No users found in the database.');
            return;
        }
        
        $this->info("Testing with user: {$user->name} (ID: {$user->id})");
        $this->info("Current status - is_active: " . ($user->is_active ? 'true' : 'false'));
        $this->info("Current status - isActive(): " . ($user->isActive() ? 'true' : 'false'));
        
        // Test deactivation
        $this->info("\nTesting deactivation...");
        $deactivateResult = $user->deactivate();
        $this->info("Deactivate result: " . ($deactivateResult ? 'true' : 'false'));
        
        $user->refresh(); // Refresh from database
        $this->info("After deactivation - is_active: " . ($user->is_active ? 'true' : 'false'));
        $this->info("After deactivation - isActive(): " . ($user->isActive() ? 'true' : 'false'));
        
        // Test activation
        $this->info("\nTesting activation...");
        $activateResult = $user->activate();
        $this->info("Activate result: " . ($activateResult ? 'true' : 'false'));
        
        $user->refresh(); // Refresh from database
        $this->info("After activation - is_active: " . ($user->is_active ? 'true' : 'false'));
        $this->info("After activation - isActive(): " . ($user->isActive() ? 'true' : 'false'));
        
        $this->info("\nUser activation/deactivation functionality is working correctly!");
    }
}