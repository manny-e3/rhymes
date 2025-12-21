

<?php $__env->startSection('title', 'OTP Verification | Rhymes Author Platform'); ?>

<?php $__env->startSection('page-title', 'Payout Authorization'); ?>

<?php $__env->startSection('page-description', 'Enter the code sent to your email address to authorize this payout'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Payout Authorization</h3>
                        <div class="nk-block-des text-soft">
                            <p>Enter the code sent to your email address to authorize this payout</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <form method="POST" action="<?php echo e(route('author.otp.payout.verify')); ?>" id="otp-verify-form">
                            <?php echo csrf_field(); ?>
                            
                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="otp">OTP Code</label>
                                </div>
                                <div class="form-control-wrap">
                                    <input type="text" name="otp" class="form-control form-control-lg <?php $__errorArgs = ['otp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="otp" placeholder="Enter 6-digit code" value="<?php echo e(old('otp')); ?>" required maxlength="6" autocomplete="one-time-code">
                                    <?php $__errorArgs = ['otp'];
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
                                <div class="form-note-s2 text-center pt-4">
                                    <p>We've sent a 6-digit code to your email address. Please check your inbox to authorize this payout request.</p>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary btn-block" id="verify-btn">
                                    <span id="verify-btn-text">Authorize Payout</span>
                                    <span id="verify-btn-spinner" class="spinner-border spinner-border-sm ms-2" style="display:none;" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>

                        <div class="form-note-s2 text-center pt-4">
                            <p>
                                Didn't receive the code? 
                                <form method="POST" action="<?php echo e(route('author.otp.payout.resend')); ?>" id="otp-resend-form" style="display: inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline" id="resend-btn">
                                        <span id="resend-btn-text">Resend Code</span>
                                        <span id="resend-btn-spinner" class="spinner-border spinner-border-sm ms-2" style="display:none;" role="status" aria-hidden="true"></span>
                                    </button>
                                </form>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-focus on the OTP input field
        var otpInput = document.getElementById('otp');
        if (otpInput) {
            otpInput.focus();
        }
        
        // Auto-submit when 6 digits are entered
        otpInput.addEventListener('input', function() {
            if (this.value.length === 6) {
                var form = document.getElementById('otp-verify-form');
                var btn = document.getElementById('verify-btn');
                var btnText = document.getElementById('verify-btn-text');
                var btnSpinner = document.getElementById('verify-btn-spinner');
                
                // Disable button and show spinner
                btn.disabled = true;
                btnSpinner.style.display = 'inline-block';
                btnText.textContent = 'Authorizing...';
                
                form.submit();
            }
        });
        
        // Handle verify form submission
        var verifyForm = document.getElementById('otp-verify-form');
        if (verifyForm) {
            verifyForm.addEventListener('submit', function() {
                var btn = document.getElementById('verify-btn');
                var btnText = document.getElementById('verify-btn-text');
                var btnSpinner = document.getElementById('verify-btn-spinner');
                
                // Disable button and show spinner
                btn.disabled = true;
                btnSpinner.style.display = 'inline-block';
                btnText.textContent = 'Authorizing...';
            });
        }
        
        // Handle resend form submission
        var resendForm = document.getElementById('otp-resend-form');
        if (resendForm) {
            resendForm.addEventListener('submit', function() {
                var btn = document.getElementById('resend-btn');
                var btnText = document.getElementById('resend-btn-text');
                var btnSpinner = document.getElementById('resend-btn-spinner');
                
                // Disable button and show spinner
                btn.disabled = true;
                btnSpinner.style.display = 'inline-block';
                btnText.textContent = 'Sending...';
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.author', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/author/otp/index.blade.php ENDPATH**/ ?>