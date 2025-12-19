@extends('layouts.admin')

@section('title', 'Payout Details | Admin Panel')

@section('page-title', 'Payout Details')

@section('page-description', 'Review payout request details')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Payout Request #{{ $payout->id }}</h3>
                        <div class="nk-block-des text-soft">
                            <p>Requested by {{ $payout->user->name }} • {{ $payout->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    @if($payout->status === 'pending')
                                        <li><a href="#" class="btn btn-success" onclick="reviewPayout({{ $payout->id }}, 'approve')"><em class="icon ni ni-check"></em><span>Approve</span></a></li>
                                        <li><a href="#" class="btn btn-danger" onclick="reviewPayout({{ $payout->id }}, 'deny')"><em class="icon ni ni-cross"></em><span>Deny</span></a></li>
                                    @endif
                                    <li><a href="{{ route('admin.payouts.index') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-arrow-left"></em><span>Back to Payouts</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- Payout Information -->
                    <div class="col-xxl-8">
                        <div class="card card-bordered card-full mb-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Payout Information</h6>
                                    </div>
                                    <div class="card-tools">
                                        @if($payout->status === 'pending')
                                            <span class="badge badge-warning">Pending Review</span>
                                        @elseif($payout->status === 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif($payout->status === 'denied')
                                            <span class="badge badge-danger">Denied</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Amount Requested</label>
                                            <div class="form-control-plaintext h4 text-primary">₦{{ number_format($payout->amount_requested, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Payment Method</label>
                                            <div class="form-control-plaintext">{{ ucfirst($payout->payment_method) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-label">Payment Details</label>
                                            <div class="form-control-plaintext">{{ $payout->payment_details ?: 'Not provided' }}</div>
                                        </div>
                                    </div>
                                    @if($payout->notes)
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Author Notes</label>
                                                <div class="alert alert-light">{{ $payout->notes }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($payout->admin_notes)
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Admin Notes</label>
                                                <div class="alert alert-info">{{ $payout->admin_notes }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($payout->processed_at)
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Processed Date</label>
                                                <div class="form-control-plaintext">{{ $payout->processed_at->format('M d, Y \a\t g:i A') }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($payout->processed_by)
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Processed By</label>
                                                <div class="form-control-plaintext">{{ $payout->processedBy->name ?? 'System' }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Author Earnings History -->
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Author Earnings Breakdown</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-4 mb-4">
                                    <div class="col-sm-3">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-success-dim">
                                                        <em class="icon ni ni-coins"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Total Earnings</p>
                                                    <h4 class="inbox-item-title">₦{{ number_format($userStats['total_earnings'], 2) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-primary-dim">
                                                        <em class="icon ni ni-tranx"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Total Payouts</p>
                                                    <h4 class="inbox-item-title">₦{{ number_format($userStats['total_payouts'], 2) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-warning-dim">
                                                        <em class="icon ni ni-clock"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Pending Payouts</p>
                                                    <h4 class="inbox-item-title">₦{{ number_format($userStats['pending_payouts'], 2) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-info-dim">
                                                        <em class="icon ni ni-wallet"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Available Balance</p>
                                                    <h4 class="inbox-item-title">₦{{ number_format($userStats['available_balance'], 2) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($payout->user->books->count() > 0)
                                    <div class="nk-tb-list nk-tb-orders">
                                        <div class="nk-tb-item nk-tb-head">
                                            <div class="nk-tb-col"><span>Book Title</span></div>
                                            <div class="nk-tb-col tb-col-md"><span>Sales</span></div>
                                            <div class="nk-tb-col tb-col-lg"><span>Revenue</span></div>
                                            <div class="nk-tb-col"><span>Status</span></div>
                                        </div>
                                        @foreach($payout->user->books as $book)
                                            @php
                                                $bookSales = $book->walletTransactions->where('type', 'sale')->count();
                                                $bookRevenue = $book->walletTransactions->where('type', 'sale')->sum('amount');
                                            @endphp
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col">
                                                    <span class="tb-lead">{{ $book->title }}</span>
                                                    <span class="tb-sub text-primary">{{ $book->genre }}</span>
                                                </div>
                                                <div class="nk-tb-col tb-col-md">
                                                    <span class="tb-lead">{{ $bookSales }}</span>
                                                </div>
                                                <div class="nk-tb-col tb-col-lg">
                                                    <span class="tb-lead">₦{{ number_format($bookRevenue, 2) }}</span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    @if($book->status === 'accepted')
                                                        <span class="badge badge-dot badge-dot-xs bg-success">Published</span>
                                                    @elseif($book->status === 'pending')
                                                        <span class="badge badge-dot badge-dot-xs bg-warning">Pending</span>
                                                    @else
                                                        <span class="badge badge-dot badge-dot-xs bg-danger">{{ ucfirst($book->status) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
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
                                        <span>{{ strtoupper(substr($payout->user->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="user-info">
                                        <h5>{{ $payout->user->name }}</h5>
                                        <span class="sub-text">{{ $payout->user->email }}</span>
                                    </div>
                                </div>
                                
                                <div class="user-meta mt-4">
                                    <ul class="nk-list-meta">
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Total Books:</span>
                                            <span class="nk-list-meta-value">{{ $payout->user->books->count() }}</span>
                                        </li>
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Published:</span>
                                            <span class="nk-list-meta-value">{{ $payout->user->books->where('status', 'accepted')->count() }}</span>
                                        </li>
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Member Since:</span>
                                            <span class="nk-list-meta-value">{{ $payout->user->created_at->format('M Y') }}</span>
                                        </li>
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Total Payouts:</span>
                                            <span class="nk-list-meta-value">{{ $payout->user->payouts->count() }}</span>
                                        </li>
                                    </ul>
                                </div>
                                
                                <div class="mt-4">
                                    <a href="{{ route('admin.users.show', $payout->user) }}" class="btn btn-outline-primary btn-block">View Author Profile</a>
                                </div>
                            </div>
                        </div>

                        <!-- Review Actions -->
                        @if($payout->status === 'pending')
                            <div class="card card-bordered card-full mb-4">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">Review Actions</h6>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-warning">
                                        <div class="alert-cta">
                                            <h6>Pending Review</h6>
                                            <p>This payout request is waiting for your review. Please approve or deny it.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <!-- Trigger approve modal -->
                                            <button type="button" class="btn btn-success btn-block" data-bs-toggle="modal" data-bs-target="#approveModal">
                                                <em class="icon ni ni-check"></em><span>Approve</span>
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <!-- Trigger deny modal -->
                                            <button type="button" class="btn btn-danger btn-block" data-bs-toggle="modal" data-bs-target="#denyModal">
                                                <em class="icon ni ni-cross"></em><span>Deny</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Payout Timeline -->
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Payout Timeline</h6>
                                    </div>
                                </div>
                                
                                <ul class="nk-activity">
                                    <li class="nk-activity-item">
                                        <div class="nk-activity-media user-avatar bg-primary">
                                            <em class="icon ni ni-plus"></em>
                                        </div>
                                        <div class="nk-activity-data">
                                            <div class="label">Payout requested</div>
                                            <span class="time">{{ $payout->created_at->format('M d, Y \a\t g:i A') }}</span>
                                        </div>
                                    </li>
                                    
                                    @if($payout->updated_at != $payout->created_at && $payout->status !== 'pending')
                                        <li class="nk-activity-item">
                                            <div class="nk-activity-media user-avatar {{ $payout->status === 'approved' ? 'bg-success' : 'bg-danger' }}">
                                                <em class="icon ni ni-{{ $payout->status === 'approved' ? 'check' : 'cross' }}"></em>
                                            </div>
                                            <div class="nk-activity-data">
                                                <div class="label">Payout {{ $payout->status }}</div>
                                                <span class="time">{{ $payout->updated_at->format('M d, Y \a\t g:i A') }}</span>
                                                @if($payout->processedBy)
                                                    <span class="sub-text">by {{ $payout->processedBy->name }}</span>
                                                @endif
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

@push('scripts')
<script>
// No JavaScript needed for form submissions
</script>
@endpush
@endsection