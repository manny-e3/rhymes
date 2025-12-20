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
    <form method="POST" action="{{ route('verification.send') }}" class="w-100 me-2">
        @csrf

        <div>
            <button type="submit" class="btn btn-lg btn-primary btn-block">
                {{ __('Resend Verification Email') }}
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
@endsection