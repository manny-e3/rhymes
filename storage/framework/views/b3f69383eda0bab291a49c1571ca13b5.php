<?php $__env->startSection('title', 'Sales Dashboard | Admin Panel'); ?>

<?php $__env->startSection('page-title', 'Sales Dashboard'); ?>

<?php $__env->startSection('page-description', 'Real-time sales analytics and performance metrics'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Sales Dashboard</h3>
                        <div class="nk-block-des text-soft">
                            <p>Real-time sales analytics and performance metrics.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="#" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-calendar"></em><span>Last 30 Days</span></a></li>
                                    <li><a href="<?php echo e(route('admin.reports.sales')); ?>" class="btn btn-primary"><em class="icon ni ni-reports"></em><span>Detailed Reports</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Metrics Component -->
            <div class="nk-block">
                <?php if (isset($component)) { $__componentOriginal8d7141744ec2ab264a87f6d816f5a8a0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d7141744ec2ab264a87f6d816f5a8a0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sales-metrics','data' => ['title1' => 'Total Revenue','value1' => '₦'.e(number_format($totalRevenue, 2)).'','change1' => ''.e(number_format($revenueChange, 1)).'','chartId1' => 'revenueMiniChart','title2' => 'Total Sales','value2' => ''.e(number_format($totalSales)).'','change2' => ''.e(number_format($salesChange, 1)).'','chartId2' => 'salesMiniChart','title3' => 'Avg. Order Value','value3' => '₦'.e(number_format($avgOrderValue, 2)).'','change3' => ''.e(number_format($aovChange, 1)).'','chartId3' => 'aovMiniChart','title4' => 'Conversion Rate','value4' => ''.e(number_format(rand(3, 6), 1)).'%','change4' => ''.e(number_format(rand(0, 2), 1)).'','chartId4' => 'conversionMiniChart','miniCharts' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sales-metrics'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title1' => 'Total Revenue','value1' => '₦'.e(number_format($totalRevenue, 2)).'','change1' => ''.e(number_format($revenueChange, 1)).'','chartId1' => 'revenueMiniChart','title2' => 'Total Sales','value2' => ''.e(number_format($totalSales)).'','change2' => ''.e(number_format($salesChange, 1)).'','chartId2' => 'salesMiniChart','title3' => 'Avg. Order Value','value3' => '₦'.e(number_format($avgOrderValue, 2)).'','change3' => ''.e(number_format($aovChange, 1)).'','chartId3' => 'aovMiniChart','title4' => 'Conversion Rate','value4' => ''.e(number_format(rand(3, 6), 1)).'%','change4' => ''.e(number_format(rand(0, 2), 1)).'','chartId4' => 'conversionMiniChart','mini-charts' => 'true']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d7141744ec2ab264a87f6d816f5a8a0)): ?>
<?php $attributes = $__attributesOriginal8d7141744ec2ab264a87f6d816f5a8a0; ?>
<?php unset($__attributesOriginal8d7141744ec2ab264a87f6d816f5a8a0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d7141744ec2ab264a87f6d816f5a8a0)): ?>
<?php $component = $__componentOriginal8d7141744ec2ab264a87f6d816f5a8a0; ?>
<?php unset($__componentOriginal8d7141744ec2ab264a87f6d816f5a8a0); ?>
<?php endif; ?>
            </div>

            <!-- Chart Components -->
            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-xxl-6">
                        <!-- Revenue Chart -->
                        <?php if (isset($component)) { $__componentOriginal24213bb977865092d0b190b66c737a9b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal24213bb977865092d0b190b66c737a9b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chart-container','data' => ['id' => 'revenueChart','title' => 'Revenue Trend','subtitle' => 'Daily revenue over the last 30 days','height' => '350px','type' => 'line','labels' => $chartData['labels'],'datasets' => [[
                                'label' => 'Revenue',
                                'data' => $chartData['revenue'],
                                'borderColor' => '#559bfb',
                                'backgroundColor' => 'rgba(85, 155, 251, 0.1)',
                                'borderWidth' => 2,
                                'fill' => true,
                                'tension' => 0.4
                            ]],'formatCurrency' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chart-container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'revenueChart','title' => 'Revenue Trend','subtitle' => 'Daily revenue over the last 30 days','height' => '350px','type' => 'line','labels' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($chartData['labels']),'datasets' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([[
                                'label' => 'Revenue',
                                'data' => $chartData['revenue'],
                                'borderColor' => '#559bfb',
                                'backgroundColor' => 'rgba(85, 155, 251, 0.1)',
                                'borderWidth' => 2,
                                'fill' => true,
                                'tension' => 0.4
                            ]]),'format-currency' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal24213bb977865092d0b190b66c737a9b)): ?>
