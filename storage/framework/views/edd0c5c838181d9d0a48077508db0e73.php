<?php $__env->startSection('title', 'Edit User | Admin Panel'); ?>

<?php $__env->startSection('page-title', 'Edit User'); ?>

<?php $__env->startSection('page-description', 'Update user information and permissions'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Edit User: <?php echo e($user->name); ?></h3>
                        <div class="nk-block-des text-soft">
                            <p>Update user information, roles, and account settings.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="btn btn-outline-light">
                            <em class="icon ni ni-arrow-left"></em><span>Back to User</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- User Information Form -->
                    <div class="col-xxl-8">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">User Information</h6>
                                    </div>
                                </div>
                                
                                <form action="<?php echo e(route('admin.users.update', $user)); ?>" method="POST" class="form-validate">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="form-group">
                <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                <div class="form-control-wrap">
                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name" value="<?php echo e(old('name', $user->name)); ?>" required>
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
        </div>
        <div class="col-lg-6">
            <div class="form-group">
                <label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
                <div class="form-control-wrap">
                    <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required>
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
        </div>
        <div class="col-lg-6">
            <div class="form-group">
                <label class="form-label" for="phone">Phone Number</label>
                <div class="form-control-wrap">
                    <input type="text" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="phone" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>">
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
        </div>
        <div class="col-lg-6">
            <div class="form-group">
                <label class="form-label" for="website">Website URL</label>
                <div class="form-control-wrap">
                    <input type="url" class="form-control <?php $__errorArgs = ['website'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="website" name="website" value="<?php echo e(old('website', $user->website)); ?>" placeholder="https://example.com">
                    <?php $__errorArgs = ['website'];
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
      
        
        <!-- Role Management -->
        <div class="col-12">
            <div class="form-group">
                <label class="form-label">User Roles</label>
                <div class="form-control-wrap">
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="role_<?php echo e($role->id); ?>" name="roles[]" value="<?php echo e($role->name); ?>" 
                                <?php echo e($user->hasRole($role->name) ? 'checked' : ''); ?>>
                            <label class="custom-control-label" for="role_<?php echo e($role->id); ?>">
                                <?php echo e(ucfirst($role->name)); ?>

                                <span class="form-note"><?php echo e($role->description ?? 'No description available'); ?></span>
                            </label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php $__errorArgs = ['roles'];
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
        
        <!-- Account Status -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="form-label">Email Verification</label>
                <div class="form-control-wrap">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="email_verified" name="email_verified" 
                            <?php echo e($user->email_verified_at ? 'checked' : ''); ?>>
                        <label class="custom-control-label" for="email_verified">Email Verified</label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12">
            <div class="form-group">
                <button type="submit" class="btn btn-lg btn-primary">Update User</button>
                <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="btn btn-lg btn-outline-light">Cancel</a>
            </div>
        </div>
    </div>
</form>
                            </div>
                        </div>
                    </div>

                    <!-- User Actions & Security -->
                    <div class="col-xxl-4">
                        <!-- Password Reset -->
                        <div class="card card-bordered card-full mb-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Password Management</h6>
                                    </div>
                                </div>
                                
                                <form action="<?php echo e(route('admin.users.reset-password', $user)); ?>" method="POST" id="passwordResetForm">
                                    <?php echo csrf_field(); ?>
                                    <div class="form-group">
                                        <label class="form-label" for="new_password">New Password</label>
                                        <div class="form-control-wrap">
                                            <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="new_password">
                                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                            </a>
                                            <input type="password" class="form-control" id="new_password" name="password" required>
                                        </div>
                                        <div class="form-note">Password must be at least 8 characters long.</div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="new_password_confirmation">Confirm Password</label>
                                        <div class="form-control-wrap">
                                            <input type="password" class="form-control" id="new_password_confirmation" name="password_confirmation" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-warning btn-block" data-confirm-reset data-confirm-message="Are you sure you want to reset the password for <?php echo e($user->name); ?>?">
                                            Reset Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Account Actions -->
                        <div class="card card-bordered card-full mb-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Account Actions</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-2">
                                    <?php if(!$user->email_verified_at): ?>
                                        <div class="col-12">
                                            <form method="POST" action="<?php echo e(route('admin.users.send-verification', $user)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-info btn-block">
                                                    <em class="icon ni ni-mail"></em><span>Send Verification Email</span>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                    
                                    <?php if(!$user->hasRole('author')): ?>
                                        <div class="col-12">
                                            <form method="POST" action="<?php echo e(route('admin.users.promote-author', $user)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-success btn-block" data-confirm-promote data-confirm-message="Are you sure you want to promote <?php echo e($user->name); ?> to author?">
                                                    <em class="icon ni ni-user-add"></em><span>Promote to Author</span>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                    
                                    <div class="col-12">
                                        <a href="<?php echo e(route('admin.users.login-as', $user)); ?>" class="btn btn-outline-primary btn-block">
                                            <em class="icon ni ni-signin"></em><span>Login as User</span>
                                        </a>
                                    </div>
                    
                                    <!-- Account Status Management -->
                                    <div class="col-12">
                                        <?php if($user->isActive()): ?>
                                            <form method="POST" action="<?php echo e(route('admin.users.deactivate', $user)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-warning btn-block" data-confirm-deactivate data-confirm-message="Are you sure you want to deactivate <?php echo e($user->name); ?>? This will prevent them from logging in.">
                                                    <em class="icon ni ni-user-cross"></em><span>Deactivate Account</span>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" action="<?php echo e(route('admin.users.activate', $user)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-success btn-block" data-confirm-activate data-confirm-message="Are you sure you want to activate <?php echo e($user->name); ?>? This will allow them to log in.">
                                                    <em class="icon ni ni-user-check"></em><span>Activate Account</span>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Danger Zone -->
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title text-danger">Danger Zone</h6>
                                    </div>
                                </div>
                                
                                <div class="alert alert-danger">
                                    <div class="alert-cta">
                                        <h6>Delete User Account</h6>
                                        <p>This action cannot be undone. All user data, books, and transactions will be permanently deleted.</p>
                                        <form method="POST" action="<?php echo e(route('admin.users.destroy', $user)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger" data-confirm-delete data-confirm-message="Are you sure you want to delete <?php echo e($user->name); ?>? This action cannot be undone. All user data, books, and transactions will be permanently deleted.">
                                                Delete Account
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordResetForm = document.getElementById('passwordResetForm');
    
    if (passwordResetForm) {
        passwordResetForm.addEventListener('submit', function(e) {
            console.log('Password reset form submitted');
            // The confirmation dialog is handled by our admin.js script
            // which looks for elements with data-confirm-reset attribute
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/users/edit.blade.php ENDPATH**/ ?>