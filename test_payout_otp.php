<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;

echo "=== Payout OTP Test ===\n";

// Find a user to test with
$user = User::first();

if ($user) {
    echo "Testing OTP for user: " . $user->email . "\n";
    
    // Enable OTP for the user
    $user->update(['otp_enabled' => true]);
    
    // Generate OTP
    $otpCode = $user->generateOTP();
    echo "Generated OTP: " . $otpCode . "\n";
    
    // Test OTP verification
    $isValid = $user->verifyOTP($otpCode);
    echo "OTP verification result: " . ($isValid ? "PASS" : "FAIL") . "\n";
    
    // Clear OTP
    $user->clearOTP();
    echo "OTP cleared\n";
} else {
    echo "No user found in the database\n";
}

echo "=== Test Completed ===\n";