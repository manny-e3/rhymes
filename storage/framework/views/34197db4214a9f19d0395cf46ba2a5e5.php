

<?php $__env->startSection('title', 'Email Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Email Management</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Admin</a></li>
                        <li class="breadcrumb-item active">Email Management</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card overflow-hidden">
                <div class="bg-primary bg-soft">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-primary p-3">
                                <h5 class="text-primary">Total Sent</h5>
                                <h3 class="mb-0"><?php echo e($stats['total_sent'] ?? 0); ?></h3>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <div class="py-4 text-center">
                                <div class="avatar-md mx-auto">
                                    <div class="avatar-title bg-primary text-primary rounded">
                                        <i class="fas fa-paper-plane font-size-24"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card overflow-hidden">
                <div class="bg-success bg-soft">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-success p-3">
                                <h5 class="text-success">Authors</h5>
                                <h3 class="mb-0"><?php echo e($stats['total_authors'] ?? 0); ?></h3>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <div class="py-4 text-center">
                                <div class="avatar-md mx-auto">
                                    <div class="avatar-title bg-success text-success rounded">
                                        <i class="fas fa-pen-fancy font-size-24"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card overflow-hidden">
                <div class="bg-warning bg-soft">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-warning p-3">
                                <h5 class="text-warning">Pending</h5>
                                <h3 class="mb-0"><?php echo e($stats['pending_emails'] ?? 0); ?></h3>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <div class="py-4 text-center">
                                <div class="avatar-md mx-auto">
                                    <div class="avatar-title bg-warning text-warning rounded">
                                        <i class="fas fa-clock font-size-24"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card overflow-hidden">
                <div class="bg-danger bg-soft">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-danger p-3">
                                <h5 class="text-danger">Failed</h5>
                                <h3 class="mb-0"><?php echo e($stats['total_failed'] ?? 0); ?></h3>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <div class="py-4 text-center">
                                <div class="avatar-md mx-auto">
                                    <div class="avatar-title bg-danger text-danger rounded">
                                        <i class="fas fa-exclamation-triangle font-size-24"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="row">
        <!-- Newsletter -->
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-header bg-primary">
                    <h4 class="card-title mb-0 text-white"><i class="fas fa-newspaper me-2"></i>Newsletter</h4>
                </div>
                <div class="card-body">
                    <p>Send newsletters to all authors or selected authors with rich content and formatting.</p>
                    <a href="<?php echo e(route('admin.emails.create')); ?>?type=newsletter" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Send Newsletter
                    </a>
                </div>
            </div>
        </div>

        <!-- Announcement -->
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-header bg-info">
                    <h4 class="card-title mb-0 text-white"><i class="fas fa-bullhorn me-2"></i>Announcement</h4>
                </div>
                <div class="card-body">
                    <p>Send important announcements to authors with special formatting and priority.</p>
                    <a href="<?php echo e(route('admin.emails.create')); ?>?type=announcement" class="btn btn-info">
                        <i class="fas fa-bullhorn me-2"></i>Send Announcement
                    </a>
                </div>
            </div>
        </div>

        <!-- Sales Report -->
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-header bg-success">
                    <h4 class="card-title mb-0 text-white"><i class="fas fa-chart-line me-2"></i>Sales Report</h4>
                </div>
                <div class="card-body">
                    <p>Send personalized sales performance reports to authors with detailed analytics.</p>
                    <a href="<?php echo e(route('admin.emails.create')); ?>?type=sales_report" class="btn btn-success">
                        <i class="fas fa-chart-line me-2"></i>Send Sales Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Personal Email -->
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h4 class="card-title mb-0 text-white"><i class="fas fa-envelope me-2"></i>Personal Email</h4>
                </div>
                <div class="card-body">
                    <p>Send a personalized email to a specific author or user.</p>
                    <a href="<?php echo e(route('admin.emails.personal.form')); ?>" class="btn btn-warning">
                        <i class="fas fa-envelope me-2"></i>Send Personal Email
                    </a>
                </div>
            </div>
        </div>

        <!-- Bulk Email -->
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-header bg-secondary">
                    <h4 class="card-title mb-0 text-white"><i class="fas fa-users me-2"></i>Bulk Email</h4>
                </div>
                <div class="card-body">
                    <p>Send emails to a selected group of users or authors.</p>
                    <a href="<?php echo e(route('admin.emails.create')); ?>?type=bulk" class="btn btn-secondary">
                        <i class="fas fa-users me-2"></i>Send Bulk Email
                    </a>
                </div>
            </div>
        </div>

        <!-- Email Logs -->
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0 text-white"><i class="fas fa-history me-2"></i>Email Logs</h4>
                </div>
                <div class="card-body">
                    <p>View history of all sent emails with status and delivery information.</p>
                    <a href="<?php echo e(route('admin.emails.logs')); ?>" class="btn btn-dark">
                        <i class="fas fa-history me-2"></i>View Email Logs
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Email Logs -->
    <?php if(isset($emailLogs) && $emailLogs->count() > 0): ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Recent Email Activity</h4>
                        <a href="<?php echo e(route('admin.emails.logs')); ?>" class="btn btn-sm btn-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Subject</th>
                                    <th>Recipients</th>
                                    <th>Status</th>
                                    <th>Sent/Failed</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $emailLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <span class="badge 
                                            <?php if($log->type == 'newsletter'): ?> badge-primary
                                            <?php elseif($log->type == 'announcement'): ?> badge-info
                                            <?php elseif($log->type == 'sales_report'): ?> badge-success
                                            <?php else: ?> badge-secondary
                                            <?php endif; ?>">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $log->type))); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e(Str::limit($log->subject, 50)); ?></td>
                                    <td><?php echo e($log->total_recipients); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php if($log->status == 'completed'): ?> badge-success
                                            <?php elseif($log->status == 'processing'): ?> badge-warning
                                            <?php elseif($log->status == 'failed'): ?> badge-danger
                                            <?php else: ?> badge-secondary
                                            <?php endif; ?>">
                                            <?php echo e(ucfirst($log->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-success"><?php echo e($log->sent_count); ?></span> / 
                                        <span class="text-danger"><?php echo e($log->failed_count); ?></span>
                                    </td>
                                    <td><?php echo e($log->created_at->diffForHumans()); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/emails/index.blade.php ENDPATH**/ ?>