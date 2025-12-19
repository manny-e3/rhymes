<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check configuration values';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Queue Connection: ' . config('queue.default'));
        $this->info('Mail Driver: ' . config('mail.default'));
        $this->info('Mail Mailer: ' . env('MAIL_MAILER'));
        $this->info('Database Connection: ' . config('database.default'));
        
        // Check mail configuration in detail
        $this->info('Mail Config:');
        $mailConfig = config('mail');
        foreach ($mailConfig['mailers'] as $mailer => $config) {
            $this->line("  {$mailer}: " . json_encode($config));
        }
    }
}