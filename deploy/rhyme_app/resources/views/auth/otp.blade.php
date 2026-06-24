@extends('layouts.auth')

@section('title', 'OTP Verification | Rhymes Author Platform')

@section('page-title', 'Two-Factor Authentication')

@section('page-description', 'Enter the code sent to your email address')

@section('content')
<form method="POST" action="{{ route('otp.verify') }}" id="otp-verify-form">
    @csrf
    
    <div class="form-group">
        <div class="form-label-group">
            <label class="form-label" for="otp">OTP Code</label>
        </div>
        <div class="form-control-wrap">
            <input type="text" name="otp" class="form-control form-control-lg @error('otp') is-invalid @enderror" id="otp" placeholder="Enter 6-digit code" value="{{ old('otp') }}" required maxlength="6" autocomplete="one-time-code">
            @error('otp')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
        <div class="form-note-s2 text-center pt-4">
            <p>We've sent a 6-digit code to your email address. Please check your inbox.</p>
        </div>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-lg btn-primary btn-block" id="verify-btn">
            <span id="verify-btn-text">Verify & Continue</span>
            <span id="verify-btn-spinner" class="spinner-border spinner-border-sm ms-2" style="display:none;" role="status" aria-hidden="true"></span>
        </button>
    </div>
</form>

<div class="form-note-s2 text-center pt-4">
    <p>
        Didn't receive the code? 
        <form method="POST" action="{{ route('otp.resend') }}" id="otp-resend-form" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-link p-0 m-0 align-baseline" id="resend-btn">
                <span id="resend-btn-text">Resend Code</span>
                <span id="resend-btn-spinner" class="spinner-border spinner-border-sm ms-2" style="display:none;" role="status" aria-hidden="true"></span>
            </button>
        </form>
    </p>
</div>
@endsection

@push('scripts')
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
                btnText.textContent = 'Verifying...';
                
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
                btnText.textContent = 'Verifying...';
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
@endpush