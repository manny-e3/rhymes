<div class="nk-sidebar is-light nk-sidebar-fixed is-light " data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-sidebar-brand">
                                     <img class=""  height="100px"  src="<?php echo e(asset('/images/rovingHeights-logo.png')); ?>" srcset="<?php echo e(asset('images/rovingHeights-logo.png 2x')); ?>" alt="logo-dark">

        </div>
        <div class="nk-menu-trigger me-n2">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
        </div>
    </div>
    <div class="nk-sidebar-element">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
                <ul class="nk-menu">
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Dashboards</h6>
                    </li>
                    <li class="nk-menu-item">
                        <a href="<?php echo e(route('author.dashboard')); ?>" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-presentation"></em></span>
                            <span class="nk-menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a href="<?php echo e(route('author.books.index')); ?>" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-folder-list"></em></span>
                            <span class="nk-menu-text">Books</span>
                        </a>
                    </li>
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Earnings</h6>
                    </li>
                    <li class="nk-menu-item">
                        <a href="<?php echo e(route('author.wallet.index')); ?>" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-wallet"></em></span>
                            <span class="nk-menu-text">Wallet</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a href="<?php echo e(route('author.payouts.index')); ?>" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-wallet-out"></em></span>
                            <span class="nk-menu-text">Payouts</span>
                        </a>
                    </li>
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Account</h6>
                    </li>
                    <li class="nk-menu-item">
                        <a href="<?php echo e(route('author.profile.edit')); ?>" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-user"></em></span>
                            <span class="nk-menu-text">Profile</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/layouts/author-sidebar.blade.php ENDPATH**/ ?>