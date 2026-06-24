@extends('layouts.admin')

@section('title', $book->title . ' | Book Details')

@section('page-title', 'Book Details')

@section('page-description', 'Review and manage book information')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">{{ $book->title }}</h3>
                        <div class="nk-block-des text-soft">
                            <p>Book ID: #{{ $book->id }} • Submitted {{ $book->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    @if($book->status === 'pending_review')
                                        <li><a href="javascript:void(0)" class="btn btn-success" onclick="reviewBook({{ $book->id }}, 'approved_awaiting_delivery'); return false;"><em class="icon ni ni-check"></em><span>Approve</span></a></li>
                                        <li><a href="javascript:void(0)" class="btn btn-danger" onclick="reviewBook({{ $book->id }}, 'rejected'); return false;"><em class="icon ni ni-cross"></em><span>Reject</span></a></li>
                                    @endif
                                    <li><a href="{{ route('admin.books.index') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-arrow-left"></em><span>Back to Books</span></a></li>
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
                                        @if($book->status === 'pending_review')
                                            <span class="badge badge-warning">Pending Review</span>
                                        @elseif($book->status === 'approved_awaiting_delivery')
                                            <span class="badge badge-success">Approved - Awaiting Delivery</span>
                                        @elseif($book->status === 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @elseif($book->status === 'stocked')
                                            <span class="badge badge-info">Stocked</span>
                                        @elseif($book->status === 'edited_pending_approval')
                                            <span class="badge badge-warning">Edited - Awaiting Approval</span>
                                        @elseif($book->status === 'retrieval_requested')
                                            <span class="badge badge-warning">Retrieval Requested</span>
                                        @elseif($book->status === 'recalled')
                                            <span class="badge badge-danger">Retrieved</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="form-group text-center">
                                            <label class="form-label">Book Cover</label>
                                            <div class="mt-2">
                                                @if($book->image)
                                                    <img src="{{ Storage::url($book->image) }}" alt="{{ $book->title }}" class="img-thumbnail" style="max-height: 300px;">
                                                    <div class="mt-2">
                                                        <a href="{{ Storage::url($book->image) }}" class="btn btn-sm btn-outline-primary" download>
                                                            <em class="icon ni ni-download-cloud"></em><span>Download Image</span>
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="p-4 bg-light text-soft">No image uploaded</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Title</label>
                                            <div class="form-control-plaintext">{{ $book->title }}</div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Genre</label>
                                            <div class="form-control-plaintext">{{ $book->genre }}</div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Price</label>
                                            <div class="form-control-plaintext">₦{{ number_format($book->price, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Language</label>
                                            <div class="form-control-plaintext">{{ $book->language ?? 'Not specified' }}</div>
                                        </div>
                                    </div>
                                    @if($book->isbn)
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">ISBN</label>
                                                <div class="form-control-plaintext">{{ $book->isbn }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($book->rev_book_id)
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">REV Book ID</label>
                                                <div class="form-control-plaintext">{{ $book->rev_book_id }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Description</label>
                                            <div class="form-control-plaintext">{{ $book->description ?: 'No description provided' }}</div>
                                        </div>
                                    </div>
                                    @if($book->admin_notes)
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Admin Notes</label>
                                                <div class="alert alert-info">{{ $book->admin_notes }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Retrieval Request Information -->
                                    @if($book->status === 'retrieval_requested' || ($book->status === 'retrieved' && $book->retrieval_location))
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Retrieval Request</label>
                                                <div class="alert alert-warning">
                                                    <div class="alert-body">
                                                        <h6>Retrieval Requested</h6>
                                                        <p><strong>Location:</strong> {{ $book->retrieval_location }}</p>
                                                        <p><strong>Quantity:</strong> {{ $book->retrieval_quantity }} copies</p>
                                                        @if($book->retrieval_reason)
                                                            <p><strong>Reason:</strong> {{ $book->retrieval_reason }}</p>
                                                        @else
                                                            <p><strong>Reason:</strong> No reason provided</p>
                                                        @endif
                                                        
                                                        @if($book->status === 'retrieval_requested')
                                                            <div class="mt-3">
                                                                <button class="btn btn-success" onclick="handleRetrieval({{ $book->id }}, 'approve')">Approve Retrieval</button>
                                                                <button class="btn btn-danger" onclick="handleRetrieval({{ $book->id }}, 'deny')">Deny Retrieval</button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Original vs New Data Comparison for edited books -->
                                    @if($book->status === 'edited_pending_approval' && $book->original_data)
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
                                                                <td>{{ $book->original_data['title'] ?? 'N/A' }}</td>
                                                                <td>{{ $book->title }}</td>
                                                                <td>
                                                                    @if(($book->original_data['title'] ?? '') !== $book->title)
                                                                        <span class="badge bg-warning">Changed</span>
                                                                    @else
                                                                        <span class="badge bg-success">Unchanged</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>ISBN</strong></td>
                                                                <td>{{ $book->original_data['isbn'] ?? 'N/A' }}</td>
                                                                <td>{{ $book->isbn }}</td>
                                                                <td>
                                                                    @if(($book->original_data['isbn'] ?? '') !== $book->isbn)
                                                                        <span class="badge bg-warning">Changed</span>
                                                                    @else
                                                                        <span class="badge bg-success">Unchanged</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Genre</strong></td>
                                                                <td>{{ $book->original_data['genre'] ?? 'N/A' }}</td>
                                                                <td>{{ $book->genre }}</td>
                                                                <td>
                                                                    @if(($book->original_data['genre'] ?? '') !== $book->genre)
                                                                        <span class="badge bg-warning">Changed</span>
                                                                    @else
                                                                        <span class="badge bg-success">Unchanged</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Price</strong></td>
                                                                <td>₦{{ number_format($book->original_data['price'] ?? 0, 2) }}</td>
                                                                <td>₦{{ number_format($book->price, 2) }}</td>
                                                                <td>
                                                                    @if(($book->original_data['price'] ?? 0) !== $book->price)
                                                                        <span class="badge bg-warning">Changed</span>
                                                                    @else
                                                                        <span class="badge bg-success">Unchanged</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Type</strong></td>
                                                                <td>{{ ucwords(str_replace('_', ' ', $book->original_data['book_type'] ?? 'N/A')) }}</td>
                                                                <td>{{ ucwords(str_replace('_', ' ', $book->book_type)) }}</td>
                                                                <td>
                                                                    @if(($book->original_data['book_type'] ?? '') !== $book->book_type)
                                                                        <span class="badge bg-warning">Changed</span>
                                                                    @else
                                                                        <span class="badge bg-success">Unchanged</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Description</strong></td>
                                                                <td>{{ Str::limit($book->original_data['description'] ?? '', 50) }}</td>
                                                                <td>{{ Str::limit($book->description, 50) }}</td>
                                                                <td>
                                                                    @if(($book->original_data['description'] ?? '') !== $book->description)
                                                                        <span class="badge bg-warning">Changed</span>
                                                                    @else
                                                                        <span class="badge bg-success">Unchanged</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Sales Performance -->
                        @if($book->status === 'stocked' && $stats['total_sales'] > 0)
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
                                                        <h4 class="inbox-item-title">{{ number_format($stats['total_sales']) }}</h4>
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
                                                        <h4 class="inbox-item-title">₦{{ number_format($stats['total_revenue'], 2) }}</h4>
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
                                                        <h4 class="inbox-item-title">₦{{ number_format($stats['average_sale_price'], 2) }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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
                                        <span>{{ strtoupper(substr($book->user->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="user-info">
                                        <h5>{{ $book->user->name }}</h5>
                                        <span class="sub-text">{{ $book->user->email }}</span>
                                    </div>
                                </div>
                                
                                <div class="user-meta mt-4">
                                    <ul class="nk-list-meta">
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Total Books:</span>
                                            <span class="nk-list-meta-value">{{ $book->user->books->count() }}</span>
                                        </li>
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Published:</span>
                                            <span class="nk-list-meta-value">{{ $book->user->books->where('status', 'accepted')->count() }}</span>
                                        </li>
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Member Since:</span>
                                            <span class="nk-list-meta-value">{{ $book->user->created_at->format('M Y') }}</span>
                                        </li>
                                    </ul>
                                </div>
                                
                                <div class="mt-4">
                                    <a href="{{ route('admin.users.show', $book->user) }}" class="btn btn-outline-primary btn-block">View Author Profile</a>
                                </div>
                            </div>
                        </div>

                        <!-- Review Actions -->
                        @if($book->status === 'pending_review')
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
                                            <button class="btn btn-success btn-block" onclick="reviewBook({{ $book->id }}, 'approved_awaiting_delivery')">
                                                <em class="icon ni ni-check"></em><span>Approve</span>
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-danger btn-block" onclick="reviewBook({{ $book->id }}, 'rejected')">
                                                <em class="icon ni ni-cross"></em><span>Reject</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($book->status === 'edited_pending_approval')
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
                                            <button class="btn btn-primary btn-block" onclick="reviewBook({{ $book->id }}, 'stocked')">
                                                <em class="icon ni ni-edit"></em><span>Edit Status</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

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
                                            <span class="time">{{ $book->created_at->format('M d, Y \a\t g:i A') }}</span>
                                        </div>
                                    </li>
                                    
                                    @if($book->updated_at != $book->created_at)
                                        <li class="nk-activity-item">
                                            <div class="nk-activity-media user-avatar bg-info">
                                                <em class="icon ni ni-edit"></em>
                                            </div>
                                            <div class="nk-activity-data">
                                                <div class="label">Book updated</div>
                                                <span class="time">{{ $book->updated_at->format('M d, Y \a\t g:i A') }}</span>
                                            </div>
                                        </li>
                                    @endif
                                    
                                    @if($book->status !== 'pending_review')
                                        <li class="nk-activity-item">
                                            <div class="nk-activity-media user-avatar {{ in_array($book->status, ['approved_awaiting_delivery', 'stocked', 'edited_pending_approval']) ? 'bg-success' : 'bg-danger' }}">
                                                <em class="icon ni ni-{{ in_array($book->status, ['approved_awaiting_delivery', 'stocked', 'edited_pending_approval']) ? 'check' : 'cross' }}"></em>
                                            </div>
                                            <div class="nk-activity-data">
                                                <div class="label">Book {{ in_array($book->status, ['approved_awaiting_delivery', 'stocked', 'edited_pending_approval']) ? 'status updated' : 'rejected' }}</div>
                                                <span class="time">{{ $book->updated_at->format('M d, Y \a\t g:i A') }}</span>
                                            </div>
                                        </li>
                                    @endif
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
                    @csrf
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

@push('scripts')
<script>
// Store the route URL in a JavaScript variable
const reviewBookRoute = "{{ route('admin.books.review', ['book' => 'BOOK_ID_PLACEHOLDER']) }}";

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
@endpush
@endsection
