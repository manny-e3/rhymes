<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Book Edited - Approval Required - Rhymes Platform</title>
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
        .book-info {
            background: #eff6ff;
            border-radius: 6px;
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
            <h2 class="greeting">Hello <?php echo e($user->name); ?>,</h2>
            
            <p>An author has edited a stocked book and it requires your approval.</p>
            
            <div class="book-info">
                <h3>Book Details:</h3>
                <p><strong>Title:</strong> <?php echo e($book->title); ?></p>
                <p><strong>Author:</strong> <?php echo e($author->name); ?></p>
                <p><strong>ISBN:</strong> <?php echo e($book->isbn); ?></p>
                <p><strong>Genre:</strong> <?php echo e($book->genre); ?></p>
                <p><strong>Status:</strong> Stocked (Edited - Awaiting Approval)</p>
            </div>
            
            <p>The author has made changes to this book which was previously stocked. Please review the changes and approve or reject them.</p>
            
            <div class="cta-button-container">
                <a href="<?php echo e(route('admin.books.show', $book)); ?>" class="cta-button">
                    Review Book
                </a>
            </div>
            
            <p>Thank you for your attention to this matter.</p>
        </div>
        
        <div class="footer">
            <p>&copy; <?php echo e(date('Y')); ?> Rhymes Platform. All rights reserved.</p>
            <p>Rovingheights Books Ltd.</p>
        </div>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/emails/book-edited-for-approval.blade.php ENDPATH**/ ?>