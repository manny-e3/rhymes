<?php $__env->startSection('title', 'Admin Profile | Admin Panel'); ?>

<?php $__env->startSection('page-title', 'Admin Profile'); ?>

<?php $__env->startSection('page-description', 'Manage your admin account settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Admin Profile</h3>
                        <div class="nk-block-des text-soft">
                            <p>Manage your admin account information and security settings.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- Profile Information -->
                    <div class="col-lg-8">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Profile Information</h6>
                                    </div>
                                </div>
                                
                                <form action="<?php echo e(route('admin.profile.update')); ?>" method="POST" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Full Name</label>
                                                <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="name" value="<?php echo e(old('name', auth()->user()->name)); ?>" required>
                                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="form-note-error"><?php echo e($message); ?></span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Email Address</label>
                                                <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(old('email', auth()->user()->email)); ?>" required>
                                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="form-note-error"><?php echo e($message); ?></span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Phone Number</label>
                                                <input type="text" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="phone" value="<?php echo e(old('phone', auth()->user()->phone)); ?>">
                                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="form-note-error"><?php echo e($message); ?></span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Timezone</label>
                                                <select class="form-select <?php $__errorArgs = ['timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="timezone">
                                                    <option value="UTC" <?php echo e((auth()->user()->timezone ?? 'UTC') === 'UTC' ? 'selected' : ''); ?>>UTC</option>
                                                    <option value="America/New_York" <?php echo e((auth()->user()->timezone ?? 'UTC') === 'America/New_York' ? 'selected' : ''); ?>>Eastern Time</option>
                                                    <option value="America/Chicago" <?php echo e((auth()->user()->timezone ?? 'UTC') === 'America/Chicago' ? 'selected' : ''); ?>>Central Time</option>
                                                    <option value="America/Denver" <?php echo e((auth()->user()->timezone ?? 'UTC') === 'America/Denver' ? 'selected' : ''); ?>>Mountain Time</option>
                                                    <option value="America/Los_Angeles" <?php echo e((auth()->user()->timezone ?? 'UTC') === 'America/Los_Angeles' ? 'selected' : ''); ?>>Pacific Time</option>
                                                    <option value="Europe/London" <?php echo e((auth()->user()->timezone ?? 'UTC') === 'Europe/London' ? 'selected' : ''); ?>>London</option>
                                                    <option value="Europe/Paris" <?php echo e((auth()->user()->timezone ?? 'UTC') === 'Europe/Paris' ? 'selected' : ''); ?>>Paris</option>
                                                </select>
                                                <?php $__errorArgs = ['timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="form-note-error"><?php echo e($message); ?></span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Bio</label>
                                                <textarea class="form-control <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="bio" rows="4"><?php echo e(old('bio', auth()->user()->bio)); ?></textarea>
                                                <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="form-note-error"><?php echo e($message); ?></span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">Update Profile</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Security Settings -->
                        <div class="card card-bordered mt-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Security Settings</h6>
                                    </div>
                                </div>
                                
                                <form action="<?php echo e(route('admin.profile.password')); ?>" method="POST" id="passwordForm">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Current Password</label>
                                                <div class="form-control-wrap">
                                                    <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="current_password">
                                                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                    </a>
                                                    <input type="password" class="form-control <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="current_password" id="current_password">
                                                    <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <span class="form-note-error"><?php echo e($message); ?></span>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6"></div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">New Password</label>
                                                <div class="form-control-wrap">
                                                    <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                    </a>
                                                    <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" id="password">
                                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <span class="form-note-error"><?php echo e($message); ?></span>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Confirm New Password</label>
                                                <div class="form-control-wrap">
                                                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-warning">Change Password</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Notification Preferences -->
                        
                    </div>

                    <!-- Profile Sidebar -->
                    <div class="col-lg-4">
                        <!-- Profile Card -->
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="user-card user-card-s2">
                                    <div class="user-avatar lg bg-primary">
                                        <span><?php echo e(strtoupper(substr(auth()->user()->name, 0, 2))); ?></span>
                                    </div>
                                    <div class="user-info">
                                        <h5><?php echo e(auth()->user()->name); ?></h5>
                                        <span class="sub-text"><?php echo e(auth()->user()->email); ?></span>
                                    </div>
                                </div>
                                
                                <div class="user-meta">
                                    <ul class="nk-list-meta">
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Role:</span>
                                            <span class="nk-list-meta-value">
                                                <?php $__currentLoopData = auth()->user()->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="badge badge-dim bg-outline-primary"><?php echo e(ucfirst($role->name)); ?></span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </span>
                                        </li>
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Member Since:</span>
                                            <span class="nk-list-meta-value"><?php echo e(auth()->user()->created_at->format('M d, Y')); ?></span>
                                        </li>
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Last Login:</span>
                                            <span class="nk-list-meta-value">
                                                <?php if(auth()->user()->last_login_at): ?>
                                                    <?php echo e(auth()->user()->last_login_at->diffForHumans()); ?>

                                                <?php else: ?>
                                                    Never
                                                <?php endif; ?>
                                            </span>
                                        </li>
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Status:</span>
                                            <span class="nk-list-meta-value">
                                                <?php if(auth()->user()->email_verified_at): ?>
                                                    <span class="badge badge-success">Verified</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">Unverified</span>
                                                <?php endif; ?>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Summary -->
                        <div class="card card-bordered mt-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Activity Summary</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-4">
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-primary-dim">
                                                        <em class="icon ni ni-users"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Users Managed</p>
                                                    <h4 class="inbox-item-title"><?php echo e($stats['users_managed'] ?? 0); ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-success-dim">
                                                        <em class="icon ni ni-book"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Books Reviewed</p>
                                                    <h4 class="inbox-item-title"><?php echo e($stats['books_reviewed'] ?? 0); ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-warning-dim">
                                                        <em class="icon ni ni-tranx"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Payouts Processed</p>
                                                    <h4 class="inbox-item-title"><?php echo e($stats['payouts_processed'] ?? 0); ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-info-dim">
                                                        <em class="icon ni ni-clock"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Hours Online</p>
                                                    <h4 class="inbox-item-title"><?php echo e($stats['hours_online'] ?? 0); ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Password form validation
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        Swal.fire('Error!', 'Passwords do not match.', 'error');
        return false;
    }
    
    if (password.length < 8) {
        e.preventDefault();
        Swal.fire('Error!', 'Password must be at least 8 characters long.', 'error');
        return false;
    }
});

function downloadData() {
    Swal.fire({
        title: 'Export Personal Data?',
        text: 'This will generate a file containing all your admin activity data.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, export!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Generating Export...',
                text: 'Please wait while we prepare your data.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/admin/profile/export-data', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            })
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = `admin-data-${new Date().toISOString().split('T')[0]}.json`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                Swal.close();
            })
            .catch(error => {
                Swal.fire('Error!', 'Failed to export data.', 'error');
            });
        }
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/rovinghe/author.rovingheights.com/resources/views/admin/profile/index.blade.php ENDPATH**/ ?>