<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// Find a user to test with
$user = User::first();

if ($user) {
    echo "Testing OTP generation for user: " . $user->email . "\n";
    
    // Generate OTP
    $otpCode = $user->generateOTP();
    
    echo "Generated OTP: " . $otpCode . "\n";
    echo "OTP expires at: " . $user->otp_expires_at . "\n";
    
    // Test verification
    $isValid = $user->verifyOTP($otpCode);
    echo "OTP verification result: " . ($isValid ? "Valid" : "Invalid") . "\n";
    
    // Test invalid OTP
    $isInvalid = $user->verifyOTP('123456');
    echo "Invalid OTP verification result: " . ($isInvalid ? "Valid" : "Invalid") . "\n";
    
    // Clear OTP
    $user->clearOTP();
    echo "OTP cleared\n";
} else {
    echo "No user found in the database\n";
}