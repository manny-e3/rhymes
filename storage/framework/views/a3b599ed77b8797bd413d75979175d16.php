<?php $__env->startSection('title', $book->title . ' | Book Details'); ?>

<?php $__env->startSection('page-title', 'Book Details'); ?>

<?php $__env->startSection('page-description', 'Review and manage book information'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title"><?php echo e($book->title); ?></h3>
                        <div class="nk-block-des text-soft">
                            <p>Book ID: #<?php echo e($book->id); ?> • Submitted <?php echo e($book->created_at->format('M d, Y')); ?></p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <?php if($book->status === 'pending_review'): ?>
                                        <li><a href="javascript:void(0)" class="btn btn-success" onclick="reviewBook(<?php echo e($book->id); ?>, 'approved_awaiting_delivery'); return false;"><em class="icon ni ni-check"></em><span>Approve</span></a></li>
                                        <li><a href="javascript:void(0)" class="btn btn-danger" onclick="reviewBook(<?php echo e($book->id); ?>, 'rejected'); return false;"><em class="icon ni ni-cross"></em><span>Reject</span></a></li>
                                    <?php endif; ?>
                                    <li><a href="<?php echo e(route('admin.books.index')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-arrow-left"></em><span>Back to Books</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- Book Information -->
                    <div class="col-xxl-8">
                        <div class="card card-bordered card-full mb-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Book Information</h6>
                                    </div>
                                    <div class="card-tools">
                                        <?php if($book->status === 'pending_review'): ?>
                                            <span class="badge badge-warning">Pending Review</span>
                                        <?php elseif($book->status === 'approved_awaiting_delivery'): ?>
                                            <span class="badge badge-success">Approved - Awaiting Delivery</span>
                                        <?php elseif($book->status === 'rejected'): ?>
                                            <span class="badge badge-danger">Rejected</span>
                                        <?php elseif($book->status === 'stocked'): ?>
                                            <span class="badge badge-info">Stocked</span>
                                        <?php elseif($book->status === 'edited_pending_approval'): ?>
                                            <span class="badge badge-warning">Edited - Awaiting Approval</span>
                                        <?php elseif($book->status === 'retrieval_requested'): ?>
                                            <span class="badge badge-warning">Retrieval Requested</span>
                                        <?php elseif($book->status === 'recalled'): ?>
                                            <span class="badge badge-danger">Retrieved</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="form-group text-center">
                                            <label class="form-label">Book Cover</label>
                                            <div class="mt-2">
                                                <?php if($book->image): ?>
                                                    <img src="<?php echo e(Storage::url($book->image)); ?>" alt="<?php echo e($book->title); ?>" class="img-thumbnail" style="max-height: 300px;">
                                                    <div class="mt-2">
                                                        <a href="<?php echo e(Storage::url($book->image)); ?>" class="btn btn-sm btn-outline-primary" download>
                                                            <em class="icon ni ni-download-cloud"></em><span>Download Image</span>
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="p-4 bg-light text-soft">No image uploaded</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Title</label>
                                            <div class="form-control-plaintext"><?php echo e($book->title); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Genre</label>
                                            <div class="form-control-plaintext"><?php echo e($book->genre); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Price</label>
                                            <div class="form-control-plaintext">₦<?php echo e(number_format($book->price, 2)); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Language</label>
                                            <div class="form-control-plaintext"><?php echo e($book->language ?? 'Not specified'); ?></div>
                                        </div>
                                    </div>
                                    <?php if($book->isbn): ?>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">ISBN</label>
                                                <div class="form-control-plaintext"><?php echo e($book->isbn); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($book->rev_book_id): ?>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">REV Book ID</label>
                                                <div class="form-control-plaintext"><?php echo e($book->rev_book_id); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Description</label>
                                            <div class="form-control-plaintext"><?php echo e($book->description ?: 'No description provided'); ?></div>
                                        </div>
                                    </div>
                                    <?php if($book->admin_notes): ?>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Admin Notes</label>
                                                <div class="alert alert-info"><?php echo e($book->admin_notes); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Retrieval Request Information -->
                                    <?php if($book->status === 'retrieval_requested' || ($book->status === 'retrieved' && $book->retrieval_location)): ?>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Retrieval Request</label>
                                                <div class="alert alert-warning">
                                                    <div class="alert-body">
                                                        <h6>Retrieval Requested</h6>
                                                        <p><strong>Location:</strong> <?php echo e($book->retrieval_location); ?></p>
                                                        <p><strong>Quantity:</strong> <?php echo e($book->retrieval_quantity); ?> copies</p>
                                                        <?php if($book->retrieval_reason): ?>
                                                            <p><strong>Reason:</strong> <?php echo e($book->retrieval_reason); ?></p>
                                                        <?php else: ?>
                                                            <p><strong>Reason:</strong> No reason provided</p>
                                                        <?php endif; ?>
                                                        
                                                        <?php if($book->status === 'retrieval_requested'): ?>
                                                            <div class="mt-3">
                                                                <button class="btn btn-success" onclick="handleRetrieval(<?php echo e($book->id); ?>, 'approve')">Approve Retrieval</button>
                                                                <button class="btn btn-danger" onclick="handleRetrieval(<?php echo e($book->id); ?>, 'deny')">Deny Retrieval</button>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Original vs New Data Comparison for edited books -->
                                    <?php if($book->status === 'edited_pending_approval' && $book->original_data): ?>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Changes Comparison</label>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Field</th>
                                                                <th>Original Value</th>
                                                                <th>New Value</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td><strong>Title</strong></td>
                                                                <td><?php echo e($book->original_data['title'] ?? 'N/A'); ?></td>
                                                                <td><?php echo e($book->title); ?></td>
                                                                <td>
                                                                    <?php if(($book->original_data['title'] ?? '') !== $book->title): ?>
                                                                        <span class="badge bg-warning">Changed</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-success">Unchanged</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>ISBN</strong></td>
                                                                <td><?php echo e($book->original_data['isbn'] ?? 'N/A'); ?></td>
                                                                <td><?php echo e($book->isbn); ?></td>
                                                                <td>
                                                                    <?php if(($book->original_data['isbn'] ?? '') !== $book->isbn): ?>
                                                                        <span class="badge bg-warning">Changed</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-success">Unchanged</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Genre</strong></td>
                                                                <td><?php echo e($book->original_data['genre'] ?? 'N/A'); ?></td>
                                                                <td><?php echo e($book->genre); ?></td>
                                                                <td>
                                                                    <?php if(($book->original_data['genre'] ?? '') !== $book->genre): ?>
                                                                        <span class="badge bg-warning">Changed</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-success">Unchanged</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Price</strong></td>
                                                                <td>₦<?php echo e(number_format($book->original_data['price'] ?? 0, 2)); ?></td>
                                                                <td>₦<?php echo e(number_format($book->price, 2)); ?></td>
                                                                <td>
                                                                    <?php if(($book->original_data['price'] ?? 0) !== $book->price): ?>
                                                                        <span class="badge bg-warning">Changed</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-success">Unchanged</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Type</strong></td>
                                                                <td><?php echo e(ucwords(str_replace('_', ' ', $book->original_data['book_type'] ?? 'N/A'))); ?></td>
                                                                <td><?php echo e(ucwords(str_replace('_', ' ', $book->book_type))); ?></td>
                                                                <td>
                                                                    <?php if(($book->original_data['book_type'] ?? '') !== $book->book_type): ?>
                                                                        <span class="badge bg-warning">Changed</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-success">Unchanged</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Description</strong></td>
                                                                <td><?php echo e(Str::limit($book->original_data['description'] ?? '', 50)); ?></td>
                                                                <td><?php echo e(Str::limit($book->description, 50)); ?></td>
                                                                <td>
                                                                    <?php if(($book->original_data['description'] ?? '') !== $book->description): ?>
                                                                        <span class="badge bg-warning">Changed</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-success">Unchanged</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Performance -->
                        <?php if($book->status === 'stocked' && $stats['total_sales'] > 0): ?>
                            <div class="card card-bordered card-full">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">Sales Performance</h6>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-4">
                                        <div class="col-sm-4">
                                            <div class="statbox">
                                                <div class="inbox-item">
                                                    <div class="inbox-item-img">
                                                        <div class="inbox-item-img bg-primary-dim">
                                                            <em class="icon ni ni-cart"></em>
                                                        </div>
                                                    </div>
                                                    <div class="inbox-item-body">
                                                        <p class="inbox-item-text">Total Sales</p>
                                                        <h4 class="inbox-item-title"><?php echo e(number_format($stats['total_sales'])); ?></h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="statbox">
                                                <div class="inbox-item">
                                                    <div class="inbox-item-img">
                                                        <div class="inbox-item-img bg-success-dim">
                                                            <em class="icon ni ni-coins"></em>
                                                        </div>
                                                    </div>
                                                    <div class="inbox-item-body">
                                                        <p class="inbox-item-text">Total Revenue</p>
                                                        <h4 class="inbox-item-title">₦<?php echo e(number_format($stats['total_revenue'], 2)); ?></h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="statbox">
                                                <div class="inbox-item">
                                                    <div class="inbox-item-img">
                                                        <div class="inbox-item-img bg-info-dim">
                                                            <em class="icon ni ni-bar-chart"></em>
                                                        </div>
                                                    </div>
                                                    <div class="inbox-item-body">
                                                        <p class="inbox-item-text">Avg. Sale Price</p>
                                                        <h4 class="inbox-item-title">₦<?php echo e(number_format($stats['average_sale_price'], 2)); ?></h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Author Information & Actions -->
                    <div class="col-xxl-4">
                        <div class="card card-bordered card-full mb-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Author Information</h6>
                                    </div>
                                </div>
                                
                                <div class="user-card">
                                    <div class="user-avatar lg bg-primary">
                                        <span><?php echo e(strtoupper(substr($book->user->name, 0, 2))); ?></span>
                                    </div>
                                    <div class="user-info">
                                        <h5><?php echo e($book->user->name); ?></h5>
                                        <span class="sub-text"><?php echo e($book->user->email); ?></span>
                                    </div>
                                </div>
                                
                                <div class="user-meta mt-4">
                                    <ul class="nk-list-meta">
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Total Books:</span>
                                            <span class="nk-list-meta-value"><?php echo e($book->user->books->count()); ?></span>
                                        </li>
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Published:</span>
                                            <span class="nk-list-meta-value"><?php echo e($book->user->books->where('status', 'accepted')->count()); ?></span>
                                        </li>
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Member Since:</span>
                                            <span class="nk-list-meta-value"><?php echo e($book->user->created_at->format('M Y')); ?></span>
                                        </li>
                                    </ul>
                                </div>
                                
                                <div class="mt-4">
                                    <a href="<?php echo e(route('admin.users.show', $book->user)); ?>" class="btn btn-outline-primary btn-block">View Author Profile</a>
                                </div>
                            </div>
                        </div>

                        <!-- Review Actions -->
                        <?php if($book->status === 'pending_review'): ?>
                            <div class="card card-bordered card-full">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">Review Actions</h6>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-warning">
                                        <div class="alert-cta">
                                            <h6>Pending Review</h6>
                                            <p>This book is waiting for your review. Please approve or reject it.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <button class="btn btn-success btn-block" onclick="reviewBook(<?php echo e($book->id); ?>, 'approved_awaiting_delivery')">
                                                <em class="icon ni ni-check"></em><span>Approve</span>
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-danger btn-block" onclick="reviewBook(<?php echo e($book->id); ?>, 'rejected')">
                                                <em class="icon ni ni-cross"></em><span>Reject</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php elseif($book->status === 'edited_pending_approval'): ?>
                            <div class="card card-bordered card-full">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">Book Status</h6>
                                        </div>
                                    </div>
                                                            
                                    <div class="alert alert-info">
                                        <div class="alert-cta">
                                            <h6>Edited - Notified</h6>
                                            <p>This book has been edited by the author. An admin has been notified of the changes.</p>
                                        </div>
                                    </div>
                                                            
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <button class="btn btn-primary btn-block" onclick="reviewBook(<?php echo e($book->id); ?>, 'stocked')">
                                                <em class="icon ni ni-edit"></em><span>Edit Status</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Book Timeline -->
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Book Timeline</h6>
                                    </div>
                                </div>
                                
                                <ul class="nk-activity">
                                    <li class="nk-activity-item">
                                        <div class="nk-activity-media user-avatar bg-primary">
                                            <em class="icon ni ni-plus"></em>
                                        </div>
                                        <div class="nk-activity-data">
                                            <div class="label">Book submitted</div>
                                            <span class="time"><?php echo e($book->created_at->format('M d, Y \a\t g:i A')); ?></span>
                                        </div>
                                    </li>
                                    
                                    <?php if($book->updated_at != $book->created_at): ?>
                                        <li class="nk-activity-item">
                                            <div class="nk-activity-media user-avatar bg-info">
                                                <em class="icon ni ni-edit"></em>
                                            </div>
                                            <div class="nk-activity-data">
                                                <div class="label">Book updated</div>
                                                <span class="time"><?php echo e($book->updated_at->format('M d, Y \a\t g:i A')); ?></span>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php if($book->status !== 'pending_review'): ?>
                                        <li class="nk-activity-item">
                                            <div class="nk-activity-media user-avatar <?php echo e(in_array($book->status, ['approved_awaiting_delivery', 'stocked', 'edited_pending_approval']) ? 'bg-success' : 'bg-danger'); ?>">
                                                <em class="icon ni ni-<?php echo e(in_array($book->status, ['approved_awaiting_delivery', 'stocked', 'edited_pending_approval']) ? 'check' : 'cross'); ?>"></em>
                                            </div>
                                            <div class="nk-activity-data">
                                                <div class="label">Book <?php echo e(in_array($book->status, ['approved_awaiting_delivery', 'stocked', 'edited_pending_approval']) ? 'status updated' : 'rejected'); ?></div>
                                                <span class="time"><?php echo e($book->updated_at->format('M d, Y \a\t g:i A')); ?></span>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" tabindex="-1" id="reviewModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Book</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="bookId" name="book_id">
                    <input type="hidden" id="reviewStatus" name="status">
                    
                    <div class="form-group">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Optional notes for the author..."></textarea>
                    </div>
                    
                    <div class="form-group" id="revBookIdGroup" style="display: none;">
                        <label class="form-label">REV Book ID</label>
                        <input type="text" class="form-control" name="rev_book_id" placeholder="Enter REV system book ID">
                        <div class="form-note">Required when approving books for REV system integration.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitReview()">Submit Review</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Store the route URL in a JavaScript variable
const reviewBookRoute = "<?php echo e(route('admin.books.review', ['book' => 'BOOK_ID_PLACEHOLDER'])); ?>";

function reviewBook(bookId, status) {
    // Prevent default action
    event.preventDefault();
    
    document.getElementById('bookId').value = bookId;
    document.getElementById('reviewStatus').value = status;
    
    // Show REV Book ID field only for stocked status
    const revBookIdGroup = document.getElementById('revBookIdGroup');
    if (status === 'stocked') {
        revBookIdGroup.style.display = 'block';
    } else {
        revBookIdGroup.style.display = 'none';
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
    modal.show();
}

function submitReview() {
    // Prevent default action
    event.preventDefault();
    
    // Get the values directly from the hidden fields
    const bookId = document.getElementById('bookId').value;
    const status = document.getElementById('reviewStatus').value;
    const adminNotes = document.querySelector('textarea[name="admin_notes"]').value;
    const revBookId = document.querySelector('input[name="rev_book_id"]').value;
    
    // Validate that we have the required data
    if (!bookId || !status) {
        Swal.fire('Error!', 'Missing required data.', 'error');
        return;
    }
    
    // Log the data being sent
    console.log('Sending review data:', {
        book_id: bookId,
        status: status,
        admin_notes: adminNotes,
        rev_book_id: revBookId
    });
    
    // Create the data object
    const data = {
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        status: status
    };
    
    // Add optional fields if they have values
    if (adminNotes) data.admin_notes = adminNotes;
    if (revBookId) data.rev_book_id = revBookId;
    
    // Generate the proper URL using the route pattern
    const url = reviewBookRoute.replace('BOOK_ID_PLACEHOLDER', bookId);
    
    fetch(url, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            Swal.fire('Success!', data.message, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error!', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        Swal.fire('Error!', 'Something went wrong: ' + error.message, 'error');
    });
    
    // Hide modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
    modal.hide();
}
    /**
     * Handle retrieval actions (approve/deny)
     */
    function handleRetrieval(bookId, action) {
        const actionText = action === 'approve' ? 'approve' : 'deny';
        const confirmBtnClass = action === 'approve' ? 'btn-success' : 'btn-danger';
        
        Swal.fire({
            title: `Are you sure?`,
            text: `You are about to ${actionText} this retrieval request.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: `Yes, ${actionText} it!`,
            confirmButtonColor: action === 'approve' ? '#1ee0ac' : '#e85347',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/admin/books/${bookId}/retrieval-action`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ action: action })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        return data;
                    } else {
                        throw new Error(data.message || `Failed to ${actionText} retrieval request`);
                    }
                })
                .catch(error => {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Success!', result.value.message, 'success').then(() => {
                    location.reload();
                });
            }
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/rovinghe/author.rovingheights.com/resources/views/admin/books/show.blade.php ENDPATH**/ ?>