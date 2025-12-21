<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\UserPasswordReset;
use App\Notifications\VerifyEmail;
use App\Notifications\OTPNotification;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'website',
        'bio',
        'email_verified_at',
        'payment_details',
        'promoted_to_author_at',
        'otp_code',
        'otp_expires_at',
        'otp_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'payment_details' => 'array',
            'promoted_to_author_at' => 'datetime',
            'deleted_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'otp_enabled' => 'boolean',
        ];
    }

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'otp_enabled' => true,
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UserPasswordReset($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Generate and send OTP code to user
     *
     * @return string
     */
    public function generateOTP()
    {
        // Generate a 6-digit random number
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Set expiration time (10 minutes from now)
        $expiresAt = now()->addMinutes(10);
        
        // Save to user record
        $this->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => $expiresAt,
            'otp_enabled' => true,
        ]);
        
        // Send OTP via email
        $this->notify(new OTPNotification($otpCode));
        
        return $otpCode;
    }

    /**
     * Verify OTP code
     *
     * @param string $otpCode
     * @return bool
     */
    public function verifyOTP($otpCode)
    {
        // Check if OTP is enabled
        if (!$this->otp_enabled) {
            return false;
        }
        
        // Check if OTP has expired
        if ($this->otp_expires_at->isPast()) {
            return false;
        }
        
        // Check if OTP matches
        return hash_equals($this->otp_code, $otpCode);
    }

    /**
     * Clear OTP after successful verification
     *
     * @return void
     */
    public function clearOTP()
    {
        $this->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);
    }

    // Relationships
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // Helper methods
    public function isAuthor()
    {
        return $this->hasRole('author') || $this->hasRole('admin');
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function getWalletBalance()
    {
        return $this->walletTransactions()->sum('amount');
    }
}