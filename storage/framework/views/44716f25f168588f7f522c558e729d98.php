<?php $__env->startSection('title', $user->name . ' | User Details'); ?>

<?php $__env->startSection('page-title', 'User Details'); ?>

<?php $__env->startSection('page-description', 'View and manage user information'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title"><?php echo e($user->name); ?></h3>
                        <div class="nk-block-des text-soft">
                            <p>User ID: #<?php echo e($user->id); ?> • Joined <?php echo e($user->created_at->format('M d, Y')); ?></p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-primary"><em class="icon ni ni-edit"></em><span>Edit User</span></a></li>
                                    <li><a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-arrow-left"></em><span>Back to Users</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- User Profile Card -->
                    <div class="col-xxl-4">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">User Profile</h6>
                                    </div>
                                    <div class="card-tools">
                                        <?php if($user->email_verified_at): ?>
                                            <span class="badge badge-success">Verified</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Unverified</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="user-card">
                                    <div class="user-avatar lg bg-primary">
                                        <span><?php echo e(strtoupper(substr($user->name, 0, 2))); ?></span>
                                    </div>
                                    <div class="user-info">
                                        <h5><?php echo e($user->name); ?></h5>
                                        <span class="sub-text"><?php echo e($user->email); ?></span>
                                    </div>
                                </div>
                                
                                <div class="user-meta mt-4">
                                    <ul class="nk-list-meta">
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Role:</span>
                                            <span class="nk-list-meta-value">
                                                <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="badge badge-dim bg-outline-primary"><?php echo e(ucfirst($role->name)); ?></span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </span>
                                        </li>
                                        <?php if($user->phone): ?>
                                            <li class="nk-list-meta-item">
                                                <span class="nk-list-meta-label">Phone:</span>
                                                <span class="nk-list-meta-value"><?php echo e($user->phone); ?></span>
                                            </li>
                                        <?php endif; ?>
                                        <?php if($user->website): ?>
                                            <li class="nk-list-meta-item">
                                                <span class="nk-list-meta-label">Website:</span>
                                                <span class="nk-list-meta-value">
                                                    <a href="<?php echo e($user->website); ?>" target="_blank" class="link"><?php echo e($user->website); ?></a>
                                                </span>
                                            </li>
                                        <?php endif; ?>
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Member Since:</span>
                                            <span class="nk-list-meta-value"><?php echo e($user->created_at->format('M d, Y')); ?></span>
                                        </li>
                                        <?php if($user->email_verified_at): ?>
                                            <li class="nk-list-meta-item">
                                                <span class="nk-list-meta-label">Verified:</span>
                                                <span class="nk-list-meta-value"><?php echo e($user->email_verified_at->format('M d, Y')); ?></span>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                
                                <?php if($user->bio): ?>
                                    <div class="user-bio mt-4">
                                        <h6 class="overline-title-alt">About</h6>
                                        <p><?php echo e($user->bio); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- User Statistics & Activity -->
                    <div class="col-xxl-8">
                        <?php if($user->hasRole('author')): ?>
                            <!-- Author Statistics -->
                            <div class="card card-bordered card-full mb-4">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">Author Statistics</h6>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-4">
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="statbox">
                                                <div class="inbox-item">
                                                    <div class="inbox-item-img">
                                                        <div class="inbox-item-img bg-primary-dim">
                                                            <em class="icon ni ni-book"></em>
                                                        </div>
                                                    </div>
                                                    <div class="inbox-item-body">
                                                        <p class="inbox-item-text">Total Books</p>
                                                        <h4 class="inbox-item-title"><?php echo e(number_format($stats['total_books'])); ?></h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="statbox">
                                                <div class="inbox-item">
                                                    <div class="inbox-item-img">
                                                        <div class="inbox-item-img bg-success-dim">
                                                            <em class="icon ni ni-check-circle"></em>
                                                        </div>
                                                    </div>
                                                    <div class="inbox-item-body">
                                                        <p class="inbox-item-text">Published</p>
                                                        <h4 class="inbox-item-title"><?php echo e(number_format($stats['published_books'])); ?></h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="statbox">
                                                <div class="inbox-item">
                                                    <div class="inbox-item-img">
                                                        <div class="inbox-item-img bg-warning-dim">
                                                            <em class="icon ni ni-clock"></em>
                                                        </div>
                                                    </div>
                                                    <div class="inbox-item-body">
                                                        <p class="inbox-item-text">Pending</p>
                                                        <h4 class="inbox-item-title"><?php echo e(number_format($stats['pending_books'])); ?></h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="statbox">
                                                <div class="inbox-item">
                                                    <div class="inbox-item-img">
                                                        <div class="inbox-item-img bg-info-dim">
                                                            <em class="icon ni ni-coins"></em>
                                                        </div>
                                                    </div>
                                                    <div class="inbox-item-body">
                                                        <p class="inbox-item-text">Total Earnings</p>
                                                        <h4 class="inbox-item-title">₦<?php echo e(number_format($stats['total_earnings'], 2)); ?></h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Recent Books -->
                            <?php if($user->books->count() > 0): ?>
                                <div class="card card-bordered card-full mb-4">
                                    <div class="card-inner">
                                        <div class="card-title-group align-start mb-3">
                                            <div class="card-title">
                                                <h6 class="title">Recent Books</h6>
                                            </div>
                                        </div>
                                        
                                        <div class="nk-tb-list nk-tb-orders">
                                            <div class="nk-tb-item nk-tb-head">
                                                <div class="nk-tb-col"><span>Title</span></div>
                                                <div class="nk-tb-col tb-col-md"><span>Status</span></div>
                                                <div class="nk-tb-col tb-col-lg"><span>Created</span></div>
                                                <div class="nk-tb-col"><span>Action</span></div>
                                            </div>
                                            <?php $__currentLoopData = $user->books->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="nk-tb-item">
                                                    <div class="nk-tb-col">
                                                        <span class="tb-lead"><?php echo e($book->title); ?></span>
                                                        <span class="tb-sub text-primary"><?php echo e($book->genre); ?></span>
                                                    </div>
                                                    <div class="nk-tb-col tb-col-md">
                                                        <?php if($book->status === 'pending'): ?>
                                                            <span class="badge badge-dot badge-dot-xs bg-warning">Pending</span>
                                                        <?php elseif($book->status === 'accepted'): ?>
                                                            <span class="badge badge-dot badge-dot-xs bg-success">Published</span>
                                                        <?php elseif($book->status === 'rejected'): ?>
                                                            <span class="badge badge-dot badge-dot-xs bg-danger">Rejected</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="nk-tb-col tb-col-lg">
                                                        <span class="tb-sub"><?php echo e($book->created_at->format('M d, Y')); ?></span>
                                                    </div>
                                                    <div class="nk-tb-col">
                                                        <a href="<?php echo e(route('admin.books.show', $book)); ?>" class="btn btn-sm btn-outline-light">View</a>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Payout History -->
                            <?php if($user->payouts->count() > 0): ?>
                                <div class="card card-bordered card-full">
                                    <div class="card-inner">
                                        <div class="card-title-group align-start mb-3">
                                            <div class="card-title">
                                                <h6 class="title">Recent Payouts</h6>
                                            </div>
                                        </div>
                                        
                                        <div class="nk-tb-list nk-tb-orders">
                                            <div class="nk-tb-item nk-tb-head">
                                                <div class="nk-tb-col"><span>Amount</span></div>
                                                <div class="nk-tb-col tb-col-md"><span>Status</span></div>
                                                <div class="nk-tb-col tb-col-lg"><span>Requested</span></div>
                                            </div>
                                            <?php $__currentLoopData = $user->payouts->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="nk-tb-item">
                                                    <div class="nk-tb-col">
                                                        <span class="tb-lead">$<?php echo e(number_format($payout->amount_requested, 2)); ?></span>
                                                        <span class="tb-sub">Fee: $<?php echo e(number_format($payout->processing_fee, 2)); ?></span>
                                                    </div>
                                                    <div class="nk-tb-col tb-col-md">
                                                        <?php if($payout->status === 'pending'): ?>
                                                            <span class="badge badge-dot badge-dot-xs bg-warning">Pending</span>
                                                        <?php elseif($payout->status === 'approved'): ?>
                                                            <span class="badge badge-dot badge-dot-xs bg-success">Approved</span>
                                                        <?php elseif($payout->status === 'denied'): ?>
                                                            <span class="badge badge-dot badge-dot-xs bg-danger">Denied</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="nk-tb-col tb-col-lg">
                                                        <span class="tb-sub"><?php echo e($payout->created_at->format('M d, Y')); ?></span>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Non-Author User Actions -->
                            <div class="card card-bordered card-full">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">User Actions</h6>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <div class="alert-cta">
                                                    <h6>Promote to Author</h6>
                                                    <p>This user is not currently an author. You can promote them to author status to allow them to publish books.</p>
                                                    <form method="POST" action="<?php echo e(route('admin.users.promote-author', $user)); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-info" data-confirm-promote data-confirm-message="Are you sure you want to promote <?php echo e($user->name); ?> to author? This will give them author privileges and allow them to publish books.">
                                                            Promote to Author
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Activities Section -->
<div class="nk-content nk-content-fluid mt-4">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="card-title-group align-start mb-3">
                        <div class="card-title">
                            <h6 class="title">User Activities</h6>
                            <p>View detailed activity log for this user</p>
                        </div>
                        <div class="card-tools">
                            <a href="<?php echo e(route('admin.users.activities', $user)); ?>" class="btn btn-primary">
                                <em class="icon ni ni-activity"></em>
                                <span>View All Activities</span>
                            </a>
                        </div>
                    </div>
                    <p class="text-soft">This section shows all activities performed by this user. Click the button above to view the complete activity log with detailed information.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>

</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/users/show.blade.php ENDPATH**/ ?>