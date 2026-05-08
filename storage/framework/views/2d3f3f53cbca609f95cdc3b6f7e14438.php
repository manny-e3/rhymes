<?php $__env->startSection('title', 'Revenue | Admin Panel'); ?>

<?php $__env->startSection('page-title', 'Revenue'); ?>

<?php $__env->startSection('page-description', 'Comprehensive sales analytics and reporting'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Revenue</h3>
                        <div class="nk-block-des text-soft">
                            <p>Comprehensive sales analytics, revenue tracking, and performance metrics.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li>
                                        <form method="GET" action="<?php echo e(route('admin.revenue.index')); ?>" class="d-flex g-2 align-items-center" id="filterForm">
                                            <div class="form-control-wrap">
                                                <select name="period" class="form-select form-select-sm" onchange="toggleCustomDates(this.value)">
                                                    <option value="today" <?php echo e(request('period') === 'today' ? 'selected' : ''); ?>>Today</option>
                                                    <option value="yesterday" <?php echo e(request('period') === 'yesterday' ? 'selected' : ''); ?>>Yesterday</option>
                                                    <option value="last_7_days" <?php echo e(request('period') === 'last_7_days' ? 'selected' : ''); ?>>Last 7 Days</option>
                                                    <option value="last_30_days" <?php echo e(request('period', 'last_30_days') === 'last_30_days' ? 'selected' : ''); ?>>Last 30 Days</option>
                                                    <option value="this_month" <?php echo e(request('period') === 'this_month' ? 'selected' : ''); ?>>This Month</option>
                                                    <option value="last_month" <?php echo e(request('period') === 'last_month' ? 'selected' : ''); ?>>Last Month</option>
                                                    <option value="this_year" <?php echo e(request('period') === 'this_year' ? 'selected' : ''); ?>>This Year</option>
                                                    <option value="custom" <?php echo e(request('period') === 'custom' ? 'selected' : ''); ?>>Custom</option>
                                                </select>
                                            </div>
                                            <div id="custom-date-inputs" class="d-flex g-2" style="display: <?php echo e(request('period') === 'custom' ? 'flex' : 'none'); ?>;">
                                                <div class="form-control-wrap">
                                                    <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo e(request('start_date')); ?>">
                                                </div>
                                                <div class="form-control-wrap">
                                                    <input type="date" name="end_date" class="form-control form-control-sm" value="<?php echo e(request('end_date')); ?>">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

         

            <!-- Key Metrics -->
            <div class="nk-block">
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-4">
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
                                    <span class="amount">₦<?php echo e(number_format($metrics['total_revenue'], 2)); ?></span>
                                    <?php if($metrics['revenue_change'] > 0): ?>
                                        <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em><?php echo e(number_format($metrics['revenue_change'], 1)); ?>%</span>
                                    <?php elseif($metrics['revenue_change'] < 0): ?>
                                        <span class="sub-title text-danger"><em class="icon ni ni-arrow-long-down"></em><?php echo e(number_format(abs($metrics['revenue_change']), 1)); ?>%</span>
                                    <?php else: ?>
                                        <span class="sub-title">No change</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Sales</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-cart text-primary"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount"><?php echo e(number_format($metrics['total_sales'])); ?></span>
                                    <?php if($metrics['sales_change'] > 0): ?>
                                        <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em><?php echo e(number_format($metrics['sales_change'], 1)); ?>%</span>
                                    <?php elseif($metrics['sales_change'] < 0): ?>
                                        <span class="sub-title text-danger"><em class="icon ni ni-arrow-long-down"></em><?php echo e(number_format(abs($metrics['sales_change']), 1)); ?>%</span>
                                    <?php else: ?>
                                        <span class="sub-title">No change</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Average Order Value</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-bar-chart text-info"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦<?php echo e(number_format($metrics['avg_order_value'], 2)); ?></span>
                                    <?php if($metrics['aov_change'] > 0): ?>
                                        <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em><?php echo e(number_format($metrics['aov_change'], 1)); ?>%</span>
                                    <?php elseif($metrics['aov_change'] < 0): ?>
                                        <span class="sub-title text-danger"><em class="icon ni ni-arrow-long-down"></em><?php echo e(number_format(abs($metrics['aov_change']), 1)); ?>%</span>
                                    <?php else: ?>
                                        <span class="sub-title">No change</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Platform Commission</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-growth text-warning"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦<?php echo e(number_format($metrics['platform_commission'], 2)); ?></span>
                                    <span class="sub-title"><?php echo e(number_format($metrics['commission_rate'], 1)); ?>% rate</span>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>

            <!-- Sales Overview Component -->
            <div class="nk-block">
                <?php if (isset($component)) { $__componentOriginal90f522c37782077ae5d1883c8a1806c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal90f522c37782077ae5d1883c8a1806c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sales-overview','data' => ['revenueData' => $overviewData['revenueData'],'metricsData' => $overviewData['metricsData'],'performanceData' => $overviewData['performanceData']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sales-overview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['revenue-data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewData['revenueData']),'metrics-data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewData['metricsData']),'performance-data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewData['performanceData'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal90f522c37782077ae5d1883c8a1806c0)): ?>
