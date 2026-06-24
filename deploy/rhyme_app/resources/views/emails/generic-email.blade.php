<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subject }} - Rhymes Platform</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f5f7fa; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #F2426E 0%, #e74c3c 100%); padding: 30px; text-align: center; color: white;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 600;">Rhymes Platform</h1>
            <p style="margin: 10px 0 0; font-size: 16px; opacity: 0.9;">{{ $subject }}</p>
        </div>
        
        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #1f2937; margin-top: 0; font-size: 24px; font-weight: 600;">Hello {{ $user->name }},</h2>
            
            <div style="font-size: 16px; color: #4b5563; margin-bottom: 25px; line-height: 1.8;">
                @php
                    $displayMessage = $message;
                    if (is_object($displayMessage)) {
                        // Handle various MailMessage types that might be passed
                        $className = get_class($displayMessage);
                        if ($className === 'Illuminate\\Mail\\Message' || 
                            $className === 'Illuminate\\Notifications\\Messages\\MailMessage') {
                            $displayMessage = 'Mail message object received instead of string content';
                        } elseif (method_exists($displayMessage, '__toString')) {
                            $displayMessage = (string) $displayMessage;
                        } else {
                            $displayMessage = $className;
                        }
                    } elseif (!is_string($displayMessage)) {
                        $displayMessage = (string) $displayMessage;
                    }
                @endphp
                {!! nl2br(e($displayMessage)) !!}
            </div>
            
            @if(isset($cta_url) && isset($cta_text))
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $cta_url }}" style="display: inline-block; background: linear-gradient(135deg, #F2426E 0%, #e74c3c 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(242, 66, 110, 0.3);">
                    {{ $cta_text }}
                </a>
            </div>
            @endif
            
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <p style="font-size: 14px; color: #6b7280; margin-bottom: 5px;">
                    <strong>Need help?</strong>
                </p>
                <p style="font-size: 14px; color: #6b7280; margin-top: 5px;">
                    Contact our support team if you have any questions.
                </p>
            </div>
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