<?php $attributes = $__attributesOriginal24213bb977865092d0b190b66c737a9b; ?>
<?php unset($__attributesOriginal24213bb977865092d0b190b66c737a9b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal24213bb977865092d0b190b66c737a9b)): ?>
<?php $component = $__componentOriginal24213bb977865092d0b190b66c737a9b; ?>
<?php unset($__componentOriginal24213bb977865092d0b190b66c737a9b); ?>
<?php endif; ?>
                    </div>
                    
                    <div class="col-xxl-6">
                        <!-- Sales by Category Chart -->
                        <?php if (isset($component)) { $__componentOriginal24213bb977865092d0b190b66c737a9b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal24213bb977865092d0b190b66c737a9b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chart-container','data' => ['id' => 'categoryChart','title' => 'Sales by Category','subtitle' => 'Distribution of sales across categories','height' => '350px','type' => 'doughnut','labels' => $genreData->pluck('genre')->toArray(),'datasets' => [[
                                'label' => 'Sales',
                                'data' => $genreData->pluck('revenue')->toArray(),
                                'backgroundColor' => [
                                    'rgba(85, 155, 251, 0.7)',
                                    'rgba(30, 224, 172, 0.7)',
                                    'rgba(244, 189, 14, 0.7)',
                                    'rgba(133, 79, 255, 0.7)',
                                    'rgba(224, 30, 126, 0.7)'
                                ],
                                'borderColor' => [
                                    'rgba(85, 155, 251, 1)',
                                    'rgba(30, 224, 172, 1)',
                                    'rgba(244, 189, 14, 1)',
                                    'rgba(133, 79, 255, 1)',
                                    'rgba(224, 30, 126, 1)'
                                ],
                                'borderWidth' => 1
                            ]]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chart-container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'categoryChart','title' => 'Sales by Category','subtitle' => 'Distribution of sales across categories','height' => '350px','type' => 'doughnut','labels' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($genreData->pluck('genre')->toArray()),'datasets' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([[
                                'label' => 'Sales',
                                'data' => $genreData->pluck('revenue')->toArray(),
                                'backgroundColor' => [
                                    'rgba(85, 155, 251, 0.7)',
                                    'rgba(30, 224, 172, 0.7)',
                                    'rgba(244, 189, 14, 0.7)',
                                    'rgba(133, 79, 255, 0.7)',
                                    'rgba(224, 30, 126, 0.7)'
                                ],
                                'borderColor' => [
                                    'rgba(85, 155, 251, 1)',
                                    'rgba(30, 224, 172, 1)',
                                    'rgba(244, 189, 14, 1)',
                                    'rgba(133, 79, 255, 1)',
                                    'rgba(224, 30, 126, 1)'
                                ],
                                'borderWidth' => 1
                            ]])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal24213bb977865092d0b190b66c737a9b)): ?>
