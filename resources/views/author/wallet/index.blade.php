@extends('layouts.author')

@section('title', 'Wallet | Rhymes Author Platform')

@section('page-title', 'Wallet')

@section('page-description', 'Manage your earnings and view transaction history')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Wallet Overview</h3>
                        <div class="nk-block-des text-soft">
                            <p>Track your earnings, view transaction history, and manage payouts.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="nk-block-tools g-3">
                            <div class="nk-block-tools-opt">
                                <a href="{{ route('author.payouts.index') }}" class="btn btn-primary">
                                    <em class="icon ni ni-wallet-out"></em><span>Request Payout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wallet Balance Cards -->
            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-md-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Available Balance</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint-icon icon ni ni-wallet text-primary"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($balance, 2) }}</span>
                                </div>
                                <div class="card-note">
                                    <span class="text-soft">Ready for withdrawal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Earnings</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint-icon icon ni ni-coins text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($transactions->where('type', 'sale')->sum('amount'), 2) }}</span>
                                </div>
                                <div class="card-note">
                                    <span class="text-soft">Lifetime earnings</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Sales</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint-icon icon ni ni-bar-chart text-info"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ $transactions->where('type', 'sale')->count() }}</span>
                                </div>
                                <div class="card-note">
                                    <span class="text-soft">Books sold</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales by Book -->
            @if(count($salesByBook) > 0)
            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner p-0">
                            <div class="nk-tb-list">
                                <div class="nk-tb-item nk-tb-head">
                                    <div class="nk-tb-col"><span>Book</span></div>
                                    <div class="nk-tb-col tb-col-md"><span>Sales Count</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span>Total Earnings</span></div>
                                    <div class="nk-tb-col tb-col-md"><span>Avg. per Sale</span></div>
                                </div>
                                @foreach($salesByBook as $sale)
                                <div class="nk-tb-item">
                                    <div class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-info">
                                                <span class="tb-lead">{{ $sale['title'] ?? 'Unknown Book' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                        <span class="tb-amount">{{ $sale['sales_count'] }}</span>
                                    </div>
                                    <div class="nk-tb-col tb-col-lg">
                                        <span class="tb-amount">₦{{ number_format($sale['total_sales'], 2) }}</span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                        <span class="tb-amount">₦{{ number_format($sale['total_sales'] / $sale['sales_count'], 2) }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Transaction History -->
            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Transaction History</h6>
                                </div>
                                <div class="card-tools">
                                    <ul class="card-tools-nav">
                                        <li><a href="#" class="active">All</a></li>
                                        <li><a href="?type=sale">Sales</a></li>
                                        <li><a href="?type=payout">Payouts</a></li>
                                        <li><a href="?type=commission">Commission</a></li>
                                    </ul>
                                    <div class="card-tools-more">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-icon btn-trigger dropdown-toggle" data-bs-toggle="dropdown">
                                                <em class="icon ni ni-more-h"></em>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="#" onclick="showExportModal()"><em class="icon ni ni-download"></em><span>Export Transactions</span></a></li>
                                                    <li><a href="#" onclick="showFilterModal()"><em class="icon ni ni-filter"></em><span>Advanced Filter</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-inner p-0">
                            <div class="nk-tb-list">
                                <div class="nk-tb-item nk-tb-head">
                                    <div class="nk-tb-col"><span>Type</span></div>
                                    <div class="nk-tb-col tb-col-md"><span>Book</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span>Amount</span></div>
                                    <div class="nk-tb-col tb-col-md"><span>Date</span></div>
                                    <div class="nk-tb-col tb-col-sm"><span>Status</span></div>
                                </div>
                                @forelse($transactions as $transaction)
                                <div class="nk-tb-item">
                                    <div class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-avatar sq bg-{{ $transaction->type === 'sale' ? 'success' : ($transaction->type === 'payout' ? 'warning' : 'info') }}">
                                                <em class="icon ni ni-{{ $transaction->type === 'sale' ? 'coins' : ($transaction->type === 'payout' ? 'wallet-out' : 'percent') }}"></em>
                                            </div>
                                            <div class="user-info">
                                                <span class="tb-lead">{{ ucfirst($transaction->type) }}</span>
                                                <span class="tb-sub">{{ $transaction->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                        <span class="tb-lead">{{ $transaction->book->title ?? 'N/A' }}</span>
                                    </div>
                                    <div class="nk-tb-col tb-col-lg">
                                        <span class="tb-amount {{ $transaction->amount > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->amount > 0 ? '+' : '' }}₦{{ number_format($transaction->amount, 2) }}
                                        </span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                        <span class="tb-sub">{{ $transaction->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <div class="nk-tb-col tb-col-sm">
                                        <span class="badge badge-dot badge-{{ $transaction->type === 'sale' ? 'success' : ($transaction->type === 'payout' ? 'warning' : 'info') }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </div>
                                </div>
                                @empty
                                <div class="nk-tb-item">
                                    <div class="nk-tb-col text-center" colspan="5">
                                        <div class="py-5">
                                            <em class="icon ni ni-wallet text-soft" style="font-size: 3rem;"></em>
                                            <h6 class="mt-3">No transactions yet</h6>
                                            <p class="text-soft">Your transaction history will appear here once you start selling books.</p>
                                        </div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                        @if($transactions->hasPages())
                        <div class="card-inner">
                            {{ $transactions->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Export modal functionality
function showExportModal() {
    Swal.fire({
        title: 'Export Transactions',
        html: `
            <form id="exportForm" action="{{ route('author.wallet.export') }}" method="GET">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Transaction Type</label>
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                <option value="sale">Sales</option>
                                <option value="payout">Payouts</option>
                                <option value="commission">Commission</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">From Date</label>
                            <input type="date" name="date_from" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">To Date</label>
                            <input type="date" name="date_to" class="form-control">
                        </div>
                    </div>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Export',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            document.getElementById('exportForm').submit();
        }
    });
}

// Filter modal functionality
function showFilterModal() {
    // Get current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    
    Swal.fire({
        title: 'Filter Transactions',
        html: `
            <form id="filterForm">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Transaction Type</label>
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                <option value="sale" ${urlParams.get('type') === 'sale' ? 'selected' : ''}>Sales</option>
                                <option value="payout" ${urlParams.get('type') === 'payout' ? 'selected' : ''}>Payouts</option>
                                <option value="commission" ${urlParams.get('type') === 'commission' ? 'selected' : ''}>Commission</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">From Date</label>
                            <input type="date" name="date_from" class="form-control" value="${urlParams.get('date_from') || ''}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">To Date</label>
                            <input type="date" name="date_to" class="form-control" value="${urlParams.get('date_to') || ''}">
                        </div>
                    </div>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Apply Filters',
        cancelButtonText: 'Clear Filters',
        reverseButtons: true,
        preConfirm: () => {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams();
            
            for (const [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }
            
            window.location.search = params.toString();
        }
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            // Clear all filters
            window.location.search = '';
        }
    });
}

// Filter transactions by type
document.addEventListener('DOMContentLoaded', function() {
    const filterLinks = document.querySelectorAll('.card-tools-nav a');
    filterLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = new URL(window.location);
            const href = this.getAttribute('href');
            
            if (href.includes('?type=')) {
                const type = href.split('type=')[1];
                url.searchParams.set('type', type);
            } else {
                url.searchParams.delete('type');
            }
            
            window.location.href = url.toString();
        });
    });
});
</script>
@endpush