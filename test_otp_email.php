<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Notifications\OTPNotification;

echo "=== OTP Email Test ===\n";

// Find a user to test with
$user = User::first();

if ($user) {
    echo "Testing OTP email for user: " . $user->email . "\n";
    
    // Generate a test OTP code
    $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    
    echo "Generated OTP: " . $otpCode . "\n";
    
    // Send the OTP email
    $user->notify(new OTPNotification($otpCode));
    
    echo "OTP email sent successfully!\n";
} else {
    echo "No user found in the database\n";
}

echo "=== Test Completed ===\n";