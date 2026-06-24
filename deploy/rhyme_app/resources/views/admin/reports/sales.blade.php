@extends('layouts.admin')

@section('title', 'Revenue | Admin Panel')

@section('page-title', 'Revenue')

@section('page-description', 'Comprehensive sales analytics and reporting')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Revenue</h3>
                        <div class="nk-block-des text-soft">
                            <p>Comprehensive sales analytics, revenue tracking, and performance metrics.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li>
                                        <form method="GET" action="{{ route('admin.revenue.index') }}" class="d-flex g-2 align-items-center" id="filterForm">
                                            <div class="form-control-wrap">
                                                <select name="period" class="form-select form-select-sm" onchange="toggleCustomDates(this.value)">
                                                    <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Today</option>
                                                    <option value="yesterday" {{ request('period') === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                                    <option value="last_7_days" {{ request('period') === 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                                                    <option value="last_30_days" {{ request('period', 'last_30_days') === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                                                    <option value="this_month" {{ request('period') === 'this_month' ? 'selected' : '' }}>This Month</option>
                                                    <option value="last_month" {{ request('period') === 'last_month' ? 'selected' : '' }}>Last Month</option>
                                                    <option value="this_year" {{ request('period') === 'this_year' ? 'selected' : '' }}>This Year</option>
                                                    <option value="custom" {{ request('period') === 'custom' ? 'selected' : '' }}>Custom</option>
                                                </select>
                                            </div>
                                            <div id="custom-date-inputs" class="d-flex g-2" style="display: {{ request('period') === 'custom' ? 'flex' : 'none' }};">
                                                <div class="form-control-wrap">
                                                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                                                </div>
                                                <div class="form-control-wrap">
                                                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

         

            <!-- Key Metrics -->
            <div class="nk-block">
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Revenue</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-coins text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($metrics['total_revenue'], 2) }}</span>
                                    @if($metrics['revenue_change'] > 0)
                                        <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em>{{ number_format($metrics['revenue_change'], 1) }}%</span>
                                    @elseif($metrics['revenue_change'] < 0)
                                        <span class="sub-title text-danger"><em class="icon ni ni-arrow-long-down"></em>{{ number_format(abs($metrics['revenue_change']), 1) }}%</span>
                                    @else
                                        <span class="sub-title">No change</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Sales</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-cart text-primary"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($metrics['total_sales']) }}</span>
                                    @if($metrics['sales_change'] > 0)
                                        <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em>{{ number_format($metrics['sales_change'], 1) }}%</span>
                                    @elseif($metrics['sales_change'] < 0)
                                        <span class="sub-title text-danger"><em class="icon ni ni-arrow-long-down"></em>{{ number_format(abs($metrics['sales_change']), 1) }}%</span>
                                    @else
                                        <span class="sub-title">No change</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Average Order Value</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-bar-chart text-info"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($metrics['avg_order_value'], 2) }}</span>
                                    @if($metrics['aov_change'] > 0)
                                        <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em>{{ number_format($metrics['aov_change'], 1) }}%</span>
                                    @elseif($metrics['aov_change'] < 0)
                                        <span class="sub-title text-danger"><em class="icon ni ni-arrow-long-down"></em>{{ number_format(abs($metrics['aov_change']), 1) }}%</span>
                                    @else
                                        <span class="sub-title">No change</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Platform Commission</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-growth text-warning"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($metrics['platform_commission'], 2) }}</span>
                                    <span class="sub-title">{{ number_format($metrics['commission_rate'], 1) }}% rate</span>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>

            <!-- Sales Overview Component -->
            <div class="nk-block">
                <x-sales-overview 
                    :revenue-data="$overviewData['revenueData']"
                    :metrics-data="$overviewData['metricsData']"
                    :performance-data="$overviewData['performanceData']"
                />
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- Revenue Chart -->
                    <!-- <div class="col-xxl-8">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Revenue Trend</h6>
                                        <p>Daily revenue over the selected period</p>
                                    </div>
                                    <div class="card-tools">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">Chart Type</a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="#" onclick="changeChartType('line')"><span>Line Chart</span></a></li>
                                                    <li><a href="#" onclick="changeChartType('bar')"><span>Bar Chart</span></a></li>
                                                    <li><a href="#" onclick="changeChartType('area')"><span>Area Chart</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="nk-chart-canvas">
                                    <canvas id="revenueChart" height="400"></canvas>
                                </div>
                            </div>
                        </div>
                    </div> -->

                   
                </div>
            </div>

            <!-- Detailed Sales Table -->
            <div class="nk-block">
                <div class="card card-bordered card-stretch">
                    <div class="card-inner-group">
                        <div class="card-inner position-relative card-tools-toggle">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Sales Transactions</h6>
                                </div>
                                <div class="card-tools">
                                    <ul class="nk-block-tools g-3">
                                        <li>
                                            <div class="form-control-wrap">
                                                <div class="form-icon form-icon-right">
                                                    <em class="icon ni ni-search"></em>
                                                </div>
                                                <input type="text" class="form-control form-control-sm" id="tableSearch" value="{{ request('search') }}" placeholder="Search transactions..." onkeyup="if(event.keyCode == 13) searchTransactions()">
                                            </div>
                                        </li>
                                        
                                        <li class="nk-block-tools-opt">
                                            <a href="{{ route('admin.revenue.index') }}" class="btn btn-icon btn-primary d-md-none"><em class="icon ni ni-reload"></em></a>
                                            <a href="{{ route('admin.revenue.index') }}" class="btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-reload"></em><span>Reset</span></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card-inner p-0">
                            <div class="nk-tb-list nk-tb-ulist">
                                <div class="nk-tb-item nk-tb-head">
                                    <div class="nk-tb-col"><span class="sub-text">Transaction</span></div>
                                    <div class="nk-tb-col tb-col-mb"><span class="sub-text">Book</span></div>
                                    <div class="nk-tb-col tb-col-md"><span class="sub-text">Author</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Amount</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Date</span></div>
                                </div>

                                @forelse($transactions as $transaction)
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <span class="tb-lead">#{{ $transaction->id }}</span>
                                            <span class="tb-sub">{{ $transaction->type }}</span>
                                            <span class="tb-sub">{{ $transaction->book->isbn }}</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-mb">
                                            <span class="tb-lead">{{ $transaction->book->title ?? 'N/A' }}</span>
                                            <span class="tb-sub">{{ $transaction->book->genre ?? '' }}</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
                                            <span class="tb-lead">{{ $transaction->user->name }}</span>
                                            <span class="tb-sub">{{ $transaction->user->email }}</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span class="tb-lead text-success">₦{{ number_format($transaction->amount, 2) }}</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span>{{ $transaction->created_at->format('M d, Y') }}</span>
                                            <span class="tb-sub">{{ $transaction->created_at->format('g:i A') }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <div class="text-center py-4">
                                                <em class="icon ni ni-tranx" style="font-size: 3rem; opacity: 0.3;"></em>
                                                <p class="text-soft mt-2">No transactions found</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        @if($transactions->hasPages())
                            <div class="card-inner">
                                {{ $transactions->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
let revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chartData['labels']),
        datasets: [{
            label: 'Revenue',
            data: @json($chartData['revenue']),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Revenue: $' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

function toggleCustomDates(period) {
    const customInputs = document.getElementById('custom-date-inputs');
    if (period === 'custom') {
        customInputs.style.display = 'flex';
    } else {
        customInputs.style.display = 'none';
        document.getElementById('filterForm').submit();
    }
}

function changeChartType(type) {
    revenueChart.config.type = type;
    revenueChart.update();
}

function searchTransactions() {
    const search = document.getElementById('tableSearch').value;
    const url = new URL(window.location);
    if (search) {
        url.searchParams.set('search', search);
    } else {
        url.searchParams.delete('search');
    }
    url.searchParams.set('page', 1); // Reset to first page
    window.location.href = url.toString();
}

function filterByBook(bookId) {
    const url = new URL(window.location);
    if (bookId) {
        url.searchParams.set('book_id', bookId);
    } else {
        url.searchParams.delete('book_id');
    }
    url.searchParams.set('page', 1); // Reset to first page
    window.location.href = url.toString();
}

function exportReport(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);
    
    Swal.fire({
        title: 'Generating Report...',
        text: 'Please wait while we prepare your report.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(`{{ route('admin.reports.sales') }}?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = `sales-report-${new Date().toISOString().split('T')[0]}.${format}`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        Swal.close();
    })
    .catch(error => {
        Swal.fire('Error!', 'Failed to generate report.', 'error');
    });
}
</script>
@endpush
@endsection