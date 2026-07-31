<?php $__env->startSection('title', 'Reset Password | Rhymes Author Platform'); ?>

<?php $__env->startSection('page-title', 'Reset Password'); ?>

<?php $__env->startSection('page-description', 'Enter your email and new password to reset your account password.'); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('password.store')); ?>" id="reset-password-form">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="token" value="<?php echo e($request->route('token')); ?>">
    
    <div class="form-group">
        <div class="form-label-group">
            <label class="form-label" for="email">Email</label>
        </div>
        <div class="form-control-wrap">
            <input readonly type="email" name="email" class="form-control form-control-lg <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" placeholder="Enter your email address" value="<?php echo e(old('email', $request->email)); ?>" required autofocus autocomplete="username">
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
        <div class="form-label-group">
            <label class="form-label" for="password">New Password</label>
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
unset($__errorArgs, $__bag); ?>" id="password" placeholder="Enter new password" required autocomplete="new-password">
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
    
    <div class="form-group">
        <div class="form-label-group">
            <label class="form-label" for="password_confirmation">Confirm Password</label>
        </div>
        <div class="form-control-wrap">
            <input type="password" name="password_confirmation" class="form-control form-control-lg <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="password_confirmation" placeholder="Confirm new password" required autocomplete="new-password">
            <?php $__errorArgs = ['password_confirmation'];
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
        <button type="submit" class="btn btn-lg btn-primary btn-block d-flex align-items-center justify-content-center" id="reset-submit-btn">
            <span id="reset-btn-text">Reset Password</span>
            <span id="reset-btn-spinner" class="spinner-border spinner-border-sm ms-2" style="display:none;" role="status" aria-hidden="true"></span>
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
        var form = document.getElementById('reset-password-form');
        var btn = document.getElementById('reset-submit-btn');
        var btnText = document.getElementById('reset-btn-text');
        var btnSpinner = document.getElementById('reset-btn-spinner');
        
        if(form && btn && btnText && btnSpinner) {
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btnSpinner.style.display = 'inline-block';
                btnText.textContent = 'Resetting...';
            });
        }
        
        // Auto-focus on email field
        var emailField = document.getElementById('email');
        if(emailField) {
            emailField.focus();
        }
        
        // Password visibility toggle
        var passcodeSwitch = document.querySelectorAll('.passcode-switch');
        passcodeSwitch.forEach(function(switchEl) {
            switchEl.addEventListener('click', function(e) {
                e.preventDefault();
                var target = document.getElementById(this.getAttribute('data-target'));
                if(target) {
                    if(target.type === 'password') {
                        target.type = 'text';
                        this.querySelector('.icon-show').style.display = 'none';
                        this.querySelector('.icon-hide').style.display = 'block';
                    } else {
                        target.type = 'password';
                        this.querySelector('.icon-show').style.display = 'block';
                        this.querySelector('.icon-hide').style.display = 'none';
                    }
                }
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/rovinghe/author.rovingheights.com/resources/views/auth/reset-password.blade.php ENDPATH**/ ?>