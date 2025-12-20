@extends('layouts.error')

@section('title', 'Something Went Wrong | Rhymes Platform')

@section('content')
<div class="error-icon">
    <em class="icon ni ni-alert-circle"></em>
</div>
<h1 class="error-code">{{ $code ?? 'Error' }}</h1>
<h2 class="error-title">{{ $title ?? 'Something Went Wrong' }}</h2>
<p class="error-message">
    {{ $message ?? 'An unexpected error occurred. Please try again later.' }}
</p>
<div class="error-actions">
    <a href="{{ url('/') }}" class="btn btn-primary">Go to Homepage</a>
    <a href="{{ url()->previous() }}" class="btn btn-light">Go Back</a>
</div>
@endsection