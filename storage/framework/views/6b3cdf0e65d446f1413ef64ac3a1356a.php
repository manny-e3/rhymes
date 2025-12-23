

<?php $__env->startSection('title', 'Access Denied | Rhymes Platform'); ?>

<?php $__env->startSection('content'); ?>
<div class="error-icon">
    <em class="icon ni ni-lock-alt"></em>
</div>
<h1 class="error-code">403</h1>
<h2 class="error-title">Access Denied</h2>
<p class="error-message">
    Sorry, you don't have permission to access this page. 
    If you believe this is an error, please contact our support team.
</p>
<div class="error-actions">
    <a href="<?php echo e(url('/')); ?>" class="btn btn-primary">Go to Homepage</a>
    <a href="<?php echo e(url()->previous()); ?>" class="btn btn-light">Go Back</a>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.error', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/errors/403.blade.php ENDPATH**/ ?>