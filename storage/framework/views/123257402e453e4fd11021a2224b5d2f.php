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
                                    <!-- <li>
                                        <button id="btn-sync-sales" class="btn btn-primary">
                                            <em class="icon ni ni-reload-alt"></em>
                                            <span>Sync Sales</span>
                                        </button>
                                    </li> -->
                                    <!-- <li><a href="<?php echo e(route('admin.erprev.inventory')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-package"></em><span>Inventory</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.products')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-grid-add"></em><span>Products</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.summary')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-bar-chart"></em><span>Summary</span></a></li> -->
                                    <li><a href="<?php echo e(route('admin.erprev.sync-stocked-books-sales')); ?>" target="_blank" class="btn btn-white btn-dim btn-outline-primary"><em class="icon ni ni-reload"></em><span>Sync Stocked Books Sales</span></a></li>
                                    <!-- <li><a href="<?php echo e(route('admin.erprev.test-endpoints')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-play-fill"></em><span>Test Endpoints</span></a></li> -->
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="lastupdated">Last Updated</label>
                                        <div class="form-control-wrap">
                                            <select name="lastupdated" id="lastupdated" class="form-select">
                                                <option value="2026-04-01" <?php echo e(request('lastupdated', $filters['lastupdated'] ?? '2026-04-01') == '2026-04-01' ? 'selected' : ''); ?>>Since April 2026</option>
                                                <option value="all" <?php echo e(request('lastupdated', $filters['lastupdated'] ?? '2026-04-01') == 'all' ? 'selected' : ''); ?>>All Records</option>
                                                <option value="5m" <?php echo e(request('lastupdated', $filters['lastupdated'] ?? '2026-04-01') == '5m' ? 'selected' : ''); ?>>Last 5 Minutes</option>
                                                <option value="10m" <?php echo e(request('lastupdated', $filters['lastupdated'] ?? '2026-04-01') == '10m' ? 'selected' : ''); ?>>Last 10 Minutes</option>
                                                <option value="30m" <?php echo e(request('lastupdated', $filters['lastupdated'] ?? '2026-04-01') == '30m' ? 'selected' : ''); ?>>Last 30 Minutes</option>
                                                <option value="1h" <?php echo e(request('lastupdated', $filters['lastupdated'] ?? '2026-04-01') == '1h' ? 'selected' : ''); ?>>Last 1 Hour</option>
                                                <option value="4h" <?php echo e(request('lastupdated', $filters['lastupdated'] ?? '2026-04-01') == '4h' ? 'selected' : ''); ?>>Last 4 Hours</option>
                                                <option value="6h" <?php echo e(request('lastupdated', $filters['lastupdated'] ?? '2026-04-01') == '6h' ? 'selected' : ''); ?>>Last 6 Hours</option>
                                                <option value="24h" <?php echo e(request('lastupdated', $filters['lastupdated'] ?? '2026-04-01') == '24h' ? 'selected' : ''); ?>>Last 24 Hours</option>
                                                <option value="7d" <?php echo e(request('lastupdated', $filters['lastupdated'] ?? '2026-04-01') == '7d' ? 'selected' : ''); ?>>Last 7 Days</option>
                                                <option value="30d" <?php echo e(request('lastupdated', $filters['lastupdated'] ?? '2026-04-01') == '30d' ? 'selected' : ''); ?>>Last 30 Days</option>
                                                <option value="60d" <?php echo e(request('lastupdated', $filters['lastupdated'] ?? '2026-04-01') == '60d' ? 'selected' : ''); ?>>Last 60 Days</option>
                                                <option value="100d" <?php echo e(request('lastupdated', $filters['lastupdated'] ?? '2026-04-01') == '100d' ? 'selected' : ''); ?>>Last 100 Days</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="start_date">Start Date</label>
                                        <div class="form-control-wrap">
                                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo e(request('start_date', $filters['start_date'] ?? '')); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="end_date">End Date</label>
                                        <div class="form-control-wrap">
                                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo e(request('end_date', $filters['end_date'] ?? '')); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="name">Product Name</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter product name" value="<?php echo e(request('name')); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="invoice_id">Invoice ID</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="invoice_id" name="invoice_id" placeholder="Enter Invoice ID" value="<?php echo e(request('invoice_id')); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="product_id">Product ID</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="product_id" name="product_id" placeholder="Enter Product ID" value="<?php echo e(request('product_id')); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <button type="submit" class="btn btn-primary me-2"><em class="icon ni ni-filter-alt"></em><span>Apply Filter</span></button>
                                            <?php if((request('lastupdated') && request('lastupdated') !== '2026-04-01') || request('start_date') || request('end_date') || request('name') || request('invoice_id') || request('product_id')): ?>
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
                                            <th>Invoice ID</th>
                                            <th>Sale ID</th>
                                            <th>Date/Time</th>
                                            <th>Product Name</th>
                                            <th>Category</th>
                                            <th>Warehouse</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                            <th>Customer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $paginator; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e(($paginator->currentPage() - 1) * $paginator->perPage() + $loop->iteration); ?></td>
                                                <td><?php echo e($sale['InvoiceID'] ?? 'N/A'); ?></td>
                                                <td><?php echo e($sale['ID'] ?? 'N/A'); ?></td>
                                                <td><?php echo e($sale['DateTime'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <strong><?php echo e($sale['Product'] ?? 'N/A'); ?></strong>
                                                    <?php if(isset($sale['ProductID'])): ?>
                                                        <br><small class="text-muted">ID: <?php echo e($sale['ProductID']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo e($sale['Category'] ?? 'N/A'); ?></td>
                                                <td><?php echo e($sale['WareHouse'] ?? 'N/A'); ?></td>
                                                <td><?php echo e(number_format((float)($sale['Qty'] ?? 0))); ?></td>
                                                <td><?php echo $sale['Currency'] ?? $sale['CurrencySymbol'] ?? '&#x20A6;'; ?><?php echo e(number_format((float)str_replace(',', '', $sale['UnitPrice'] ?? 0), 2)); ?></td>
                                                <td><?php echo $sale['Currency'] ?? $sale['CurrencySymbol'] ?? '&#x20A6;'; ?><?php echo e(number_format((float)str_replace(',', '', $sale['Amount'] ?? 0), 2)); ?></td>
                                                <td>
                                                    <?php echo e($sale['CustomerName'] ?? 'N/A'); ?>

                                                    <?php if(!empty($sale['CustomerMobile'])): ?>
                                                        <br><small class="text-muted"><?php echo e($sale['CustomerMobile']); ?></small>
                                                    <?php endif; ?>
                                                </td>
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
                                           'lastupdated' => request('lastupdated', $filters['lastupdated'] ?? '2026-04-01'),
                                           'start_date' => request('start_date'),
                                           'end_date' => request('end_date'),
                                           'name' => request('name'),
                                           'invoice_id' => request('invoice_id')
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

<?php $__env->startSection('scripts'); ?>
<script>
    document.getElementById('btn-sync-sales').addEventListener('click', function() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const lastUpdated = document.getElementById('lastupdated').value;

        let syncUrl = '<?php echo e(route("api.erprev.sync-sales-deep")); ?>?dry_run=0&status=all';
        let syncText = 'Performing deep search of sales from April 2026 to update wallets. Please wait.';

        if (startDate) {
            syncUrl += '&start_date=' + encodeURIComponent(startDate);
            syncText = `Performing deep search of sales starting from ${startDate} to update wallets. Please wait.`;
            if (endDate) {
                syncUrl += '&end_date=' + encodeURIComponent(endDate);
                syncText = `Performing deep search of sales from ${startDate} to ${endDate} to update wallets. Please wait.`;
            }
        } else {
            syncUrl += '&lastupdated=' + encodeURIComponent(lastUpdated);
            if (lastUpdated !== 'all' && lastUpdated !== '2026-04-01') {
                syncText = `Performing search of sales from the last ${lastUpdated} to update wallets. Please wait.`;
            }
        }

        Swal.fire({
            title: 'Synchronizing Sales Data...',
            text: syncText,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(syncUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const stats = data.statistics;
                    Swal.fire({
                        icon: 'success',
                        title: 'Sales Sync Completed',
                        html: `
                            <p>${data.message}</p>
                            <div class="text-start mt-3">
                                <ul>
                                    <li><strong>Processed Sales:</strong> ${stats.processed}</li>
                                    <li><strong>Skipped (Duplicates):</strong> ${stats.duplicates}</li>
                                    <li><strong>Books Not Found:</strong> ${stats.books_not_found}</li>
                                    <li><strong>Total Records Received:</strong> ${stats.total_records_received}</li>
                                    <li><strong>Errors:</strong> ${stats.errors}</li>
                                </ul>
                            </div>
                        `,
                        confirmButtonText: 'Reload Page',
                        confirmButtonColor: '#099fff',
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sync Failed',
                        text: data.message || 'An error occurred during synchronization.',
                        confirmButtonColor: '#e85347'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Request Failed',
                    text: 'Unable to communicate with the synchronization endpoint. ' + error.message,
                    confirmButtonColor: '#e85347'
                });
            });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\authors_portal\resources\views/admin/erprev/sales.blade.php ENDPATH**/ ?>