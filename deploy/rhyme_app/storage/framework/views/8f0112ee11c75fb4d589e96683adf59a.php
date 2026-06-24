

<?php $__env->startSection('title', 'Page Not Found | Rhymes Platform'); ?>

<?php $__env->startSection('content'); ?>
<div class="error-icon">
    <em class="icon ni ni-alert-circle"></em>
</div>
<h1 class="error-code">404</h1>
<h2 class="error-title">Page Not Found</h2>
<p class="error-message">
    Oops! The page you're looking for doesn't exist or has been moved. 
    Please check the URL or navigate back to the homepage.
</p>
<div class="error-actions">
    <a href="<?php echo e(url('/')); ?>" class="btn btn-primary">Go to Homepage</a>
    <a href="<?php echo e(url()->previous()); ?>" class="btn btn-light">Go Back</a>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.error', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/errors/404.blade.php ENDPATH**/ ?>