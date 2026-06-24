<!-- Payment Details Modal -->
<div class="modal fade" tabindex="-1" id="payment-details" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
            <div class="modal-body modal-body-lg">
                <h5 class="title">Payment Details</h5>
                <p class="text-soft">Configure your payment method for receiving payouts.</p>
                
                <form id="payment-details-form" class="form-validate is-alter" method="POST" action="{{ route('author.profile.payment-details.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="row gy-4">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Payment Method</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" id="payment_method" name="payment_method" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="bank_transfer" {{ old('payment_method', $user->payment_details['payment_method'] ?? '') == 'bank_transfer' ? 'selected' : '' }}>
                                            Bank Transfer
                                        </option>
                                        {{-- <option value="paypal" {{ old('payment_method', $user->payment_details['payment_method'] ?? '') == 'paypal' ? 'selected' : '' }}>
                                            PayPal
                                        </option>
                                        <option value="stripe" {{ old('payment_method', $user->payment_details['payment_method'] ?? '') == 'stripe' ? 'selected' : '' }}>
                                            Stripe
                                        </option> --}}
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="account_holder_name">Account Holder Name</label>
                                <div class="form-control-wrap">
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="account_holder_name" 
                                           name="account_holder_name" 
                                           placeholder="Full name on account" 
                                           value="{{ old('account_holder_name', $user->payment_details['account_holder_name'] ?? '') }}"
                                           required>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer Fields -->
                        <div id="bank_transfer_fields" class="payment-method-fields">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="bank_name">Bank Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" 
                                               class="form-control form-control-lg" 
                                               id="bank_name" 
                                               name="bank_name" 
                                               placeholder="Name of your bank" 
                                               value="{{ old('bank_name', $user->payment_details['bank_name'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="account_number">Account Number</label>
                                    <div class="form-control-wrap">
                                        <input type="text" 
                                               class="form-control form-control-lg" 
                                               id="account_number" 
                                               name="account_number" 
                                               placeholder="Bank account number" 
                                               value="{{ old('account_number', $user->payment_details['account_number'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PayPal Fields -->
                        <div id="paypal_fields" class="payment-method-fields">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="paypal_email">PayPal Email</label>
                                    <div class="form-control-wrap">
                                        <input type="email" 
                                               class="form-control form-control-lg" 
                                               id="paypal_email" 
                                               name="paypal_email" 
                                               placeholder="your.email@example.com" 
                                               value="{{ old('paypal_email', $user->payment_details['paypal_email'] ?? '') }}">
                                        <div class="form-note">
                                            Enter the email address associated with your PayPal account
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stripe Fields -->
                        <div id="stripe_fields" class="payment-method-fields">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="stripe_account_id">Stripe Account ID</label>
                                    <div class="form-control-wrap">
                                        <input type="text" 
                                               class="form-control form-control-lg" 
                                               id="stripe_account_id" 
                                               name="stripe_account_id" 
                                               placeholder="acct_xxxxxxxxxx" 
                                               value="{{ old('stripe_account_id', $user->payment_details['stripe_account_id'] ?? '') }}">
                                        <div class="form-note">
                                            Your Stripe Connect account ID for receiving payments
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method Information -->
                        <div class="col-12">
                            <div class="payment-method-info" id="bank_transfer_info">
                                <div class="alert alert-info">
                                    <h6><em class="icon ni ni-building"></em> Bank Transfer</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li>• Processing time: 3-5 business days</li>
                                        <li>• No processing fees</li>
                                        <li>• Minimum payout: ₦5,000.00</li>
                                        
                                    </ul>
                                </div>
                            </div>

                            <div class="payment-method-info" id="paypal_info">
                                <div class="alert alert-primary">
                                    <h6><em class="icon ni ni-paypal"></em> PayPal</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li>• Processing time: 1-2 business days</li>
                                        <li>• No processing fees</li>
                                        <li>• Minimum payout: ₦10.00</li>
                                        <li>• Available in 200+ countries</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="payment-method-info" id="stripe_info">
                                <div class="alert alert-success">
                                    <h6><em class="icon ni ni-cc-stripe"></em> Stripe</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li>• Processing time: 2-7 business days</li>
                                        <li>• No processing fees</li>
                                        <li>• Minimum payout: ₦10.00</li>
                                        <li>• Available in 40+ countries</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-warning">
                                <em class="icon ni ni-alert-circle"></em>
                                <strong>Security Notice:</strong> Your payment details are encrypted and stored securely. We never share your financial information with third parties.
                            </div>
                        </div>

                        <div class="col-12">
                            <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                                <li>
                                    <button type="submit" class="btn btn-lg btn-primary">Save Payment Details</button>
                                </li>
                                <li>
                                    <a href="#" data-bs-dismiss="modal" class="link link-light">Cancel</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Payment method field visibility (JavaScript enhancement only)
function showPaymentMethodFields() {
    const paymentMethodSelect = document.getElementById('payment_method');
    const selectedMethod = paymentMethodSelect.value;
    
    // Hide all fields and info
    document.getElementById('bank_transfer_fields').style.display = 'none';
    document.getElementById('paypal_fields').style.display = 'none';
    document.getElementById('stripe_fields').style.display = 'none';
    document.getElementById('bank_transfer_info').style.display = 'none';
    document.getElementById('paypal_info').style.display = 'none';
    document.getElementById('stripe_info').style.display = 'none';
    
    // Remove required attributes
    document.getElementById('bank_name').removeAttribute('required');
    document.getElementById('account_number').removeAttribute('required');
    document.getElementById('paypal_email').removeAttribute('required');
    document.getElementById('stripe_account_id').removeAttribute('required');

    // Show selected method fields and info
    if (selectedMethod) {
        if (selectedMethod === 'bank_transfer') {
            document.getElementById('bank_transfer_fields').style.display = 'block';
            document.getElementById('bank_transfer_info').style.display = 'block';
            document.getElementById('bank_name').setAttribute('required', '');
            document.getElementById('account_number').setAttribute('required', '');
        } else if (selectedMethod === 'paypal') {
            document.getElementById('paypal_fields').style.display = 'block';
            document.getElementById('paypal_info').style.display = 'block';
            document.getElementById('paypal_email').setAttribute('required', '');
        } else if (selectedMethod === 'stripe') {
            document.getElementById('stripe_fields').style.display = 'block';
            document.getElementById('stripe_info').style.display = 'block';
            document.getElementById('stripe_account_id').setAttribute('required', '');
        }
    }
}

// Initialize when modal is shown (JavaScript enhancement only)
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('payment_method');
    if (paymentMethodSelect) {
        // Set initial state based on selected value
        showPaymentMethodFields();
        
        // Add event listener for changes
        paymentMethodSelect.addEventListener('change', showPaymentMethodFields);
    }
});
</script>