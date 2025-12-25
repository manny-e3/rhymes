

<?php $__env->startSection('title', $user->name . ' - User Activities | Admin Panel'); ?>

<?php $__env->startSection('page-title', $user->name . ' - User Activities'); ?>

<?php $__env->startSection('page-description', 'Activity log for user: ' . $user->name . ' (' . $user->email . ')'); ?>

<?php $__env->startSection('content'); ?>
<!-- content @s -->
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Activities for <?php echo e($user->name); ?></h3>
                        <div class="nk-block-des text-soft">
                            <p>User ID: #<?php echo e($user->id); ?> • Joined <?php echo e($user->created_at->format('M d, Y')); ?></p>
                        </div>
                    </div><!-- .nk-block-head-content -->
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li>
                                        <form method="GET" action="<?php echo e(route('admin.users.activities', $user)); ?>">
                                            <div class="form-control-wrap">
                                                <select name="period" class="form-select" onchange="this.form.submit()">
                                                    <option value="7" <?php echo e(request('period', 30) == 7 ? 'selected' : ''); ?>>Last 7 days</option>
                                                    <option value="30" <?php echo e(request('period', 30) == 30 ? 'selected' : ''); ?>>Last 30 days</option>
                                                    <option value="90" <?php echo e(request('period', 30) == 90 ? 'selected' : ''); ?>>Last 90 days</option>
                                                </select>
                                            </div>
                                        </form>
                                    </li>
                                    <li>
                                        <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="btn btn-white btn-dim btn-outline-light">
                                            <em class="icon ni ni-user"></em><span>User Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-white btn-dim btn-outline-light">
                                            <em class="icon ni ni-users"></em><span>All Users</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- .nk-block-head-content -->
                </div><!-- .nk-block-between -->
            </div><!-- .nk-block-head -->

            <div class="nk-block">
                <div class="card card-bordered card-full">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-3">
                            <div class="card-title">
                                <h6 class="title">Activity Log</h6>
                                <p>Recent activities performed by this user</p>
                            </div>
                        </div>

                        <?php if($paginatedActivities->count() > 0): ?>
                            <ul class="nk-activity">
                                <?php $__currentLoopData = $paginatedActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="nk-activity-item">
                                        <div class="nk-activity-media user-avatar <?php echo e($activity->activity_type === 'login' ? 'bg-success' : ($activity->activity_type === 'logout' ? 'bg-warning' : 'bg-primary')); ?>">
                                            <em class="icon ni ni-activity-round"></em>
                                        </div>
                                        <div class="nk-activity-data">
                                            <div class="label"><?php echo e($activity->description); ?></div>
                                            <span class="time"><?php echo e($activity->created_at->diffForHumans()); ?></span>
                                            <?php if($activity->metadata): ?>
                                                <div class="text-soft small mt-1">
                                                    <details>
                                                        <summary>View Details</summary>
                                                        <pre class="mb-0" style="font-size: 0.8em; background: #f8f9fa; padding: 8px; border-radius: 4px;"><?php echo e(json_encode($activity->metadata, JSON_PRETTY_PRINT)); ?></pre>
                                                    </details>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>

                            <div class="mt-4">
                                <?php echo e($paginatedActivities->appends(['period' => request('period')])->links()); ?>

                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <em class="icon ni ni-activity" style="font-size: 3rem; opacity: 0.3;"></em>
                                <p class="text-soft mt-2">No activities found for this user</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div><!-- .nk-block -->
        </div>
    </div>
</div>
<!-- content @e -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/users/user-activities.blade.php ENDPATH**/ ?>