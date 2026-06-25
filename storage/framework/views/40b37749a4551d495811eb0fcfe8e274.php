<?php $__env->startSection('title', 'ERPREV Endpoint Tester | Rhymes Platform'); ?>

<?php $__env->startSection('page-title', 'ERPREV Endpoint Tester'); ?>

<?php $__env->startSection('page-description', 'Test correct ERPREV sales and order endpoints with dynamic pagination'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">ERPREV Endpoint Tester</h3>
                        <div class="nk-block-des text-soft">
                            <p>Verify and fetch records directly from correct ERPREV API endpoints</p>
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
                                    <li><a href="<?php echo e(route('admin.erprev.monitoring')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-activity-alt"></em><span>Sync Monitoring</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuration Card -->
            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <h5 class="card-title">Request Configuration</h5>
                        <form method="POST" action="<?php echo e(route('admin.erprev.run-test-endpoint')); ?>" class="mt-4">
                            <?php echo csrf_field(); ?>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="endpoint">API Endpoint <span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <select name="endpoint" id="endpoint" class="form-select select2" required>
                                                <option value="">-- Select Endpoint to Test --</option>
                                                <?php $__currentLoopData = $endpoints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $path => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($path); ?>" <?php echo e((isset($selectedEndpoint) && $selectedEndpoint == $path) ? 'selected' : ''); ?>>
                                                        <?php echo e($label); ?> (<?php echo e($path); ?>)
                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <span class="form-note">Select the specific Sales or Order endpoint to query.</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="lastupdated">Last Updated Filter (Relative or Custom Date Y-m-d)</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="lastupdated" name="lastupdated" list="lastupdated-presets" placeholder="e.g. 2026-04-01 or 100d" value="<?php echo e($selectedLastUpdated ?? '100d'); ?>">
                                            <datalist id="lastupdated-presets">
                                                <option value="all">All Records</option>
                                                <option value="2026-04-01">April 2026 till date</option>
                                                <option value="5m">Last 5 Minutes</option>
                                                <option value="10m">Last 10 Minutes</option>
                                                <option value="30m">Last 30 Minutes</option>
                                                <option value="1h">Last 1 Hour</option>
                                                <option value="4h">Last 4 Hours</option>
                                                <option value="6h">Last 6 Hours</option>
                                                <option value="24h">Last 24 Hours</option>
                                                <option value="7d">Last 7 Days</option>
                                                <option value="30d">Last 30 Days</option>
                                                <option value="60d">Last 60 Days</option>
                                                <option value="100d">Last 100 Days</option>
                                            </datalist>
                                        </div>
                                        <span class="form-note">Pass dynamic lastupdated threshold value (e.g. 100d or custom date string like 2026-04-01). <strong class="text-warning">Warning:</strong> Querying 'all' without other filters on large tables can result in timeouts.</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="Product">Product Name (Deep Search Filter)</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="Product" name="Product" placeholder="e.g. Sanya" value="<?php echo e($selectedProduct ?? ''); ?>">
                                        </div>
                                        <span class="form-note">Filter by specific Product name query parameter on the server side.</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="ProductID">Product ID (Deep Search Filter)</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="ProductID" name="ProductID" placeholder="e.g. 30585" value="<?php echo e($selectedProductID ?? ''); ?>">
                                        </div>
                                        <span class="form-note">Filter by specific ProductID query parameter on the server side.</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="startRow">Pagination: startRow</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" id="startRow" name="startRow" placeholder="e.g. 1000" value="<?php echo e($selectedStartRow ?? ''); ?>">
                                        </div>
                                        <span class="form-note">The start row index for subsequent pagination requests. Leave empty for the first page.</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="TotalRecords">Pagination: TotalRecords</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" id="TotalRecords" name="TotalRecords" placeholder="e.g. 500000" value="<?php echo e($selectedTotalRecords ?? ''); ?>">
                                        </div>
                                        <span class="form-note">The total records count returned from the previous pagination object. Leave empty for the first page.</span>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-lg btn-primary">
                                            <em class="icon ni ni-play-fill"></em><span>Send Request to ERPREV API</span>
                                        </button>
                                        <a href="<?php echo e(route('admin.erprev.test-endpoints')); ?>" class="btn btn-lg btn-light">Reset Form</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <?php if(isset($result)): ?>
                <div class="nk-block mt-4">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="nk-block-between align-start mb-3">
                                <div class="nk-block-head-content">
                                    <h5 class="card-title">API Response Results</h5>
                                </div>
                                <div class="nk-block-head-content">
                                    <?php if($result['success']): ?>
                                        <span class="badge bg-success px-3 py-2 fs-6">SUCCESS (HTTP OK)</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger px-3 py-2 fs-6">FAILED (API ERROR)</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if(isset($result['url'])): ?>
                                <div class="alert alert-fill alert-light alert-icon mb-4">
                                    <em class="icon ni ni-globe"></em>
                                    <strong>Requested URL:</strong> <code class="text-dark bg-white p-1 rounded"><?php echo e($result['url']); ?></code>
                                </div>
                            <?php endif; ?>

                            <?php if($result['success']): ?>
                                <?php
                                    $dataPayload = $result['data'] ?? [];
                                    $pagenation = $dataPayload['pagenation'] ?? $dataPayload['pagination'] ?? null;
                                    $records = $dataPayload['records'] ?? $dataPayload['data'] ?? $dataPayload['records_view'] ?? [];
                                ?>

                                <!-- Pagination Details Card -->
                                <div class="card card-bordered bg-light mb-4">
                                    <div class="card-inner">
                                        <h6 class="title text-primary"><em class="icon ni ni-swap-alt-h me-1"></em>Pagination Object (Returned by API)</h6>
                                        <?php if($pagenation): ?>
                                            <div class="row g-3 mt-2">
                                                <div class="col-md-3 col-6">
                                                    <div class="bg-white p-3 rounded border text-center">
                                                        <span class="text-soft fs-11px text-uppercase">Total Records</span>
                                                        <h4 class="mt-1 text-dark"><?php echo e($pagenation['TotalRecords'] ?? $pagenation['totalRecords'] ?? 'N/A'); ?></h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6">
                                                    <div class="bg-white p-3 rounded border text-center">
                                                        <span class="text-soft fs-11px text-uppercase">Page Limit</span>
                                                        <h4 class="mt-1 text-dark"><?php echo e($pagenation['PageLimit'] ?? $pagenation['pageLimit'] ?? 'N/A'); ?></h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6">
                                                    <div class="bg-white p-3 rounded border text-center">
                                                        <span class="text-soft fs-11px text-uppercase">Start Row</span>
                                                        <h4 class="mt-1 text-dark"><?php echo e($pagenation['startRow'] ?? $pagenation['StartRow'] ?? 'N/A'); ?></h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6">
                                                    <div class="bg-white p-3 rounded border text-center">
                                                        <span class="text-soft fs-11px text-uppercase">End Row</span>
                                                        <h4 class="mt-1 text-dark"><?php echo e($pagenation['endRow'] ?? $pagenation['EndRow'] ?? 'N/A'); ?></h4>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if(isset($pagenation['TotalRecords']) && isset($pagenation['endRow'])): ?>
                                                <?php
                                                    $nextStart = (int)($pagenation['endRow']) + 1;
                                                    $totalRecs = $pagenation['TotalRecords'];
                                                ?>
                                                <?php if($nextStart <= $totalRecs): ?>
                                                    <div class="alert alert-fill alert-info mt-3 mb-0">
                                                        <em class="icon ni ni-info-fill"></em>
                                                        <strong>Next Page Query:</strong> To fetch the next block of records, copy the following values into the input fields above:
                                                        <ul class="mt-2 mb-0 pl-3">
                                                            <li><strong>startRow:</strong> <code class="bg-white text-info px-1 rounded"><?php echo e($nextStart); ?></code></li>
                                                            <li><strong>TotalRecords:</strong> <code class="bg-white text-info px-1 rounded"><?php echo e($totalRecs); ?></code></li>
                                                        </ul>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="alert alert-fill alert-success mt-3 mb-0">
                                                        <em class="icon ni ni-check-circle-fill"></em>
                                                        <strong>End of Records:</strong> You have reached the end of the available records.
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="text-muted mt-2">
                                                No `pagenation` object returned in this response. The API might have returned all records at once or has empty data.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Records Rendered Table -->
                                <div class="mb-4">
                                    <h6 class="title mb-3"><em class="icon ni ni-list-index me-1"></em>Records Found (<?php echo e(count($records)); ?>)</h6>
                                    <?php if(count($records) > 0): ?>
                                        <div class="table-responsive border rounded bg-white">
                                            <table class="table table-striped mb-0">
                                                <thead>
                                                    <tr>
                                                        <?php
                                                            $sampleRecord = $records[0];
                                                            $displayKeys = array_slice(array_keys($sampleRecord), 0, 8); // Show first 8 columns for neatness
                                                        ?>
                                                        <?php $__currentLoopData = $displayKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <th><?php echo e($key); ?></th>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if(count(array_keys($sampleRecord)) > 8): ?>
                                                            <th>Actions</th>
                                                        <?php endif; ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <?php $__currentLoopData = $displayKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <td>
                                                                    <?php if(is_array($record[$key])): ?>
                                                                        <span class="badge bg-light text-dark">Array (<?php echo e(count($record[$key])); ?>)</span>
                                                                    <?php else: ?>
                                                                        <?php echo e(Str::limit((string)$record[$key], 50)); ?>

                                                                    <?php endif; ?>
                                                                </td>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if(count(array_keys($sampleRecord)) > 8): ?>
                                                                <td>
                                                                    <button class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#recordDetails<?php echo e($index); ?>">
                                                                        Full Detail
                                                                    </button>

                                                                    <!-- Record Details Modal -->
                                                                    <div class="modal fade" id="recordDetails<?php echo e($index); ?>" tabindex="-1" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title">Record Details - Row #<?php echo e($index + 1); ?></h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <div class="table-responsive">
                                                                                        <table class="table table-bordered mb-0">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th width="30%">Field Name</th>
                                                                                                    <th>Value</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                <?php $__currentLoopData = $record; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                    <tr>
                                                                                                        <td><strong><?php echo e($field); ?></strong></td>
                                                                                                        <td>
                                                                                                            <?php if(is_array($val)): ?>
                                                                                                                <pre class="bg-light p-2 rounded"><code><?php echo e(json_encode($val, JSON_PRETTY_PRINT)); ?></code></pre>
                                                                                                            <?php else: ?>
                                                                                                                <?php echo e($val); ?>

                                                                                                            <?php endif; ?>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            <?php endif; ?>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-fill alert-warning">
                                            <em class="icon ni ni-alert-circle"></em>
                                            The API successfully connected but returned 0 records.
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Raw JSON Payload Explorer -->
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="title mb-0"><em class="icon ni ni-code me-1"></em>Raw Response JSON Payload</h6>
                                        <button class="btn btn-xs btn-outline-secondary" onclick="copyRawJson()">
                                            <em class="icon ni ni-copy"></em><span>Copy JSON</span>
                                        </button>
                                    </div>
                                    <div class="bg-dark p-3 rounded" style="max-height: 500px; overflow-y: auto;">
                                        <pre><code id="rawJsonCode" class="text-success"><?php echo e(json_encode($dataPayload, JSON_PRETTY_PRINT)); ?></code></pre>
                                    </div>
                                </div>

                            <?php else: ?>
                                <!-- Error Section -->
                                <div class="alert alert-fill alert-danger mb-4">
                                    <em class="icon ni ni-cross-circle-fill"></em>
                                    <strong>API Connection Error:</strong> <?php echo e($result['message']); ?>

                                </div>
                                <p class="text-soft">Check your API credentials, network connection, or parameters. You can inspect more details inside the System Sync logs.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    function copyRawJson() {
        const copyText = document.getElementById("rawJsonCode").innerText;
        navigator.clipboard.writeText(copyText).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Raw JSON payload copied to clipboard.',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }).catch(err => {
            console.error('Failed to copy text: ', err);
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/erprev/test_endpoints.blade.php ENDPATH**/ ?>