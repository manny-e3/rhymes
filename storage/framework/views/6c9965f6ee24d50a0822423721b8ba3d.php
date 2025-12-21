<?php $__env->startSection('title', 'Admin Dashboard | Rhymes Platform'); ?>

<?php $__env->startSection('page-title', 'Admin Dashboard'); ?>

<?php $__env->startSection('page-description', 'Platform Overview & Analytics'); ?>

<?php $__env->startSection('content'); ?>
<!-- main header @e -->
<!-- content @s -->
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Admin Dashboard</h3>
                        <div class="nk-block-des text-soft">
                            <p>Platform overview, analytics, and management tools.</p>
                        </div>
                    </div><!-- .nk-block-head-content -->
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="<?php echo e(route('admin.reports.sales-dashboard')); ?>" class="btn btn-primary"><em class="icon ni ni-dashboard"></em><span>Sales Dashboard</span></a></li>
                                    <li><a href="<?php echo e(route('admin.books.index')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-eye"></em><span>Review Books</span></a></li>
                                    <li><a href="<?php echo e(route('admin.payouts.index')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-tranx"></em><span>Manage Payouts</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.sales')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-swap"></em><span>ERPREV Data</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- .nk-block-head-content -->
                </div><!-- .nk-block-between -->
            </div><!-- .nk-block-head -->
            
            <!-- SweetAlert Test Buttons -->
         
            
            <div class="nk-block">
                <!-- Overview Stats Cards -->
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Users</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-users text-primary"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount"><?php echo e(number_format($analytics['stats']['total_users'])); ?> </span>
                                    <span class="sub-title">Authors: <?php echo e(number_format($analytics['stats']['total_authors'])); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Revenue</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-coins text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦<?php echo e(number_format($analytics['stats']['total_revenue'], 2)); ?></span>
                                    <?php if($analytics['stats']['revenue_growth'] > 0): ?>
                                        <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em><?php echo e(number_format($analytics['stats']['revenue_growth'], 1)); ?>% this month</span>
                                    <?php elseif($analytics['stats']['revenue_growth'] < 0): ?>
                                        <span class="sub-title text-danger"><em class="icon ni ni-arrow-long-down"></em><?php echo e(number_format(abs($analytics['stats']['revenue_growth']), 1)); ?>% this month</span>
                                    <?php else: ?>
                                      

                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Books</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-book text-info"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount"><?php echo e(number_format($analytics['stats']['total_books'])); ?></span>
                                    <span class="sub-title">Published: <?php echo e(number_format($analytics['stats']['published_books'])); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Pending Reviews</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-clock text-warning"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount"><?php echo e(number_format($analytics['stats']['pending_books'])); ?></span>
                                    <span class="sub-title">Need Attention</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Secondary Stats -->
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Pending Payouts</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-tranx text-warning"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount"><?php echo e(number_format($analytics['stats']['pending_payouts'])); ?></span>
                                    <span class="sub-title">₦<?php echo e(number_format($analytics['stats']['pending_payout_amount'], 2)); ?> pending</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">This Month Revenue</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-growth text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦<?php echo e(number_format($analytics['stats']['this_month_revenue'], 2)); ?></span>
                                    <span class="sub-title">vs ₦<?php echo e(number_format($analytics['stats']['last_month_revenue'], 2)); ?> last month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Payouts</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-check-circle text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦<?php echo e(number_format($analytics['stats']['total_payout_amount'], 2)); ?></span>
                                    <span class="sub-title"><?php echo e(number_format($analytics['stats']['approved_payouts'])); ?> completed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-gs">
                    <!-- Recent Books Requiring Review -->
                    <div class="col-xxl-8">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Recent Book Submissions</h6>
                                        <p>Latest books submitted for review</p>
                                    </div>
                                    
                                </div>
                                <?php if(count($analytics['recent']['books']) > 0): ?>
                                    <div class="nk-tb-list nk-tb-orders">
                                        <div class="nk-tb-item nk-tb-head">
                                            <div class="nk-tb-col"><span>Book Title</span></div>
                                            <div class="nk-tb-col tb-col-md"><span>Author</span></div>
                                            <div class="nk-tb-col"><span>Status</span></div>
                                            <div class="nk-tb-col"><span>Submitted</span></div>
                                        </div>
                                        <?php $__currentLoopData = $analytics['recent']['books']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col">
                                                    <span class="tb-product">
                                                        <span class="title"><?php echo e(Str::limit($book->title, 25)); ?></span>
                                                    </span>
                                                </div>
                                                <div class="nk-tb-col tb-col-md">
                                                    <span><?php echo e($book->user->name); ?></span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span class="badge bg-<?php echo e($book->status === 'accepted' ? 'success' : ($book->status === 'rejected' ? 'danger' : 'warning')); ?>"><?php echo e(ucfirst($book->status)); ?></span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span><?php echo e($book->created_at->diffForHumans()); ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center p-3">
                                        <em class="icon ni ni-book" style="font-size: 2rem;"></em>
                                        <p class="text-soft mt-2">No recent book submissions</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent User Registrations -->
                    <div class="col-xxl-4">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Recent Users</h6>
                                        <p>New platform registrations</p>
                                    </div>
                                </div>
                                <?php if(count($analytics['recent']['users']) > 0): ?>
                                    <div class="nk-tb-list is-alt">
                                        <?php $__currentLoopData = $analytics['recent']['users']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col">
                                                    <div class="user-card">
                                                        <div class="user-avatar bg-primary-dim">
                                                            <span><?php echo e(strtoupper(substr($user->name, 0, 2))); ?></span>
                                                        </div>
                                                        <div class="user-info">
                                                            <span class="tb-lead"><?php echo e($user->name); ?></span>
                                                            <span><?php echo e($user->email); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span class="sub-text"><?php echo e($user->created_at->diffForHumans()); ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center p-3">
                                        <em class="icon ni ni-user" style="font-size: 2rem;"></em>
                                        <p class="text-soft mt-2">No recent user registrations</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>