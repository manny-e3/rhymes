<?php $__env->startSection('title', 'Forgot Password | Rhymes Author Platform'); ?>

<?php $__env->startSection('page-title', 'Forgot Password'); ?>

<?php $__env->startSection('page-description', 'Forgot your password? No problem. Enter your email address and we\'ll send you a password reset link.'); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('password.email')); ?>" id="forgot-password-form">
    <?php echo csrf_field(); ?>
    <div class="form-group">
        <div class="form-label-group">
            <label class="form-label" for="email">Email</label>
        </div>
        <div class="form-control-wrap">
            <input type="email" name="email" class="form-control form-control-lg <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" placeholder="Enter your email address" value="<?php echo e(old('email')); ?>" required autofocus>
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="invalid-feedback" role="alert"><strong><?php echo e($message); ?></strong></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-lg btn-primary btn-block d-flex align-items-center justify-content-center" id="forgot-submit-btn">
            <span id="forgot-btn-text">Email Password Reset Link</span>
            <span id="forgot-btn-spinner" class="spinner-border spinner-border-sm ms-2" style="display:none;" role="status" aria-hidden="true"></span>
        </button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('auth-links'); ?>
Remember your password? <a href="<?php echo e(route('login')); ?>"><strong>Sign in</strong></a>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('forgot-password-form');
        var btn = document.getElementById('forgot-submit-btn');
        var btnText = document.getElementById('forgot-btn-text');
        var btnSpinner = document.getElementById('forgot-btn-spinner');
        
        if(form && btn && btnText && btnSpinner) {
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btnSpinner.style.display = 'inline-block';
                btnText.textContent = 'Sending...';
            });
        }
        
        // Auto-focus on email field
        var emailField = document.getElementById('email');
        if(emailField) {
            emailField.focus();
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/auth/forgot-password.blade.php ENDPATH**/ ?>