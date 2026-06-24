<!DOCTYPE html>
<html>
<head>
    <title>Test Payment Form</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="./assets/css/dashlite.css?ver=3.2.3">
    <link id="skin-default" rel="stylesheet" href="./assets/css/theme.css?ver=3.2.3">
</head>
<body>
    <div class="container">
        <h1>Test Payment Form Submission</h1>
        
        @if(session('payment-success'))
        <div class="alert alert-success">
            {{ session('payment-success') }}
        </div>
        @endif
        
        @if(session('payment-error'))
        <div class="alert alert-danger">
            {{ session('payment-error') }}
        </div>
        @endif
        
        @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        @include('author.profile.modals.payment-details')
        
        <script src="./assets/js/bundle.js?ver=3.2.3"></script>
        <script src="./assets/js/scripts.js?ver=3.2.3"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <script>
        // Show the modal on page load for testing
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('payment-details'));
            modal.show();
        });
        </script>
    </div>
</body>
</html>