@extends('layouts.app')

@section('title', 'Error Pages Illustration | Rhymes Platform')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 text-center mb-5">
            <h1>Error Pages Illustration</h1>
            <p class="lead">Preview of all custom error pages</p>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="error-icon mb-3">
                        <em class="icon ni ni-alert-circle" style="font-size: 48px;"></em>
                    </div>
                    <h3 class="card-title">404 Error</h3>
                    <p class="card-text">Page Not Found</p>
                    <a href="{{ route('errors.show', '404') }}" class="btn btn-primary">View Page</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="error-icon mb-3">
                        <em class="icon ni ni-lock-alt" style="font-size: 48px;"></em>
                    </div>
                    <h3 class="card-title">403 Error</h3>
                    <p class="card-text">Access Denied</p>
                    <a href="{{ route('errors.show', '403') }}" class="btn btn-primary">View Page</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="error-icon mb-3">
                        <em class="icon ni ni-server" style="font-size: 48px;"></em>
                    </div>
                    <h3 class="card-title">500 Error</h3>
                    <p class="card-text">Server Error</p>
                    <a href="{{ route('errors.show', '500') }}" class="btn btn-primary">View Page</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="error-icon mb-3">
                        <em class="icon ni ni-wrench" style="font-size: 48px;"></em>
                    </div>
                    <h3 class="card-title">503 Error</h3>
                    <p class="card-text">Service Unavailable</p>
                    <a href="{{ route('errors.show', '503') }}" class="btn btn-primary">View Page</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection