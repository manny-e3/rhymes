<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Payout settings
        Setting::set('min_payout_amount', 300000);
        Setting::set('payout_fee', 2.5);
        Setting::set('payout_frequency_days', 30);
        Setting::set('payout_processing_time_min', 3);
        Setting::set('payout_processing_time_max', 5);
        
        // Other settings
        Setting::set('site_description', 'Rhymes Platform - Submit your books to Rovingheights for stocking consideration');
        Setting::set('contact_email', 'support@rhymesplatform.com');
        Setting::set('support_email', 'support@rhymesplatform.com');
    }
}