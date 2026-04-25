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
                                                    <option value="approved_awaiting_delivery" <?php echo e(request('status') === 'approved_awaiting_delivery' ? 'selected' : ''); ?>>Approved - AWaiting Delivery</option>
                                                    <option value="rejected" <?php echo e(request('status') === 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                                                    <option value="stocked" <?php echo e(request('status') === 'stocked' ? 'selected' : ''); ?>>Stocked</option>
                                                    <option value="edited_pending_approval" <?php echo e(request('status') === 'edited_pending_approval' ? 'selected' : ''); ?>>Edited - Awaiting Approval</option>

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
                                                    <br>

                        </div>

                        <div class="card-inner p-0">
                            <div class="nk-tb-list nk-tb-ulist">
                                <div class="nk-tb-item nk-tb-head">
                                    <div class="nk-tb-col"><span class="sub-text">Cover</span></div>
                                    <div class="nk-tb-col"><span class="sub-text">Book</span></div>
                                    <div class="nk-tb-col tb-col-mb"><span class="sub-text">Author</span></div>
                                    <div class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Sales</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Quantity</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Submitted</span></div>
                                    <div class="nk-tb-col nk-tb-col-tools text-end">
                                        
                                    </div>
                                </div>

                                <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <?php if($book->image): ?>
                                                <div class="user-card">
                                                    <div class="user-avatar bg-transparent">
                                                        <a href="<?php echo e(asset($book->image)); ?>" download="<?php echo e(Str::slug($book->title)); ?>-cover">
                                                            <img src="<?php echo e(asset($book->image)); ?>" alt="<?php echo e($book->title); ?>" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="user-avatar bg-light">
                                                    <em class="icon ni ni-img-fill text-soft" style="font-size: 20px;"></em>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="nk-tb-col">
                                            <div class="user-card">
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
                                            <?php elseif($book->status === 'edited_pending_approval'): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-warning">Edited - Awaiting Approval</span>
                                            <?php elseif($book->status === 'retrieval_requested'): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-warning">Retrieval Requested</span>
                                            <?php elseif($book->status === 'recalled'): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-danger">Retrieved</span>
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
                                            <?php if($book->status === 'stocked' && $book->quantity): ?>
                                                <span class="tb-lead"><?php echo e($book->quantity); ?></span>
                                            <?php else: ?>
                                                <span class="tb-sub">N/A</span>
                                            <?php endif; ?>
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
                                                                 <?php if($book->status == 'retrieval_requested'): ?>
                                                                        <li class="divider"></li>
                                                                        <li>
                                                                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#approveRetrievalModal-<?php echo e($book->id); ?>"><em class="icon ni ni-check"></em><span>Approve Retrieval</span></a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#denyRetrievalModal-<?php echo e($book->id); ?>"><em class="icon ni ni-cross"></em><span>Deny Retrieval</span></a>
                                                                        </li>
                                                                        <?php endif; ?>
                                                                       

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
                                                                        <form method="POST" action="<?php echo e(route('admin.books.bulk-action')); ?>" style="display:inline;" class="sweet-alert-form" data-message="This action cannot be undone! The book will be permanently removed from the system.">
                                                                            <?php echo csrf_field(); ?>
                                                                            <input type="hidden" name="action" value="forceDelete">
                                                                            <input type="hidden" name="book_ids[]" value="<?php echo e($book->id); ?>">
                                                                            <button type="submit" class="dropdown-item text-danger"><em class="icon ni ni-trash-fill"></em><span>Permanently Delete</span></button>
                                                                        </form>
                                                                    </li>
                                                                <?php else: ?>
                                                                    <li>
                                                                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changeStatusModal-<?php echo e($book->id); ?>"><em class="icon ni ni-edit"></em><span>Change Status</span></a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                    <li>
                                                                        <form method="POST" action="<?php echo e(route('admin.books.bulk-action')); ?>" style="display:inline;" class="sweet-alert-form" data-message="This action will soft delete the book. You can restore it later.">
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

<!-- View Details Modal -->
<div class="modal fade" tabindex="-1" id="viewDetailsModal-<?php echo e($book->id); ?>" aria-labelledby="viewDetailsModalLabel-<?php echo e($book->id); ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel-<?php echo e($book->id); ?>">Book Details: <?php echo e($book->title); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card card-bordered h-100">
                            <div class="card-inner d-flex flex-column align-items-center justify-content-center p-2 bg-light" style="min-height: 200px;">
                                <?php if($book->image): ?>
                                    <img src="<?php echo e(asset($book->image)); ?>" class="rounded shadow-sm mb-2" alt="<?php echo e($book->title); ?>" style="max-width: 100%; max-height: 280px; object-fit: contain;">
                                    <a href="<?php echo e(asset($book->image)); ?>" download="<?php echo e(Str::slug($book->title)); ?>-cover" class="btn btn-sm btn-outline-primary">
                                        <em class="icon ni ni-download"></em><span>Download Cover</span>
                                    </a>
                                <?php else: ?>
                                    <div class="text-center text-soft">
                                        <em class="icon ni ni-book-read" style="font-size: 48px;"></em>
                                        <p class="mt-2 small">No Cover</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row g-3">
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
                                    <?php elseif($book->status === 'recalled'): ?>
                                        <span class="badge badge-sm bg-warning">Retrieved</span>
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
                            <?php if($book->status === 'stocked' && $book->quantity): ?>
                            <tr>
                                <td class="text-muted">Quantity:</td>
                                <td><?php echo e($book->quantity); ?> copies</td>
                            </tr>
                            <?php endif; ?>
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
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#sendReviewCopyModal-<?php echo e($book->id); ?>">Send Review Copy</button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveForDeliveryModal-<?php echo e($book->id); ?>">Approve for Delivery</button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectBookModal-<?php echo e($book->id); ?>">Reject</button>
                <?php elseif($book->status === 'send_review_copy'): ?>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveForDeliveryModal-<?php echo e($book->id); ?>">Approve for Delivery</button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectBookModal-<?php echo e($book->id); ?>">Reject</button>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#pendingReviewModal-<?php echo e($book->id); ?>">Set Pending Review</button>
                <?php elseif($book->status === 'approved_awaiting_delivery'): ?>
                <!-- Button to trigger the quantity modal instead of directly stocking -->
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#quantityModal-<?php echo e($book->id); ?>">Stock Book</button>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#pendingReviewModal-<?php echo e($book->id); ?>">Set Pending Review</button>
                <?php elseif($book->status === 'rejected'): ?>
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#sendReviewCopyModal-<?php echo e($book->id); ?>">Send Review Copy</button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveForDeliveryModal-<?php echo e($book->id); ?>">Approve for Delivery</button>
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#stockBookModal-<?php echo e($book->id); ?>">Stock Book</button>
                <?php elseif($book->status === 'stocked'): ?>
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#sendReviewCopyModal-<?php echo e($book->id); ?>">Send Review Copy</button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveForDeliveryModal-<?php echo e($book->id); ?>">Approve for Delivery</button>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#pendingReviewModal-<?php echo e($book->id); ?>">Set Pending Review</button>
                <?php elseif($book->status === 'recalled'): ?>
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#sendReviewCopyModal-<?php echo e($book->id); ?>">Send Review Copy</button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveForDeliveryModal-<?php echo e($book->id); ?>">Approve for Delivery</button>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#pendingReviewModal-<?php echo e($book->id); ?>">Set Pending Review</button>
                <?php elseif($book->status === 'edited_pending_approval'): ?>
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#viewDetailsModal-<?php echo e($book->id); ?>">View Details</button>
                <?php else: ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal-<?php echo e($book->id); ?>" data-bs-dismiss="modal">Edit Status</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Change Status Modal for each book -->
<?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" tabindex="-1" id="changeStatusModal-<?php echo e($book->id); ?>" aria-labelledby="changeStatusModalLabel-<?php echo e($book->id); ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeStatusModalLabel-<?php echo e($book->id); ?>">Change Status: <?php echo e($book->title); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?php echo e(route('admin.books.review', $book)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select status-select" data-book-id="<?php echo e($book->id); ?>" required>
                            <option value="pending_review" <?php echo e($book->status === 'pending_review' ? 'selected' : ''); ?>>Pending Review</option>
                            <option value="send_review_copy" <?php echo e($book->status === 'send_review_copy' ? 'selected' : ''); ?>>Send Review Copy</option>
                            <option value="approved_awaiting_delivery" <?php echo e($book->status === 'approved_awaiting_delivery' ? 'selected' : ''); ?>>Approved - Awaiting Delivery</option>
                            <option value="rejected" <?php echo e($book->status === 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                            <option value="stocked" <?php echo e($book->status === 'stocked' ? 'selected' : ''); ?>>Stocked</option>
                            <!-- <option value="edited_pending_approval" <?php echo e($book->status === 'edited_pending_approval' ? 'selected' : ''); ?>>Edited - Awaiting Approval</option> -->
                            <option value="retrieval_requested" <?php echo e($book->status === 'retrieval_requested' ? 'selected' : ''); ?>>Retrieval Requested</option>
                            <option value="recalled" <?php echo e($book->status === 'recalled' ? 'selected' : ''); ?>>Retrieved</option>
                        </select>
                    </div>
                    
                    <div class="form-group mb-3 quantity-group-<?php echo e($book->id); ?>" style="<?php echo e($book->status === 'stocked' ? 'display: block;' : 'display: none;'); ?>">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control quantity-input-<?php echo e($book->id); ?>" name="quantity" value="<?php echo e($book->quantity); ?>" placeholder="Enter quantity" min="1">
                        <div class="form-note">Enter the number of copies being stocked in inventory.</div>
                    </div>
                    
                    <!-- <div class="form-group mb-3 rev-book-id-group-<?php echo e($book->id); ?>" id="revBookIdGroup-<?php echo e($book->id); ?>" style="<?php echo e($book->status === 'stocked' ? 'display: block;' : 'display: none;'); ?>">
                        <label class="form-label">REV Book ID</label>
                        <input type="text" class="form-control" name="rev_book_id" placeholder="Enter REV system book ID" value="<?php echo e($book->rev_book_id); ?>">
                        <div class="form-note">This will be automatically populated when the book is registered with the ERP system.</div>
                    </div> -->

                    <div class="form-group mb-3 admin-notes-group-<?php echo e($book->id); ?>" id="adminNotesGroup-<?php echo e($book->id); ?>" style="<?php echo e($book->status === 'rejected' ? 'display: none;' : 'display: block;'); ?>">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Optional notes for the author..."><?php echo e($book->admin_notes); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Show/hide fields based on status selection
function toggleRevBookIdField(bookId) {
    const modal = document.getElementById(`changeStatusModal-${bookId}`);
    if (!modal) return;
    
    const statusSelect = modal.querySelector('select[name="status"]');
    if (!statusSelect) return;
    
    const revBookIdGroup = document.getElementById(`revBookIdGroup-${bookId}`);
    const quantityGroup = modal.querySelector(`.quantity-group-${bookId}`);
    const adminNotesGroup = document.getElementById(`adminNotesGroup-${bookId}`);
    
    // Function to handle the actual toggling
    function updateVisibility() {
        const value = statusSelect.value;
        
        if (value === 'stocked') {
            if (revBookIdGroup) revBookIdGroup.style.display = 'block';
            if (quantityGroup) quantityGroup.style.display = 'block';
        } else {
            if (revBookIdGroup) revBookIdGroup.style.display = 'none';
            if (quantityGroup) quantityGroup.style.display = 'none';
        }
        
        if (value === 'rejected') {
            if (adminNotesGroup) adminNotesGroup.style.display = 'none';
        } else {
            if (adminNotesGroup) adminNotesGroup.style.display = 'block';
        }
    }
    
    // Remove old listener to prevent duplicates if toggled multiple times
    statusSelect.removeEventListener('change', updateVisibility);
    // Add new listener
    statusSelect.addEventListener('change', updateVisibility);
    
    // Call once to set initial state
    updateVisibility();
}

// Initialize when a modal is shown
document.addEventListener('shown.bs.modal', function (event) {
    const modal = event.target;
    if (modal.id && modal.id.startsWith('changeStatusModal-')) {
        const bookId = modal.id.replace('changeStatusModal-', '');
        if (bookId) {
            toggleRevBookIdField(bookId);
        }
    }
});

// Function to show SweetAlert confirmation
function showSweetAlert(title, text, callback) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, continue',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
}

// Function to handle form submission with SweetAlert
function handleFormSubmit(event, title, text) {
    event.preventDefault();
    
    const form = event.target;
    
    showSweetAlert(title, text, function() {
        form.submit();
    });
}

// Convert all confirm dialogs to SweetAlert
function convertConfirmToSweetAlert() {
    // Bulk action confirmations
    document.querySelectorAll('button[onclick*="confirm"]').forEach(button => {
        const originalOnClick = button.getAttribute('onclick');
        if (originalOnClick && originalOnClick.includes('return confirm')) {
            const match = originalOnClick.match(/confirm\(['"](.*)['"]\)/);
            if (match) {
                const message = match[1];
                
                button.removeAttribute('onclick');
                
                // Find the parent form
                let form = button.closest('form');
                if (form) {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        showSweetAlert('Confirm Action', message, function() {
                            form.submit();
                        });
                    });
                }
            }
        }
    });
    
    // Form submit confirmations
    document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
        const originalOnsubmit = form.getAttribute('onsubmit');
        if (originalOnsubmit && originalOnsubmit.includes('return confirm')) {
            const match = originalOnsubmit.match(/confirm\(['"](.*)['"]\)/);
            if (match) {
                const message = match[1];
                
                form.removeAttribute('onsubmit');
                
                form.addEventListener('submit', function(e) {
                    handleFormSubmit(e, 'Confirm Action', message);
                });
            }
        }
    });
    
    // Handle forms with sweet-alert-form class
    document.querySelectorAll('form.sweet-alert-form').forEach(form => {
        const message = form.getAttribute('data-message');
        
        // Remove any existing submit listeners to avoid duplicates
        form.removeEventListener('submit', form.submitHandler);
        
        form.submitHandler = function(e) {
            handleFormSubmit(e, 'Confirm Action', message);
        };
        
        form.addEventListener('submit', form.submitHandler);
    });
    
    // Handle buttons with sweet-alert-button class
    document.querySelectorAll('button.sweet-alert-button').forEach(button => {
        const message = button.getAttribute('data-message');
        
        // Find the parent form
        let form = button.closest('form');
        if (form && message) {
            // Remove any existing click listeners to avoid duplicates
            button.removeEventListener('click', button.clickHandler);
            
            button.clickHandler = function(e) {
                e.preventDefault();
                
                showSweetAlert('Confirm Action', message, function() {
                    form.submit();
                });
            };
            
            button.addEventListener('click', button.clickHandler);
        }
    });
}

// Re-run conversion when DOM changes (for dynamically added content)
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.type === 'childList') {
            convertConfirmToSweetAlert();
        }
    });
});

observer.observe(document.body, { childList: true, subtree: true });

// Run after DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', convertConfirmToSweetAlert);
} else {
    convertConfirmToSweetAlert();
}

// Function to handle recall requests
function handleRecallRequest(bookId, action) {
    let title, text, confirmText;
    
    if (action === 'approve') {
        title = 'Approve Retrieval Request';
        text = 'Are you sure you want to approve this retrieval request?';
        confirmText = 'Approve';
    } else {
        title = 'Deny Retrieval Request';
        text = 'Are you sure you want to deny this retrieval request?';
        confirmText = 'Deny';
    }
    
    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmText,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Make AJAX request to handle the retrieval action
            fetch(`/admin/books/${bookId}/retrieval-action`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Success!',
                        data.message,
                        'success'
                    ).then(() => {
                        // Reload the page to reflect changes
                        location.reload();
                    });
                } else {
                    Swal.fire(
                        'Error!',
                        data.message || 'An error occurred while processing the recall request.',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('Recall action error:', error);
                Swal.fire(
                    'Error!',
                    'An error occurred while processing the recall request.',
                    'error'
                );
            });
        }
    });
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/books/index.blade.php ENDPATH**/ ?>