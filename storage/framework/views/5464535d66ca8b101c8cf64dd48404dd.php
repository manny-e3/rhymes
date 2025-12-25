<?php $__env->startSection('title', 'Login | Rhymes Author Platform'); ?>

<?php $__env->startSection('page-title', 'Sign In'); ?>

<?php $__env->startSection('page-description', 'Access your Rhymes Author account'); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('login')); ?>">
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
unset($__errorArgs, $__bag); ?>" id="email" placeholder="Enter your email address" value="<?php echo e(old('email')); ?>" required autofocus autocomplete="username">
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
        <div class="form-label-group d-flex justify-content-between align-items-center">
            <label class="form-label" for="password">Password</label>
            <?php if(Route::has('password.request')): ?>
                <a class="link link-sm" href="<?php echo e(route('password.request')); ?>">Forgot Password?</a>
            <?php endif; ?>
        </div>
        <div class="form-control-wrap position-relative">
            <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
            </a>
            <input type="password" name="password" class="form-control form-control-lg <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="password" placeholder="Enter your password" required autocomplete="current-password">
            <?php $__errorArgs = ['password'];
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
    
    <div class="form-group d-flex align-items-center justify-content-between">
        <div class="custom-control custom-control-xs custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="remember_me" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
            <label class="custom-control-label" for="remember_me">Remember me</label>
        </div>
        <button type="submit" class="btn btn-lg btn-primary d-flex align-items-center justify-content-center" id="login-submit-btn">
            <span id="login-btn-text">Sign in</span>
            <span id="login-btn-spinner" class="spinner-border spinner-border-sm ms-2" style="display:none;" role="status" aria-hidden="true"></span>
        </button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('auth-links'); ?>
New on our platform? <a href="<?php echo e(route('register')); ?>"><strong>Create an account</strong></a>
<?php $__env->stopSection(); ?>



<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.querySelector('form[action="<?php echo e(route('login')); ?>"]');
        var btn = document.getElementById('login-submit-btn');
        var btnText = document.getElementById('login-btn-text');
        var btnSpinner = document.getElementById('login-btn-spinner');
        
        if(form && btn && btnText && btnSpinner) {
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btnSpinner.style.display = 'inline-block';
                btnText.textContent = 'Signing in...';
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/auth/login.blade.php ENDPATH**/ ?>