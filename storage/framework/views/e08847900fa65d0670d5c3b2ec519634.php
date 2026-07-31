<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password - Rhymes Platform</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #F2426E;">Rhymes Platform</h1>
        </div>
        
        <div style="background: #f9fafb; border-radius: 8px; padding: 30px; margin-bottom: 30px;">
            <h2 style="color: #1f2937; margin-top: 0;">Hello <?php echo e($user->name); ?>,</h2>
            
            <p>You are receiving this email because we received a password reset request for your account.</p>
            
            <div style="background: #eff6ff; border-radius: 6px; padding: 20px; margin: 25px 0;">
                <h3 style="color: #1f2937; margin-top: 0;">🔐 Password Reset Request</h3>
                <p>Click the button below to reset your password:</p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="<?php echo e($resetUrl); ?>" 
                   style="background: #F2426E; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                    Reset Password
                </a>
            </div>
            
            <p>This password reset link will expire in <?php echo e($count); ?> minutes.</p>
            
            <p style="background: #fffbeb; border-radius: 6px; padding: 15px;">
                <strong>If you did not request a password reset, no further action is required.</strong>
            </p>
            
            <p>If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</p>
            <p style="word-break: break-all; color: #6b7280; font-size: 14px;"><?php echo e($resetUrl); ?></p>
        </div>
        
        <div style="text-align: center; color: #6b7280; font-size: 14px;">
            <p>&copy; <?php echo e(date('Y')); ?> Rhymes Platform. All rights reserved.</p>
            <p>Rovingheights Books Ltd.</p>
        </div>
    </div>
</body>
</html><?php /**PATH /home/rovinghe/author.rovingheights.com/resources/views/emails/password-reset.blade.php ENDPATH**/ ?>