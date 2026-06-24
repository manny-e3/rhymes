@extends('layouts.error')

@section('title', 'Page Not Found | Rhymes Platform')

@section('content')
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
    <a href="{{ url('/') }}" class="btn btn-primary">Go to Homepage</a>
    <a href="{{ url()->previous() }}" class="btn btn-light">Go Back</a>
</div>
@endsection