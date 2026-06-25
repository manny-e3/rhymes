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
                                                    <option value="approved_awaiting_delivery" {{ request('status') === 'approved_awaiting_delivery' ? 'selected' : '' }}>Approved - AWaiting Delivery</option>
                                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                    <option value="stocked" {{ request('status') === 'stocked' ? 'selected' : '' }}>Stocked</option>
                                                    <option value="edited_pending_approval" {{ request('status') === 'edited_pending_approval' ? 'selected' : '' }}>Edited - Awaiting Approval</option>

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
                                             <div class="form-wrap w-100px">
                                                 <input type="number" name="quantity" class="form-control form-control-sm" placeholder="Qty" value="{{ request('quantity') }}">
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
                                        {{-- <div class="dropdown">
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
                                        </div> --}}
                                    </div>
                                </div>

                                @forelse($books as $book)
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            @if($book->image)
                                                <div class="user-card">
                                                    <div class="user-avatar bg-transparent">
                                                        <a href="{{ asset($book->image) }}" download="{{ Str::slug($book->title) }}-cover">
                                                            <img src="{{ asset($book->image) }}" alt="{{ $book->title }}" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="user-avatar bg-light">
                                                    <em class="icon ni ni-img-fill text-soft" style="font-size: 20px;"></em>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="nk-tb-col">
                                            <div class="user-card">
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
                                            @elseif($book->status === 'edited_pending_approval')
                                                <span class="badge badge-sm badge-dim bg-outline-warning">Edited - Awaiting Approval</span>
                                            @elseif($book->status === 'retrieval_requested')
                                                <span class="badge badge-sm badge-dim bg-outline-warning">Retrieval Requested</span>
                                            @elseif($book->status === 'recalled')
                                                <span class="badge badge-sm badge-dim bg-outline-danger">Retrieved</span>
                                            @endif
                                           
                                            @if($book->trashed())
                                                <span class="badge badge-sm badge-dim bg-outline-secondary">Deleted</span>
                                            @endif
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                             @php
                                                 $salesCount = $book->walletTransactions->where('type', 'sale')->sum(function ($t) {
                                                     return $t->meta['quantity_sold'] ?? $t->meta['QuantitySold'] ?? 1;
                                                 });
                                                 $revenue = $book->walletTransactions->where('type', 'sale')->sum('amount');
                                             @endphp
                                             <span class="tb-lead">{{ $salesCount }}</span>
                                             <span class="tb-sub">₦{{ number_format($revenue, 2) }}</span>
                                         </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            @if($book->status === 'stocked' && !is_null($book->quantity))
                                                @php
                                                    $copiesSold = $book->walletTransactions->where('type', 'sale')->sum(function ($t) {
                                                        return $t->meta['quantity_sold'] ?? $t->meta['QuantitySold'] ?? 1;
                                                    });
                                                    $initialQty = $book->quantity + $copiesSold;
                                                @endphp
                                                <span class="tb-lead">{{ $initialQty }}</span>
                                                @if($copiesSold > 0)
                                                    <span class="tb-sub" style="font-size: 11px; color: #e6820e; font-weight: 600;">({{ $book->quantity }} remaining)</span>
                                                @endif
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
                                                                <li><a href="#" data-bs-toggle="modal" data-bs-target="#editBookModal-{{$book->id}}"><em class="icon ni ni-pen2"></em><span>Edit Book</span></a></li>
                                                                 @if($book->status == 'retrieval_requested')
                                                                        <li class="divider"></li>
                                                                        <li>
                                                                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#approveRetrievalModal-{{ $book->id }}"><em class="icon ni ni-check"></em><span>Approve Retrieval</span></a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#denyRetrievalModal-{{ $book->id }}"><em class="icon ni ni-cross"></em><span>Deny Retrieval</span></a>
                                                                        </li>
                                                                        @endif
                                                                       

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
                                                                    <li>
                                                                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changeStatusModal-{{$book->id}}"><em class="icon ni ni-edit"></em><span>Change Status</span></a>
                                                                    </li>
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

