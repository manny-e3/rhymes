<?php $__env->startSection('title', 'My Books | Rhymes Author Platform'); ?>
<?php $__env->startSection('page-title', 'My Books'); ?>
<?php $__env->startSection('page-description', 'Manage your books here'); ?>
<?php $__env->startSection('content'); ?>
                <!-- main header @e -->
                <!-- content @s -->
                <div class="nk-content nk-content-fluid">
                    <div class="container-xl wide-xl">
                        <div class="nk-content-body">
                            <div class="components-preview wide-xl mx-auto">
                                <div class="nk-block-head nk-block-head-lg wide-sm">
                                   
                                </div><!-- .nk-block-head -->
                                <div class="nk-block nk-block-lg">
                                     <div class="nk-block-head nk-block-head-sm">
                                <div class="nk-block-between g-3">
                                    <div class="nk-block-head-content">
                                        <h3 class="nk-block-title page-title">Books </h3>
                                        <div class="nk-block-des text-soft">
                                            <p>List of books you have created.</p>
                                        </div>
                                    </div>
                                    <div class="nk-block-head-content">
                                        <a href="<?php echo e(route('author.books.create')); ?>"  class="btn btn-primary d-none d-sm-inline-flex"><em class="icon ni ni-plus"></em><span>Create New</span></a>
                                        <a href="<?php echo e(route('author.books.create')); ?>" class="btn btn-icon btn-primary d-inline-flex d-sm-none"><em class="icon ni ni-plus"></em></a>
                                    </div>
                                </div>
                            </div><!-- .nk-block-head -->
                                    <div class="card card-preview">
                                        <div class="card-inner">
                                              <table class="datatable-init-export nowrap table" data-export-title="Export">
                                                <thead>
                                                    <tr>
                                                        <th>S/N</th>
                                                        <th>Cover</th>
                                                        <th>Book Details</th>
                                                        <th>ISBN</th>
                                                        <th>Type</th>
                                        
                                                        <th>Price</th>
                                                        <th>Status</th>
                                                        <th>Quantity</th>
                                                        <th>Submitted</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr class="nk-tb-item">
                                                        <td class="nk-tb-col">
                                                            <span><?php echo e($loop->iteration); ?></span>
                                                        </td>
                                                        <td class="nk-tb-col">
                                                            <div class="user-avatar bg-light border">
                                                                <?php if($book->image): ?>
                                                                    <img src="<?php echo e(asset($book->image)); ?>" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                                <?php else: ?>
                                                                    <em class="icon ni ni-book-read text-soft"></em>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                            
                                                        <td class="nk-tb-col">
                                                            <div class="user-card">
                                                                <div class="user-info">
                                                                    <span class="tb-lead"><?php echo e($book->title); ?> <span class="dot dot-success d-md-none ms-1"></span></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                            
                                                        <td class="nk-tb-col tb-col-mb">
                                                            <?php echo e($book->isbn); ?>

                                                        </td>
                                            
                                                        <td class="nk-tb-col tb-col-mb">
                                                            <?php echo e(ucwords(str_replace('_', ' ', $book->book_type))); ?>

                                                        </td>
                                            
                                                        
                                            
                                                        <td class="nk-tb-col tb-col-md">
                                                            <span class="tb-amount">₦<?php echo e(number_format($book->price, 2)); ?></span>
                                                        </td>
                                            
                                                        <td class="nk-tb-col tb-col-lg">
                                                            <ul class="list-status">
                                                                <?php if($book->status === 'pending_review'): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-warning">Pending Review</span>
                                            <?php elseif($book->status === 'send_review_copy'): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-info">Send Review Copy</span>
                                            <?php elseif($book->status === 'approved_awaiting_delivery'): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-success">Approved - Awaiting Delivery</span>
                                            <?php elseif($book->status === 'rejected'): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-danger">Rejected</span>
                                            <?php elseif($book->status === 'stocked'): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-info">Stocked</span>
                                            <?php elseif($book->status === 'edited_pending_approval'): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-warning">Edited - Awaiting Approval</span>
                                            <?php elseif($book->status === 'retrieval_requested'): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-warning">Retrieval Requested</span>
                                            <?php elseif($book->status === 'retrieved'): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-danger">Retrieved</span>
                                            <?php endif; ?>
                                           
                                            <?php if($book->trashed()): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-secondary">Deleted</span>
                                            <?php endif; ?>
                                                            </ul>
                                                        </td>
                                            
                                                        <td class="nk-tb-col tb-col-lg">
                                                            <?php if($book->status === 'stocked' && !is_null($book->quantity)): ?>
                                                                <?php
                                                                    $copiesSold = $book->walletTransactions->where('type', 'sale')->sum(function ($t) {
                                                                        return $t->meta['quantity_sold'] ?? $t->meta['QuantitySold'] ?? 1;
                                                                    });
                                                                ?>
                                                                <span class="tb-amount"><?php echo e($book->quantity + $copiesSold); ?></span>
                                                                <?php if($copiesSold > 0): ?>
                                                                    <span class="tb-sub" style="font-size: 11px; color: #e6820e; font-weight: 600;">(<?php echo e($book->quantity); ?> remaining)</span>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                <span class="tb-sub">N/A</span>
                                                            <?php endif; ?>
                                                        </td>
                                            
                                                        <td class="nk-tb-col tb-col-lg">
                                                            <span><?php echo e(optional($book->created_at)->format('M d, Y')); ?></span>
                                                        </td>
                                            
                                                        <td class="nk-tb-col nk-tb-col-tools">
                                                            <ul class="nk-tb-actions gx-1">
                                                                <li>
                                                                    <div class="dropdown">
                                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                                            <em class="icon ni ni-more-h"></em>
                                                                        </a>
                                                                        <div class="dropdown-menu dropdown-menu-end">
                                                                            <ul class="link-list-opt no-bdr">
                                                                                <li>
                                                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#viewBook-<?php echo e($book->id); ?>">
                                                                                        <em class="icon ni ni-eye"></em>
                                                                                        <span>View Book</span>
                                                                                    </a>
                                                                                </li>

                                                                                 <?php if($book->status === 'stocked'): ?>
                                                                                 <li>
                                                                                             <a href="<?php echo e(route('author.books.edit', $book->id)); ?>">
                                                                                                 <em class="icon ni ni-edit-fill"></em>
                                                                                                 <span>Edit Book</span>
                                                                                             </a>
                                                                                         </li>
                                                                                        <?php endif; ?>
                                                                                        <?php if($book->status === 'stocked' &&  $book->status !== 'retrieval_requested' && $book->status !== 'retrieved'): ?>
                                                                                        <li class="divider"></li>
                                                                                        <li>
                                                                                            <a href="#" onclick="requestBookRetrieval(<?php echo e($book->id); ?>); return false;">
                                                                                                <em class="icon ni ni-exclamation-circle"></em>
                                                                                                <span>Retrieval of Book</span>
                                                                                            </a>
                                                                                        </li>
                                                                                        <?php endif; ?>

                                                                                <?php if($book->trashed()): ?>
                                                                                    <li>
                                                                                        <a href="#" onclick="restoreBook(<?php echo e($book->id); ?>, '<?php echo e($book->title); ?>'); return false;">
                                                                                            <em class="icon ni ni-reload"></em>
                                                                                            <span>Restore</span>
                                                                                        </a>
                                                                                    </li>
                                                                                <?php else: ?>
                                                                                    <?php if($book->status === 'pending_review'): ?>
                                                    
                                                                                    <?php endif; ?>
                                                                                         <?php if($book->status === 'rejected'): ?>
                                                                                         <li>
                                                                                             <a href="<?php echo e(route('author.books.edit', $book->id)); ?>">
                                                                                                 <em class="icon ni ni-edit"></em>
                                                                                                 <span>Edit & Resubmit</span>
                                                                                             </a>
                                                                                         </li>

                                                                                        <li class="divider"></li>
                                                                                        <li>
                                                                                            <a href="#" onclick="deleteBook(<?php echo e($book->id); ?>, '<?php echo e($book->title); ?>'); return false;">
                                                                                                <em class="icon ni ni-trash"></em>
                                                                                                <span>Delete</span>
                                                                                            </a>
                                                                                        </li>
                                                                                    <?php endif; ?>
                                                                                <?php endif; ?>
                                                                            </ul> 
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                            
                                        </div>
                                    </div><!-- .card-preview -->
                                </div> <!-- nk-block -->
                               
                               
                            </div><!-- .components-preview -->
                        </div>
                    </div>
                </div>
                <!-- content @e -->
                <!-- footer @s -->


      

  

    <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <!-- View Book Modal -->
    <div class="modal fade" id="viewBook-<?php echo e($book->id); ?>" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book Details</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <div class="card card-bordered h-100">
                                <div class="card-inner d-flex align-items-center justify-content-center p-2 bg-light" style="min-height: 250px;">
                                    <?php if($book->image): ?>
                                        <img src="<?php echo e(asset($book->image)); ?>" class="rounded shadow-sm" alt="<?php echo e($book->title); ?>" style="max-width: 100%; max-height: 350px; object-fit: contain;">
                                    <?php else: ?>
                                        <div class="text-center text-soft">
                                            <em class="icon ni ni-book-read" style="font-size: 64px;"></em>
                                            <p class="mt-2">No Cover Available</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">ISBN</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="<?php echo e($book->isbn); ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Book Title</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="<?php echo e($book->title); ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Genre</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="<?php echo e($book->genre); ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Price</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="₦<?php echo e(number_format($book->price, 2)); ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Book Type</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="<?php echo e(ucwords(str_replace('_', ' ', $book->book_type))); ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Status</label>
                                                <div class="form-control-wrap">
                                                    <span class="badge badge-sm 
                                                        <?php switch($book->status):
                                                            case ('pending_review'): ?> badge-dim bg-warning <?php break; ?>
                                                            <?php case ('send_review_copy'): ?> badge-dim bg-warning <?php break; ?>
                                                            <?php case ('approved_awaiting_delivery'): ?> badge-dim bg-success <?php break; ?>
                                                            <?php case ('stocked'): ?> badge-dim bg-success <?php break; ?>
                                                            <?php case ('edited_pending_approval'): ?> badge-dim bg-warning <?php break; ?>
                                                            <?php case ('retrieval_requested'): ?> badge-dim bg-warning <?php break; ?>
                                                            <?php case ('retrieved'): ?> badge-dim bg-secondary <?php break; ?>
                                                            <?php case ('rejected'): ?> badge-dim bg-danger <?php break; ?>
                                                        <?php endswitch; ?>
                                                    "><?php echo e(ucfirst(str_replace('_', ' ', $book->status))); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($book->status === 'stocked' && !is_null($book->quantity)): ?>
                                             <?php
                                                 $copiesSold = $book->walletTransactions->where('type', 'sale')->sum(function ($t) {
                                                     return $t->meta['quantity_sold'] ?? $t->meta['QuantitySold'] ?? 1;
                                                 });
                                                 $initialQty = $book->quantity + $copiesSold;
                                             ?>
                                             <div class="col-md-6">
                                                 <div class="form-group">
                                                     <label class="form-label">Quantity</label>
                                                     <div class="form-control-wrap">
                                                          <input type="text" class="form-control" value="<?php echo e($initialQty); ?> copies" readonly>
                                                          <?php if($copiesSold > 0): ?>
                                                              <small style="color: #e6820e; font-weight: 600;">(<?php echo e($book->quantity); ?> remaining)</small>
                                                          <?php endif; ?>
                                                     </div>
                                                 </div>
                                             </div>
                                        <?php endif; ?>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Description</label>
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control" rows="6" readonly><?php echo e($book->description); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Submitted Date</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="<?php echo e($book->created_at->format('M d, Y h:i A')); ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Last Updated</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="<?php echo e($book->updated_at->format('M d, Y h:i A')); ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($book->admin_notes): ?>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Admin Notes</label>
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control bg-light" rows="3" readonly><?php echo e($book->admin_notes); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
    </form>

 <?php $__env->stopSection(); ?>
  
