# OTP Implementation Documentation

## Overview
This document explains the implementation of One-Time Password (OTP) functionality for enhanced security in the Rhymes Author Platform.

## Features Implemented
1. OTP generation and email delivery
2. OTP verification with expiration
3. Two-factor authentication flow
4. OTP resending capability
5. OTP for financial operations (payouts)

## Technical Details

### Database Changes
Added three new columns to the `users` table:
- `otp_code` (string, nullable) - Stores the generated OTP code
- `otp_expires_at` (timestamp, nullable) - Expiration time for the OTP
- `otp_enabled` (boolean, default: true) - Flag to enable/disable OTP functionality

### Key Components

#### 1. User Model Enhancements
The `User` model (`app/Models/User.php`) includes new methods:
- `generateOTP()` - Generates a 6-digit code, sets expiration (10 minutes), and sends via email
- `verifyOTP($otpCode)` - Validates the provided OTP code
- `clearOTP()` - Clears OTP data after successful verification

#### 2. OTP Notification
A new notification class `OTPNotification` sends the OTP code via email to the user.

#### 3. OTP Controller
The `OTPController` handles:
- Displaying the OTP verification form
- Verifying the submitted OTP code
- Resending OTP codes
- Separate methods for payout OTP verification

#### 4. Authentication Flow Modification
The login process now includes an additional step:
1. User enters email/password
2. System validates credentials
3. System generates and emails OTP code
4. User redirected to OTP verification page
5. User enters OTP code
6. System verifies OTP and completes login

#### 5. Payout Security Enhancement
Financial operations now require additional verification:
1. User submits payout request
2. System checks if OTP is enabled
3. If enabled, redirects to OTP verification page
4. User receives OTP via email
5. User enters OTP code
6. System verifies OTP and processes payout

### Routes Added
- `GET /otp` - Display OTP verification form
- `POST /otp/verify` - Verify OTP code
- `POST /otp/resend` - Resend OTP code
- `GET /otp/payout` - Display payout OTP verification form
- `POST /otp/payout/verify` - Verify payout OTP code
- `POST /otp/payout/resend` - Resend payout OTP code

### Views Added
- `resources/views/auth/otp.blade.php` - OTP verification form
- `resources/views/auth/otp-payout.blade.php` - Payout OTP verification form

## Security Considerations
1. OTP codes are 6 digits for usability while maintaining security
2. Codes expire after 10 minutes
3. Rate limiting prevents brute force attacks
4. OTP codes are not stored in plain text
5. Failed attempts are logged for security monitoring
6. Session-based verification prevents replay attacks
7. Automatic cleanup after verification

## Usage
The OTP functionality is automatically enabled for all users during the login process. For payouts, OTP is required when enabled for financial security.

## Testing
To test the OTP functionality:
1. Navigate to the login page
2. Enter valid credentials
3. Check email for OTP code
4. Enter OTP code on verification page
5. User should be logged in successfully

To test payout OTP:
1. Ensure user has OTP enabled
2. Navigate to payout request page
3. Fill in amount and submit
4. Verify redirection to OTP page
5. Check email for OTP code
6. Enter OTP code
7. Verify redirection back to payout page
8. Confirm payout request is processed

## Future Enhancements
1. Support for SMS-based OTP
2. Time-based One-Time Password (TOTP) using authenticator apps
3. Backup codes for recovery
4. Device recognition for trusted devices
5. Threshold-based OTP requirements for high-value transactions