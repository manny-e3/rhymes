<!-- Profile Edit Modal -->
<div class="modal fade" tabindex="-1" id="profile-edit" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
            <div class="modal-body modal-body-lg">
                <h5 class="title">Update Profile</h5>
                <form id="profile-edit-modal-form" class="form-validate is-alter">
                    <?php echo csrf_field(); ?>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="full-name">Full Name</label>
                                <input type="text" class="form-control form-control-lg" id="full-name" name="name" value="<?php echo e($user->name); ?>" placeholder="Enter Full name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="phone-no">Phone Number</label>
                                <input type="text" class="form-control form-control-lg" id="phone-no" name="phone" value="<?php echo e($user->phone ?? ''); ?>" placeholder="Phone Number">
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="phone-no">Bio</label>
                                <textarea type="text" class="form-control form-control-lg" id="phone-no" name="account_description" placeholder="Description"><?php echo e($user->account_description ?? ''); ?></textarea>
                            </div>
                        </div>


                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="address-line-1">Address</label>
                                <input type="text" class="form-control form-control-lg" id="address-line-1" name="address" value="<?php echo e($user->address); ?>" placeholder="Enter your address">
                            </div>
                        </div>
                        <div class="col-12">
                            <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                                <li>
                                    <button type="submit" class="btn btn-lg btn-primary">Update Profile</button>
                                </li>
                                <li>
                                    <a href="#" data-bs-dismiss="modal" class="link link-light">Cancel</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Check if form exists before binding event
        if ($('#profile-edit-modal-form').length > 0) {
            
            // Handle profile form submission from modal
            $('#profile-edit-modal-form').on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
        
                // Get CSRF token from meta tag or input field
                const csrfToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
                
                if (!csrfToken) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Security token missing. Please refresh the page.'
                    });
                    return;
                }
                
                // Show loading indicator
                const submitBtn = $('#profile-edit-modal-form').find('button[type="submit"]');
                const originalText = submitBtn.text();
                submitBtn.prop('disabled', true).text('Updating...');
                
                $.ajax({
                    url: '<?php echo e(route("author.profile.update")); ?>',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-HTTP-Method-Override': 'PATCH'
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
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'An unknown error occurred.'
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
                                text: xhr.responseJSON?.message || `Server error (${xhr.status}). Please try again.`
                            });
                        }
                    },
                    complete: function() {
                        // Re-enable button
                        $('#profile-edit-modal-form button[type="submit"]').prop('disabled', false).text(originalText);
                    }
                });
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/author/profile/modals/edit-profile.blade.php ENDPATH**/ ?>