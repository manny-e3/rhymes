<?php $__env->startSection('title', 'Payment Details | Rhymes Author Platform'); ?>

<?php $__env->startSection('page-title', 'Payment Details'); ?>

<?php $__env->startSection('page-description', 'Configure your payment method for receiving payouts'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Payment Details</h3>
                        <div class="nk-block-des text-soft">
                            <p>Configure your preferred payment method to receive payouts.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="nk-block-tools g-3">
                            <div class="nk-block-tools-opt">
                                <a href="<?php echo e(route('author.payouts.index')); ?>" class="btn btn-outline-light">
                                    <em class="icon ni ni-arrow-left"></em><span>Back to Payouts</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-lg-8">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Payment Method Configuration</h6>
                                        <p class="text-soft">Choose and configure your preferred payment method for receiving payouts.</p>
                                    </div>
                                </div>

                                <?php if(session('success')): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?php echo e(session('success')); ?>

                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <?php if($errors->any()): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div><?php echo e($error); ?></div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <form action="<?php echo e(route('author.payouts.payment-details.update')); ?>" method="POST" id="paymentDetailsForm">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Payment Method</label>
                                                <div class="form-control-wrap">
                                                    <select class="form-select <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                            id="payment_method" 
                                                            name="payment_method" 
                                                            required>
                                                        <option value="">Select Payment Method</option>
                                                        <option value="bank_transfer" <?php echo e(old('payment_method', $user->payment_details['payment_method'] ?? '') == 'bank_transfer' ? 'selected' : ''); ?>>
                                                            Bank Transfer
                                                        </option>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="account_holder_name">Account Holder Name</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" 
                                                           class="form-control <?php $__errorArgs = ['account_holder_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                           id="account_holder_name" 
                                                           name="account_holder_name" 
                                                           placeholder="Full name on account" 
                                                           value="<?php echo e(old('account_holder_name', $user->payment_details['account_holder_name'] ?? '')); ?>"
                                                           required>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bank Transfer Fields -->
                                        <div id="bank_transfer_fields" class="payment-method-fields" style="display: none;">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="account_number">Account Number</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" 
                                                               class="form-control <?php $__errorArgs = ['account_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                               id="account_number" 
                                                               name="account_number" 
                                                               placeholder="Bank account number" 
                                                               value="<?php echo e(old('account_number', $user->payment_details['account_number'] ?? '')); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label" for="bank_name">Bank Name</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" 
                                                               class="form-control <?php $__errorArgs = ['bank_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                               id="bank_name" 
                                                               name="bank_name" 
                                                               placeholder="Name of your bank" 
                                                               value="<?php echo e(old('bank_name', $user->payment_details['bank_name'] ?? '')); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- PayPal Fields -->
                                        <div id="paypal_fields" class="payment-method-fields" style="display: none;">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label" for="paypal_email">PayPal Email</label>
                                                    <div class="form-control-wrap">
                                                        <input type="email" 
                                                               class="form-control <?php $__errorArgs = ['paypal_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                               id="paypal_email" 
                                                               name="paypal_email" 
                                                               placeholder="your.email@example.com" 
                                                               value="<?php echo e(old('paypal_email', $user->payment_details['paypal_email'] ?? '')); ?>">
                                                        <div class="form-note">
                                                            Enter the email address associated with your PayPal account
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Stripe Fields -->
                                        <div id="stripe_fields" class="payment-method-fields" style="display: none;">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label" for="stripe_account_id">Stripe Account ID</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" 
                                                               class="form-control <?php $__errorArgs = ['stripe_account_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                               id="stripe_account_id" 
                                                               name="stripe_account_id" 
                                                               placeholder="acct_xxxxxxxxxx" 
                                                               value="<?php echo e(old('stripe_account_id', $user->payment_details['stripe_account_id'] ?? '')); ?>">
                                                        <div class="form-note">
                                                            Your Stripe Connect account ID for receiving payments
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">
                                                    <em class="icon ni ni-check"></em>
                                                    <span>Save Payment Details</span>
                                                </button>
                                                <a href="<?php echo e(route('author.payouts.index')); ?>" class="btn btn-outline-light ms-2">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <h6 class="title mb-3">Payment Method Information</h6>
                                
                                <div class="payment-method-info" id="bank_transfer_info" style="display: none;">
                                    <div class="alert alert-info">
                                        <h6><em class="icon ni ni-building"></em> Bank Transfer</h6>
                                        <ul class="list-unstyled mb-0">
                                            <li>• Processing time: 3-5 business days</li>
                                            <li>• No processing fees</li>
                                            
                                        </ul>
                                    </div>
                                </div>

                                

                                <div class="alert alert-warning mt-3">
                                    <em class="icon ni ni-alert-circle"></em>
                                    <strong>Security Notice:</strong> Your payment details are encrypted and stored securely. We never share your financial information with third parties.
                                </div>
                            </div>
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
    const paymentMethodSelect = document.getElementById('payment_method');
    const paymentMethodFields = document.querySelectorAll('.payment-method-fields');
    const paymentMethodInfos = document.querySelectorAll('.payment-method-info');

    function showPaymentMethodFields() {
        const selectedMethod = paymentMethodSelect.value;
        
        // Hide all fields and info
        paymentMethodFields.forEach(field => {
            field.style.display = 'none';
            // Make fields optional when hidden
            const inputs = field.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.removeAttribute('required');
            });
        });
        
        paymentMethodInfos.forEach(info => {
            info.style.display = 'none';
        });

        // Show selected method fields and info
        if (selectedMethod) {
            const selectedFields = document.getElementById(selectedMethod + '_fields');
            const selectedInfo = document.getElementById(selectedMethod + '_info');
            
            if (selectedFields) {
                selectedFields.style.display = 'block';
                // Make visible fields required based on method
                if (selectedMethod === 'bank_transfer') {
                    document.getElementById('account_number').setAttribute('required', '');
                    document.getElementById('bank_name').setAttribute('required', '');
                } else if (selectedMethod === 'paypal') {
                    document.getElementById('paypal_email').setAttribute('required', '');
                } else if (selectedMethod === 'stripe') {
                    document.getElementById('stripe_account_id').setAttribute('required', '');
                }
            }
            
            if (selectedInfo) {
                selectedInfo.style.display = 'block';
            }
        }
    }

    // Initialize on page load
    showPaymentMethodFields();

    // Handle payment method change
    paymentMethodSelect.addEventListener('change', showPaymentMethodFields);

    // Form validation
    document.getElementById('paymentDetailsForm').addEventListener('submit', function(e) {
        const selectedMethod = paymentMethodSelect.value;
        
        if (!selectedMethod) {
            e.preventDefault();
            alert('Please select a payment method.');
            return;
        }

        // Additional validation based on method
        if (selectedMethod === 'paypal') {
            const paypalEmail = document.getElementById('paypal_email').value;
            if (!paypalEmail || !paypalEmail.includes('@')) {
                e.preventDefault();
                alert('Please enter a valid PayPal email address.');
                return;
            }
        }

        if (confirm('Are you sure you want to update your payment details? This will affect how you receive future payouts.')) {
            return true;
        } else {
            e.preventDefault();
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.author', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/rovinghe/author.rovingheights.com/resources/views/author/payouts/payment-details.blade.php ENDPATH**/ ?>