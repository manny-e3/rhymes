<?php $__env->startSection('title', 'User Management | Admin Panel'); ?>

<?php $__env->startSection('page-title', 'User Management'); ?>

<?php $__env->startSection('page-description', ''); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Users</h3>
                        <div class="nk-block-des text-soft">
                            <p>Manage all platform users, roles, and permissions.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary"><em class="icon ni ni-plus"></em><span>Add User</span></a></li>
                                    <li><a href="<?php echo e(route('admin.users.authors')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-users"></em><span>View Authors</span></a></li>
                                    <li><a href="<?php echo e(route('admin.users.trashed')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-trash"></em><span>Trashed Users</span></a></li>
                                    <li>
                                        <div class="dropdown">
                                            <a class="btn btn-white btn-dim btn-outline-light dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                                <em class="icon ni ni-download-cloud"></em><span>Export</span>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="<?php echo e(route('admin.users.export.csv', request()->query())); ?>"><em class="icon ni ni-file-text"></em><span>Export as CSV</span></a>
                                                <a class="dropdown-item" href="<?php echo e(route('admin.users.export.pdf', request()->query())); ?>"><em class="icon ni ni-file-pdf"></em><span>Export as PDF</span></a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="card card-bordered card-stretch">
                    <div class="card-inner-group">
                        <div class="card-inner position-relative card-tools-toggle">
                            <div class="card-title-group">
                                <div class="card-tools">
                                    <div class="form-inline flex-nowrap gx-3">
                                        <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="d-flex gap-2">
                                            <div class="form-wrap w-150px">
                                                <select name="role" class="form-select form-select-sm">
                                                    <option value="">All Roles</option>
                                                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($role->name); ?>" <?php echo e(request('role') === $role->name ? 'selected' : ''); ?>>
                                                            <?php echo e(ucfirst($role->name)); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="form-wrap w-150px">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="">All Status</option>
                                                    <option value="verified" <?php echo e(request('status') === 'verified' ? 'selected' : ''); ?>>Verified</option>
                                                    <option value="unverified" <?php echo e(request('status') === 'unverified' ? 'selected' : ''); ?>>Unverified</option>
                                                </select>
                                            </div>
                                            <div class="form-wrap flex-md-nowrap">
                                                <div class="form-icon form-icon-right">
                                                    <em class="icon ni ni-search"></em>
                                                </div>
                                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search users..." value="<?php echo e(request('search')); ?>">
                                            </div>
                                            <div class="btn-wrap">
                                                <button type="submit" class="btn btn-sm btn-icon btn-primary"><em class="icon ni ni-search"></em></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <br>
                                <br>
                               
                                
                            </div>
                        </div>

                        <div class="card-inner p-0">
                            <div class="nk-tb-list nk-tb-ulist">
                                <div class="nk-tb-item nk-tb-head">
                                    <div class="nk-tb-col"><span class="sub-text">User</span></div>
                                    <div class="nk-tb-col tb-col-mb"><span class="sub-text">Role</span></div>
                                    <div class="nk-tb-col tb-col-md"><span class="sub-text">Email Status</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Account Status</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Joined</span></div>
                                    <div class="nk-tb-col nk-tb-col-tools text-end">
                                        <div class="dropdown">
                                            
                                            <div class="dropdown-menu dropdown-menu-end">
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary">
                                                    <span><?php echo e(strtoupper(substr($user->name, 0, 2))); ?></span>
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead"><?php echo e($user->name); ?> 
                                                        <?php if($user->email_verified_at): ?>
                                                            <span class="dot dot-success d-md-none ms-1"></span>
                                                        <?php endif; ?>
                                                    </span>
                                                    <span><?php echo e($user->email); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col tb-col-mb">
                                            <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge badge-sm badge-dim bg-outline-primary"><?php echo e(ucfirst($role->name)); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
                                            <?php if($user->email_verified_at): ?>
                                                <span class="tb-status text-success">Verified</span>
                                            <?php else: ?>
                                                <span class="tb-status text-warning">Unverified</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <?php if($user->isActive()): ?>
                                                <span class="tb-status text-success">Active</span>
                                            <?php else: ?>
                                                <span class="tb-status text-danger">Deactivated</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span><?php echo e($user->created_at->format('M d, Y')); ?></span>
                                        </div>
                                        <div class="nk-tb-col nk-tb-col-tools">
                                            <ul class="nk-tb-actions gx-1">
                                                <li class="nk-tb-action-hidden">
                                                    <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                                        <em class="icon ni ni-eye-fill"></em>
                                                    </a>
                                                </li>
                                                
                                                <li>
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li><a href="<?php echo e(route('admin.users.show', $user)); ?>"><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                
                                                                <?php if(!$user->hasRole('author')): ?>
                                                                    <li>
                                                                        <form method="POST" action="<?php echo e(route('admin.users.promote-author', $user)); ?>">
                                                                            <?php echo csrf_field(); ?>
                                                                            <button type="submit" class="btn btn-link text-start w-100" data-confirm-promote data-confirm-message="Are you sure you want to promote <?php echo e($user->name); ?> to author?">
                                                                                <em class="icon ni ni-user-add"></em><span>Promote to Author</span>
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                <?php endif; ?>
                                                                
                                                                <!-- Account Status Management -->
                                                                <li>
                                                                    <?php if($user->isActive()): ?>
                                                                        <form method="POST" action="<?php echo e(route('admin.users.deactivate', $user)); ?>">
                                                                            <?php echo csrf_field(); ?>
                                                                            <button type="submit" class="btn btn-link text-start w-100 text-warning" data-confirm-deactivate data-confirm-message="Are you sure you want to deactivate <?php echo e($user->name); ?>? This will prevent them from logging in.">
                                                                                <em class="icon ni ni-user-cross"></em><span>Deactivate Account</span>
                                                                            </button>
                                                                        </form>
                                                                    <?php else: ?>
                                                                        <form method="POST" action="<?php echo e(route('admin.users.activate', $user)); ?>">
                                                                            <?php echo csrf_field(); ?>
                                                                            <button type="submit" class="btn btn-link text-start w-100 text-success" data-confirm-activate data-confirm-message="Are you sure you want to activate <?php echo e($user->name); ?>? This will allow them to log in.">
                                                                                <em class="icon ni ni-user-check"></em><span>Activate Account</span>
                                                                            </button>
                                                                        </form>
                                                                    <?php endif; ?>
                                                                </li>
                                                                
                                                                <li class="divider"></li>
                                                                <li>
                                                                    <form method="POST" action="<?php echo e(route('admin.users.destroy', $user)); ?>">
                                                                        <?php echo csrf_field(); ?>
                                                                        <?php echo method_field('DELETE'); ?>
                                                                        <button type="submit" class="btn btn-link text-start w-100 text-danger" data-confirm-delete data-confirm-message="Are you sure you want to delete <?php echo e($user->name); ?>? This action cannot be undone!">
                                                                            <em class="icon ni ni-trash"></em><span>Delete User</span>
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <div class="text-center py-4">
                                                <em class="icon ni ni-users" style="font-size: 3rem; opacity: 0.3;"></em>
                                                <p class="text-soft mt-2">No users found</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-inner">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Showing <?php echo e($users->firstItem()); ?> to <?php echo e($users->lastItem()); ?> of <?php echo e($users->total()); ?> entries
                                </div>
                                <?php if($users->hasPages()): ?>
                                    <div>
                                        <?php echo e($users->appends([
                                           
                                        ])->links('vendor.pagination.bootstrap-4')); ?>

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

<?php $__env->startPush('scripts'); ?>
<script>
// The confirmation dialog is handled by our admin.js script
// which looks for elements with data-confirm-activate and data-confirm-deactivate attributes
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/users/index.blade.php ENDPATH**/ ?>