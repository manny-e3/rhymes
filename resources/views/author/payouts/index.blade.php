@extends('layouts.author')

@section('title', 'Payouts | Rhymes Author Platform')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('page-title', 'Payouts')

@section('page-description', 'Request payouts and track withdrawal history')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Payout Management</h3>
                        <div class="nk-block-des text-soft">
                            <p>Request withdrawals and track your payout history.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="nk-block-tools g-3">
                            <div class="nk-block-tools-opt">
                                <a href="{{ route('author.wallet.index') }}" class="btn btn-outline-light">
                                    <em class="icon ni ni-wallet"></em><span>View Wallet</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payout Request Form -->
            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-lg-8">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Request Payout</h6>
                                        <p class="text-soft">Withdraw your available earnings to your payment method.</p>
                                    </div>
                                </div>


                                <form action="{{ route('author.payouts.store') }}" method="POST" id="payoutForm">
                                    @csrf
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="amount_requested">Payout Amount</label>
                                                <div class="form-control-wrap">
                                                    <input type="number" 
                                                           class="form-control @error('amount_requested') is-invalid @enderror" 
                                                           id="amount_requested" 
                                                           name="amount_requested" 
                                                           placeholder="0.00" 
                                                           step="0.01" 
                                                           min="300000" 
                                                           max="{{ $availableBalance }}"
                                                           value="{{ old('amount_requested') }}">
                                                    <div class="form-note">
                                                        Minimum: ₦300,000.00 | Available: ₦{{ number_format($availableBalance, 2) }}
                                                        @if($payoutStats['pending_amount'] > 0)
                                                            <br><small class="text-warning">(₦{{ number_format($payoutStats['pending_amount'], 2) }} pending in other requests)</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary" {{ $availableBalance < 300000 ? 'disabled' : '' }}>
                                                    <em class="icon ni ni-wallet-out"></em>
                                                    <span>Request Payout</span>
                                                </button>
                                                @if($availableBalance < 300000)
                                                    <div class="form-note text-danger mt-2">
                                                        @if($payoutStats['pending_amount'] > 0)
                                                            Insufficient available balance. You have ₦{{ number_format($payoutStats['pending_amount'], 2) }} in pending payouts.
                                                        @else
                                                            Minimum balance of ₦300,000.00 required for payout
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
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
                                    <span class="amount">₦{{ number_format($availableBalance, 2) }}</span>
                                </div>
                                <div class="card-note">
                                    <span class="text-soft">Ready for withdrawal</span>
                                    @if($payoutStats['pending_amount'] > 0)
                                        <br><small class="text-warning">Total Balance: ₦{{ number_format($walletBalance, 2) }}</small>
                                        <br><small class="text-warning">Pending: ₦{{ number_format($payoutStats['pending_amount'], 2) }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card card-bordered mt-4">
                            <div class="card-inner">
                                <h6 class="title mb-3">Payout Information</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>Minimum Payout</span>
                                        <strong>₦300,000.00</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>Processing Time</span>
                                        <strong>3-5 days</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>Frequency Limit</span>
                                        <strong>Once every 30 days</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>Payment Method</span>
                                        <strong>{{ auth()->user()->payment_details ? 'Configured' : 'Not Set' }}</strong>
                                    </li>
                                </ul>
                                @if(!auth()->user()->payment_details)
                                    <div class="alert alert-warning mt-3" role="alert">
                                        <em class="icon ni ni-alert-circle"></em>
                                        Please configure your payment details to receive payouts.
                                        <div class="mt-2">
                                            <a href="{{ route('author.payouts.payment-details') }}" class="btn btn-sm btn-warning">
                                                <em class="icon ni ni-setting"></em> Configure Payment Details
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-3">
                                        <a href="{{ route('author.payouts.payment-details') }}" class="btn btn-sm btn-outline-primary">
                                            <em class="icon ni ni-edit"></em> Update Payment Details
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payout History -->
            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Payout History</h6>
                                </div>
                                <div class="card-tools">
                                    <ul class="card-tools-nav">
                                        <li><a href="#" class="active">All</a></li>
                                        <li><a href="?status=pending">Pending</a></li>
                                        <li><a href="?status=approved">Approved</a></li>
                                        <li><a href="?status=denied">Denied</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-inner p-0">
                            <div class="nk-tb-list">
                                <div class="nk-tb-item nk-tb-head">
                                    <div class="nk-tb-col"><span>Request Date</span></div>
                                    <div class="nk-tb-col tb-col-md"><span>Amount</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span>Status</span></div>
                                    <div class="nk-tb-col tb-col-md"><span>Processed Date</span></div>
                                    <div class="nk-tb-col tb-col-sm"><span>Notes</span></div>
                                </div>
                                @forelse($payouts as $payout)
                                <div class="nk-tb-item">
                                    <div class="nk-tb-col">
                                        <span class="tb-date">{{ $payout->created_at->format('M d, Y') }}</span>
                                        <span class="tb-sub">{{ $payout->created_at->format('H:i') }}</span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                        <span class="tb-amount">₦{{ number_format($payout->amount_requested, 2) }}</span>
                                    </div>
                                    <div class="nk-tb-col tb-col-lg">
                                        <span class="tb-status text-{{ $payout->status === 'approved' ? 'success' : ($payout->status === 'denied' ? 'danger' : 'warning') }}">
                                            <em class="icon ni ni-{{ $payout->status === 'approved' ? 'check-circle' : ($payout->status === 'denied' ? 'cross-circle' : 'clock') }}"></em>
                                            {{ ucfirst($payout->status) }}
                                        </span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                        <span class="tb-date">
                                            {{ $payout->processed_at ? $payout->processed_at->format('M d, Y') : 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="nk-tb-col tb-col-sm">
                                        <span class="tb-sub">{{ $payout->admin_notes ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                @empty
                                <div class="nk-tb-item">
                                    <div class="nk-tb-col text-center" colspan="5">
                                        <div class="py-4">
                                            <em class="icon ni ni-wallet-out text-soft" style="font-size: 3rem;"></em>
                                            <h6 class="mt-2">No payout requests yet</h6>
                                            <p class="text-soft">Your payout history will appear here once you make withdrawal requests.</p>
                                        </div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                        @if($payouts->hasPages())
                        <div class="card-inner">
                            {{ $payouts->links() }}
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
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount_requested');
    const form = document.getElementById('payoutForm');

    // Show success/error notifications
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ $errors->first() }}',
            confirmButtonText: 'OK'
        });
    @endif

    // Form validation and confirmation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const amount = parseFloat(amountInput.value) || 0;
        const maxAmount = {{ $availableBalance }};

        if (amount < 300000) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Amount',
                text: 'Minimum payout amount is ₦300,000.00',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (amount > maxAmount) {
            Swal.fire({
                icon: 'warning',
                title: 'Insufficient Balance',
                text: `Payout amount cannot exceed your available balance of ₦${maxAmount.toFixed(2)}`,
                confirmButtonText: 'OK'
            });
            return;
        }

        // Confirm payout request with SweetAlert
        Swal.fire({
            title: 'Confirm Payout Request',
            text: `Are you sure you want to request a payout of ₦${amount.toFixed(2)}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, request payout!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    text: 'Submitting your payout request',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit the form
                form.submit();
            }
        });
    });

    // Filter payouts by status
    const filterLinks = document.querySelectorAll('.card-tools-nav a');
    filterLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = new URL(window.location);
            const href = this.getAttribute('href');
            
            if (href.includes('?status=')) {
                const status = href.split('status=')[1];
                url.searchParams.set('status', status);
            } else {
                url.searchParams.delete('status');
            }
            
            window.location.href = url.toString();
        });
    });

    // Real-time balance validation
    amountInput.addEventListener('input', function() {
        const amount = parseFloat(this.value) || 0;
        const maxAmount = {{ $availableBalance }};
        const submitBtn = document.querySelector('button[type="submit"]');
        
        if (amount > maxAmount) {
            this.classList.add('is-invalid');
            submitBtn.disabled = true;
        } else {
            this.classList.remove('is-invalid');
            submitBtn.disabled = amount < 300000;
        }
    });
});
</script>
@endpush