<!-- View Details Modal -->
<div class="modal fade" tabindex="-1" id="viewDetailsModal-{{$book->id}}" aria-labelledby="viewDetailsModalLabel-{{$book->id}}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel-{{$book->id}}">Book Details: {{ $book->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card card-bordered h-100">
                            <div class="card-inner d-flex flex-column align-items-center justify-content-center p-2 bg-light" style="min-height: 200px;">
                                @if($book->image)
                                    <img src="{{ asset($book->image) }}" class="rounded shadow-sm mb-2" alt="{{ $book->title }}" style="max-width: 100%; max-height: 280px; object-fit: contain;">
                                    <a href="{{ asset($book->image) }}" download="{{ Str::slug($book->title) }}-cover" class="btn btn-sm btn-outline-primary">
                                        <em class="icon ni ni-download"></em><span>Download Cover</span>
                                    </a>
                                @else
                                    <div class="text-center text-soft">
                                        <em class="icon ni ni-book-read" style="font-size: 48px;"></em>
                                        <p class="mt-2 small">No Cover</p>
                                    </div>
                                @endif
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
                                    @elseif($book->status === 'recalled')
                                        <span class="badge badge-sm bg-warning">Retrieved</span>
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
                            @if($book->status === 'stocked' && !is_null($book->quantity))
                            <tr>
                                <td class="text-muted">Quantity:</td>
                                <td>
                                    @php
                                        $copiesSold = $book->walletTransactions->where('type', 'sale')->sum(function ($t) {
                                            return $t->meta['quantity_sold'] ?? $t->meta['QuantitySold'] ?? 1;
                                        });
                                        $initialQty = $book->quantity + $copiesSold;
                                    @endphp
                                    {{ $initialQty }} copies
                                    @if($copiesSold > 0)
                                        <br><small style="color: #e6820e; font-weight: 600;">({{ $book->quantity }} remaining)</small>
                                    @endif
                                </td>
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
                <!-- @if($book->status === 'pending_review')
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#sendReviewCopyModal-{{$book->id}}">Send Review Copy</button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveForDeliveryModal-{{$book->id}}">Approve for Delivery</button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectBookModal-{{$book->id}}">Reject</button>
                @elseif($book->status === 'send_review_copy')
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveForDeliveryModal-{{$book->id}}">Approve for Delivery</button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectBookModal-{{$book->id}}">Reject</button>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#pendingReviewModal-{{$book->id}}">Set Pending Review</button>
                @elseif($book->status === 'approved_awaiting_delivery')
              
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
                @elseif($book->status === 'recalled')
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#sendReviewCopyModal-{{$book->id}}">Send Review Copy</button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveForDeliveryModal-{{$book->id}}">Approve for Delivery</button>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#pendingReviewModal-{{$book->id}}">Set Pending Review</button>
                @elseif($book->status === 'edited_pending_approval')
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#viewDetailsModal-{{$book->id}}">View Details</button>
                @else
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal-{{$book->id}}" data-bs-dismiss="modal">Edit Status</button>
                @endif -->
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Change Status Modal for each book -->
@foreach($books as $book)
<div class="modal fade" tabindex="-1" id="changeStatusModal-{{$book->id}}" aria-labelledby="changeStatusModalLabel-{{$book->id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeStatusModalLabel-{{$book->id}}">Change Status: {{ $book->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.books.review', $book) }}">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select status-select" data-book-id="{{ $book->id }}" required>
                            <option value="pending_review" {{ $book->status === 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                            <option value="send_review_copy" {{ $book->status === 'send_review_copy' ? 'selected' : '' }}>Send Review Copy</option>
                            <option value="approved_awaiting_delivery" {{ $book->status === 'approved_awaiting_delivery' ? 'selected' : '' }}>Approved - Awaiting Delivery</option>
                            <option value="rejected" {{ $book->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="stocked" {{ $book->status === 'stocked' ? 'selected' : '' }}>Stocked</option>
                            <!-- <option value="edited_pending_approval" {{ $book->status === 'edited_pending_approval' ? 'selected' : '' }}>Edited - Awaiting Approval</option> -->
                            <option value="retrieval_requested" {{ $book->status === 'retrieval_requested' ? 'selected' : '' }}>Retrieval Requested</option>
                            <option value="recalled" {{ $book->status === 'recalled' ? 'selected' : '' }}>Retrieved</option>
                        </select>
                    </div>
                    
                    <div class="form-group mb-3 quantity-group-{{$book->id}}" style="{{ $book->status === 'stocked' ? 'display: block;' : 'display: none;' }}">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control quantity-input-{{$book->id}}" name="quantity" value="{{ $book->quantity }}" placeholder="Enter quantity" min="1">
                        <div class="form-note">Enter the number of copies being stocked in inventory.</div>
                    </div>
                    
                    <!-- <div class="form-group mb-3 rev-book-id-group-{{$book->id}}" id="revBookIdGroup-{{$book->id}}" style="{{ $book->status === 'stocked' ? 'display: block;' : 'display: none;' }}">
                        <label class="form-label">REV Book ID</label>
                        <input type="text" class="form-control" name="rev_book_id" placeholder="Enter REV system book ID" value="{{ $book->rev_book_id }}">
                        <div class="form-note">This will be automatically populated when the book is registered with the ERP system.</div>
                    </div> -->

                    <div class="form-group mb-3 admin-notes-group-{{$book->id}}" id="adminNotesGroup-{{$book->id}}" style="{{ $book->status === 'rejected' ? 'display: none;' : 'display: block;' }}">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Optional notes for the author...">{{ $book->admin_notes }}</textarea>
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
@endforeach

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

{{-- Edit Book Modals --}}
@foreach($books as $book)
<div class="modal fade" tabindex="-1" id="editBookModal-{{$book->id}}" aria-labelledby="editBookModalLabel-{{$book->id}}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBookModalLabel-{{$book->id}}"><em class="icon ni ni-pen2 me-2"></em>Edit Book: {{ $book->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.books.edit', $book) }}">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="title" value="{{ $book->title }}" required maxlength="255">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Book Type <span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <select name="book_type" class="form-select" required>
                                        <option value="">Select Type</option>
                                        <option value="paper_back" {{ $book->book_type === 'paper_back' ? 'selected' : '' }}>Paper back</option>
                                        <option value="hard_back" {{ $book->book_type === 'hard_back' ? 'selected' : '' }}>Hard back</option>
                                        <option value="both" {{ $book->book_type === 'both' ? 'selected' : '' }}>Both</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Genre <span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="genre" value="{{ $book->genre }}" required maxlength="100">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Price (₦) <span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <input type="number" class="form-control" name="price" value="{{ $book->price }}" required min="0" step="0.01">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">ISBN</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="isbn" value="{{ $book->isbn }}" maxlength="50" placeholder="e.g. 9780241217931">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control" name="description" rows="4" maxlength="5000" placeholder="Book description...">{{ $book->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 alert alert-warning py-2">
                        <em class="icon ni ni-alert-circle me-1"></em>
                        <small>Editing book details will update the information visible to the author and on the platform. It will <strong>not</strong> change the book's status or notify the author.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><em class="icon ni ni-save me-1"></em>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection