<?php $__env->startSection('title', 'ERPREV Sales Data | Rhymes Platform'); ?>

<?php $__env->startSection('page-title', 'ERPREV Sales Data'); ?>

<?php $__env->startSection('page-description', 'Sales transactions from ERPREV system'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">ERPREV Sales Data</h3>
                        <div class="nk-block-des text-soft">
                            <p>Sales transactions synchronized from ERPREV system</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="<?php echo e(route('admin.erprev.inventory')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-package"></em><span>Inventory</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.products')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-grid-add"></em><span>Products</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.summary')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-bar-chart"></em><span>Summary</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="card card-bordered card-full">
                    <div class="card-inner">
                        <!-- Filter Section -->
                        <div class="nk-block-head nk-block-head-sm">
                            <div class="nk-block-between">
                                <div class="nk-block-head-content">
                                    <h6 class="nk-block-title">Filter Sales Data</h6>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-inner mb-3">
                            <form method="GET" action="<?php echo e(route('admin.erprev.sales')); ?>" class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="lastupdated">Last Updated</label>
                                        <div class="form-control-wrap">
                                            <select name="lastupdated" id="lastupdated" class="form-select">
                                                <option value="" <?php echo e(request('lastupdated') == '' ? 'selected' : ''); ?>>All Time</option>
                                                <option value="all" <?php echo e(request('lastupdated') == 'all' ? 'selected' : ''); ?>>All Records</option>
                                                <option value="5m" <?php echo e(request('lastupdated') == '5m' ? 'selected' : ''); ?>>Last 5 Minutes</option>
                                                <option value="10m" <?php echo e(request('lastupdated') == '10m' ? 'selected' : ''); ?>>Last 10 Minutes</option>
                                                <option value="30m" <?php echo e(request('lastupdated') == '30m' ? 'selected' : ''); ?>>Last 30 Minutes</option>
                                                <option value="1h" <?php echo e(request('lastupdated') == '1h' ? 'selected' : ''); ?>>Last 1 Hour</option>
                                                <option value="4h" <?php echo e(request('lastupdated') == '4h' ? 'selected' : ''); ?>>Last 4 Hours</option>
                                                <option value="6h" <?php echo e(request('lastupdated') == '6h' ? 'selected' : ''); ?>>Last 6 Hours</option>
                                                <option value="24h" <?php echo e(request('lastupdated') == '24h' ? 'selected' : ''); ?>>Last 24 Hours</option>
                                                <option value="7d" <?php echo e(request('lastupdated') == '7d' ? 'selected' : ''); ?>>Last 7 Days</option>
                                                <option value="30d" <?php echo e(request('lastupdated') == '30d' ? 'selected' : ''); ?>>Last 30 Days</option>
                                                <option value="60d" <?php echo e(request('lastupdated') == '60d' ? 'selected' : ''); ?>>Last 60 Days</option>
                                                <option value="100d" <?php echo e(request('lastupdated') == '100d' ? 'selected' : ''); ?>>Last 100 Days</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="name">Product Name</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter product name" value="<?php echo e(request('name')); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <button type="submit" class="btn btn-primary me-2"><em class="icon ni ni-filter-alt"></em><span>Apply Filter</span></button>
                                            <?php if(request('lastupdated') || request('name')): ?>
                                                <a href="<?php echo e(route('admin.erprev.sales')); ?>" class="btn btn-secondary"><em class="icon ni ni-reload"></em><span>Clear Filter</span></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <?php if($paginator->count() > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Barcode</th>
                                            <th>Name</th>
                                           
                                            <th>Category</th>
                                            <th>Warehouse</th>
                                            <th>Units</th>
                                            <th>Price</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $paginator; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($sale['SN'] ?? 'N/A'); ?></td>
                                                <td><?php echo e($sale['Barcode'] ?? 'N/A'); ?></td>

                                                <td>
                                                    <strong><?php echo e($sale['Name'] ?? 'N/A'); ?></strong>
                                                </td>
                                                <td><?php echo e($sale['Category'] ?? 'N/A'); ?></td>
                                                <td><?php echo e($sale['WareHouse'] ?? 'N/A'); ?></td>
                                                <td><?php echo e(number_format((float)($sale['UnitsInStock'] ?? 0))); ?></td>
                                                <td><?php echo $sale['CurrencySymbol'] ?? '&#x20A6;'; ?><?php echo e(number_format((float)($sale['SellingPrice'] ?? 0), 2)); ?></td>
                                               
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination Links -->
                            <div class="card-inner">
                                <div class="nk-block-between-md g-3">
                                    <div class="g">
                                        <?php if($paginator->hasPages()): ?>
                                    <div>
                                        <?php echo e($paginator->appends([
                                           'lastupdated' => request('lastupdated'),
                                           'name' => request('name')
                                        ])->links('vendor.pagination.bootstrap-4')); ?>

                                    </div>
                                <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <em class="icon ni ni-file-text" style="font-size: 48px; opacity: 0.3;"></em>
                                <p class="mt-3">No sales data found</p>
                                <p class="text-muted">Try adjusting your filters or check the ERPREV connection</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/erprev/sales.blade.php ENDPATH**/ ?>