<?php $attributes = $__attributesOriginal24213bb977865092d0b190b66c737a9b; ?>
<?php unset($__attributesOriginal24213bb977865092d0b190b66c737a9b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal24213bb977865092d0b190b66c737a9b)): ?>
<?php $component = $__componentOriginal24213bb977865092d0b190b66c737a9b; ?>
<?php unset($__componentOriginal24213bb977865092d0b190b66c737a9b); ?>
<?php endif; ?>
                    </div>
                    
                    <div class="col-xxl-12">
                        <!-- Performance Chart -->
                        <?php if (isset($component)) { $__componentOriginal24213bb977865092d0b190b66c737a9b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal24213bb977865092d0b190b66c737a9b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chart-container','data' => ['id' => 'performanceChart','title' => 'Sales Performance','subtitle' => 'Monthly sales performance comparison','height' => '400px','type' => 'bar','labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],'datasets' => [[
                                'label' => '2024',
                                'data' => [12000, 19000, 15000, 18000, 22000, 25000],
                                'backgroundColor' => 'rgba(85, 155, 251, 0.7)',
                                'borderColor' => 'rgba(85, 155, 251, 1)',
                                'borderWidth' => 1
                            ], [
                                'label' => '2023',
                                'data' => [10000, 15000, 12000, 16000, 18000, 20000],
                                'backgroundColor' => 'rgba(30, 224, 172, 0.7)',
                                'borderColor' => 'rgba(30, 224, 172, 1)',
                                'borderWidth' => 1
                            ]],'formatCurrency' => true,'options' => [
                                'plugins' => [
                                    'tooltip' => [
                                        'mode' => 'index',
                                        'intersect' => false
                                    ]
                                ]
                            ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chart-container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'performanceChart','title' => 'Sales Performance','subtitle' => 'Monthly sales performance comparison','height' => '400px','type' => 'bar','labels' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']),'datasets' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([[
                                'label' => '2024',
                                'data' => [12000, 19000, 15000, 18000, 22000, 25000],
                                'backgroundColor' => 'rgba(85, 155, 251, 0.7)',
                                'borderColor' => 'rgba(85, 155, 251, 1)',
                                'borderWidth' => 1
                            ], [
                                'label' => '2023',
                                'data' => [10000, 15000, 12000, 16000, 18000, 20000],
                                'backgroundColor' => 'rgba(30, 224, 172, 0.7)',
                                'borderColor' => 'rgba(30, 224, 172, 1)',
                                'borderWidth' => 1
                            ]]),'format-currency' => true,'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                                'plugins' => [
                                    'tooltip' => [
                                        'mode' => 'index',
                                        'intersect' => false
                                    ]
                                ]
                            ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal24213bb977865092d0b190b66c737a9b)): ?>
<?php $attributes = $__attributesOriginal24213bb977865092d0b190b66c737a9b; ?>
<?php unset($__attributesOriginal24213bb977865092d0b190b66c737a9b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal24213bb977865092d0b190b66c737a9b)): ?>
<?php $component = $__componentOriginal24213bb977865092d0b190b66c737a9b; ?>
<?php unset($__componentOriginal24213bb977865092d0b190b66c737a9b); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Top Selling Books -->
            <div class="nk-block">
                <div class="card card-bordered card-preview">
                    <div class="card-inner">
                        <div class="card-title-group">
                            <div class="card-title">
                                <h6 class="title">Top Selling Books</h6>
                            </div>
                        </div>
                        <div class="nk-tb-list nk-tb-ulist">
                            <div class="nk-tb-item nk-tb-head">
                                <div class="nk-tb-col"><span>Book Title</span></div>
                                <div class="nk-tb-col tb-col-md"><span>Sales</span></div>
                                <div class="nk-tb-col"><span>Revenue</span></div>
                            </div>
                            <?php $__currentLoopData = $topBooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    <span class="tb-lead"><?php echo e($book->title); ?></span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span><?php echo e($book->sales_count); ?></span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-lead">₦<?php echo e(number_format($book->total_revenue, 2)); ?></span>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<!-- Include any additional scripts specific to this page here -->
<?php $__env->stopPush(); ?>

<?php $__env->startSection('scripts'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/rovinghe/author.rovingheights.com/resources/views/admin/reports/sales-dashboard.blade.php ENDPATH**/ ?>