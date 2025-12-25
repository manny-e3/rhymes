<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to <?php echo e(config('app.name')); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .content {
            margin: 20px 0;
        }
        .login-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #007cba;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007cba;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 15px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 0.9em;
            color: #666;
        }
        .security-note {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 12px;
            border-radius: 4px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to <?php echo e(config('app.name')); ?>!</h1>
        <p>Your account has been created successfully</p>
    </div>

    <div class="content">
        <p>Hello <?php echo e($user->name); ?>,</p>
        
        <p>Your account has been created successfully on our platform. Here are your login details:</p>

        <div class="login-details">
            <h3>Login Information</h3>
            <p><strong>Email:</strong> <?php echo e($user->email); ?></p>
            <p><strong>Password:</strong> <?php echo e($password); ?></p>
        </div>

        <div class="security-note">
            <strong>Security Notice:</strong> For your security, we strongly recommend changing your password after your first login.
        </div>

        <p>To access your account, click the button below:</p>
        
        <p>
            <a href="<?php echo e(url('/login')); ?>" class="button">Login to Your Account</a>
        </p>

        <p>After logging in, you can update your profile, change your password, and explore all the features available to you.</p>

        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

        <p>Thank you for joining our platform! We're excited to have you as part of our community.</p>
    </div>

    <div class="footer">
        <p>&copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. All rights reserved.</p>
        <p>This is an automated message, please do not reply to this email.</p>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/emails/new-user-login-details.blade.php ENDPATH**/ ?>