@extends('layouts.admin')

@section('title', 'Book Management | Admin Panel')

@section('page-title', 'Book Management')

@section('page-description', 'Review and manage all books on the platform')

@section('content')
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
                                                <a class="dropdown-item" href="{{ route('admin.books.export.csv', request()->query()) }}"><em class="icon ni ni-file-text"></em><span>Export as CSV</span></a>
                                                <a class="dropdown-item" href="{{ route('admin.books.export.pdf', request()->query()) }}"><em class="icon ni ni-file-pdf"></em><span>Export as PDF</span></a>
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
                                        <form method="GET" action="{{ route('admin.books.index') }}" class="d-flex gap-2">
                                            <div class="form-wrap w-150px">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="">All Status</option>
                                                    <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                                                    <option value="send_review_copy" {{ request('status') === 'send_review_copy' ? 'selected' : '' }}>Send Review Copy</option>
                                                    <option value="approved_awaiting_delivery" {{ request('status') === 'approved_awaiting_delivery' ? 'selected' : '' }}>Approved - Awaiting Delivery</option>
                                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                    <option value="stocked" {{ request('status') === 'stocked' ? 'selected' : '' }}>Stocked</option>
                                                </select>
                                            </div>
                                            <div class="form-wrap w-150px">
                                                <select name="genre" class="form-select form-select-sm">
                                                    <option value="">All Genres</option>
                                                    @foreach($genres as $genre)
                                                        <option value="{{ $genre }}" {{ request('genre') === $genre ? 'selected' : '' }}>
                                                            {{ $genre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-wrap flex-md-nowrap">
                                                <div class="form-icon form-icon-right">
                                                    <em class="icon ni ni-search"></em>
                                                </div>
                                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search books..." value="{{ request('search') }}">
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
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Quantity</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Submitted</span></div>
                                    <div class="nk-tb-col nk-tb-col-tools text-end">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-xs btn-outline-light btn-icon dropdown-toggle" data-bs-toggle="dropdown"><em class="icon ni ni-plus"></em></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.books.bulk-action') }}" style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="action" value="pending_review">
                                                            <button type="submit" class="dropdown-item sweet-alert-button" data-message="Are you sure you want to set all selected books to Pending Review?"><span>Set Pending Review</span></button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.books.bulk-action') }}" style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="action" value="send_review_copy">
                                                            <button type="submit" class="dropdown-item sweet-alert-button" data-message="Are you sure you want to send review copies for all selected books?"><span>Send Review Copies</span></button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.books.bulk-action') }}" style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="action" value="approve_delivery">
                                                            <button type="submit" class="dropdown-item sweet-alert-button" data-message="Are you sure you want to approve all selected books for delivery?"><span>Approve for Delivery</span></button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.books.bulk-action') }}" style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="action" value="stock">
                                                            <button type="submit" class="dropdown-item sweet-alert-button" data-message="Are you sure you want to stock all selected books? This will register them with the ERP system."><span>Stock Books</span></button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.books.bulk-action') }}" style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="action" value="reject">
                                                            <button type="submit" class="dropdown-item sweet-alert-button" data-message="Are you sure you want to reject all selected books?"><span>Bulk Reject</span></button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @forelse($books as $book)
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary-dim">
                                                    <em class="icon ni ni-book"></em>
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead">{{ $book->title }}</span>
                                                    <span>{{ $book->genre }} • ₦{{ number_format($book->price, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col tb-col-mb">
                                            <span class="tb-lead-sub">{{ $book->user->name }}</span>
                                            <span class="tb-sub">{{ $book->user->email }}</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
                                            @if($book->status === 'pending_review')
                                                <span class="badge badge-sm badge-dim bg-outline-warning">Pending Review</span>
                                            @elseif($book->status === 'send_review_copy')
                                                <span class="badge badge-sm badge-dim bg-outline-info">Send Review Copy</span>
                                            @elseif($book->status === 'approved_awaiting_delivery')
                                                <span class="badge badge-sm badge-dim bg-outline-success">Approved - Awaiting Delivery</span>
                                            @elseif($book->status === 'rejected')
                                                <span class="badge badge-sm badge-dim bg-outline-danger">Rejected</span>
                                            @elseif($book->status === 'stocked')
                                                <span class="badge badge-sm badge-dim bg-outline-info">Stocked</span>
                                            @endif
                                            @if($book->trashed())
                                                <span class="badge badge-sm badge-dim bg-outline-secondary">Deleted</span>
                                            @endif
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            @php
                                                $salesCount = $book->walletTransactions->where('type', 'sale')->count();
                                                $revenue = $book->walletTransactions->where('type', 'sale')->sum('amount');
                                            @endphp
                                            <span class="tb-lead">{{ $salesCount }}</span>
                                            <span class="tb-sub">₦{{ number_format($revenue, 2) }}</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            @if($book->status === 'stocked' && $book->quantity)
                                                <span class="tb-lead">{{ $book->quantity }}</span>
                                            @else
                                                <span class="tb-sub">N/A</span>
                                            @endif
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span>{{ $book->created_at->format('M d, Y') }}</span>
                                            <span class="tb-sub">{{ $book->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="nk-tb-col nk-tb-col-tools">
                                            <ul class="nk-tb-actions gx-1">
                                                <li>
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li><a href="#" data-bs-toggle="modal" data-bs-target="#viewDetailsModal-{{$book->id}}"><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                @if($book->trashed())
                                                                    <li>
                                                                        <form method="POST" action="{{ route('admin.books.bulk-action') }}" style="display:inline;">
                                                                            @csrf
                                                                            <input type="hidden" name="action" value="restore">
                                                                            <input type="hidden" name="book_ids[]" value="{{ $book->id }}">
                                                                            <button type="submit" class="dropdown-item"><em class="icon ni ni-reload"></em><span>Restore</span></button>
                                                                        </form>
                                                                    </li>
                                                                    <li>
                                                                        <form method="POST" action="{{ route('admin.books.bulk-action') }}" style="display:inline;" class="sweet-alert-form" data-message="This action cannot be undone! The book will be permanently removed from the system.">
                                                                            @csrf
                                                                            <input type="hidden" name="action" value="forceDelete">
                                                                            <input type="hidden" name="book_ids[]" value="{{ $book->id }}">
                                                                            <button type="submit" class="dropdown-item text-danger"><em class="icon ni ni-trash-fill"></em><span>Permanently Delete</span></button>
                                                                        </form>
                                                                    </li>
                                                                @else
                                                                    @if($book->status === 'pending_review')
                                                                        <li>
                                                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#sendReviewCopyModal-{{$book->id}}"><em class="icon ni ni-mail"></em><span>Send Review Copy</span></button>
                                                                        </li>
                                                                        {{-- <li>
                                                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#approveForDeliveryModal-{{$book->id}}"><em class="icon ni ni-check"></em><span>Approve for Delivery</span></button>
                                                                        </li> --}}
                                                                        <li>
                                                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#rejectBookModal-{{$book->id}}"><em class="icon ni ni-cross"></em><span>Reject</span></button>
                                                                        </li>
                                                                    @elseif($book->status === 'send_review_copy')
                                                                        <li>
                                                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#approveForDeliveryModal-{{$book->id}}"><em class="icon ni ni-check"></em><span>Approve for Delivery</span></button>
                                                                        </li>
                                                                        <li>
                                                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#rejectBookModal-{{$book->id}}"><em class="icon ni ni-cross"></em><span>Reject</span></button>
                                                                        </li>
                                                                    @elseif($book->status === 'approved_awaiting_delivery')
                                                                        <!-- Button to trigger the quantity modal instead of directly stocking -->
                                                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#quantityModal-{{$book->id}}"><em class="icon ni ni-package"></em><span>Stock Book</span></button>
                                                                    @elseif($book->status === 'rejected')
                                                                        <li>
                                                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reviewModal-{{$book->id}}"><em class="icon ni ni-edit"></em><span>Edit Status</span></button>
                                                                        </li>
                                                                    @elseif($book->status === 'stocked')
                                                                        <li>
                                                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reviewModal-{{$book->id}}"><em class="icon ni ni-edit"></em><span>Edit Status</span></button>
                                                                        </li>
                                                                    @else
                                                                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#reviewModal-{{$book->id}}"><em class="icon ni ni-edit"></em><span>Edit Status</span></a></li>
                                                                    @endif
                                                                    <li class="divider"></li>
                                                                    <li>
                                                                        <form method="POST" action="{{ route('admin.books.bulk-action') }}" style="display:inline;" class="sweet-alert-form" data-message="This action will soft delete the book. You can restore it later.">
                                                                            @csrf
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="book_ids[]" value="{{ $book->id }}">
                                                                            <button type="submit" class="dropdown-item text-danger"><em class="icon ni ni-trash"></em><span>Delete</span></button>
                                                                        </form>
                                                                    </li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                @empty
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <div class="text-center py-4">
                                                <em class="icon ni ni-book" style="font-size: 3rem; opacity: 0.3;"></em>
                                                <p class="text-soft mt-2">No books found</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="card-inner">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Showing {{ $books->firstItem() }} to {{ $books->lastItem() }} of {{ $books->total() }} entries
                                </div>
                                @if ($books->hasPages())
                                    <div>
                                        {{ $books->appends([
                                            'status' => request('status', ''),
                                            'genre' => request('genre', ''),
                                            'search' => request('search', '')
                                        ])->links('vendor.pagination.bootstrap-4') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($books as $book)

<!-- Review Modal -->
<div class="modal fade" tabindex="-1" id="reviewModal-{{$book->id}}" aria-labelledby="reviewModalLabel-{{$book->id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel-{{$book->id}}">Review Book: {{ $book->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reviewForm-{{$book->id}}" method="POST" action="{{ route('admin.books.review', $book) }}">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Author:</strong> {{ $book->user->name }}</p>
                            <p><strong>Email:</strong> {{ $book->user->email }}</p>
                            <p><strong>Genre:</strong> {{ $book->genre }}</p>
                            <p><strong>Price:</strong> ₦{{ number_format($book->price, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                @if($book->status === 'pending_review')
                                    <span class="badge badge-sm badge-dim bg-outline-warning">Pending Review</span>
                                @elseif($book->status === 'send_review_copy')
                                    <span class="badge badge-sm badge-dim bg-outline-info">Send Review Copy</span>
                                @elseif($book->status === 'approved_awaiting_delivery')
                                    <span class="badge badge-sm badge-dim bg-outline-success">Approved - Awaiting Delivery</span>
                                @elseif($book->status === 'rejected')
                                    <span class="badge badge-sm badge-dim bg-outline-danger">Rejected</span>
                                @elseif($book->status === 'stocked')
                                    <span class="badge badge-sm badge-dim bg-outline-info">Stocked</span>
                                @endif
                            </p>
                            @if($book->status === 'stocked' && $book->quantity)
                            <p><strong>Quantity:</strong> {{ $book->quantity }} copies</p>
                            @endif
                            <p><strong>Sales:</strong> {{ $book->getSalesCount() }}</p>
                            <p><strong>Revenue:</strong> ₦{{ number_format($book->getTotalSales(), 2) }}</p>
                            <p><strong>Submitted:</strong> {{ $book->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    
                    @if($book->description)
                    <div class="form-group mb-3">
                        <label class="form-label"><strong>Description:</strong></label>
                        <div class="form-control-wrap">
                            <p>{{ $book->description }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Admin Decision</label>
                        <div class="form-control-wrap">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="pending-review-{{$book->id}}" value="pending_review" 
                                       {{ $book->status === 'pending_review' ? 'checked' : '' }}>
                                <label class="form-check-label" for="pending-review-{{$book->id}}">Pending Review</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="send-review-copy-{{$book->id}}" value="send_review_copy" 
                                       {{ $book->status === 'send_review_copy' ? 'checked' : '' }}>
                                <label class="form-check-label" for="send-review-copy-{{$book->id}}">Send Review Copy</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="approve-delivery-{{$book->id}}" value="approved_awaiting_delivery" 
                                       {{ $book->status === 'approved_awaiting_delivery' ? 'checked' : '' }}>
                                <label class="form-check-label" for="approve-delivery-{{$book->id}}">Approve for Delivery</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="reject-{{$book->id}}" value="rejected" 
                                       {{ $book->status === 'rejected' ? 'checked' : '' }}>
                                <label class="form-check-label" for="reject-{{$book->id}}">Reject</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="stock-{{$book->id}}" value="stocked" 
                                       {{ $book->status === 'stocked' ? 'checked' : '' }}>
                                <label class="form-check-label" for="stock-{{$book->id}}">Stock</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3" id="adminNotesGroup-{{$book->id}}">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Optional notes for the author...">{{ $book->admin_notes }}</textarea>
                        <div class="form-note">These notes will be included in the email sent to the author (except for Rejected status).</div>
                    </div>
                    
                    
                    
                    <div class="form-group mb-3" id="revBookIdGroup-{{$book->id}}" style="{{ $book->status !== 'stocked' ? 'display: none;' : '' }}">
                        <label class="form-label">REV Book ID</label>
                        <input type="text" class="form-control" name="rev_book_id" placeholder="Enter REV system book ID" value="{{ $book->rev_book_id }}">
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
<div class="modal fade" tabindex="-1" id="viewDetailsModal-{{$book->id}}" aria-labelledby="viewDetailsModalLabel-{{$book->id}}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel-{{$book->id}}">Book Details: {{ $book->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="small text-muted">Book Information</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted">Title:</td>
                                <td><strong>{{ $book->title }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Author:</td>
                                <td>{{ $book->user->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email:</td>
                                <td>{{ $book->user->email }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Genre:</td>
                                <td>{{ $book->genre }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Price:</td>
                                <td>₦{{ number_format($book->price, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">ISBN:</td>
                                <td>{{ $book->isbn ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Type:</td>
                                <td>{{ ucfirst($book->book_type) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="small text-muted">Status & Performance</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted">Status:</td>
                                <td>
                                    @if($book->status === 'pending_review')
                                        <span class="badge badge-sm bg-warning">Pending Review</span>
                                    @elseif($book->status === 'send_review_copy')
                                        <span class="badge badge-sm bg-info">Send Review Copy</span>
                                    @elseif($book->status === 'approved_awaiting_delivery')
                                        <span class="badge badge-sm bg-success">Approved - Awaiting Delivery</span>
                                    @elseif($book->status === 'rejected')
                                        <span class="badge badge-sm bg-danger">Rejected</span>
                                    @elseif($book->status === 'stocked')
                                        <span class="badge badge-sm bg-info">Stocked</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Sales:</td>
                                <td>{{ $book->getSalesCount() }} copies</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Revenue:</td>
                                <td>₦{{ number_format($book->getTotalSales(), 2) }}</td>
                            </tr>
                            @if($book->status === 'stocked' && $book->quantity)
                            <tr>
                                <td class="text-muted">Quantity:</td>
                                <td>{{ $book->quantity }} copies</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted">Submitted:</td>
                                <td>{{ $book->created_at->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Last Updated:</td>
                                <td>{{ $book->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                            @if($book->rev_book_id)
                            <tr>
                                <td class="text-muted">ERP Book ID:</td>
                                <td>{{ $book->rev_book_id }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                @if($book->description)
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="small text-muted">Description</h6>
                        <div class="border p-3 rounded">
                            <p class="mb-0">{{ $book->description }}</p>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($book->admin_notes)
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="small text-muted">Admin Notes</h6>
                        <div class="border p-3 rounded bg-light">
                            <p class="mb-0">{{ $book->admin_notes }}</p>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="row">
                    <div class="col-12">
                        <h6 class="small text-muted">Recent Sales</h6>
                        @php
                            $recentSales = $book->walletTransactions()->where('type', 'sale')->latest()->limit(5)->get();
                        @endphp
                        @if($recentSales->count() > 0)
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
                                    @foreach($recentSales as $sale)
                                    <tr>
                                        <td>{{ $sale->created_at->format('M d, Y') }}</td>
                                        <td>₦{{ number_format($sale->amount, 2) }}</td>
                                        <td>{{ $sale->transaction_id ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted">No sales recorded yet.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
                @if($book->status === 'pending_review')
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#sendReviewCopyModal-{{$book->id}}">Send Review Copy</button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveForDeliveryModal-{{$book->id}}">Approve for Delivery</button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectBookModal-{{$book->id}}">Reject</button>
                @elseif($book->status === 'send_review_copy')
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveForDeliveryModal-{{$book->id}}">Approve for Delivery</button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectBookModal-{{$book->id}}">Reject</button>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#pendingReviewModal-{{$book->id}}">Set Pending Review</button>
                @elseif($book->status === 'approved_awaiting_delivery')
                <!-- Button to trigger the quantity modal instead of directly stocking -->
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#quantityModal-{{$book->id}}">Stock Book</button>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#pendingReviewModal-{{$book->id}}">Set Pending Review</button>
                @elseif($book->status === 'rejected')
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#sendReviewCopyModal-{{$book->id}}">Send Review Copy</button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveForDeliveryModal-{{$book->id}}">Approve for Delivery</button>
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#stockBookModal-{{$book->id}}">Stock Book</button>
                @elseif($book->status === 'stocked')
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#sendReviewCopyModal-{{$book->id}}">Send Review Copy</button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveForDeliveryModal-{{$book->id}}">Approve for Delivery</button>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#pendingReviewModal-{{$book->id}}">Set Pending Review</button>
                @else
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal-{{$book->id}}" data-bs-dismiss="modal">Edit Status</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Quantity Modal for each book -->
@foreach($books as $book)
<div class="modal fade" tabindex="-1" id="quantityModal-{{$book->id}}" aria-labelledby="quantityModalLabel-{{$book->id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quantityModalLabel-{{$book->id}}">Enter Quantity for {{ $book->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.books.review', $book) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="stocked">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" placeholder="Enter quantity" min="1" required>
                        <div class="form-note">Enter the number of copies being stocked in inventory.</div>
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">Admin Notes (Optional)</label>
                        <textarea class="form-control" name="admin_notes" rows="3" placeholder="Optional notes for the author...">{{ $book->admin_notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Stock Book</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Send Review Copy Modal for each book -->
@foreach($books as $book)
<div class="modal fade" tabindex="-1" id="sendReviewCopyModal-{{$book->id}}" aria-labelledby="sendReviewCopyModalLabel-{{$book->id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendReviewCopyModalLabel-{{$book->id}}">Send Review Copy for {{ $book->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.books.review', $book) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="send_review_copy">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <p>This will send a review copy of the book to the author and update the book status to "Send Review Copy".</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Admin Notes (Optional)</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Optional notes to include in the email to the author...">{{ $book->admin_notes }}</textarea>
                        <div class="form-note">These notes will be included in the email sent to the author.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Review Copy</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Approve for Delivery Modal for each book -->
@foreach($books as $book)
<div class="modal fade" tabindex="-1" id="approveForDeliveryModal-{{$book->id}}" aria-labelledby="approveForDeliveryModalLabel-{{$book->id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveForDeliveryModalLabel-{{$book->id}}">Approve for Delivery - {{ $book->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.books.review', $book) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="approved_awaiting_delivery">
                <div class="modal-body">
                    <div class="alert alert-success">
                        <p>This will approve the book for delivery and update the book status to "Approved - Awaiting Delivery".</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Admin Notes (Optional)</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Optional notes to include in the email to the author...">{{ $book->admin_notes }}</textarea>
                        <div class="form-note">These notes will be included in the email sent to the author.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Approve for Delivery</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Reject Book Modal for each book -->
@foreach($books as $book)
<div class="modal fade" tabindex="-1" id="rejectBookModal-{{$book->id}}" aria-labelledby="rejectBookModalLabel-{{$book->id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectBookModalLabel-{{$book->id}}">Reject Book - {{ $book->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.books.review', $book) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <p>Are you sure you want to reject this book? This will update the book status to "Rejected".</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Admin Notes (Required for rejection)</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Please provide reasons for rejection...">{{ $book->admin_notes }}</textarea>
                        <div class="form-note">These notes will be included in the email sent to the author.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Book</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Pending Review Modal for each book -->
@foreach($books as $book)
<div class="modal fade" tabindex="-1" id="pendingReviewModal-{{$book->id}}" aria-labelledby="pendingReviewModalLabel-{{$book->id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pendingReviewModalLabel-{{$book->id}}">Set to Pending Review - {{ $book->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.books.review', $book) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="pending_review">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <p>This will set the book status back to "Pending Review".</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Admin Notes (Optional)</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Optional notes to include in the email to the author...">{{ $book->admin_notes }}</textarea>
                        <div class="form-note">These notes will be included in the email sent to the author.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Set Pending Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Stock Book Modal for each book -->
@foreach($books as $book)
<div class="modal fade" tabindex="-1" id="stockBookModal-{{$book->id}}" aria-labelledby="stockBookModalLabel-{{$book->id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockBookModalLabel-{{$book->id}}">Stock Book - {{ $book->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.books.review', $book) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="stocked">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <p>This will stock the book in inventory and update the book status to "Stocked".</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Admin Notes (Optional)</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Optional notes to include in the email to the author...">{{ $book->admin_notes }}</textarea>
                        <div class="form-note">These notes will be included in the email sent to the author.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Stock Book</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Show/hide REV Book ID and admin notes fields based on status selection
function toggleRevBookIdField(bookId) {
    const modal = document.getElementById(`reviewModal-${bookId}`);
    if (!modal) return;
    
    const statusInputs = modal.querySelectorAll('input[name="status"]');
    const revBookIdGroup = document.getElementById(`revBookIdGroup-${bookId}`);
    const quantityGroup = document.getElementById(`quantityGroup-${bookId}`); // Added quantity group
    const adminNotesGroup = document.getElementById(`adminNotesGroup-${bookId}`);
    
    statusInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Handle REV Book ID field
            if (this.value === 'stocked') {
                if (revBookIdGroup) revBookIdGroup.style.display = 'block';
                if (quantityGroup) quantityGroup.style.display = 'block'; // Show quantity field
            } else {
                if (revBookIdGroup) revBookIdGroup.style.display = 'none';
                if (quantityGroup) quantityGroup.style.display = 'none'; // Hide quantity field
            }
            
            // Hide admin notes field only for rejected status
            if (this.value === 'rejected') {
                if (adminNotesGroup) adminNotesGroup.style.display = 'none';
            } else {
                if (adminNotesGroup) adminNotesGroup.style.display = 'block';
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
</script>
@endsection