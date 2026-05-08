<?php $__env->startSection('title', 'Unified Dashboard | Rhymes Platform'); ?>

<?php $__env->startSection('page-title', 'Unified Dashboard'); ?>

<?php $__env->startSection('page-description', 'Comprehensive platform analytics and reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Unified Dashboard</h3>
                        <div class="nk-block-des text-soft">
                            <p>Comprehensive analytics and reports for the Rhymes Platform</p>
                        </div>
                    </div><!-- .nk-block-head-content -->
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="<?php echo e(route('admin.reports.sales-dashboard')); ?>" class="btn btn-primary"><em class="icon ni ni-dashboard"></em><span>Sales Dashboard</span></a></li>
                                    <li><a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-dashboard"></em><span>Main Dashboard</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.sales')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-swap"></em><span>ERPREV Data</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- .nk-block-head-content -->
                </div><!-- .nk-block-between -->
            </div><!-- .nk-block-head -->

            <div class="nk-block">
                <!-- Date Range Filter -->
                <div class="card card-bordered mb-4">
                    <div class="card-inner">
                        <form method="GET" action="<?php echo e(route('admin.unified-dashboard')); ?>">
                            <div class="row g-gs">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="form-label">Date Range</label>
                                        <div class="form-control-wrap">
                                            <div class="input-daterange datepicker-wrap">
                                                <div class="input-group">
                                                    <input type="text" class="form-control date-picker" name="start_date" value="<?php echo e(request('start_date', now()->subDays(30)->format('m/d/Y'))); ?>" placeholder="Start Date">
                                                    <div class="input-group-addon">TO</div>
                                                    <input type="text" class="form-control date-picker" name="end_date" value="<?php echo e(request('end_date', now()->format('m/d/Y'))); ?>" placeholder="End Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label d-none d-md-block">&nbsp;</label>
                                        <div class="form-control-wrap">
                                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Overview Cards -->
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Active Users</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-users text-primary"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount"><?php echo e(number_format($overview['stats']['total_users'] ?? 0)); ?></span>
                                    <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em><?php echo e(number_format($overview['stats']['active_users'] ?? 0)); ?> active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">New Users</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-user-add text-info"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount"><?php echo e(number_format($overview['stats']['new_users'] ?? 0)); ?></span>
                                    <span class="sub-title">This period</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Gross Revenue</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-coins text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦<?php echo e(number_format($overview['stats']['gross_revenue'] ?? 0, 2)); ?></span>
                                    <span class="sub-title">Platform: ₦<?php echo e(number_format($overview['stats']['platform_revenue'] ?? 0, 2)); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Author Earnings</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-user-c text-warning"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦<?php echo e(number_format($overview['stats']['author_earnings'] ?? 0, 2)); ?></span>
                                    <span class="sub-title">Payouts: ₦<?php echo e(number_format(abs($overview['stats']['payouts_paid'] ?? 0), 2)); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row g-gs mb-4">
                    <div class="col-lg-8">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Platform Analytics</h6>
                                        <p>Users, Authors & Books</p>
                                    </div>
                                </div>
                                <div class="nk-ck">
                                    <canvas class="analytics-chart" id="analyticsChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Sales Metrics</h6>
                                        <p>Key performance indicators</p>
                                    </div>
                                </div>
                                <div class="nk-ck">
                                    <canvas class="sales-chart" id="salesChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Tables Section -->
                <div class="row g-gs">
                    <div class="col-lg-6">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Top Performing Authors</h6>
                                        <p>By total earnings</p>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Author</th>
                                                <th>Books</th>
                                                <th>Earnings</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $topAuthors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $author): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td>
                                                        <div class="user-card">
                                                            <div class="user-avatar bg-primary-dim">
                                                                <span><?php echo e(strtoupper(substr($author->name, 0, 2))); ?></span>
                                                            </div>
                                                            <div class="user-info">
                                                                <span class="tb-lead"><?php echo e($author->name); ?></span>
                                                                <span><?php echo e($author->email); ?></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo e($author->books_count); ?></td>
                                                    <td>₦<?php echo e(number_format($author->total_earnings, 2)); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">No data available</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Top Selling Books</h6>
                                        <p>By revenue generated</p>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Book</th>
                                                <th>Author</th>
                                                <th>Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $topBooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td>
                                                        <span class="tb-lead"><?php echo e(Str::limit($book->title, 30)); ?></span>
                                                        <span class="small text-muted"><?php echo e($book->genre); ?></span>
                                                    </td>
                                                    <td><?php echo e($book->user->name); ?></td>
                                                    <td>₦<?php echo e(number_format($book->total_revenue, 2)); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">No data available</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Analytics Chart
    var analyticsCtx = document.getElementById('analyticsChart').getContext('2d');
    var analyticsChart = new Chart(analyticsCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($analytics['chartData']['labels'] ?? [], 15, 512) ?>,
            datasets: [{
                label: 'Users',
                data: <?php echo json_encode($analytics['chartData']['users'] ?? [], 15, 512) ?>,
                borderColor: '#559bfb',
                backgroundColor: 'rgba(85, 155, 251, 0.1)',
                borderWidth: 2,
                fill: true
            }, {
                label: 'Authors',
                data: <?php echo json_encode($analytics['chartData']['authors'] ?? [], 15, 512) ?>,
                borderColor: '#1ee0ac',
                backgroundColor: 'rgba(30, 224, 172, 0.1)',
                borderWidth: 2,
                fill: true
            }, {
                label: 'Books',
                data: <?php echo json_encode($analytics['chartData']['books'] ?? [], 15, 512) ?>,
                borderColor: '#f4bd0e',
                backgroundColor: 'rgba(244, 189, 14, 0.1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Sales Chart
    var salesCtx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: ['Revenue', 'Sales', 'AOV'],
            datasets: [{
                label: 'Current Period',
                data: [
                    <?php echo e($sales['metrics']['total_revenue'] ?? 0); ?>,
                    <?php echo e($sales['metrics']['total_sales'] ?? 0); ?>,
                    <?php echo e($sales['metrics']['avg_order_value'] ?? 0); ?>

                ],
                backgroundColor: [
                    'rgba(85, 155, 251, 0.7)',
                    'rgba(30, 224, 172, 0.7)',
                    'rgba(244, 189, 14, 0.7)'
                ],
                borderColor: [
                    'rgba(85, 155, 251, 1)',
                    'rgba(30, 224, 172, 1)',
                    'rgba(244, 189, 14, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/unified-dashboard.blade.php ENDPATH**/ ?>