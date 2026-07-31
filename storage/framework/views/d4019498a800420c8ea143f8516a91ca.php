<?php $__env->startSection('title', 'Email Verification | Rhymes Author Platform'); ?>

<?php $__env->startSection('page-title', 'Email Verification'); ?>

<?php $__env->startSection('page-description', 'Thanks for signing up! Before getting started, please verify your email address by clicking the link we just emailed to you.'); ?>

<?php $__env->startSection('content'); ?>
<div class="text-center mb-4">
    <div class="mb-4 text-sm text-gray-600">
        <?php echo e(__('If you didn\'t receive the email, we will gladly send you another.')); ?>

    </div>

    <?php if(session('status') == 'verification-link-sent'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>Success!</strong> <?php echo e(__('A new verification link has been sent to the email address you provided during registration.')); ?>

        </div>
    <?php endif; ?>
</div>

<div class="mt-4 d-flex justify-content-between">
    <form id="resend-form" method="POST" action="<?php echo e(route('verification.send')); ?>" class="w-100 me-2">
        <?php echo csrf_field(); ?>

        <div>
            <button type="submit" id="resend-btn" class="btn btn-lg btn-primary btn-block">
                <span id="resend-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                <span id="resend-text"><?php echo e(__('Resend Verification Email')); ?></span>
            </button>
        </div>
    </form>

    <form method="POST" action="<?php echo e(route('logout')); ?>" class="w-100 ms-2">
        <?php echo csrf_field(); ?>

        <button type="submit" class="btn btn-lg btn-light btn-block">
            <?php echo e(__('Log Out')); ?>

        </button>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.getElementById('resend-form').addEventListener('submit', function() {
        const btn = document.getElementById('resend-btn');
        const spinner = document.getElementById('resend-spinner');
        const text = document.getElementById('resend-text');
        
        btn.disabled = true;
        spinner.classList.remove('d-none');
        spinner.classList.add('me-1');
        text.innerText = 'Sending...';
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/rovinghe/author.rovingheights.com/resources/views/auth/verify-email.blade.php ENDPATH**/ ?>