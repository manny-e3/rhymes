<?php $__env->startSection('title', 'ERPREV Sync Monitoring | Rhymes Platform'); ?>

<?php $__env->startSection('page-title', 'ERPREV Sync Monitoring'); ?>

<?php $__env->startSection('page-description', 'Monitor synchronization operations with ERPREV system'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">ERPREV Sync Monitoring</h3>
                        <div class="nk-block-des text-soft">
                            <p>Monitor synchronization operations with ERPREV system</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="<?php echo e(route('admin.erprev.sales')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-swap"></em><span>Sales Data</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.inventory')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-package"></em><span>Inventory</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.products')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-grid-add"></em><span>Products</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.test-endpoints')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-play-fill"></em><span>Test Endpoints</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Total Sync Operations</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount"><?php echo e(number_format($summary['total'])); ?></span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Successful Syncs</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount text-success"><?php echo e(number_format($summary['successful'])); ?></span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-success" style="width: <?php echo e($summary['successful'] > 0 ? ($summary['successful'] / max($summary['total'], 1)) * 100 : 0); ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Failed Syncs</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount text-danger"><?php echo e(number_format($summary['failed'])); ?></span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-danger" style="width: <?php echo e($summary['failed'] > 0 ? ($summary['failed'] / max($summary['total'], 1)) * 100 : 0); ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Success Rate</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount"><?php echo e($summary['success_rate']); ?>%</span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-info" style="width: <?php echo e($summary['success_rate']); ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Sync Operations by Area</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-3 gx-1">
                                    <?php $__currentLoopData = $summary['by_area']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted"><?php echo e(ucfirst($area)); ?></span>
                                            <span><?php echo e($data->count); ?></span>
                                        </div>
                                        <div class="progress progress-md mt-1">
                                            <div class="progress-bar bg-primary" style="width: <?php echo e(($data->count / max($summary['total'], 1)) * 100); ?>%"></div>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Recent Sync Operations (24h)</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-3 gx-1">
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Successful</span>
                                            <span><?php echo e($summary['recent']->get('success', (object)['count' => 0])->count ?? 0); ?></span>
                                        </div>
                                        <div class="progress progress-md mt-1">
                                            <div class="progress-bar bg-success" style="width: <?php echo e(($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) > 0 ? (($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) / max(($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) + ($summary['recent']->get('error', (object)['count' => 0])->count ?? 0), 1)) * 100 : 0); ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Failed</span>
                                            <span><?php echo e($summary['recent']->get('error', (object)['count' => 0])->count ?? 0); ?></span>
                                        </div>
                                        <div class="progress progress-md mt-1">
                                            <div class="progress-bar bg-danger" style="width: <?php echo e(($summary['recent']->get('error', (object)['count' => 0])->count ?? 0) > 0 ? (($summary['recent']->get('error', (object)['count' => 0])->count ?? 0) / max(($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) + ($summary['recent']->get('error', (object)['count' => 0])->count ?? 0), 1)) * 100 : 0); ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Errors -->
            <?php if(count($recentErrors) > 0): ?>
            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-3">
                            <div class="card-title">
                                <h6 class="title">Recent Sync Errors</h6>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Area</th>
                                        <th>Message</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $recentErrors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><span class="badge badge-dot bg-danger"><?php echo e(ucfirst($error->area)); ?></span></td>
                                        <td><?php echo e(Str::limit($error->message, 100)); ?></td>
                                        <td><?php echo e($error->created_at->diffForHumans()); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Sync Logs -->
            <div class="nk-block">
                <div class="card card-bordered card-full">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-3">
                            <div class="card-title">
                                <h6 class="title">Sync Operation Logs</h6>
                            </div>
                        </div>

                        <!-- Filters -->
                        <form method="GET" action="<?php echo e(route('admin.erprev.monitoring')); ?>" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="area">Area</label>
                                        <select class="form-select" id="area" name="area">
                                            <option value="">All Areas</option>
                                            <option value="books" <?php echo e(request('area') == 'books' ? 'selected' : ''); ?>>Books</option>
                                            <option value="sales" <?php echo e(request('area') == 'sales' ? 'selected' : ''); ?>>Sales</option>
                                            <option value="inventory" <?php echo e(request('area') == 'inventory' ? 'selected' : ''); ?>>Inventory</option>
                                            <option value="products" <?php echo e(request('area') == 'products' ? 'selected' : ''); ?>>Products</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="status">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="">All Statuses</option>
                                            <option value="success" <?php echo e(request('status') == 'success' ? 'selected' : ''); ?>>Success</option>
                                            <option value="error" <?php echo e(request('status') == 'error' ? 'selected' : ''); ?>>Error</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label" for="date_from">From Date</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo e(request('date_from')); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label" for="date_to">To Date</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo e(request('date_to')); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary me-2"><em class="icon ni ni-search"></em><span>Filter</span></button>
                                            <a href="<?php echo e(route('admin.erprev.monitoring')); ?>" class="btn btn-light"><em class="icon ni ni-reload"></em><span>Reset</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <?php if(count($logs) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Area</th>
                                            <th>Status</th>
                                            <th>Message</th>
                                            <th>Timestamp</th>
                                            <th>Payload</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <span class="badge badge-dot bg-<?php echo e($log->area == 'books' ? 'primary' : ($log->area == 'sales' ? 'success' : ($log->area == 'inventory' ? 'warning' : 'info'))); ?>">
                                                        <?php echo e(ucfirst($log->area)); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if($log->status == 'success'): ?>
                                                        <span class="badge bg-success">Success</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Error</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo e(Str::limit($log->message, 80)); ?></td>
                                                <td><?php echo e($log->created_at->format('M d, Y H:i:s')); ?></td>
                                                <td>
                                                    <?php if($log->payload): ?>
                                                        <button class="btn btn-xs btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#payloadModal<?php echo e($log->id); ?>">
                                                            View Details
                                                        </button>
                                                        
                                                        <!-- Payload Modal -->
                                                        <div class="modal fade" id="payloadModal<?php echo e($log->id); ?>" tabindex="-1" aria-labelledby="payloadModalLabel<?php echo e($log->id); ?>" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="payloadModalLabel<?php echo e($log->id); ?>">Payload Details</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <pre><code><?php echo e(json_encode($log->payload, JSON_PRETTY_PRINT)); ?></code></pre>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted">No payload</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Showing <?php echo e($logs->firstItem()); ?> to <?php echo e($logs->lastItem()); ?> of <?php echo e($logs->total()); ?> entries
                                </div>
                                <div>
                                    <?php echo e($logs->appends(request()->except('page'))->links()); ?>

                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <em class="icon ni ni-file-text" style="font-size: 48px; opacity: 0.3;"></em>
                                <p class="mt-3">No sync logs found</p>
                                <?php if(empty(request()->except('page'))): ?>
                                    <p class="text-muted">There are no sync operations recorded yet</p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>@extends('layouts.admin')

<?php $__env->startSection('title', 'ERPREV Sync Monitoring | Rhymes Platform'); ?>

<?php $__env->startSection('page-title', 'ERPREV Sync Monitoring'); ?>

<?php $__env->startSection('page-description', 'Monitor synchronization operations with ERPREV system'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">ERPREV Sync Monitoring</h3>
                        <div class="nk-block-des text-soft">
                            <p>Monitor synchronization operations with ERPREV system</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="<?php echo e(route('admin.erprev.sales')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-swap"></em><span>Sales Data</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.inventory')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-package"></em><span>Inventory</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.products')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-grid-add"></em><span>Products</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.test-endpoints')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-play-fill"></em><span>Test Endpoints</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Total Sync Operations</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount"><?php echo e(number_format($summary['total'])); ?></span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Successful Syncs</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount text-success"><?php echo e(number_format($summary['successful'])); ?></span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-success" style="width: <?php echo e($summary['successful'] > 0 ? ($summary['successful'] / max($summary['total'], 1)) * 100 : 0); ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Failed Syncs</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount text-danger"><?php echo e(number_format($summary['failed'])); ?></span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-danger" style="width: <?php echo e($summary['failed'] > 0 ? ($summary['failed'] / max($summary['total'], 1)) * 100 : 0); ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Success Rate</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount"><?php echo e($summary['success_rate']); ?>%</span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-info" style="width: <?php echo e($summary['success_rate']); ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Sync Operations by Area</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-3 gx-1">
                                    <?php $__currentLoopData = $summary['by_area']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted"><?php echo e(ucfirst($area)); ?></span>
                                            <span><?php echo e($data->count); ?></span>
                                        </div>
                                        <div class="progress progress-md mt-1">
                                            <div class="progress-bar bg-primary" style="width: <?php echo e(($data->count / max($summary['total'], 1)) * 100); ?>%"></div>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Recent Sync Operations (24h)</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-3 gx-1">
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Successful</span>
                                            <span><?php echo e($summary['recent']->get('success', (object)['count' => 0])->count ?? 0); ?></span>
                                        </div>
                                        <div class="progress progress-md mt-1">
                                            <div class="progress-bar bg-success" style="width: <?php echo e(($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) > 0 ? (($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) / max(($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) + ($summary['recent']->get('error', (object)['count' => 0])->count ?? 0), 1)) * 100 : 0); ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Failed</span>
                                            <span><?php echo e($summary['recent']->get('error', (object)['count' => 0])->count ?? 0); ?></span>
                                        </div>
                                        <div class="progress progress-md mt-1">
                                            <div class="progress-bar bg-danger" style="width: <?php echo e(($summary['recent']->get('error', (object)['count' => 0])->count ?? 0) > 0 ? (($summary['recent']->get('error', (object)['count' => 0])->count ?? 0) / max(($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) + ($summary['recent']->get('error', (object)['count' => 0])->count ?? 0), 1)) * 100 : 0); ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Errors -->
            <?php if(count($recentErrors) > 0): ?>
            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-3">
                            <div class="card-title">
                                <h6 class="title">Recent Sync Errors</h6>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Area</th>
                                        <th>Message</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $recentErrors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><span class="badge badge-dot bg-danger"><?php echo e(ucfirst($error->area)); ?></span></td>
                                        <td><?php echo e(Str::limit($error->message, 100)); ?></td>
                                        <td><?php echo e($error->created_at->diffForHumans()); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Sync Logs -->
            <div class="nk-block">
                <div class="card card-bordered card-full">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-3">
                            <div class="card-title">
                                <h6 class="title">Sync Operation Logs</h6>
                            </div>
                        </div>

                        <!-- Filters -->
                        <form method="GET" action="<?php echo e(route('admin.erprev.monitoring')); ?>" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="area">Area</label>
                                        <select class="form-select" id="area" name="area">
                                            <option value="">All Areas</option>
                                            <option value="books" <?php echo e(request('area') == 'books' ? 'selected' : ''); ?>>Books</option>
                                            <option value="sales" <?php echo e(request('area') == 'sales' ? 'selected' : ''); ?>>Sales</option>
                                            <option value="inventory" <?php echo e(request('area') == 'inventory' ? 'selected' : ''); ?>>Inventory</option>
                                            <option value="products" <?php echo e(request('area') == 'products' ? 'selected' : ''); ?>>Products</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="status">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="">All Statuses</option>
                                            <option value="success" <?php echo e(request('status') == 'success' ? 'selected' : ''); ?>>Success</option>
                                            <option value="error" <?php echo e(request('status') == 'error' ? 'selected' : ''); ?>>Error</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label" for="date_from">From Date</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo e(request('date_from')); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label" for="date_to">To Date</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo e(request('date_to')); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary me-2"><em class="icon ni ni-search"></em><span>Filter</span></button>
                                            <a href="<?php echo e(route('admin.erprev.monitoring')); ?>" class="btn btn-light"><em class="icon ni ni-reload"></em><span>Reset</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <?php if(count($logs) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Area</th>
                                            <th>Status</th>
                                            <th>Message</th>
                                            <th>Timestamp</th>
                                            <th>Payload</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <span class="badge badge-dot bg-<?php echo e($log->area == 'books' ? 'primary' : ($log->area == 'sales' ? 'success' : ($log->area == 'inventory' ? 'warning' : 'info'))); ?>">
                                                        <?php echo e(ucfirst($log->area)); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if($log->status == 'success'): ?>
                                                        <span class="badge bg-success">Success</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Error</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo e(Str::limit($log->message, 80)); ?></td>
                                                <td><?php echo e($log->created_at->format('M d, Y H:i:s')); ?></td>
                                                <td>
                                                    <?php if($log->payload): ?>
                                                        <button class="btn btn-xs btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#payloadModal<?php echo e($log->id); ?>">
                                                            View Details
                                                        </button>
                                                        
                                                        <!-- Payload Modal -->
                                                        <div class="modal fade" id="payloadModal<?php echo e($log->id); ?>" tabindex="-1" aria-labelledby="payloadModalLabel<?php echo e($log->id); ?>" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="payloadModalLabel<?php echo e($log->id); ?>">Payload Details</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <pre><code><?php echo e(json_encode($log->payload, JSON_PRETTY_PRINT)); ?></code></pre>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted">No payload</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Showing <?php echo e($logs->firstItem()); ?> to <?php echo e($logs->lastItem()); ?> of <?php echo e($logs->total()); ?> entries
                                </div>
                                <div>
                                    <?php echo e($logs->appends(request()->except('page'))->links()); ?>

                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <em class="icon ni ni-file-text" style="font-size: 48px; opacity: 0.3;"></em>
                                <p class="mt-3">No sync logs found</p>
                                <?php if(empty(request()->except('page'))): ?>
                                    <p class="text-muted">There are no sync operations recorded yet</p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/erprev/monitoring.blade.php ENDPATH**/ ?>