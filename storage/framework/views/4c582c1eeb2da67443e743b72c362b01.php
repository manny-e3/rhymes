

<?php $__env->startSection('title', 'ERPREV Product Listings | Rhymes Platform'); ?>

<?php $__env->startSection('page-title', 'ERPREV Product Listings'); ?>

<?php $__env->startSection('page-description', 'Product catalog from ERPREV system'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">ERPREV Product Listings</h3>
                        <div class="nk-block-des text-soft">
                            <p>Product catalog synchronized from ERPREV system</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="<?php echo e(route('admin.erprev.sales')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-tranx"></em><span>Sales</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.inventory')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-package"></em><span>Inventory</span></a></li>
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
                                    <h6 class="nk-block-title">Filter Product Listings</h6>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-inner mb-3">
                            <form method="GET" action="<?php echo e(route('admin.erprev.products')); ?>" class="row g-3 align-items-end">
                                <div class="col-md-8">
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
                                            <?php if(request('name')): ?>
                                                <a href="<?php echo e(route('admin.erprev.products')); ?>" class="btn btn-secondary"><em class="icon ni ni-reload"></em><span>Clear Filter</span></a>
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
                                            <th>Name</th>
                                            <th>Barcode</th>
                                            <th>Category</th>
                                          
                                            <th>Units In Stock</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $paginator; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($product['SN'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <strong><?php echo e($product['Name'] ?? 'N/A'); ?></strong>
                                                </td>
                                                <td><?php echo e($product['Barcode'] ?? 'N/A'); ?></td>
                                                <td><?php echo e($product['Category'] ?? 'N/A'); ?></td>
                                               
                                                
                                                <td><?php echo e(number_format((float)($product['UnitsInStock'] ?? 0))); ?></td>
                                              
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
                                           'name' => request('name')
                                        ])->links('vendor.pagination.bootstrap-4')); ?>

                                    </div>
                                <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <em class="icon ni ni-grid-add" style="font-size: 48px; opacity: 0.3;"></em>
                                <p class="mt-3">No products found</p>
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/erprev/products.blade.php ENDPATH**/ ?>