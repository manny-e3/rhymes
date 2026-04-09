@extends('layouts.auth')

@section('title', 'Email Verification | Rhymes Author Platform')

@section('page-title', 'Email Verification')

@section('page-description', 'Thanks for signing up! Before getting started, please verify your email address by clicking the link we just emailed to you.')

@section('content')
<div class="text-center mb-4">
    <div class="mb-4 text-sm text-gray-600">
        {{ __('If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>Success!</strong> {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif
</div>

<div class="mt-4 d-flex justify-content-between">
    <form id="resend-form" method="POST" action="{{ route('verification.send') }}" class="w-100 me-2">
        @csrf

        <div>
            <button type="submit" id="resend-btn" class="btn btn-lg btn-primary btn-block">
                <span id="resend-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                <span id="resend-text">{{ __('Resend Verification Email') }}</span>
            </button>
        </div>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="w-100 ms-2">
        @csrf

        <button type="submit" class="btn btn-lg btn-light btn-block">
            {{ __('Log Out') }}
        </button>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('resend-form').addEventListener('submit', function() {
        const btn = document.getElementById('resend-btn');
        const spinner = document.getElementById('resend-spinner');
        const text = document.getElementById('resend-text');
        
        btn.disabled = true;
        spinner.classList.remove('d-none');
        spinner.classList.add('me-1');
        text.innerText = 'Sending...';
    });
</script>
@endpush
@endsection