<?php $__env->startSection('title', 'My Profile | Rhymes Author Platform'); ?>
<?php $__env->startSection('page-title', 'My Profile'); ?>
<?php $__env->startSection('page-description', 'Manage your profile here'); ?>
<?php $__env->startSection('content'); ?>

<?php if(session('payment-success')): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?php echo e(session('payment-success')); ?>',
            timer: 3000,
            showConfirmButton: false
        });
    });
</script>
<?php endif; ?>

<?php if(session('payment-error')): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?php echo e(session('payment-error')); ?>',
            timer: 3000,
            showConfirmButton: false
        });
    });
</script>
<?php endif; ?>

<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block">
                <div class="card">
                    <div class="card-aside-wrap">
                        <div class="card-inner card-inner-lg">
                            <div class="nk-block-head nk-block-head-lg">
                                <div class="nk-block-between">
                                    <div class="nk-block-head-content">
                                        <h4 class="nk-block-title">Personal Information</h4>
                                        <div class="nk-block-des">
                                            <p>Basic info, like your name and address, that you use on Rhymes Platform.</p>
                                        </div>
                                    </div>
                                    <div class="nk-block-head-content align-self-start d-lg-none">
                                        <a href="#" class="toggle btn btn-icon btn-trigger mt-n1" data-target="userAside"><em class="icon ni ni-menu-alt-r"></em></a>
                                    </div>
                                </div>
                            </div><!-- .nk-block-head -->
                            <div class="nk-block">
                                <div class="nk-data data-list">
                                    <div class="data-head">
                                        <h6 class="overline-title">Basics</h6>
                                    </div>
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#profile-edit">
                                        <div class="data-col">
                                            <span class="data-label">Full Name</span>
                                            <span class="data-value"><?php echo e($user->name); ?></span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                    </div><!-- data-item -->
                                    
                                    <div class="data-item">
                                        <div class="data-col">
                                            <span class="data-label">Email</span>
                                            <span class="data-value"><?php echo e($user->email); ?></span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more disable"><em class="icon ni ni-lock-alt"></em></span></div>
                                    </div><!-- data-item -->
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#profile-edit">
                                        <div class="data-col">
                                            <span class="data-label">Phone Number</span>
                                            <span class="data-value <?php echo e(!$user->phone ? 'text-soft' : ''); ?>"><?php echo e($user->phone ?? 'Not added yet'); ?></span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                    </div><!-- data-item -->
                                    
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#profile-edit">
                                        <div class="data-col">
                                            <span class="data-label">Address</span>
                                            <span class="data-value <?php echo e(!$user->address ? 'text-soft' : ''); ?>">
                                                <?php echo e($user->address ?? 'Not added yet'); ?>

                                            </span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                    </div><!-- data-item -->
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#profile-edit">
                                        <div class="data-col">
                                            <span class="data-label">Bio</span>
                                            <span class="data-value <?php echo e(!isset($user->profile_data['account_description']) ? 'text-soft' : ''); ?>">
                                                <?php echo e($user->account_description ?? 'Not added yet'); ?>

                                            </span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                    </div>
                                    
                                </div><!-- data-list -->
                                
                                <div class="nk-data data-list">
                                    <div class="data-head">
                                        <h6 class="overline-title">Author Statistics</h6>
                                    </div>
                                    <div class="data-item">
                                        <div class="data-col">
                                            <span class="data-label">Total Books</span>
                                            <span class="data-value"><?php echo e($totalBooks); ?></span>
                                        </div>
                                        <div class="data-col data-col-end"><a href="<?php echo e(route('author.books.index')); ?>" class="link link-primary">View Books</a></div>
                                    </div><!-- data-item -->
                                    <div class="data-item">
                                        <div class="data-col">
                                            <span class="data-label">Published Books</span>
                                            <span class="data-value"><?php echo e($publishedBooks); ?></span>
                                        </div>
                                        <div class="data-col data-col-end"><a href="<?php echo e(route('author.books.index')); ?>?status=published" class="link link-primary">View Published</a></div>
                                    </div><!-- data-item -->
                                    <div class="data-item">
                                        <div class="data-col">
                                            <span class="data-label">Wallet Balance</span>
                                            <span class="data-value">₦<?php echo e(number_format($walletBalance, 2)); ?></span>
                                        </div>
                                        <div class="data-col data-col-end"><a href="<?php echo e(route('author.wallet.index')); ?>" class="link link-primary">View Wallet</a></div>
                                    </div><!-- data-item -->
                                    <div class="data-item">
                                        <div class="data-col">
                                            <span class="data-label">Member Since</span>
                                            <span class="data-value"><?php echo e($user->created_at->format('M d, Y')); ?></span>
                                        </div>
                                        <div class="data-col data-col-end">
                                            
                                        </div>
                                    </div><!-- data-item -->
                                </div><!-- data-list -->

                                <?php if(isset($user->social_links) && !empty(array_filter($user->social_links))): ?>
                                <div class="nk-data data-list">
                                    <div class="data-head">
                                        <h6 class="overline-title">Social Links</h6>
                                    </div>
                                    <?php $__currentLoopData = $user->social_links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $platform => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($url): ?>
                                        <div class="data-item" data-bs-toggle="modal" data-bs-target="#profile-edit">
                                            <div class="data-col">
                                                <span class="data-label"><?php echo e(ucfirst($platform)); ?></span>
                                                <span class="data-value">
                                                    <a href="<?php echo e($url); ?>" target="_blank" class="link link-primary"><?php echo e($url); ?></a>
                                                </span>
                                            </div>
                                            <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                        </div><!-- data-item -->
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div><!-- data-list -->
                                <?php endif; ?>
                                
                                <div class="nk-data data-list">
                                    <div class="data-head">
                                        <h6 class="overline-title">Payment Details</h6>
                                    </div>
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#payment-details">
                                        <div class="data-col">
                                            <span class="data-label">Payment Method</span>
                                            <span class="data-value <?php echo e(!isset($user->payment_details['payment_method']) ? 'text-soft' : ''); ?>">
                                                <?php if(isset($user->payment_details['payment_method'])): ?>
                                                    <?php echo e(ucwords(str_replace('_', ' ', $user->payment_details['payment_method']))); ?>

                                                <?php else: ?>
                                                    Not configured
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                    </div><!-- data-item -->
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#payment-details">
                                        <div class="data-col">
                                            <span class="data-label">Account Holder</span>
                                            <span class="data-value <?php echo e(!isset($user->payment_details['account_holder_name']) ? 'text-soft' : ''); ?>">
                                                <?php echo e($user->payment_details['account_holder_name'] ?? 'Not set'); ?>

                                            </span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                    </div><!-- data-item -->
                                    <?php if(isset($user->payment_details['payment_method'])): ?>
                                        <?php if($user->payment_details['payment_method'] === 'bank_transfer'): ?>
                                            <div class="data-item" data-bs-toggle="modal" data-bs-target="#payment-details">
                                                <div class="data-col">
                                                    <span class="data-label">Bank Name</span>
                                                    <span class="data-value"><?php echo e($user->payment_details['bank_name'] ?? 'Not set'); ?></span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item" data-bs-toggle="modal" data-bs-target="#payment-details">
                                                <div class="data-col">
                                                    <span class="data-label">Account Number</span>
                                                    <span class="data-value"><?php echo e($user->payment_details['account_number'] ?? 'Not set'); ?></span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                        <?php elseif($user->payment_details['payment_method'] === 'paypal'): ?>
                                            <div class="data-item" data-bs-toggle="modal" data-bs-target="#payment-details">
                                                <div class="data-col">
                                                    <span class="data-label">PayPal Email</span>
                                                    <span class="data-value"><?php echo e($user->payment_details['paypal_email'] ?? 'Not set'); ?></span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                        <?php elseif($user->payment_details['payment_method'] === 'stripe'): ?>
                                            <div class="data-item" data-bs-toggle="modal" data-bs-target="#payment-details">
                                                <div class="data-col">
                                                    <span class="data-label">Stripe Account</span>
                                                    <span class="data-value"><?php echo e($user->payment_details['stripe_account_id'] ?? 'Not set'); ?></span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#payment-details">
                                        <div class="data-col">
                                            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#payment-details">Update Payment Details</button>
                                        </div>
                                    </div><!-- data-item -->
                                </div><!-- data-list -->
                                
                              
                            </div><!-- .nk-block -->
                        </div>
                        
                        <!-- Sidebar -->
                        <div class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg" data-toggle-body="true" data-content="userAside" data-toggle-screen="lg" data-toggle-overlay="true">
                            <div class="card-inner-group" data-simplebar>
                                <div class="card-inner">
                                    <div class="user-card">
                                        <div class="user-avatar <?php echo e($user->avatar ? '' : 'bg-primary'); ?>">
                                            <?php if($user->avatar): ?>
                                                <img src="<?php echo e(asset('storage/images/avatar/' . $user->avatar)); ?>" alt="<?php echo e($user->name); ?>">
                                            <?php else: ?>
                                                <span><?php echo e(strtoupper(substr($user->name, 0, 2))); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="user-info">
                                            <span class="lead-text"><?php echo e($user->name); ?></span>
                                            <span class="sub-text"><?php echo e($user->email); ?></span>
                                        </div>
                                        <div class="user-action">
                                            <div class="dropdown">
                                                <a class="btn btn-icon btn-trigger me-n2" data-bs-toggle="dropdown" href="#"><em class="icon ni ni-more-v"></em></a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#avatar-upload"><em class="icon ni ni-camera-fill"></em><span>Change Photo</span></a></li>
                                                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#profile-edit"><em class="icon ni ni-edit-fill"></em><span>Update Profile</span></a></li>
                                                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#password-change"><em class="icon ni ni-lock-alt-fill"></em><span>Change Password</span></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- .user-card -->
                                </div><!-- .card-inner -->
                                <div class="card-inner">
                                    <div class="user-account-info py-0">
                                        <h6 class="overline-title-alt">Author Wallet</h6>
                                        <div class="user-balance">₦<?php echo e(number_format($walletBalance, 2)); ?> <small class="currency">NGN</small></div>
                                        <div class="user-balance-sub">Total Books <span><?php echo e($totalBooks); ?></span></div>
                                    </div>
                                </div><!-- .card-inner -->
                                <div class="card-inner p-0">
                                    <ul class="link-list-menu">
                                        <li><a class="active" href="<?php echo e(route('author.profile.edit')); ?>"><em class="icon ni ni-user-fill-c"></em><span>Personal Information</span></a></li>
                                        
                                </div><!-- .card-inner -->
                            </div><!-- .card-inner-group -->
                        </div><!-- card-aside -->
                    </div><!-- .card-aside-wrap -->
                </div><!-- .card -->
            </div><!-- .nk-block -->
        </div>
    </div>
