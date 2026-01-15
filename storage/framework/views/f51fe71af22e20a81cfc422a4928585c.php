

<?php $__env->startSection('title', 'Server Error | Rhymes Platform'); ?>

<?php $__env->startSection('content'); ?>
<div class="error-icon">
    <em class="icon ni ni-server"></em>
</div>
<h1 class="error-code">500</h1>
<h2 class="error-title">Server Error</h2>
<p class="error-message">
    Oops! Something went wrong on our end. Our team has been notified and is working to fix the issue. 
    Please try again later.
</p>
<div class="error-actions">
    <a href="<?php echo e(url('/')); ?>" class="btn btn-primary">Go to Homepage</a>
    <a href="<?php echo e(url()->previous()); ?>" class="btn btn-light">Go Back</a>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.error', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/errors/500.blade.php ENDPATH**/ ?>