<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Initialize Select2 for all genre dropdowns
        $('#genre').select2({
            placeholder: "Select Genre",
            allowClear: true,
            width: '100%'
        });
    });
    
    // Delete book function with SweetAlert confirmation
    function deleteBook(bookId, bookTitle) {
        confirmAction(`Are you sure you want to delete the book "${bookTitle}"? This action cannot be undone.`, function() {
            const form = document.getElementById('deleteForm');
            form.action = `/author/books/${bookId}`;
            form.submit();
        });
    }
    
    // Restore book function with SweetAlert confirmation
    function restoreBook(bookId, bookTitle) {
        confirmAction(`Are you sure you want to restore the book "${bookTitle}"?`, function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/author/books/${bookId}/restore`;
            
            const csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'POST';
            
            form.appendChild(csrfField);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        });
    }
    
    /**
     * Request book retrieval
     */
    function requestBookRetrieval(bookId) {
        Swal.fire({
            title: 'Retrieval of Book',
            html: `
                <div class="text-start mb-3">
                    <label class="form-label" for="retrieval_location">Location for Retrieval <span class="text-danger">*</span></label>
                    <input type="text" id="retrieval_location" class="swal2-input m-0 w-100" placeholder="e.g. Lagos Warehouse">
                </div>
                <div class="text-start mb-3">
                    <label class="form-label" for="retrieval_quantity">Quantity to Retrieve <span class="text-danger">*</span></label>
                    <input type="number" id="retrieval_quantity" class="swal2-input m-0 w-100" placeholder="Quantity" min="1">
                </div>
                <div class="text-start">
                    <label class="form-label" for="recall_reason">Reason (Optional)</label>
                    <textarea id="recall_reason" class="swal2-textarea m-0 w-100" placeholder="Optional reason..."></textarea>
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Submit Request',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const location = document.getElementById('retrieval_location').value;
                const quantity = document.getElementById('retrieval_quantity').value;
                const reason = document.getElementById('recall_reason').value;
                
                if (!location) {
                    Swal.showValidationMessage('Retrieval location is required');
                    return false;
                }
                if (!quantity || quantity < 1) {
                    Swal.showValidationMessage('A valid quantity is required');
                    return false;
                }
                
                return {
                    retrieval_location: location,
                    retrieval_quantity: quantity,
                    recall_reason: reason
                };
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable all recall buttons
                const buttons = document.querySelectorAll('a[onclick^="requestBookRetrieval"]');
                buttons.forEach(button => {
                    button.disabled = true;
                    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                });
                
                fetch(`/author/books/${bookId}/retrieval`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(result.value)
                })
                .then(response => response.json())
                .then(data => {
                    // Re-enable buttons
                    buttons.forEach(button => {
                        button.disabled = false;
                        button.innerHTML = 'Retrieval of Book';
                    });
                    
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                        }).then(() => {
                            // Reload the page on success
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'An error occurred while requesting the retrieval.',
                            icon: 'error',
                            confirmButtonColor: '#d33',
                        });
                    }
                })
                .catch(error => {
                    console.error('Retrieval request error:', error);
                    
                    // Re-enable buttons
                    buttons.forEach(button => {
                        button.disabled = false;
                        button.innerHTML = 'Retrieval of Book';
                    });
                    
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while sending the retrieval request.',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                    });
                });
            }
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.author', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/rovinghe/author.rovingheights.com/resources/views/author/books/index.blade.php ENDPATH**/ ?>