</div>

<?php echo $__env->make('author.profile.modals.edit-profile', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('author.profile.modals.change-password', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('author.profile.modals.upload-avatar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('author.profile.modals.payment-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Handle all profile forms (personal, address, social)
    $('#profile-form, #address-form, #social-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '<?php echo e(route("author.profile.update")); ?>',
            method: 'PATCH',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = 'Please fix the following errors:\n';
                    Object.keys(errors).forEach(key => {
                        errorMessage += `• ${errors[key][0]}\n`;
                    });
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: errorMessage
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating profile.'
                    });
                }
            }
        });
    });

    // Password change form
    $('#password-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '<?php echo e(route("author.profile.password.update")); ?>',
            method: 'PUT',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                    $('#password-change').modal('hide');
                    $('#password-form')[0].reset();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'An error occurred while updating password.'
                });
            }
        });
    });

    // Avatar upload form
    $('#avatar-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '<?php echo e(route("author.profile.avatar.update")); ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                let errorMessage = 'An error occurred while uploading avatar.';
                
                if (response?.errors?.avatar) {
                    errorMessage = response.errors.avatar[0];
                } else if (response?.message) {
                    errorMessage = response.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });

    // Password visibility toggle
    $('.passcode-switch').on('click', function(e) {
        e.preventDefault();
        const target = $(this).attr('data-target');
        const input = $('#' + target);
        const type = input.attr('type');
        
        if (type === 'password') {
            input.attr('type', 'text');
            $(this).find('.icon-show').hide();
            $(this).find('.icon-hide').show();
        } else {
            input.attr('type', 'password');
            $(this).find('.icon-show').show();
            $(this).find('.icon-hide').hide();
        }
    });
    
    // Handle payment details form submission for non-JavaScript users
    // This is just for consistency - the form will submit normally without JavaScript
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.author', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/author/profile/index.blade.php ENDPATH**/ ?>