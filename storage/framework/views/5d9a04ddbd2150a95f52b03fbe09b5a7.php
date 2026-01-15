<?php $__env->startSection('title', 'System Settings | Admin Panel'); ?>

<?php $__env->startSection('page-title', 'System Settings'); ?>

<?php $__env->startSection('page-description', 'Configure platform settings and preferences'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">System Settings</h3>
                        <div class="nk-block-des text-soft">
                            <p>Configure platform settings, payment options, and system preferences.</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success">
                    <em class="icon ni ni-check-circle"></em>
                    <strong>Success!</strong> <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger">
                    <em class="icon ni ni-cross-circle"></em>
                    <strong>Error!</strong> <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <em class="icon ni ni-cross-circle"></em>
                    <strong>Error!</strong>
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- General Settings -->
                    <div class="col-lg-8">
                        <form id="settingsForm" action="<?php echo e(route('admin.settings.update')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">General Settings</h6>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Site Name</label>
                                                <input type="text" class="form-control" name="site_name" value="<?php echo e(config('app.name')); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Site URL</label>
                                                <input type="url" class="form-control" name="site_url" value="<?php echo e(config('app.url')); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Site Description</label>
                                                <textarea class="form-control" name="site_description" rows="3"><?php echo e($settings['site_description'] ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Contact Email</label>
                                                <input type="email" class="form-control" name="contact_email" value="<?php echo e($settings['contact_email'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Support Email</label>
                                                <input type="email" class="form-control" name="support_email" value="<?php echo e($settings['support_email'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payout Settings -->
                            <div class="card card-bordered mt-4">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">Payout Settings</h6>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Minimum Payout Amount (₦)</label>
                                                <input type="number" class="form-control" name="min_payout_amount" value="<?php echo e($settings['min_payout_amount'] ?? 300000); ?>" min="1" step="0.01" required>
                                                <div class="form-note">Authors must have at least this amount to request a payout</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Payout Frequency Limit (Days)</label>
                                                <input type="number" class="form-control" name="payout_frequency_days" value="<?php echo e($settings['payout_frequency_days'] ?? 30); ?>" min="1" max="365" required>
                                                <div class="form-note">Authors can only request payouts once every X days</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Processing Time (Min Days)</label>
                                                <input type="number" class="form-control" name="payout_processing_time_min" value="<?php echo e($settings['payout_processing_time_min'] ?? 3); ?>" min="1" max="30" required>
                                                <div class="form-note">Minimum number of days for payout processing</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Processing Time (Max Days)</label>
                                                <input type="number" class="form-control" name="payout_processing_time_max" value="<?php echo e($settings['payout_processing_time_max'] ?? 5); ?>" min="1" max="30" required>
                                                <div class="form-note">Maximum number of days for payout processing</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="card card-bordered mt-4">
                                <div class="card-inner">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <em class="icon ni ni-save"></em>
                                            <span>Save All Settings</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" onclick="clearCache()">
                                            <em class="icon ni ni-reload"></em>
                                            <span>Clear Cache</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Quick Actions</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-2">
                                    <div class="col-12">
                                        <button class="btn btn-outline-info btn-block" onclick="testEmail()">
                                            <em class="icon ni ni-mail"></em><span>Test Email</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payout Information Preview -->
                        <div class="card card-bordered mt-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Current Payout Settings</h6>
                                    </div>
                                </div>
                                
                                <ul class="nk-list-meta">
                                    <li class="nk-list-meta-item">
                                        <span class="nk-list-meta-label">Minimum Payout:</span>
                                        <span class="nk-list-meta-value">₦<?php echo e(number_format($settings['min_payout_amount'] ?? 300000, 2)); ?></span>
                                    </li>
                                    <li class="nk-list-meta-item">
                                        <span class="nk-list-meta-label">Processing Time:</span>
                                        <span class="nk-list-meta-value"><?php echo e($settings['payout_processing_time_min'] ?? 3); ?>-<?php echo e($settings['payout_processing_time_max'] ?? 5); ?> days</span>
                                    </li>
                                    <li class="nk-list-meta-item">
                                        <span class="nk-list-meta-label">Frequency Limit:</span>
                                        <span class="nk-list-meta-value">Once every <?php echo e($settings['payout_frequency_days'] ?? 30); ?> days</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for non-form actions -->
<script>
function clearCache() {
    Swal.fire({
        title: 'Clear Cache?',
        text: "This will clear all cached data including settings.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e85347',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, clear it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?php echo e(route('admin.settings.clear-cache')); ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cache Cleared!',
                        text: data.message,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An unexpected error occurred. Please try again.',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

function testEmail() {
    Swal.fire({
        title: 'Test Email',
        text: 'Enter an email address to send a test message:',
        input: 'email',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Send Test',
        showLoaderOnConfirm: true,
        preConfirm: (email) => {
            return fetch('<?php echo e(route('admin.settings.test-email')); ?>', {
                method: 'POST',
                body: JSON.stringify({email: email}),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message);
                }
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Email Sent!',
                text: result.value.message,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        }
    });
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/settings/index.blade.php ENDPATH**/ ?>