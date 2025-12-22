<?php $__env->startSection('title', 'Book Management | Admin Panel'); ?>

<?php $__env->startSection('page-title', 'Book Management'); ?>

<?php $__env->startSection('page-description', 'Review and manage all books on the platform'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Books Management</h3>
                        <div class="nk-block-des text-soft">
                            <p>Review, approve, and manage all books submitted by authors.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li>
                                        <div class="dropdown">
                                            <a class="btn btn-white btn-dim btn-outline-light dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                                <em class="icon ni ni-download-cloud"></em><span>Export</span>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="<?php echo e(route('admin.books.export.csv', request()->query())); ?>"><em class="icon ni ni-file-text"></em><span>Export as CSV</span></a>
                                                <a class="dropdown-item" href="<?php echo e(route('admin.books.export.pdf', request()->query())); ?>"><em class="icon ni ni-file-pdf"></em><span>Export as PDF</span></a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="card card-bordered card-stretch">
                    <div class="card-inner-group">
                        <div class="card-inner position-relative card-tools-toggle">
                            <div class="card-title-group">
                                <div class="card-tools">
                                    <div class="form-inline flex-nowrap gx-3">
                                        <form method="GET" action="<?php echo e(route('admin.books.index')); ?>" class="d-flex gap-2">
                                            <div class="form-wrap w-150px">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="">All Status</option>
                                                    <option value="pending_review" <?php echo e(request('status') === 'pending_review' ? 'selected' : ''); ?>>Pending Review</option>
                                                    <option value="send_review_copy" <?php echo e(request('status') === 'send_review_copy' ? 'selected' : ''); ?>>Send Review Copy</option>
                                                    <option value="approved_awaiting_delivery" <?php echo e(request('status') === 'approved_awaiting_delivery' ? 'selected' : ''); ?>>Approved - Awaiting Delivery</option>
                                                    <option value="rejected" <?php echo e(request('status') === 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                                                    <option value="stocked" <?php echo e(request('status') === 'stocked' ? 'selected' : ''); ?>>Stocked</option>
                                                </select>
                                            </div>
                                            <div class="form-wrap w-150px">
                                                <select name="genre" class="form-select form-select-sm">
                                                    <option value="">All Genres</option>
                                                    <?php $__currentLoopData = $genres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($genre); ?>" <?php echo e(request('genre') === $genre ? 'selected' : ''); ?>>
                                                            <?php echo e($genre); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="form-wrap flex-md-nowrap">
                                                <div class="form-icon form-icon-right">
                                                    <em class="icon ni ni-search"></em>
                                                </div>
                                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search books..." value="<?php echo e(request('search')); ?>">
                                            </div>
                                            <div class="btn-wrap">
                                                <button type="submit" class="btn btn-sm btn-icon btn-primary"><em class="icon ni ni-search"></em></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-inner p-0">
                            <div class="nk-tb-list nk-tb-ulist">
                                <div class="nk-tb-item nk-tb-head">
                                    <div class="nk-tb-col"><span class="sub-text">Book</span></div>
                                    <div class="nk-tb-col tb-col-mb"><span class="sub-text">Author</span></div>
                                    <div class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Sales</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Submitted</span></div>
                                    <div class="nk-tb-col nk-tb-col-tools text-end">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-xs btn-outline-light btn-icon dropdown-toggle" data-bs-toggle="dropdown"><em class="icon ni ni-plus"></em></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li>
                                                        <form method="POST" action="<?php echo e(route('admin.books.bulk-action')); ?>" style="display:inline;">
                                                            <?php echo csrf_field(); ?>
                                                            <input type="hidden" name="action" value="pending_review">
                                                            <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to set all selected books to Pending Review?')"><span>Set Pending Review</span></button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="<?php echo e(route('admin.books.bulk-action')); ?>" style="display:inline;">
                                                            <?php echo csrf_field(); ?>
                                                            <input type="hidden" name="action" value="send_review_copy">
                                                            <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to request review copies for all selected books?')"><span>Request Review Copies</span></button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="<?php echo e(route('admin.books.bulk-action')); ?>" style="display:inline;">
                                                            <?php echo csrf_field(); ?>
                                                            <input type="hidden" name="action" value="approve_delivery">
                                                            <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to approve all selected books for delivery?')"><span>Approve for Delivery</span></button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="<?php echo e(route('admin.books.bulk-action')); ?>" style="display:inline;">
                                                            <?php echo csrf_field(); ?>
                                                            <input type="hidden" name="action" value="stock">
                                                            <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to stock all selected books? This will register them with the ERP system.')"><span>Stock Books</span></button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="<?php echo e(route('admin.books.bulk-action')); ?>" style="display:inline;">
                                                            <?php echo csrf_field(); ?>
                                                            <input type="hidden" name="action" value="reject">
                                                            <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to reject all selected books?')"><span>Bulk Reject</span></button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary-dim">
                                                    <em class="icon ni ni-book"></em>
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead"><?php echo e($book->title); ?></span>
                                                    <span><?php echo e($book->genre); ?> • ₦<?php echo e(number_format($book->price, 2)); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col tb-col-mb">
                                            <span class="tb-lead-sub"><?php echo e($book->user->name); ?></span>
                                            <span class="tb-sub"><?php echo e($book->user->email); ?></span>
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
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
                                            <?php endif; ?>
                                            <?php if($book->trashed()): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-secondary">Deleted</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <?php
                                                $salesCount = $book->walletTransactions->where('type', 'sale')->count();
                                                $revenue = $book->walletTransactions->where('type', 'sale')->sum('amount');
                                            ?>
                                            <span class="tb-lead"><?php echo e($salesCount); ?></span>
                                            <span class="tb-sub">₦<?php echo e(number_format($revenue, 2)); ?></span>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span><?php echo e($book->created_at->format('M d, Y')); ?></span>
                                            <span class="tb-sub"><?php echo e($book->created_at->diffForHumans()); ?></span>
                                        </div>
                                        <div class="nk-tb-col nk-tb-col-tools">
                                            <ul class="nk-tb-actions gx-1">
                                                <li>
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li><a href="#" data-bs-toggle="modal" data-bs-target="#viewDetailsModal-<?php echo e($book->id); ?>"><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                <?php if($book->trashed()): ?>
                                                                    <li>
                                                                        <form method="POST" action="<?php echo e(route('admin.books.bulk-action')); ?>" style="display:inline;">
                                                                            <?php echo csrf_field(); ?>
                                                                            <input type="hidden" name="action" value="restore">
                                                                            <input type="hidden" name="book_ids[]" value="<?php echo e($book->id); ?>">
                                                                            <button type="submit" class="dropdown-item"><em class="icon ni ni-reload"></em><span>Restore</span></button>
                                                                        </form>
                                                                    </li>
                                                                    <li>
                                                                        <form method="POST" action="<?php echo e(route('admin.books.bulk-action')); ?>" style="display:inline;" onsubmit="return confirm('This action cannot be undone! The book will be permanently removed from the system.')">
                                                                            <?php echo csrf_field(); ?>
                                                                            <input type="hidden" name="action" value="forceDelete">
                                                                            <input type="hidden" name="book_ids[]" value="<?php echo e($book->id); ?>">
                                                                            <button type="submit" class="dropdown-item text-danger"><em class="icon ni ni-trash-fill"></em><span>Permanently Delete</span></button>
                                                                        </form>
                                                                    </li>
                                                                <?php else: ?>
                                                                    <?php if($book->status === 'pending_review'): ?>
                                                                        <li>
                                                                            <form method="POST" action="<?php echo e(route('admin.books.review', $book)); ?>" style="display:inline;">
                                                                                <?php echo csrf_field(); ?>
                                                                                <?php echo method_field('PATCH'); ?>
                                                                                <input type="hidden" name="status" value="send_review_copy">
                                                                                <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to request a review copy for this book?')"><em class="icon ni ni-mail"></em><span>Request Review Copy</span></button>
                                                                            </form>
                                                                        </li>
                                                                        <li>
                                                                            <form method="POST" action="<?php echo e(route('admin.books.review', $book)); ?>" style="display:inline;">
                                                                                <?php echo csrf_field(); ?>
                                                                                <?php echo method_field('PATCH'); ?>
                                                                                <input type="hidden" name="status" value="approved_awaiting_delivery">
                                                                                <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to approve this book for delivery?')"><em class="icon ni ni-check"></em><span>Approve for Delivery</span></button>
                                                                            </form>
                                                                        </li>
                                                                        <li>
                                                                            <form method="POST" action="<?php echo e(route('admin.books.review', $book)); ?>" style="display:inline;">
                                                                                <?php echo csrf_field(); ?>
                                                                                <?php echo method_field('PATCH'); ?>
                                                                                <input type="hidden" name="status" value="rejected">
                                                                                <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to reject this book?')"><em class="icon ni ni-cross"></em><span>Reject</span></button>
                                                                            </form>
                                                                        </li>
                                                                    <?php elseif($book->status === 'approved_awaiting_delivery'): ?>
                                                                        <li>
                                                                            <form method="POST" action="<?php echo e(route('admin.books.review', $book)); ?>" style="display:inline;">
                                                                                <?php echo csrf_field(); ?>
                                                                                <?php echo method_field('PATCH'); ?>
                                                                                <input type="hidden" name="status" value="stocked">
                                                                                <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to stock this book? This will register it with the ERP system.')"><em class="icon ni ni-package"></em><span>Stock Book</span></button>
                                                                            </form>
                                                                        </li>
                                                                    <?php else: ?>
                                                                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#reviewModal-<?php echo e($book->id); ?>"><em class="icon ni ni-edit"></em><span>Edit Status</span></a></li>
                                                                    <?php endif; ?>
                                                                    <li class="divider"></li>
                                                                    <li>
                                                                        <form method="POST" action="<?php echo e(route('admin.books.bulk-action')); ?>" style="display:inline;" onsubmit="return confirm('This action will soft delete the book. You can restore it later.')">
                                                                            <?php echo csrf_field(); ?>
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="book_ids[]" value="<?php echo e($book->id); ?>">
                                                                            <button type="submit" class="dropdown-item text-danger"><em class="icon ni ni-trash"></em><span>Delete</span></button>
                                                                        </form>
                                                                    </li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <div class="text-center py-4">
                                                <em class="icon ni ni-book" style="font-size: 3rem; opacity: 0.3;"></em>
                                                <p class="text-soft mt-2">No books found</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-inner">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Showing <?php echo e($books->firstItem()); ?> to <?php echo e($books->lastItem()); ?> of <?php echo e($books->total()); ?> entries
                                </div>
                                <?php if($books->hasPages()): ?>
                                    <div>
                                        <?php echo e($books->appends([
                                            'status' => request('status', ''),
                                            'genre' => request('genre', ''),
                                            'search' => request('search', '')
                                        ])->links('vendor.pagination.bootstrap-4')); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<!-- Review Modal -->
<div class="modal fade" tabindex="-1" id="reviewModal-<?php echo e($book->id); ?>" aria-labelledby="reviewModalLabel-<?php echo e($book->id); ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel-<?php echo e($book->id); ?>">Review Book: <?php echo e($book->title); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reviewForm-<?php echo e($book->id); ?>" method="POST" action="<?php echo e(route('admin.books.review', $book)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Author:</strong> <?php echo e($book->user->name); ?></p>
                            <p><strong>Email:</strong> <?php echo e($book->user->email); ?></p>
                            <p><strong>Genre:</strong> <?php echo e($book->genre); ?></p>
                            <p><strong>Price:</strong> ₦<?php echo e(number_format($book->price, 2)); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
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
                                <?php endif; ?>
                            </p>
                            <p><strong>Sales:</strong> <?php echo e($book->getSalesCount()); ?></p>
                            <p><strong>Revenue:</strong> ₦<?php echo e(number_format($book->getTotalSales(), 2)); ?></p>
                            <p><strong>Submitted:</strong> <?php echo e($book->created_at->format('M d, Y')); ?></p>
                        </div>
                    </div>
                    
                    <?php if($book->description): ?>
                    <div class="form-group mb-3">
                        <label class="form-label"><strong>Description:</strong></label>
                        <div class="form-control-wrap">
                            <p><?php echo e($book->description); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Admin Decision</label>
                        <div class="form-control-wrap">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="pending-review-<?php echo e($book->id); ?>" value="pending_review" 
                                       <?php echo e($book->status === 'pending_review' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="pending-review-<?php echo e($book->id); ?>">Pending Review</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="send-review-copy-<?php echo e($book->id); ?>" value="send_review_copy" 
                                       <?php echo e($book->status === 'send_review_copy' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="send-review-copy-<?php echo e($book->id); ?>">Send Review Copy</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="approve-delivery-<?php echo e($book->id); ?>" value="approved_awaiting_delivery" 
                                       <?php echo e($book->status === 'approved_awaiting_delivery' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="approve-delivery-<?php echo e($book->id); ?>">Approve for Delivery</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="reject-<?php echo e($book->id); ?>" value="rejected" 
                                       <?php echo e($book->status === 'rejected' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="reject-<?php echo e($book->id); ?>">Reject</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="stock-<?php echo e($book->id); ?>" value="stocked" 
                                       <?php echo e($book->status === 'stocked' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="stock-<?php echo e($book->id); ?>">Stock</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Optional notes for the author..."><?php echo e($book->admin_notes); ?></textarea>
                    </div>
                    
                    <div class="form-group mb-3" id="revBookIdGroup-<?php echo e($book->id); ?>" style="<?php echo e($book->status !== 'stocked' ? 'display: none;' : ''); ?>">
                        <label class="form-label">REV Book ID</label>
                        <input type="text" class="form-control" name="rev_book_id" placeholder="Enter REV system book ID" value="<?php echo e($book->rev_book_id); ?>">
                        <div class="form-note">This will be automatically populated when the book is registered with the ERP system.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" tabindex="-1" id="viewDetailsModal-<?php echo e($book->id); ?>" aria-labelledby="viewDetailsModalLabel-<?php echo e($book->id); ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel-<?php echo e($book->id); ?>">Book Details: <?php echo e($book->title); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="small text-muted">Book Information</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted">Title:</td>
                                <td><strong><?php echo e($book->title); ?></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Author:</td>
                                <td><?php echo e($book->user->name); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email:</td>
                                <td><?php echo e($book->user->email); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Genre:</td>
                                <td><?php echo e($book->genre); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Price:</td>
                                <td>₦<?php echo e(number_format($book->price, 2)); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">ISBN:</td>
                                <td><?php echo e($book->isbn ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Type:</td>
                                <td><?php echo e(ucfirst($book->book_type)); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="small text-muted">Status & Performance</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted">Status:</td>
                                <td>
                                    <?php if($book->status === 'pending_review'): ?>
                                        <span class="badge badge-sm bg-warning">Pending Review</span>
                                    <?php elseif($book->status === 'send_review_copy'): ?>
                                        <span class="badge badge-sm bg-info">Send Review Copy</span>
                                    <?php elseif($book->status === 'approved_awaiting_delivery'): ?>
                                        <span class="badge badge-sm bg-success">Approved - Awaiting Delivery</span>
                                    <?php elseif($book->status === 'rejected'): ?>
                                        <span class="badge badge-sm bg-danger">Rejected</span>
                                    <?php elseif($book->status === 'stocked'): ?>
                                        <span class="badge badge-sm bg-info">Stocked</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Sales:</td>
                                <td><?php echo e($book->getSalesCount()); ?> copies</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Revenue:</td>
                                <td>₦<?php echo e(number_format($book->getTotalSales(), 2)); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Submitted:</td>
                                <td><?php echo e($book->created_at->format('M d, Y')); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Last Updated:</td>
                                <td><?php echo e($book->updated_at->format('M d, Y H:i')); ?></td>
                            </tr>
                            <?php if($book->rev_book_id): ?>
                            <tr>
                                <td class="text-muted">ERP Book ID:</td>
                                <td><?php echo e($book->rev_book_id); ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
                
                <?php if($book->description): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="small text-muted">Description</h6>
                        <div class="border p-3 rounded">
                            <p class="mb-0"><?php echo e($book->description); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if($book->admin_notes): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="small text-muted">Admin Notes</h6>
                        <div class="border p-3 rounded bg-light">
                            <p class="mb-0"><?php echo e($book->admin_notes); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-12">
                        <h6 class="small text-muted">Recent Sales</h6>
                        <?php
                            $recentSales = $book->walletTransactions()->where('type', 'sale')->latest()->limit(5)->get();
                        ?>
                        <?php if($recentSales->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Transaction ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($sale->created_at->format('M d, Y')); ?></td>
                                        <td>₦<?php echo e(number_format($sale->amount, 2)); ?></td>
                                        <td><?php echo e($sale->transaction_id ?? 'N/A'); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted">No sales recorded yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
                <?php if($book->status === 'pending_review'): ?>
                <form method="POST" action="<?php echo e(route('admin.books.review', $book)); ?>" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <input type="hidden" name="status" value="send_review_copy">
                    <button type="submit" class="btn btn-info" onclick="return confirm('Are you sure you want to request a review copy for this book?')">Request Review Copy</button>
                </form>
                <form method="POST" action="<?php echo e(route('admin.books.review', $book)); ?>" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <input type="hidden" name="status" value="approved_awaiting_delivery">
                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this book for delivery?')">Approve for Delivery</button>
                </form>
                <form method="POST" action="<?php echo e(route('admin.books.review', $book)); ?>" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this book?')">Reject</button>
                </form>
                <?php elseif($book->status === 'approved_awaiting_delivery'): ?>
                <form method="POST" action="<?php echo e(route('admin.books.review', $book)); ?>" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <input type="hidden" name="status" value="stocked">
                    <button type="submit" class="btn btn-info" onclick="return confirm('Are you sure you want to stock this book? This will register it with the ERP system.')">Stock Book</button>
                </form>
                <?php else: ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal-<?php echo e($book->id); ?>" data-bs-dismiss="modal">Edit Status</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Show/hide REV Book ID field based on status selection
function toggleRevBookIdField(bookId) {
    const modal = document.getElementById(`reviewModal-${bookId}`);
    if (!modal) return;
    
    const statusInputs = modal.querySelectorAll('input[name="status"]');
    const revBookIdGroup = document.getElementById(`revBookIdGroup-${bookId}`);
    
    statusInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value === 'stocked') {
                if (revBookIdGroup) revBookIdGroup.style.display = 'block';
            } else {
                if (revBookIdGroup) revBookIdGroup.style.display = 'none';
            }
        });
    });
}

// Initialize when a modal is shown
document.addEventListener('shown.bs.modal', function (event) {
    const modal = event.target;
    if (modal.id && modal.id.startsWith('reviewModal-')) {
        const bookId = modal.id.replace('reviewModal-', '');
        if (bookId) {
            toggleRevBookIdField(bookId);
        }
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/books/index.blade.php ENDPATH**/ ?>