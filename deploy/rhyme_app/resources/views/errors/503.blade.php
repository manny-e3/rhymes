@extends('layouts.error')

@section('title', 'Service Unavailable | Rhymes Platform')

@section('content')
<div class="error-icon">
    <em class="icon ni ni-wrench"></em>
</div>
<h1 class="error-code">503</h1>
<h2 class="error-title">Service Unavailable</h2>
<p class="error-message">
    We're currently performing maintenance on our system. 
    Please check back shortly while we make improvements to enhance your experience.
</p>
<div class="error-actions">
    <a href="{{ url('/') }}" class="btn btn-primary">Refresh Page</a>
</div>
@endsection