<?php $attributes = $__attributesOriginal90f522c37782077ae5d1883c8a1806c0; ?>
<?php unset($__attributesOriginal90f522c37782077ae5d1883c8a1806c0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal90f522c37782077ae5d1883c8a1806c0)): ?>
<?php $component = $__componentOriginal90f522c37782077ae5d1883c8a1806c0; ?>
<?php unset($__componentOriginal90f522c37782077ae5d1883c8a1806c0); ?>
<?php endif; ?>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- Revenue Chart -->
                    <!-- <div class="col-xxl-8">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Revenue Trend</h6>
                                        <p>Daily revenue over the selected period</p>
                                    </div>
                                    <div class="card-tools">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">Chart Type</a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="#" onclick="changeChartType('line')"><span>Line Chart</span></a></li>
                                                    <li><a href="#" onclick="changeChartType('bar')"><span>Bar Chart</span></a></li>
                                                    <li><a href="#" onclick="changeChartType('area')"><span>Area Chart</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="nk-chart-canvas">
                                    <canvas id="revenueChart" height="400"></canvas>
                                </div>
                            </div>
                        </div>
                    </div> -->

                   
                </div>
            </div>

            <!-- Detailed Sales Table -->
            <div class="nk-block">
                <div class="card card-bordered card-stretch">
                    <div class="card-inner-group">
                        <div class="card-inner position-relative card-tools-toggle">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Sales Transactions</h6>
                                </div>
                                <div class="card-tools">
                                    <ul class="nk-block-tools g-3">
                                        <li>
                                            <div class="form-control-wrap">
                                                <div class="form-icon form-icon-right">
                                                    <em class="icon ni ni-search"></em>
                                                </div>
                                                <input type="text" class="form-control form-control-sm" id="tableSearch" value="<?php echo e(request('search')); ?>" placeholder="Search transactions..." onkeyup="if(event.keyCode == 13) searchTransactions()">
                                            </div>
                                        </li>
                                        
                                        <li class="nk-block-tools-opt">
                                            <a href="<?php echo e(route('admin.revenue.index')); ?>" class="btn btn-icon btn-primary d-md-none"><em class="icon ni ni-reload"></em></a>
                                            <a href="<?php echo e(route('admin.revenue.index')); ?>" class="btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-reload"></em><span>Reset</span></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card-inner p-0">
                            <div class="nk-tb-list nk-tb-ulist">
                                <div class="nk-tb-item nk-tb-head">
                                    <div class="nk-tb-col"><span class="sub-text">Transaction</span></div>
                                    <div class="nk-tb-col tb-col-mb"><span class="sub-text">Book</span></div>
                                    <div class="nk-tb-col tb-col-md"><span class="sub-text">Author</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Amount</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Date</span></div>
                                </div>

                                <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <span class="tb-lead">#<?php echo e($transaction->id); ?></span>
                                            <span class="tb-sub"><?php echo e($transaction->type); ?></span>
                                        </div>
                                        <div class="nk-tb-col tb-col-mb">
                                            <span class="tb-lead"><?php echo e($transaction->book->title ?? 'N/A'); ?></span>
                                            <span class="tb-sub"><?php echo e($transaction->book->genre ?? ''); ?></span>
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
                                            <span class="tb-lead"><?php echo e($transaction->user->name); ?></span>
                                            <span class="tb-sub"><?php echo e($transaction->user->email); ?></span>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span class="tb-lead text-success">₦<?php echo e(number_format($transaction->amount, 2)); ?></span>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span><?php echo e($transaction->created_at->format('M d, Y')); ?></span>
                                            <span class="tb-sub"><?php echo e($transaction->created_at->format('g:i A')); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <div class="text-center py-4">
                                                <em class="icon ni ni-tranx" style="font-size: 3rem; opacity: 0.3;"></em>
                                                <p class="text-soft mt-2">No transactions found</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if($transactions->hasPages()): ?>
                            <div class="card-inner">
                                <?php echo e($transactions->appends(request()->query())->links()); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
let revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chartData['labels'], 15, 512) ?>,
        datasets: [{
            label: 'Revenue',
            data: <?php echo json_encode($chartData['revenue'], 15, 512) ?>,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Revenue: $' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

function toggleCustomDates(period) {
    const customInputs = document.getElementById('custom-date-inputs');
    if (period === 'custom') {
        customInputs.style.display = 'flex';
    } else {
        customInputs.style.display = 'none';
        document.getElementById('filterForm').submit();
    }
}

function changeChartType(type) {
    revenueChart.config.type = type;
    revenueChart.update();
}

function searchTransactions() {
    const search = document.getElementById('tableSearch').value;
    const url = new URL(window.location);
    if (search) {
        url.searchParams.set('search', search);
    } else {
        url.searchParams.delete('search');
    }
    url.searchParams.set('page', 1); // Reset to first page
    window.location.href = url.toString();
}

function filterByBook(bookId) {
    const url = new URL(window.location);
    if (bookId) {
        url.searchParams.set('book_id', bookId);
    } else {
        url.searchParams.delete('book_id');
    }
    url.searchParams.set('page', 1); // Reset to first page
    window.location.href = url.toString();
}

function exportReport(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);
    
    Swal.fire({
        title: 'Generating Report...',
        text: 'Please wait while we prepare your report.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(`<?php echo e(route('admin.reports.sales')); ?>?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = `sales-report-${new Date().toISOString().split('T')[0]}.${format}`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        Swal.close();
    })
    .catch(error => {
        Swal.fire('Error!', 'Failed to generate report.', 'error');
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/reports/sales.blade.php ENDPATH**/ ?>