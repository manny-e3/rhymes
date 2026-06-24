@extends('layouts.error')

@section('title', 'Access Denied | Rhymes Platform')

@section('content')
<div class="error-icon">
    <em class="icon ni ni-lock-alt"></em>
</div>
<h1 class="error-code">403</h1>
<h2 class="error-title">Access Denied</h2>
<p class="error-message">
    Sorry, you don't have permission to access this page. 
    If you believe this is an error, please contact our support team.
</p>
<div class="error-actions">
    <a href="{{ url('/') }}" class="btn btn-primary">Go to Homepage</a>
    <a href="{{ url()->previous() }}" class="btn btn-light">Go Back</a>
</div>
@endsection