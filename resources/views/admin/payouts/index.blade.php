@extends('layouts.admin')

@section('title', 'Payout Management | Admin Panel')

@section('page-title', 'Payout Management')

@section('page-description', 'Review and manage author payout requests')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Payout Management</h3>
                        <div class="nk-block-des text-soft">
                            <p>Review, approve, and manage author payout requests.</p>
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
                                                <a class="dropdown-item" href="{{ route('admin.payouts.export.csv', request()->query()) }}"><em class="icon ni ni-file-text"></em><span>Export as CSV</span></a>
                                                <a class="dropdown-item" href="{{ route('admin.payouts.export.pdf', request()->query()) }}"><em class="icon ni ni-file-pdf"></em><span>Export as PDF</span></a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-gs mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-2">
                                <div class="card-title">
                                    <h6 class="title">Total Payouts</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-tranx text-primary"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount">{{ number_format($stats['total_payouts']) }}</span>
                                <span class="sub-title">₦{{ number_format($stats['total_amount_requested'], 2) }} requested</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-2">
                                <div class="card-title">
                                    <h6 class="title">Pending</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-clock text-warning"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount">{{ number_format($stats['pending_payouts']) }}</span>
                                <span class="sub-title">₦{{ number_format($stats['pending_amount'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-2">
                                <div class="card-title">
                                    <h6 class="title">Approved</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-check-circle text-success"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount">{{ number_format($stats['approved_payouts']) }}</span>
                                <span class="sub-title">₦{{ number_format($stats['approved_amount'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-2">
                                <div class="card-title">
                                    <h6 class="title">Denied</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-cross-circle text-danger"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount">{{ number_format($stats['denied_payouts']) }}</span>
                                <span class="sub-title">Rejected requests</span>
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
                                        <form method="GET" action="{{ route('admin.payouts.index') }}" class="d-flex gap-2">
                                            <div class="form-wrap w-150px">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="">All Status</option>
                                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                                    <option value="denied" {{ request('status') === 'denied' ? 'selected' : '' }}>Denied</option>
                                                </select>
                                            </div>
                                            <div class="form-wrap w-150px">
                                                <input type="number" name="amount_min" class="form-control form-control-sm" placeholder="Min Amount" value="{{ request('amount_min') }}">
                                            </div>
                                            <div class="form-wrap w-150px">
                                                <input type="number" name="amount_max" class="form-control form-control-sm" placeholder="Max Amount" value="{{ request('amount_max') }}">
                                            </div>
                                            <div class="form-wrap flex-md-nowrap">
                                                <div class="form-icon form-icon-right">
                                                    <em class="icon ni ni-search"></em>
                                                </div>
                                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search authors..." value="{{ request('search') }}">
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
                                    <div class="nk-tb-col nk-tb-col-check">
                                        <div class="custom-control custom-control-sm custom-checkbox notext">
                                            <input type="checkbox" class="custom-control-input" id="selectAll">
                                            <label class="custom-control-label" for="selectAll"></label>
                                        </div>
                                    </div>
                                    <div class="nk-tb-col"><span class="sub-text">Author</span></div>
                                    <div class="nk-tb-col tb-col-mb"><span class="sub-text">Amount</span></div>
                                    <div class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Payment Method</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Requested</span></div>
                                    {{-- <div class="nk-tb-col nk-tb-col-tools text-end">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-xs btn-outline-light btn-icon dropdown-toggle" data-bs-toggle="dropdown"><em class="icon ni ni-plus"></em></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="javascript:void(0)" onclick="bulkAction('approve'); return false;"><span>Bulk Approve</span></a></li>
                                                    <li><a href="javascript:void(0)" onclick="bulkAction('deny'); return false;"><span>Bulk Deny</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>

                                @forelse($payouts as $payout)
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col nk-tb-col-check">
                                            <div class="custom-control custom-control-sm custom-checkbox notext">
                                                <input type="checkbox" class="custom-control-input payout-checkbox" id="payout{{ $payout->id }}" value="{{ $payout->id }}" {{ $payout->status !== 'pending' ? 'disabled' : '' }}>
                                                <label class="custom-control-label" for="payout{{ $payout->id }}"></label>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary-dim">
                                                    <span>{{ strtoupper(substr($payout->user->name, 0, 2)) }}</span>
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead">{{ $payout->user->name }}</span>
                                                    <span>{{ $payout->user->email }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col tb-col-mb">
                                            <span class="tb-lead">₦{{ number_format($payout->amount_requested, 2) }}</span>
                                            <span class="tb-sub">Requested</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
                                            @if($payout->status === 'pending')
                                                <span class="badge badge-sm badge-dim bg-outline-warning">Pending</span>
                                            @elseif($payout->status === 'approved')
                                                <span class="badge badge-sm badge-dim bg-outline-success">Approved</span>
                                            @elseif($payout->status === 'denied')
                                                <span class="badge badge-sm badge-dim bg-outline-danger">Denied</span>
                                            @endif
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span class="tb-lead">{{ ucfirst($payout->payment_method) }}</span>
                                            <span class="tb-sub">{{ $payout->payment_details }}</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span>{{ $payout->created_at->format('M d, Y') }}</span>
                                            <span class="tb-sub">{{ $payout->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="nk-tb-col nk-tb-col-tools">
                                            <ul class="nk-tb-actions gx-1">
                                                <li>
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li><a href="#" data-bs-toggle="modal" data-bs-target="#viewModal-{{ $payout->id }}"><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                @if($payout->status === 'pending')
                                                                    <li><a href="#" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $payout->id }}"><em class="icon ni ni-check"></em><span>Approve</span></a></li>
                                                                    <li><a href="#" data-bs-toggle="modal" data-bs-target="#denyModal-{{ $payout->id }}"><em class="icon ni ni-cross"></em><span>Deny</span></a></li>
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
                                                <em class="icon ni ni-tranx" style="font-size: 3rem; opacity: 0.3;"></em>
                                                <p class="text-soft mt-2">No payout requests found</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="card-inner">
                            {{ $payouts->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($payouts as $payout)
<!-- View Payout Modal for {{ $payout->id }} -->
<div class="modal fade" id="viewModal-{{ $payout->id }}" tabindex="-1" aria-labelledby="viewModalLabel-{{ $payout->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel-{{ $payout->id }}">Payout Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row gy-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Author</label>
                            <div class="form-control-wrap">
                                <div class="user-card">
                                    <div class="user-avatar bg-primary-dim">
                                        <span>{{ strtoupper(substr($payout->user->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="user-info">
                                        <span class="tb-lead">{{ $payout->user->name }}</span>
                                        <span>{{ $payout->user->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Amount Requested</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control form-control-lg" value="₦{{ number_format($payout->amount_requested, 2) }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <div class="form-control-wrap">
                                @if($payout->status === 'pending')
                                    <span class="badge badge-dot bg-warning">Pending</span>
                                @elseif($payout->status === 'approved')
                                    <span class="badge badge-dot bg-success">Approved</span>
                                @elseif($payout->status === 'denied')
                                    <span class="badge badge-dot bg-danger">Denied</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Requested Date</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" value="{{ $payout->created_at->format('M d, Y') }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Processed Date</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" value="{{ $payout->processed_at ? $payout->processed_at->format('M d, Y') : 'Not processed yet' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Payment Method</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" value="{{ ucfirst($payout->payment_method) }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Payment Details</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control" rows="2" readonly>{{ $payout->payment_details }}</textarea>
                            </div>
                        </div>
                    </div>
                    @if($payout->admin_notes)
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Admin Notes</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control" rows="3" readonly>{{ $payout->admin_notes }}</textarea>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
                @if($payout->status === 'pending')
                    <!-- Trigger approve modal -->
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $payout->id }}">Approve</button>
                    <!-- Trigger deny modal -->
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#denyModal-{{ $payout->id }}">Deny</button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal for {{ $payout->id }} -->
<div class="modal fade" id="approveModal-{{ $payout->id }}" tabindex="-1" aria-labelledby="approveModalLabel-{{ $payout->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.payouts.approve', $payout) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel-{{ $payout->id }}">Approve Payout Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Amount Requested</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" value="₦{{ number_format($payout->amount_requested, 2) }}" readonly>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Author</label>
                        <div class="form-control-wrap">
                            <div class="user-card">
                                <div class="user-avatar bg-primary-dim">
                                    <span>{{ strtoupper(substr($payout->user->name, 0, 2)) }}</span>
                                </div>
                                <div class="user-info">
                                    <span class="tb-lead">{{ $payout->user->name }}</span>
                                    <span>{{ $payout->user->email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Admin Notes (Optional)</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Optional notes for the author..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Payout</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Deny Modal for {{ $payout->id }} -->
<div class="modal fade" id="denyModal-{{ $payout->id }}" tabindex="-1" aria-labelledby="denyModalLabel-{{ $payout->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.payouts.deny', $payout) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="denyModalLabel-{{ $payout->id }}">Deny Payout Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Amount Requested</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" value="₦{{ number_format($payout->amount_requested, 2) }}" readonly>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Author</label>
                        <div class="form-control-wrap">
                            <div class="user-card">
                                <div class="user-avatar bg-primary-dim">
                                    <span>{{ strtoupper(substr($payout->user->name, 0, 2)) }}</span>
                                </div>
                                <div class="user-info">
                                    <span class="tb-lead">{{ $payout->user->name }}</span>
                                    <span>{{ $payout->user->email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Reason for Denial (Required)</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Reason for denying this payout..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Deny Payout</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
// Bulk action function
function bulkAction(action) {
    event.preventDefault();
    
    const selectedPayouts = Array.from(document.querySelectorAll('.payout-checkbox:checked')).map(cb => cb.value);
    
    if (selectedPayouts.length === 0) {
        Swal.fire('Warning!', 'Please select at least one payout request.', 'warning');
        return;
    }
    
    const actionText = action === 'approve' ? 'approve' : 'deny';
    
    Swal.fire({
        title: `${actionText.charAt(0).toUpperCase() + actionText.slice(1)} Payouts?`,
        text: `This will ${actionText} ${selectedPayouts.length} selected payout request(s).`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: action === 'approve' ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${actionText}!`
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
            
            fetch('/admin/payouts/bulk-action', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    action: action,
                    payout_ids: selectedPayouts
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire('Success!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message || 'Failed to process request', 'error');
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
            });
        }
    });
}

// Select all checkbox functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.payout-checkbox:not([disabled])');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Update select all checkbox state when individual checkboxes change
    const payoutCheckboxes = document.querySelectorAll('.payout-checkbox');
    payoutCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allCheckboxes = document.querySelectorAll('.payout-checkbox:not([disabled])');
            const checkedCheckboxes = document.querySelectorAll('.payout-checkbox:not([disabled]):checked');
            const selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.checked = allCheckboxes.length === checkedCheckboxes.length && allCheckboxes.length > 0;
            }
        });
    });
});
</script>
@endpush
@endsection