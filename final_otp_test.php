<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== OTP Implementation Test ===\n";

// Create a test user if none exists
$user = User::first();
if (!$user) {
    echo "No user found. Creating a test user...\n";
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);
    echo "Test user created.\n";
}

echo "User: " . $user->email . "\n";

// Test 1: Generate OTP
echo "\n--- Test 1: Generate OTP ---\n";
$otpCode = $user->generateOTP();
echo "Generated OTP: " . $otpCode . "\n";
echo "Expires at: " . $user->otp_expires_at . "\n";
echo "OTP Enabled: " . ($user->otp_enabled ? 'Yes' : 'No') . "\n";

// Test 2: Verify valid OTP
echo "\n--- Test 2: Verify Valid OTP ---\n";
$isValid = $user->verifyOTP($otpCode);
echo "OTP verification result: " . ($isValid ? "PASS" : "FAIL") . "\n";

// Test 3: Verify invalid OTP
echo "\n--- Test 3: Verify Invalid OTP ---\n";
$isInvalid = $user->verifyOTP('000000');
echo "Invalid OTP verification result: " . ($isInvalid ? "FAIL - Should be invalid" : "PASS - Correctly rejected") . "\n";

// Test 4: Clear OTP
echo "\n--- Test 4: Clear OTP ---\n";
$user->clearOTP();
echo "OTP cleared\n";
echo "OTP Code: " . ($user->otp_code ?? 'NULL') . "\n";
echo "OTP Expires: " . ($user->otp_expires_at ?? 'NULL') . "\n";

// Test 5: Expired OTP
echo "\n--- Test 5: Expired OTP Test ---\n";
$otpCode = $user->generateOTP();
// Manually set expiration to past
$user->update(['otp_expires_at' => now()->subMinutes(5)]);
$isExpired = $user->verifyOTP($otpCode);
echo "Expired OTP verification result: " . ($isExpired ? "FAIL - Should be expired" : "PASS - Correctly rejected") . "\n";

// Clean up
$user->clearOTP();

echo "\n=== All Tests Completed ===\n";