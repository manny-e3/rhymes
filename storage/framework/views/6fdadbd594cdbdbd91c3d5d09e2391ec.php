<?php $__env->startSection('title', 'Trashed Users | Admin Panel'); ?>

<?php $__env->startSection('page-title', 'Trashed Users'); ?>

<?php $__env->startSection('page-description', 'Manage deleted platform users'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Trashed Users</h3>
                        <div class="nk-block-des text-soft">
                            <p>Manage deleted platform users. These users have been soft deleted and can be restored.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Back to Users</span></a></li>
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
                                        <form method="GET" action="<?php echo e(route('admin.users.trashed')); ?>" class="d-flex gap-2">
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
                                            <div class="form-wrap flex-md-nowrap">
                                                <div class="form-icon form-icon-right">
                                                    <em class="icon ni ni-search"></em>
                                                </div>
                                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search trashed users..." value="<?php echo e(request('search')); ?>">
                                            </div>
                                            <div class="btn-wrap">
                                                <button type="submit" class="btn btn-sm btn-icon btn-primary"><em class="icon ni ni-search"></em></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="card-tools me-n1">
                                    <ul class="btn-toolbar gx-1">
                                        <li>
                                            <a href="#" class="btn btn-icon search-toggle toggle-search" data-target="search"><em class="icon ni ni-search"></em></a>
                                        </li>
                                        <li class="btn-toolbar-sep"></li>
                                        <li>
                                            <div class="toggle-wrap">
                                                <a href="#" class="btn btn-icon btn-trigger toggle" data-target="cardTools"><em class="icon ni ni-menu-right"></em></a>
                                                <div class="toggle-content" data-content="cardTools">
                                                    <ul class="btn-toolbar gx-1">
                                                        <li class="toggle-close">
                                                            <a href="#" class="btn btn-icon btn-trigger toggle" data-target="cardTools"><em class="icon ni ni-arrow-left"></em></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card-inner p-0">
                            <div class="nk-tb-list nk-tb-ulist">
                                <div class="nk-tb-item nk-tb-head">
                                    <div class="nk-tb-col"><span class="sub-text">User</span></div>
                                    <div class="nk-tb-col tb-col-mb"><span class="sub-text">Role</span></div>
                                    <div class="nk-tb-col tb-col-md"><span class="sub-text">Deleted At</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Joined</span></div>
                                    <div class="nk-tb-col nk-tb-col-tools text-end">
                                        <span class="sub-text">Actions</span>
                                    </div>
                                </div>

                                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-avatar bg-danger">
                                                    <span><?php echo e(strtoupper(substr($user->name, 0, 2))); ?></span>
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead"><?php echo e($user->name); ?></span>
                                                    <span><?php echo e($user->email); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col tb-col-mb">
                                            <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge badge-sm badge-dim bg-outline-danger"><?php echo e(ucfirst($role->name)); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
                                            <span class="tb-sub"><?php echo e($user->deleted_at->format('M d, Y H:i')); ?></span>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span><?php echo e($user->created_at->format('M d, Y')); ?></span>
                                        </div>
                                        <div class="nk-tb-col nk-tb-col-tools">
                                            <ul class="nk-tb-actions gx-1">
                                                <li>
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li>
                                                                    <form method="POST" action="<?php echo e(route('admin.users.restore', $user)); ?>">
                                                                        <?php echo csrf_field(); ?>
                                                                        <button type="submit" class="btn btn-link text-start w-100">
                                                                            <em class="icon ni ni-recover"></em><span>Restore User</span>
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li class="divider"></li>
                                                                
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
                                                <p class="text-soft mt-2">No trashed users found</p>
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
                                            'role' => request('role', ''),
                                            'search' => request('search', '')
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/users/trashed.blade.php ENDPATH**/ ?>