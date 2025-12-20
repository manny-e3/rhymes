<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verify Your Email Address - Rhymes Platform</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f5f7fa; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #F2426E 0%, #e74c3c 100%); padding: 30px; text-align: center; color: white;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 600;">Rhymes Platform</h1>
            <p style="margin: 10px 0 0; font-size: 16px; opacity: 0.9;">Welcome to Rovingheights Books</p>
        </div>
        
        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #1f2937; margin-top: 0; font-size: 24px; font-weight: 600;">Hello {{ $user->name }},</h2>
            
            <p style="font-size: 16px; color: #4b5563; margin-bottom: 25px;">
                Thank you for joining Rhymes Platform! We're excited to have you as part of our community of authors.
            </p>
            
            <div style="background: #eff6ff; border-radius: 8px; padding: 25px; margin: 30px 0; border-left: 4px solid #3b82f6;">
                <h3 style="color: #1f2937; margin-top: 0; font-size: 20px;">🔒 Account Verification Required</h3>
                <p style="font-size: 16px; color: #4b5563; margin-bottom: 20px;">
                    To activate your account and gain full access to all features, please verify your email address by clicking the button below:
                </p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $verificationUrl }}" 
                       style="background: linear-gradient(135deg, #F2426E 0%, #e74c3c 100%); color: white; padding: 14px 28px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(242, 66, 110, 0.2);">
                        Verify My Email Address
                    </a>
                </div>
            </div>
            
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 25px;">
                <strong>This verification link will expire in 60 minutes.</strong>
            </p>
            
            <div style="background: #fffbeb; border-radius: 8px; padding: 20px; border-left: 4px solid #f59e0b; margin-bottom: 30px;">
                <p style="margin: 0; font-size: 15px; color: #92400e;">
                    <strong>Didn't create an account?</strong> If you didn't register for Rhymes Platform, you can safely ignore this email.
                </p>
            </div>
            
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 5px;">
                <strong>Having trouble with the button?</strong>
            </p>
            <p style="font-size: 14px; color: #6b7280; margin-top: 5px;">
                Copy and paste this link into your browser:
            </p>
            <p style="word-break: break-all; color: #4b5563; font-size: 14px; background: #f9fafb; padding: 12px; border-radius: 4px; font-family: monospace;">
                {{ $verificationUrl }}
            </p>
        </div>
        
        <!-- Footer -->
        <div style="background: #f9fafb; padding: 25px 30px; border-top: 1px solid #e5e7eb; text-align: center;">
            <p style="margin: 0; color: #6b7280; font-size: 14px;">
                &copy; {{ date('Y') }} Rhymes Platform by Rovingheights Books Ltd. All rights reserved.
            </p>
            <p style="margin: 10px 0 0; color: #9ca3af; font-size: 13px;">
                123 Publishing Street, Literary District, Bookville
            </p>
        </div>
    </div>
</body>
</html>