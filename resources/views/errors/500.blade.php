@extends('layouts.error')

@section('title', 'Server Error | Rhymes Platform')

@section('content')
<div class="error-icon">
    <em class="icon ni ni-server"></em>
</div>
<h1 class="error-code">500</h1>
<h2 class="error-title">Server Error</h2>
<p class="error-message">
    Oops! Something went wrong on our end. Our team has been notified and is working to fix the issue. 
    Please try again later.
</p>
<div class="error-actions">
    <a href="{{ url('/') }}" class="btn btn-primary">Go to Homepage</a>
    <a href="{{ url()->previous() }}" class="btn btn-light">Go Back</a>
</div>
@endsection