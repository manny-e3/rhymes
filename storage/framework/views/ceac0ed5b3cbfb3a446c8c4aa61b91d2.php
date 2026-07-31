<?php $__env->startSection('title', 'ERPREV Sales Summary | Rhymes Platform'); ?>

<?php $__env->startSection('page-title', 'ERPREV Sales Summary'); ?>

<?php $__env->startSection('page-description', 'Sales summary from ERPREV system'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">ERPREV Sales Summary</h3>
                        <div class="nk-block-des text-soft">
                            <p>Sales summary synchronized from ERPREV system</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="<?php echo e(route('admin.erprev.sales')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-tranx"></em><span>Sales</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.inventory')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-package"></em><span>Inventory</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.products')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-grid-add"></em><span>Products</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="card card-bordered card-full">
                    <div class="card-inner">
                        <?php if($paginator->count() > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Barcode</th>
                                            <th>Category</th>
                                            <th>Units Sold</th>
                                            <th>Price</th>
                                            <th>Total Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $paginator; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($item['SN'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <strong><?php echo e($item['Name'] ?? 'N/A'); ?></strong>
                                                </td>
                                                <td><?php echo e($item['Barcode'] ?? 'N/A'); ?></td>
                                                <td><?php echo e($item['Category'] ?? 'N/A'); ?></td>
                                                <td><?php echo e(number_format((float)($item['UnitsInStock'] ?? 0))); ?></td>
                                                <td><?php echo $item['CurrencySymbol'] ?? '&#x20A6;'; ?><?php echo e(number_format((float)($item['SellingPrice'] ?? 0), 2)); ?></td>
                                                <td><?php echo $item['CurrencySymbol'] ?? '&#x20A6;'; ?><?php echo e(number_format(((float)($item['SellingPrice'] ?? 0)) * ((float)($item['UnitsInStock'] ?? 0)), 2)); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination Links -->
                            <div class="card-inner">
                                <div class="nk-block-between-md g-3">
                                    <div class="g">
                                        <?php echo e($paginator->links()); ?>

                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <em class="icon ni ni-bar-chart" style="font-size: 48px; opacity: 0.3;"></em>
                                <p class="mt-3">No sales summary data found</p>
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/rovinghe/author.rovingheights.com/resources/views/admin/erprev/summary.blade.php ENDPATH**/ ?>