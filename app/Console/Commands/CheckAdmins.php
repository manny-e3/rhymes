<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckAdmins extends Command
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
    protected $description = 'Check admin users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        $this->info('Found ' . $admins->count() . ' admins:');
        
        foreach($admins as $admin) {
            $this->line('- ID: ' . $admin->id . ', Name: ' . $admin->name . ', Email: ' . $admin->email);
        }
    }
}