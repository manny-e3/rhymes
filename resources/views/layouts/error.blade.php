<!DOCTYPE html>
<html lang="en" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Rhymes Platform">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Rhymes Author Platform - Submit your books to Rovingheights for stocking consideration">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fav Icon -->
    <link rel="shortcut icon" href="{{ asset('images/rovingHeights-logo.png') }}">
    
    <!-- Page Title -->
    <title>@yield('title', 'Error | Rhymes Platform')</title>
    
    <!-- StyleSheets -->
    <link rel="stylesheet" href="{{asset('/assets/css/dashlite.css')}}">
    <link id="skin-default" rel="stylesheet" href="{{asset('/assets/css/theme.css')}}">
    <link rel="stylesheet" href="{{asset('/css/theme-overrides.css')}}">
    
    <style>
        .error-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7f1 100%);
            padding: 20px;
        }
        
        .error-card {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
        }
        
        .error-code {
            font-size: 72px;
            font-weight: 700;
            color: #F2426E;
            margin: 0 0 10px;
            line-height: 1;
        }
        
        .error-title {
            font-size: 24px;
            font-weight: 600;
            color: #1a202c;
            margin: 0 0 15px;
        }
        
        .error-message {
            font-size: 16px;
            color: #4a5568;
            margin: 0 0 30px;
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .error-actions .btn {
            min-width: 120px;
        }
        
        .error-icon {
            font-size: 60px;
            margin-bottom: 20px;
            color: #cbd5e0;
        }
    </style>
</head>

<body class="nk-body bg-white">
    <div class="error-page">
        <div class="error-card">
            @yield('content')
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="{{asset('/assets/js/bundle.js')}}"></script>
    <script src="{{asset('/assets/js/scripts.js')}}"></script>
</body>

</html>