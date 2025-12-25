# Email Module Documentation

## Overview
This module provides functionality for sending both bulk emails to authors and personal emails to users within the Rhymes Platform.

## Components Created

### 1. EmailNotification Class
- Located at: `app/Notifications/EmailNotification.php`
- Handles sending emails with customizable subjects, messages, and templates
- Supports both generic and custom email templates

### 2. EmailService
- Located at: `app/Services/EmailService.php`
- Provides methods for sending personal emails and bulk emails
- Includes functions for sending emails to authors, specific user groups, and all users
- Features email statistics and logging

### 3. Email Controller
- Located at: `app/Http/Controllers/Admin/EmailController.php`
- Handles the admin interface for email management
- Provides routes for sending bulk emails and personal emails

### 4. Email Views
- Located at: `resources/views/admin/emails/`
- Includes views for email dashboard, bulk email form, and personal email form
- Responsive admin interface for email management

### 5. Routes
- Added to `routes/web.php` in the admin section
- Includes routes for email dashboard, sending bulk emails, and personal emails

### 6. Sidebar Integration
- Added "Email Management" menu item to the admin sidebar
- Accessible via the admin panel

## Features

### Bulk Email Functionality
- Send emails to all authors
- Send emails to selected users
- Send emails to users based on role filters
- Send emails to users based on custom criteria

### Personal Email Functionality
- Send personalized emails to specific users
- Supports custom templates
- Comprehensive logging

### Email Templates
- Uses the generic email template by default
- Supports existing templates like OTP, book status, payout status, and verify email
- Customizable template system

## Usage

### Admin Panel Access
1. Log in to the admin panel
2. Navigate to "Email Management" in the sidebar
3. Choose from the available options:
   - Send Bulk Email to Authors
   - Send Personal Email
   - Send Bulk Email to Selected Users

### API Usage
The EmailService can also be used programmatically:

```php
use App\Services\EmailService;

$emailService = new EmailService();

// Send personal email
$emailService->sendPersonalEmail($user, $subject, $message);

// Send bulk emails to authors
$emailService->sendBulkEmailsToAuthors($subject, $message);

// Send bulk emails to specific users
$emailService->sendBulkEmails($userIds, $subject, $message);
```

## Email Templates
The module supports the following templates:
- Default generic template
- OTP code template
- Book status changed template
- Payout status changed template
- Verify email template

## Security
- Only users with admin role can access the email management interface
- All email operations are logged for audit purposes
- Input validation is performed on all email content

## Configuration
Email settings are configured in the standard Laravel mail configuration (`config/mail.php`).
The application uses the default mailer configured in the `.env` file.

## Logging
All email operations are logged with:
- User information
- Email subject
- Success/failure status
- Timestamp
- Error details (if any)

## Testing
To test email functionality in development mode, make sure your `.env` file has the appropriate mail settings.
For development, you can use the 'log' mailer to see emails in the log files instead of sending them.