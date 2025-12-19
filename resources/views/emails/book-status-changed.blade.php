<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Book Status Update - Rhymes Platform</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #F2426E;">Rhymes Platform</h1>
        </div>
        
        <div style="background: #f9fafb; border-radius: 8px; padding: 30px; margin-bottom: 30px;">
            <h2 style="color: #1f2937; margin-top: 0;">Hello {{ $user->name }},</h2>
            
           
            
            @if(trim($newStatus) === 'approved_awaiting_delivery')
               
                <div style="background: #dcfce7; border-radius: 6px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #1f2937; margin-top: 0;">📦 Please Deliver Physical Copies</h3>
                    <p>Your book has been approved and you need to deliver the physical copies as instructed.</p>
                    {{-- <p>You have been promoted to Author status and can now access your author dashboard.</p> --}}
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ route('dashboard') }}" 
                       style="background: #F2426E; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                        View Author Dashboard
                    </a>
                </div>
            @elseif(trim($newStatus) === 'stocked')
                <p>Your book "<strong>{{ $book->title }}</strong>" approved. 🚀 Great News!</p>
                
                <div style="background: #dbeafe; border-radius: 6px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #1f2937; margin-top: 0;">📚 Your Book is Now Available!</h3>
                    <p>Your book is now available in our inventory.</p>
                    <p>Sales tracking is now active and you can monitor your earnings.</p>
                    @if($book->admin_notes)
                        <p><strong>Admin notes:</strong> {{ $book->admin_notes }}</p>
                    @endif
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ route('author.wallet.index') }}" 
                       style="background: #F2426E; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                        View Wallet
                    </a>
                </div>
            @elseif(trim($newStatus) === 'pending_review')
                <p>Your book "<strong>{{ $book->title }}</strong>" status has been updated to <strong>{{ ucfirst(str_replace('_', ' ', $newStatus)) }}</strong>.</p>
                
                <div style="background: #fffbeb; border-radius: 6px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #1f2937; margin-top: 0;">📋 Book Submitted for Review</h3>
                    <p>Your book has been successfully submitted and is now pending review by our team.</p>
                    <p>We'll review your submission and get back to you soon.</p>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ route('dashboard') }}" 
                       style="background: #F2426E; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                        View Dashboard
                    </a>
                </div>
            @elseif(trim($newStatus) === 'send_review_copy')
                <p>Your book "<strong>{{ $book->title }}</strong>" status has been updated to <strong>{{ ucfirst(str_replace('_', ' ', $newStatus)) }}</strong>.</p>
                
                <div style="background: #eff6ff; border-radius: 6px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #1f2937; margin-top: 0;">📧 Review Copy Requested</h3>
                    <p>Our team has requested a review copy of your book "<strong>{{ $book->title }}</strong>".</p>
                    @if($book->admin_notes)
                        <p><strong>Admin notes:</strong> {{ $book->admin_notes }}</p>
                    @endif
                    <p>Please check your dashboard for instructions on how to provide the review copy.</p>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ route('dashboard') }}" 
                       style="background: #F2426E; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                        View Dashboard
                    </a>
                </div>
            @elseif(trim($newStatus) === 'rejected')
                <p>Your book "<strong>{{ $book->title }}</strong>" status has been updated to <strong>{{ ucfirst(str_replace('_', ' ', $newStatus)) }}</strong>.</p>
                
                <div style="background: #fee2e2; border-radius: 6px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #1f2937; margin-top: 0;">⚠️ Book Rejected</h3>
                    <p>Unfortunately, your book submission was not accepted at this time.</p>
                    @if($book->admin_notes)
                        <p><strong>Admin notes:</strong> {{ $book->admin_notes }}</p>
                    @else
                        <p>No additional notes provided.</p>
                    @endif
                    <p>You can edit and resubmit your book with improvements.</p>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ route('author.books.edit', $book) }}" 
                       style="background: #F2426E; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                        Edit Book
                    </a>
                </div>
            @else
                <p>Your book "<strong>{{ $book->title }}</strong>" status has been updated to <strong>{{ ucfirst(str_replace('_', ' ', $newStatus)) }}</strong>.</p>
                <p style="color: red; font-size: 12px;">DEBUG: Unexpected status "{{ $newStatus }}" - using fallback message</p>
            @endif
            
            <p>Thank you for being part of the Rhymes platform!</p>
        </div>
        
        <div style="text-align: center; color: #6b7280; font-size: 14px;">
            <p>&copy; {{ date('Y') }} Rhymes Platform. All rights reserved.</p>
            <p>Rovingheights Books Ltd.</p>
        </div>
    </div>
</body>
</html>