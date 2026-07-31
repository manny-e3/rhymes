<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payout Status Update - Rhymes Platform</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #F2426E;">Rhymes Platform</h1>
        </div>
        
        <div style="background: #f9fafb; border-radius: 8px; padding: 30px; margin-bottom: 30px;">
            <h2 style="color: #1f2937; margin-top: 0;">Hello <?php echo e($user->name); ?>,</h2>
            
            <?php if(trim($newStatus) === 'approved'): ?>
                <p>Your payout request of <strong>₦<?php echo e(number_format($payout->amount_requested, 2)); ?></strong> has been <strong>approved</strong>.</p>
                
                <div style="background: #dcfce7; border-radius: 6px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #1f2937; margin-top: 0;">✅ Payout Approved</h3>
                    <p>Great news! Your payout request has been approved.</p>
                    <p>The payment will be processed within 3-5 business days.</p>
                    <p>You will receive the funds via your registered payment method.</p>
                    
                    <?php if($adminNotes): ?>
                        <p><strong>Admin notes:</strong> <?php echo e($adminNotes); ?></p>
                    <?php endif; ?>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="<?php echo e(route('author.payouts.index')); ?>" 
                       style="background: #F2426E; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                        View Payout History
                    </a>
                </div>
            <?php elseif(trim($newStatus) === 'denied'): ?>
                <p>Your payout request of <strong>₦<?php echo e(number_format($payout->amount_requested, 2)); ?></strong> has been <strong>denied</strong>.</p>
                
                <div style="background: #fee2e2; border-radius: 6px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #1f2937; margin-top: 0;">❌ Payout Denied</h3>
                    <p>Unfortunately, your payout request was denied.</p>
                    
                    <?php if($adminNotes): ?>
                        <p><strong>Admin notes:</strong> <?php echo e($adminNotes); ?></p>
                    <?php else: ?>
                        <p><strong>Admin notes:</strong> No specific reason provided.</p>
                    <?php endif; ?>
                    
                    <p>You can submit a new payout request if you meet the requirements.</p>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="<?php echo e(route('author.payouts.index')); ?>" 
                       style="background: #F2426E; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                        Submit New Request
                    </a>
                </div>
            <?php elseif(trim($newStatus) === 'completed'): ?>
                <p>Your payout of <strong>₦<?php echo e(number_format($payout->amount_requested, 2)); ?></strong> has been <strong>completed</strong>.</p>
                
                <div style="background: #dbeafe; border-radius: 6px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #1f2937; margin-top: 0;">🎉 Payout Completed</h3>
                    <p>Your payout has been completed!</p>
                    <p>The payment of ₦<?php echo e(number_format($payout->amount_requested, 2)); ?> has been sent.</p>
                    <p>Please check your payment method for the funds.</p>
                    
                    <?php if($adminNotes): ?>
                        <p><strong>Admin notes:</strong> <?php echo e($adminNotes); ?></p>
                    <?php endif; ?>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="<?php echo e(route('author.wallet.index')); ?>" 
                       style="background: #F2426E; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                        View Wallet
                    </a>
                </div>
            <?php else: ?>
                <p>Your payout request of <strong>₦<?php echo e(number_format($payout->amount_requested, 2)); ?></strong> status has been updated to <strong><?php echo e(ucfirst($newStatus)); ?></strong>.</p>
                
                <div style="background: #fffbeb; border-radius: 6px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #1f2937; margin-top: 0;">Payout Status Updated</h3>
                    <p>Your payout request status has been updated.</p>
                    
                    <?php if($adminNotes): ?>
                        <p><strong>Admin notes:</strong> <?php echo e($adminNotes); ?></p>
                    <?php endif; ?>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="<?php echo e(route('author.payouts.index')); ?>" 
                       style="background: #F2426E; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                        View Payout History
                    </a>
                </div>
            <?php endif; ?>
            
            <p>Thank you for being part of the Rhymes platform!</p>
        </div>
        
        <div style="text-align: center; color: #6b7280; font-size: 14px;">
            <p>&copy; <?php echo e(date('Y')); ?> Rhymes Platform. All rights reserved.</p>
            <p>Rovingheights Books Ltd.</p>
        </div>
    </div>
</body>
</html><?php /**PATH /home/rovinghe/author.rovingheights.com/resources/views/emails/payout-status-changed.blade.php ENDPATH**/ ?>