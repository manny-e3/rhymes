<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckAdminUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:admins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check admin users in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        $this->info('Found ' . $admins->count() . ' admin users:');
        
        foreach ($admins as $admin) {
            $this->line('- ID: ' . $admin->id . ', Name: ' . $admin->name . ', Email: ' . $admin->email);
        }
        
        if ($admins->count() == 0) {
            $this->warn('No admin users found! This could be why notifications are not being sent.');
        }
    }
}