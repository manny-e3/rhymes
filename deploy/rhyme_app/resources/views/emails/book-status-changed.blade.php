<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Book Status Update - Rhymes Platform</title>
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
        .status-box {
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid;
        }
        .delivery-info {
            background: #f0fdf4;
            border-left-color: #22c55e;
        }
        .stocked-info {
            background: #eff6ff;
            border-left-color: #3b82f6;
        }
        .review-info {
            background: #fffbeb;
            border-left-color: #f59e0b;
        }
        .review-process {
            background: #fef3c7;
            border-left-color: #f59e0b;
        }
        .rejected-info {
            background: #fef2f2;
            border-left-color: #ef4444;
        }
        .status-title {
            color: #1f2937;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
        }
        .status-title i {
            margin-right: 10px;
            font-size: 20px;
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
        .admin-notes {
            background: #f3f4f6;
            border-radius: 6px;
            padding: 12px;
            margin: 15px 0;
            font-style: italic;
            border-left: 3px solid #9ca3af;
        }
        .address {
            background: #f8fafc;
            border-radius: 6px;
            padding: 15px;
            margin: 10px 0;
            border-left: 3px solid #3b82f6;
        }
        .debug-message {
            color: #dc2626;
            font-size: 12px;
            font-weight: bold;
            background: #fef2f2;
            padding: 8px;
            border-radius: 4px;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            padding: 20px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
        .address p {
            margin: 5px 0;
        }
        .address strong {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="logo">Rhymes Platform</h1>
        </div>
        
        <div class="content">
            <h2 class="greeting">Dear {{ $user->name }},</h2>
            
            @if(trim($newStatus) === 'approved_awaiting_delivery')
                <div class="status-box delivery-info">
                    
                    <p>We are pleased to inform you that your book <strong>{{ $book->title }}</strong> has been accepted for sale at Rovingheights Bookstore.</p>
                    
                    <p>Please mail 20 copies to any of our locations listed below:</p>
                    
                    <div class="address">
                        <strong>Lagos Drop-off Location:</strong>
                        <p>Digital Bridge Institute</p>
                        <p>1, Nitel Road, Cappa Oshodi, Lagos</p>
                        <p>Contact: Bidemi – 0810 411 3185</p>
                    </div>
                    
                    <div class="address">
                        <strong>Abuja Drop-off Location:</strong>
                        <p>CVS Plaza, Shop 4.1, Block B</p>
                        <p>145, Ademola Adetokunbo Crescent, opposite Oti Carpets, Wuse 2, Abuja</p>
                        <p>Contact: 0902 666 6195</p>
                    </div>
                    
                    <p>Please note that shipping costs will be covered by the author/supplier. You are also required to attach a printed copy of this approval email along with the books to ensure proper identification and processing. Additionally, authors/suppliers are responsible for promoting their books.</p>
                    
                    <p>Once your books have been stocked, we will send you the URL to your book's listing on our website. Please allow up to one week after delivery for processing and listing.</p>
                    
                    <p>Kindly note that it is your responsibility to request a monthly sales report. You are required to send us a reminder email each month if you wish to receive a sales report.</p>
                    
                    <p>Remittances will only be processed to Nigerian bank accounts. Payments will be made after sales have been recorded and processed.</p>
                    
                    <p>If less than 50% of the supplied stock is sold within three months, we will remove the remaining stock from our shelves and notify you to retrieve the unsold copies. Kindly note that the review copy is non-returnable. If you wish to initiate recovery at any time, please give us at least one week's notice so we can prepare the books for pickup.</p>
                    
                    <p>If you have any further questions or require clarification, please feel free to reach out to us at rovingheights@gmail.com.</p>
                    
                    <p>We look forward to a fruitful partnership.</p>
                    
                    <p>Regards,</p>
                    <p>Team Rovingheights.</p>
                    
                    @if((isset($adminNotes) && $adminNotes) || $book->admin_notes)
                        <div class="admin-notes">
                            <strong>Admin notes:</strong> 
                            @if(isset($adminNotes) && $adminNotes)
                                {{ $adminNotes }}
                            @elseif($book->admin_notes)
                                {{ $book->admin_notes }}
                            @endif
                        </div>
                    @endif
                </div>
            @elseif(trim($newStatus) === 'stocked')
                @if($book->getOriginal('status') === 'edited_pending_approval')
                    <p>Your book "<strong>{{ $book->title }}</strong>" edit has been approved! 🚀 Great News!</p>
                    
                    <div class="status-box stocked-info">
                        <h3 class="status-title"><i>📚</i> Your Book Edit Has Been Approved!</h3>
                        <p>Your book changes have been approved and are now reflected in our inventory.</p>
                        <p>Sales tracking continues to be active and you can monitor your earnings.</p>
                        
                        @if((isset($adminNotes) && $adminNotes) || $book->admin_notes)
                            <div class="admin-notes">
                                <strong>Admin notes:</strong> 
                                @if(isset($adminNotes) && $adminNotes)
                                    {{ $adminNotes }}
                                @elseif($book->admin_notes)
                                    {{ $book->admin_notes }}
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <p>Your book "<strong>{{ $book->title }}</strong>" has been approved. 🚀 Great News!</p>
                    
                    <div class="status-box stocked-info">
                        <h3 class="status-title"><i>📚</i> Your Book is Now Available!</h3>
                        <p>Your book is now available in our inventory.</p>
                        <p>Sales tracking is now active and you can monitor your earnings.</p>
                        
                        @if((isset($adminNotes) && $adminNotes) || $book->admin_notes)
                            <div class="admin-notes">
                                <strong>Admin notes:</strong> 
                                @if(isset($adminNotes) && $adminNotes)
                                    {{ $adminNotes }}
                                @elseif($book->admin_notes)
                                    {{ $book->admin_notes }}
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
                
                <div class="cta-button-container">
                    <a href="{{ route('author.wallet.index') }}" class="cta-button">
                        View Wallet
                    </a>
                </div>
            @elseif(trim($newStatus) === 'pending_review')
                <p>Your book "<strong>{{ $book->title }}</strong>" status has been updated to <strong>{{ ucfirst(str_replace('_', ' ', $newStatus)) }}</strong>.</p>
                
               
                     <div class="status-box delivery-info">
                    
                    <h3 class="status-title"><i>📋</i> Book Submitted for Review</h3>
                    <p>Your book has been successfully submitted and is now pending review by our team.</p>
                    <p>We'll review your submission and get back to you soon.</p>
                </div>
                
                <div class="cta-button-container">
                    <a href="{{ route('dashboard') }}" class="cta-button">
                        View Dashboard
                    </a>
                </div>
            @elseif(trim($newStatus) === 'send_review_copy')

             <div class="status-box delivery-info">
                    
                    <p>Thank you for submitting your book details to us. We appreciate your interest in partnering with Rovingheights. As the next step in our review process, we require a copy of your book for evaluation, <strong>{{ $book->title }}</strong>.</p>
                    
                    <p>If you are in Lagos, please submit a copy to:</p>
                    
                    <div class="address">
                        <strong>Rovingheights Bookstore</strong>
                        <p>28, Ogunlana Drive, Surulere.</p>
                        <p>Contact: 0810 979 5365</p>
                    </div>
                    
                    <p>If you are in Abuja, please submit a copy to:</p>

                    <div class="address">
                        <strong>Rovingheights Bookstore</strong>
                        <p>Shop S01, 2nd floor, City Centre Mall, Gimbiya Street, Area 11, Garki.</p>
                        <p>Contact: 0802 828 7089</p>
                    </div>
                    
                    <p>The review process typically takes 1–2 weeks. Once it is completed, we will communicate our final stocking decision. If we are unable to proceed with stocking your book, we will notify you. Please note that submitted copies are not returned.</p>

                    <p>As part of our evaluation, we will assess the content of the book, as well as the printing and production quality. Additionally, your book should have a valid ISBN and a scannable barcode.</p>

                    <p>We require that only one copy of your book be submitted for review. If more than one copy is dropped off, any extra copies will be returned.</p>

                    <p>Once you have submitted your book, please reply to this email to confirm the drop-off.</p>

                    <p>If you have any other inquiries, please direct them to rovingheights@gmail.com.</p>

                    <p>We look forward to your submission.</p>

                    <p>Regards,</p>
                    <p>Team Rovingheights.</p>
                    
                    @if((isset($adminNotes) && $adminNotes) || $book->admin_notes)
                        <div class="admin-notes">
                            <strong>Admin notes:</strong> 
                            @if(isset($adminNotes) && $adminNotes)
                                {{ $adminNotes }}
                            @elseif($book->admin_notes)
                                {{ $book->admin_notes }}
                            @endif
                        </div>
                    @endif
                </div>


               

            @elseif(trim($newStatus) === 'edited_pending_approval')
                <p>Your book "<strong>{{ $book->title }}</strong>" has been edited and remains stocked.</p>
                
                <div class="status-box review-info">
                    <h3 class="status-title"><i>📋</i> Book Successfully Updated</h3>
                    <p>Your book has been successfully updated and remains in the stocked status.</p>
                    <p>An admin has been notified of your changes.</p>
                    
                    @if((isset($adminNotes) && $adminNotes) || $book->admin_notes)
                        <div class="admin-notes">
                            <strong>Admin notes:</strong> 
                            @if(isset($adminNotes) && $adminNotes)
                                {{ $adminNotes }}
                            @elseif($book->admin_notes)
                                {{ $book->admin_notes }}
                            @endif
                        </div>
                    @endif
                </div>
                
                <div class="cta-button-container">
                    <a href="{{ route('dashboard') }}" class="cta-button">
                        View Dashboard
                    </a>
                </div>
            @elseif(trim($newStatus) === 'rejected')
                <p>Your book "<strong>{{ $book->title }}</strong>" status has been updated to <strong>{{ ucfirst(str_replace('_', ' ', $newStatus)) }}</strong>.</p>
                
                <div class="status-box rejected-info">
                    <h3 class="status-title"><i>⚠️</i> Book Rejected</h3>
                    <p>Unfortunately, your book submission was not accepted at this time.</p>
                    
                    @if((isset($adminNotes) && $adminNotes) || $book->admin_notes)
                        <div class="admin-notes">
                            <strong>Admin notes:</strong> 
                            @if(isset($adminNotes) && $adminNotes)
                                {{ $adminNotes }}
                            @elseif($book->admin_notes)
                                {{ $book->admin_notes }}
                            @endif
                        </div>
                    @else
                        <p>No additional notes provided.</p>
                    @endif
                    
                    <p>You can edit and resubmit your book with improvements.</p>
                </div>
                
                <div class="cta-button-container">
                    <a href="{{ route('author.books.edit', $book) }}" class="cta-button">
                        Edit Book
                    </a>
                </div>
            @elseif(trim($newStatus) === 'recalled')
                <p>Your book "<strong>{{ $book->title }}</strong>" status has been updated to <strong>Retrieved</strong>.</p>
                
                <div class="status-box rejected-info">
                    <h3 class="status-title"><i>⚠️</i> Book Retrieved</h3>
                    <p>Your book has been retrieved from our inventory.</p>
                    
                    @if((isset($adminNotes) && $adminNotes) || $book->admin_notes)
                        <div class="admin-notes">
                            <strong>Admin notes:</strong> 
                            @if(isset($adminNotes) && $adminNotes)
                                {{ $adminNotes }}
                            @elseif($book->admin_notes)
                                {{ $book->admin_notes }}
                            @endif
                        </div>
                    @else
                        <p>No additional notes provided.</p>
                    @endif
                    
                    <p>If you have questions about this retrieval, please contact us.</p>
                </div>
                
                <div class="cta-button-container">
                    <a href="{{ route('dashboard') }}" class="cta-button">
                        View Dashboard
                    </a>
                </div>
            @else
                <p>Your book "<strong>{{ $book->title }}</strong>" status has been updated to <strong>{{ ucfirst(str_replace('_', ' ', $newStatus)) }}</strong>.</p>
                <p>We appreciate your participation in the Rhymes platform!</p>
            @endif
            
            <p>Thank you for being part of the Rhymes platform!</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Rhymes Platform. All rights reserved.</p>
            <p>Rovingheights Books Ltd.</p>
        </div>
    </div>
</body>
</html>