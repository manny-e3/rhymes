<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="js">
<head>
    <base href="<?php echo e(url('/')); ?>/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Laravel')); ?></title>
    
    <!-- Fav Icon -->
    <link rel="shortcut icon" href="./images/favicon.png">
    
    <!-- DashLite Styles -->
    <link rel="stylesheet" href="<?php echo e(asset('/assets/css/dashlite.css')); ?>">
    <link id="skin-default" rel="stylesheet" href="<?php echo e(asset('/assets/css/theme.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('/css/theme-overrides.css')); ?>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
   
</head>
<body class="nk-body bg-lighter npc-default has-sidebar">
    <div class="nk-app-root">
        <div class="nk-main">
            <!-- Sidebar for author pages -->
            <?php if(request()->is('author*')): ?>
                <?php echo $__env->make('layouts.author-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>
            
            <!-- Wrap -->
            <div class="nk-wrap">
                <!-- Main Header -->
                <?php if(auth()->check()): ?>
                <div class="nk-header is-light nk-header-fixed is-light">
                    <div class="container-xl wide-xl">
                        <div class="nk-header-wrap">
                            <div class="nk-menu-trigger d-xl-none ms-n1 me-3">
                                <?php if(request()->is('author*')): ?>
                                    <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
                                <?php endif; ?>
                            </div>
                            <div class="nk-header-brand d-xl-none">
                                <a href="<?php echo e(route('dashboard')); ?>" class="logo-link">
                                    <img class="logo-light logo-img" src="./images/logo.png" srcset="./images/logo2x.png 2x" alt="logo">
                                </a>
                            </div>
                           
                            <div class="nk-header-tools">
                                <ul class="nk-quick-nav">
                                    <?php if(auth()->check()): ?>
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
                                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside">
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
                                                        <?php if(Auth::user()->avatar): ?>
                                                            <img src="<?php echo e(asset('storage/images/avatar/' . Auth::user()->avatar)); ?>" alt="<?php echo e(Auth::user()->name); ?>">
                                                        <?php else: ?>
                                                            <img src="<?php echo e(asset('storage/images/avatar/default.png')); ?>" alt="<?php echo e(Auth::user()->name); ?>">
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="user-info">
                                                        <span class="lead-text"><?php echo e(Auth::user()->name); ?></span>
                                                        <span class="sub-text"><?php echo e(Auth::user()->email); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                              <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a href="<?php echo e(route('admin.profile.index')); ?>"><em class="icon ni ni-user-alt"></em><span>View Profile</span></a></li>
                                                    
                                                    <li><a href="#" id="loginActivityLink"><em class="icon ni ni-activity-alt"></em><span>Login Activity</span></a></li>
                                                    <li><a href="#" id="darkModeToggleProfile"><em class="icon ni ni-moon"></em><span>Dark Mode</span></a></li>
                                                    <?php if(auth()->user()->hasRole('author')): ?>
                                                        <li><a href="/author/dashboard"><em class="icon ni ni-swap-alt"></em><span>Switch to Author</span></a></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li>
                                                        <form method="POST" action="<?php echo e(route('logout')); ?>" id="logout-form">
                                                            <?php echo csrf_field(); ?>
                                                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                                <em class="icon ni ni-signout"></em><span>Sign out</span>
                                                            </a>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <!-- Main Header End -->

                <!-- Main Content -->
                <div class="nk-content">
                    <div class="container-xl wide-xl">
                        <div class="nk-content-body">
                            <?php echo $__env->yieldContent('content'); ?>
                        </div>
                    </div>
                </div>
                <!-- Main Content End -->

                <!-- Footer -->
                <div class="nk-footer">
                    <div class="container-xl wide-xl">
                        <div class="nk-footer-wrap">
                            <div class="nk-footer-copyright"> &copy; 2025 Rhymes Platform
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Footer End -->
            </div>
            <!-- Wrap End -->
        </div>
    </div>
    
    <!-- Pusher JS library -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    <script>
        // Pusher configuration from Laravel
        window.pusherKey = '<?php echo e(config("broadcasting.connections.pusher.key")); ?>';
        window.pusherCluster = '<?php echo e(config("broadcasting.connections.pusher.options.cluster")); ?>';
        window.userId = <?php echo e(auth()->check() ? auth()->user()->id : 'null'); ?>;
    </script>
    
    <script src="<?php echo e(asset('assets/js/bundle.js?ver=3.2.3')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/scripts.js?ver=3.2.3')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/charts/chart-ecommerce.js?ver=3.2.3')); ?>"></script>
    
    <!-- Notifications Script -->
    <script src="<?php echo e(asset('js/notifications.js')); ?>"></script>
    
    <!-- Admin Custom Script -->
    <script src="<?php echo e(asset('js/admin.js')); ?>"></script>
    
    <!-- SweetAlert2 Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Display success message if session has 'success' key
            <?php if(session('success')): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?php echo e(session('success')); ?>',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            <?php endif; ?>

            // Display error message if session has 'error' key
            <?php if(session('error')): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '<?php echo e(session('error')); ?>',
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#e85347'
                });
            <?php endif; ?>

            // Display warning message if session has 'warning' key
            <?php if(session('warning')): ?>
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: '<?php echo e(session('warning')); ?>',
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f4bd0e'
                });
            <?php endif; ?>

            // Display info message if session has 'info' key
            <?php if(session('info')): ?>
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: '<?php echo e(session('info')); ?>',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            <?php endif; ?>
            
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
    
    <?php echo $__env->yieldContent('scripts'); ?>
    
    <!-- Fix for dropdown issue -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure Bootstrap dropdowns are properly initialized
            var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        });
    </script>
</body>
</html><?php /**PATH /home/rovinghe/author.rovingheights.com/resources/views/layouts/app.blade.php ENDPATH**/ ?>