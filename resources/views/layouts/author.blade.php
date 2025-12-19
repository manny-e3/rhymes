<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="./images/favicon.png">
    <!-- Page Title  -->
    <title>@yield('title', 'Rhymes Author Platform')</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{asset('/assets/css/dashlite.css')}}">
    <link id="skin-default" rel="stylesheet" href="{{asset('/assets/css/theme.css')}}">
       <link rel="stylesheet" href="{{ asset('css/theme-overrides.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

 

</head>

<body class="nk-body ui-rounder has-sidebar ">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- sidebar @s -->
            @include('layouts.author-sidebar')
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
                <div class="nk-header is-light nk-header-fixed is-light">
                    <div class="container-xl wide-xl">
                        <div class="nk-header-wrap">
                            <div class="nk-menu-trigger d-xl-none ms-n1 me-3">
                                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
                            </div>
                            <div class="nk-header-brand d-xl-none">
                               
                                    
                                    <img class="logo-light logo-img" width="100px" src="{{ asset('images/logo.png') }}" srcset="{{ asset('images/logo2x.png') }} 2x" alt="logo">
                            </div><!-- .nk-header-brand -->
                           
                            <div class="nk-header-tools">
                                <ul class="nk-quick-nav">
                                    <!-- Dark Mode Toggle -->
                                    <li class="dropdown">
                                        <a href="#" id="darkModeToggle" class="nk-quick-nav-icon">
                                            <div class="quick-icon">
                                                <em id="darkModeIcon" class="icon ni ni-moon"></em>
                                            </div>
                                        </a>
                                    </li>
                                   
                                    <li class="dropdown notification-dropdown">
                                        <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-bs-toggle="dropdown">
                                            <div class="icon-status icon-status-info">
                                                <em class="icon ni ni-bell"></em>
                                                <span class="notification-badge" style="display: none; position: absolute; top: -5px; right: -5px; background: #e85347; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 10px; display: flex; align-items: center; justify-content: center;">0</span>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-xl dropdown-menu-end">
                                            <div class="dropdown-head">
                                                <span class="sub-title nk-dropdown-title">Notifications</span>
                                                <a href="#" id="markAllAsRead">Mark All as Read</a>
                                            </div>
                                            <div class="dropdown-body">
                                                <div class="nk-notification" id="notificationsList">
                                                    <div class="nk-notification-item text-center py-4">
                                                        <div class="nk-notification-content">
                                                            <div class="nk-notification-text text-muted">Loading notifications...</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown-foot center">
                                                <a href="#">View All</a>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="dropdown user-dropdown">
                                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                            <div class="user-toggle">
                                                <div class="user-avatar sm">
                                                    <em class="icon ni ni-user-alt"></em>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end">
                                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                                <div class="user-card">
                                                    <div class="user-avatar">
                                                        @if(Auth::user()->avatar)
                                                            <img src="{{ asset('storage/images/avatar/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}">
                                                        @else
                                                            <span>{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="user-info">
                                                        <span class="lead-text">{{ Auth::user()->name }}</span>
                                                        <span class="sub-text">{{ Auth::user()->email }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a href="{{ route('author.profile.edit') }}"><em class="icon ni ni-user-alt"></em><span>View Profile</span></a></li>
                                                    <li><a href="{{ route('author.profile.edit') }}"><em class="icon ni ni-setting-alt"></em><span>Account Settings</span></a></li>
                                                    <li><a href="#" id="loginActivityLink"><em class="icon ni ni-activity-alt"></em><span>Login Activity</span></a></li>
                                                    <li><a href="#" id="darkModeToggleProfile"><em class="icon ni ni-moon"></em><span>Dark Mode</span></a></li>
                                                </ul>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li>
                                                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                                            @csrf
                                                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                                <em class="icon ni ni-signout"></em><span>Sign out</span>
                                                            </a>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div><!-- .nk-header-tools -->
                        </div><!-- .nk-header-wrap -->
                    </div><!-- .container-fliud -->
                </div>

                   @yield('content')



        
                   <!-- content @e -->
                <!-- footer @s -->
               <div class="nk-footer">
                    <div class="container-fluid">
                        <div class="nk-footer-wrap">
                            <div class="nk-footer-copyright">    <p class="text-soft">&copy; <?php echo date("Y"); ?> Roving Heights. All Rights Reserved.</p>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <!-- footer @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    
    <!-- JavaScript -->
    <script src="{{asset('/assets/js/bundle.js')}}"></script>
    <script src="{{asset('/assets/js/scripts.js')}}"></script>
    <script src="{{asset('/assets/js/charts/gd-default.js')}}"></script>
    <script src="{{asset('/assets/js/libs/datatable-btns.js')}}"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Notifications Script -->
    <script src="{{ asset('js/notifications.js') }}"></script>
    
    <!-- SweetAlert2 Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Display success message if session has 'success' key
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            @endif

            // Display error message if session has 'error' key
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#e85347'
                });
            @endif

            // Display warning message if session has 'warning' key
            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: '{{ session('warning') }}',
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f4bd0e'
                });
            @endif

            // Display info message if session has 'info' key
            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: '{{ session('info') }}',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            @endif
            
            // Global function to show SweetAlert messages
            window.showSuccessMessage = function(message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: message,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            };
            
            window.showErrorMessage = function(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: message,
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#e85347'
                });
            };
            
            window.showWarningMessage = function(message) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: message,
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f4bd0e'
                });
            };
            
            window.showInfoMessage = function(message) {
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: message,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            };
            
            // Confirm dialog function
            window.confirmAction = function(message, callback) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e85347',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, proceed!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        callback();
                    }
                });
            };
        });
    </script>
    
    @stack('scripts')
</body>

</html>