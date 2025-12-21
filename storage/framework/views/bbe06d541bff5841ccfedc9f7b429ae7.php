<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payout Authorization Code - Rhymes Platform</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f5f7fa; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #F2426E 0%, #e74c3c 100%); padding: 30px; text-align: center; color: white;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 600;">Rhymes Platform</h1>
            <p style="margin: 10px 0 0; font-size: 16px; opacity: 0.9;">Payout Authorization Required</p>
        </div>
        
        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #1f2937; margin-top: 0; font-size: 24px; font-weight: 600;">Hello <?php echo e($user->name); ?>,</h2>
            
            <p style="font-size: 16px; color: #4b5563; margin-bottom: 25px;">
                You're receiving this email because a payout request was initiated on your Rhymes Platform account.
            </p>
            
            <div style="background: #eff6ff; border-radius: 8px; padding: 25px; margin: 30px 0; border-left: 4px solid #3b82f6;">
                <h3 style="color: #1f2937; margin-top: 0; font-size: 20px;">💰 Payout Authorization Code</h3>
                <p style="font-size: 16px; color: #4b5563; margin-bottom: 20px;">
                    Enter the following code to authorize your payout request:
                </p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <div style="background: linear-gradient(135deg, #F2426E 0%, #e74c3c 100%); color: white; padding: 20px; border-radius: 8px; display: inline-block; font-weight: 700; font-size: 32px; letter-spacing: 5px; box-shadow: 0 4px 6px rgba(242, 66, 110, 0.2);">
                        <?php echo e($otpCode); ?>

                    </div>
                </div>
                
                <p style="font-size: 15px; color: #4b5563; margin-top: 25px; text-align: center;">
                    This code will expire in <strong>10 minutes</strong>.
                </p>
            </div>
            
            <div style="background: #fffbeb; border-radius: 8px; padding: 20px; border-left: 4px solid #f59e0b; margin-bottom: 30px;">
                <p style="margin: 0; font-size: 15px; color: #92400e;">
                    <strong>Security Notice:</strong> If you didn't initiate this payout request, please contact our support team immediately at hello@travaiq.com.
                </p>
            </div>
            
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 5px;">
                <strong>Need help?</strong>
            </p>
            <p style="font-size: 14px; color: #6b7280; margin-top: 5px;">
                Contact our support team at hello@travaiq.com if you have any questions.
            </p>
        </div>
        
        <!-- Footer -->
        <div style="background: #f9fafb; padding: 25px 30px; border-top: 1px solid #e5e7eb; text-align: center;">
            <p style="margin: 0; color: #6b7280; font-size: 14px;">
                &copy; <?php echo e(date('Y')); ?> Rhymes Platform by Rovingheights Books Ltd. All rights reserved.
            </p>
            <p style="margin: 10px 0 0; color: #9ca3af; font-size: 13px;">
                123 Publishing Street, Literary District, Bookville
            </p>
        </div>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/emails/otp-payout.blade.php ENDPATH**/ ?>