<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Payout Request - Rhymes Platform</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #F2426E;">Rhymes Platform</h1>
        </div>
        
        <div style="background: #f9fafb; border-radius: 8px; padding: 30px; margin-bottom: 30px;">
            <h2 style="color: #1f2937; margin-top: 0;">Hello {{ $admin->name }},</h2>
            
            <p>A new payout request has been submitted by an author.</p>
            
            <div style="background: #eff6ff; border-radius: 6px; padding: 20px; margin: 25px 0;">
                <h3 style="color: #1f2937; margin-top: 0;">📋 Payout Request Details</h3>
                
                <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
                    <tr>
                        <td style="padding: 8px; vertical-align: top; width: 40%;"><strong>Author:</strong></td>
                        <td style="padding: 8px; vertical-align: top;">{{ $payout->user->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; vertical-align: top;"><strong>Author Email:</strong></td>
                        <td style="padding: 8px; vertical-align: top;">{{ $payout->user->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; vertical-align: top;"><strong>Amount Requested:</strong></td>
                        <td style="padding: 8px; vertical-align: top;">₦{{ number_format($payout->amount_requested, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; vertical-align: top;"><strong>Payment Method:</strong></td>
                        <td style="padding: 8px; vertical-align: top;">{{ ucfirst($payout->payment_method) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; vertical-align: top;"><strong>Requested Date:</strong></td>
                        <td style="padding: 8px; vertical-align: top;">{{ $payout->created_at->format('M d, Y \a\t g:i A') }}</td>
                    </tr>
                </table>
                
                <p style="margin-top: 20px;">Please review this payout request at your earliest convenience.</p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/admin/payouts/' . $payout->id) }}" 
                   style="background: #F2426E; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                    Review Payout Request
                </a>
            </div>
            
            <p>You can approve or deny this payout request from the admin panel.</p>
        </div>
        
        <div style="text-align: center; color: #6b7280; font-size: 14px;">
            <p>&copy; {{ date('Y') }} Rhymes Platform. All rights reserved.</p>
            <p>Rovingheights Books Ltd.</p>
        </div>
    </div>
</body>
</html>