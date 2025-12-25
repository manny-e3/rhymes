<?php $__env->startSection('title', 'Authors Management | Admin Panel'); ?>

<?php $__env->startSection('page-title', 'Authors Management'); ?>

<?php $__env->startSection('page-description', 'Manage platform authors and their performance'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Authors</h3>
                        <div class="nk-block-des text-soft">
                            <p>Manage all platform authors and track their performance.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-primary"><em class="icon ni ni-users"></em><span>All Users</span></a></li>
                                    <li><a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-plus"></em><span>Add User</span></a></li>
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
                                        <form method="GET" action="<?php echo e(route('admin.users.authors')); ?>" class="d-flex gap-2">
                                            <div class="form-wrap w-150px">
                                                <select name="sort" class="form-select form-select-sm">
                                                    <option value="newest" <?php echo e(request('sort') === 'newest' ? 'selected' : ''); ?>>Newest First</option>
                                                    <option value="oldest" <?php echo e(request('sort') === 'oldest' ? 'selected' : ''); ?>>Oldest First</option>
                                                    <option value="most_books" <?php echo e(request('sort') === 'most_books' ? 'selected' : ''); ?>>Most Books</option>
                                                    <option value="highest_earnings" <?php echo e(request('sort') === 'highest_earnings' ? 'selected' : ''); ?>>Highest Earnings</option>
                                                </select>
                                            </div>
                                            <div class="form-wrap flex-md-nowrap">
                                                <div class="form-icon form-icon-right">
                                                    <em class="icon ni ni-search"></em>
                                                </div>
                                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search authors..." value="<?php echo e(request('search')); ?>">
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
                                    <div class="nk-tb-col"><span class="sub-text">Author</span></div>
                                    <div class="nk-tb-col tb-col-mb"><span class="sub-text">Books</span></div>
                                    <div class="nk-tb-col tb-col-md"><span class="sub-text">Earnings</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Last Active</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Joined</span></div>
                                    <div class="nk-tb-col nk-tb-col-tools text-end">
                                        <span class="sub-text">Actions</span>
                                    </div>
                                </div>

                                <?php $__empty_1 = true; $__currentLoopData = $authors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $author): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $totalBooks = $author->books->count();
                                        $publishedBooks = $author->books->where('status', 'accepted')->count();
                                        $totalEarnings = $author->walletTransactions()->where('type', 'sale')->sum('amount');
                                    ?>
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary">
                                                    <span><?php echo e(strtoupper(substr($author->name, 0, 2))); ?></span>
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead"><?php echo e($author->name); ?> 
                                                        <?php if($author->email_verified_at): ?>
                                                            <span class="dot dot-success d-md-none ms-1"></span>
                                                        <?php endif; ?>
                                                    </span>
                                                    <span><?php echo e($author->email); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col tb-col-mb">
                                            <span class="tb-lead"><?php echo e($totalBooks); ?></span>
                                            <span class="tb-sub"><?php echo e($publishedBooks); ?> published</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
                                            <span class="tb-lead">₦<?php echo e(number_format($totalEarnings, 2)); ?></span>
                                            <span class="tb-sub">Total earnings</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <?php if($author->last_login_at): ?>
                                                <span><?php echo e($author->last_login_at->diffForHumans()); ?></span>
                                            <?php else: ?>
                                                <span class="text-soft">Never</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span><?php echo e($author->created_at->format('M d, Y')); ?></span>
                                        </div>
                                        <div class="nk-tb-col nk-tb-col-tools">
                                            <ul class="nk-tb-actions gx-1">
                                                <li class="nk-tb-action-hidden">
                                                    <a href="<?php echo e(route('admin.users.show', $author)); ?>" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="View Profile">
                                                        <em class="icon ni ni-eye-fill"></em>
                                                    </a>
                                                </li>
                                                <li class="nk-tb-action-hidden">
                                                    <a href="<?php echo e(route('admin.users.edit', $author)); ?>" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                        <em class="icon ni ni-edit-fill"></em>
                                                    </a>
                                                </li>
                                                <li>
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li><a href="<?php echo e(route('admin.users.show', $author)); ?>"><em class="icon ni ni-eye"></em><span>View Profile</span></a></li>
                                                                <li><a href="<?php echo e(route('admin.users.edit', $author)); ?>"><em class="icon ni ni-edit"></em><span>Edit Author</span></a></li>
                                                                <li><a href="<?php echo e(route('admin.books.index', ['author' => $author->id])); ?>"><em class="icon ni ni-book"></em><span>View Books</span></a></li>
                                                                <li><a href="<?php echo e(route('admin.payouts.index', ['author' => $author->id])); ?>"><em class="icon ni ni-tranx"></em><span>View Payouts</span></a></li>
                                                                <li class="divider"></li>
                                                                <li><a href="#" onclick="sendMessage(<?php echo e($author->id); ?>)"><em class="icon ni ni-mail"></em><span>Send Message</span></a></li>
                                                                <li><a href="#" class="text-danger" onclick="suspendAuthor(<?php echo e($author->id); ?>)"><em class="icon ni ni-user-cross"></em><span>Suspend Author</span></a></li>
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
                                                <em class="icon ni ni-users" style="font-size: 3rem; opacity: 0.3;"></em>
                                                <p class="text-soft mt-2">No authors found</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-inner">
                            <?php echo e($authors->appends(request()->query())->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" tabindex="-1" id="messageModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Message to Author</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form id="messageForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="authorId" name="author_id">
                    
                    <div class="form-group">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" name="subject" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" name="message" rows="5" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitMessage()">Send Message</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function sendMessage(authorId) {
    document.getElementById('authorId').value = authorId;
    const modal = new bootstrap.Modal(document.getElementById('messageModal'));
    modal.show();
}

function submitMessage() {
    const form = document.getElementById('messageForm');
    const formData = new FormData(form);
    
    fetch('/admin/authors/send-message', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success!', 'Message sent successfully.', 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('messageModal'));
            modal.hide();
            form.reset();
        } else {
            Swal.fire('Error!', data.message || 'Failed to send message.', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error!', 'Something went wrong.', 'error');
    });
}

function suspendAuthor(authorId) {
    Swal.fire({
        title: 'Suspend Author?',
        text: 'This will temporarily suspend the author\'s account.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, suspend!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/authors/${authorId}/suspend`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Suspended!', 'Author has been suspended.', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.message || 'Failed to suspend author.', 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Something went wrong.', 'error');
            });
        }
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/users/authors.blade.php ENDPATH**/ ?>