<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Book Recall Request - Rhymes Platform</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: #f9fafb;
            text-align: center;
            padding: 30px 20px;
            border-bottom: 1px solid #e5e7eb;
        }
        .logo {
            color: #F2426E;
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }
        .content {
            padding: 30px;
            background: #f9fafb;
        }
        .greeting {
            color: #1f2937;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 20px;
        }
        .recall-info {
            background: #fef2f2;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid #ef4444;
        }
        .book-details {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid #3b82f6;
        }
        .cta-button-container {
            text-align: center;
            margin: 30px 0;
        }
        .cta-button {
            background: #F2426E;
            color: white !important;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 6px;
            display: inline-block;
            font-weight: bold;
            font-size: 16px;
            transition: background 0.3s;
        }
        .cta-button:hover {
            background: #d13a5f;
            text-decoration: none;
        }
        .reason-section {
            background: #fffbeb;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            border-left: 3px solid #f59e0b;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            padding: 20px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="logo">Rhymes Platform</h1>
        </div>
        
        <div class="content">
            <h2 class="greeting">Dear Admin,</h2>
            
            <div class="recall-info">
                <h3 style="color: #dc2626; margin-top: 0;">
                    <i>⚠️</i> Book Recall Request
                </h3>
                <p>The author <strong>{{ $author->name }}</strong> ({{ $author->email }}) has requested to recall their book:</p>
                <h4 style="color: #1f2937;">{{ $book->title }}</h4>
            </div>
            
            <div class="book-details">
                <h4 style="margin-top: 0;">Book Details</h4>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;"><strong>Author:</strong></td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $author->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;"><strong>Email:</strong></td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $author->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;"><strong>Book Title:</strong></td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $book->title }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;"><strong>Genre:</strong></td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $book->genre }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;"><strong>ISBN:</strong></td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $book->isbn ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;"><strong>Status:</strong></td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">
                            <span style="background-color: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 4px;">
                                {{ ucfirst(str_replace('_', ' ', $book->status)) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;"><strong>Submitted Date:</strong></td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $book->created_at->format('M d, Y') }}</td>
                    </tr>
                </table>
            </div>
            
            @if($recallReason)
            <div class="reason-section">
                <h4 style="margin-top: 0; color: #d97706;">Recall Reason</h4>
                <p style="margin: 10px 0;">{{ $recallReason }}</p>
            </div>
            @else
            <div class="reason-section">
                <h4 style="margin-top: 0; color: #d97706;">Recall Reason</h4>
                <p style="margin: 10px 0;"><em>No specific reason provided by the author.</em></p>
            </div>
            @endif
            
            <p>As an admin, you may want to review this request and take appropriate action if necessary.</p>
            
            <div class="cta-button-container">
                <a href="{{ route('admin.books.show', $book) }}" class="cta-button">
                    View Book Details
                </a>
            </div>
            
            <p>Thank you for managing the Rhymes platform!</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Rhymes Platform. All rights reserved.</p>
            <p>Rovingheights Books Ltd.</p>
        </div>
    </div>
</body>
</html>