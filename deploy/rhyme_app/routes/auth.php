<?php

use App\Http\Controllers\Auth\CustomAuthController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Custom Auth Routes
    Route::get('register', [CustomAuthController::class, 'showRegister'])
        ->name('register');

    Route::post('register', [CustomAuthController::class, 'register']);

    Route::get('login', [CustomAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('login', [CustomAuthController::class, 'login']);

    // OTP Routes
    Route::get('otp', [OTPController::class, 'showOTPForm'])
        ->name('otp.show');
        
    Route::post('otp/verify', [OTPController::class, 'verifyOTP'])
        ->name('otp.verify');
        
    Route::post('otp/resend', [OTPController::class, 'resendOTP'])
        ->name('otp.resend');
        
    // Payout OTP Routes
    Route::get('otp/payout', [OTPController::class, 'showPayoutOTPForm'])
        ->name('otp.payout.show');
        
    Route::post('otp/payout/verify', [OTPController::class, 'verifyPayoutOTP'])
        ->name('otp.payout.verify');
        
    Route::post('otp/payout/resend', [OTPController::class, 'resendPayoutOTP'])
        ->name('otp.payout.resend');

    // Keep password reset functionality from Breeze
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    // Custom logout
    Route::post('logout', [CustomAuthController::class, 'logout'])
        ->name('logout');

    // Keep email verification from Breeze
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Keep password confirmation from Breeze
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
});