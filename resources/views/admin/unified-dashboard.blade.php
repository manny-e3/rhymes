@extends('layouts.admin')

@section('title', 'Unified Dashboard | Rhymes Platform')

@section('page-title', 'Unified Dashboard')

@section('page-description', 'Comprehensive platform analytics and reports')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Unified Dashboard</h3>
                        <div class="nk-block-des text-soft">
                            <p>Comprehensive analytics and reports for the Rhymes Platform</p>
                        </div>
                    </div><!-- .nk-block-head-content -->
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="{{ route('admin.reports.sales-dashboard') }}" class="btn btn-primary"><em class="icon ni ni-dashboard"></em><span>Sales Dashboard</span></a></li>
                                    <li><a href="{{ route('admin.dashboard') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-dashboard"></em><span>Main Dashboard</span></a></li>
                                    <li><a href="{{ route('admin.erprev.sales') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-swap"></em><span>ERPREV Data</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- .nk-block-head-content -->
                </div><!-- .nk-block-between -->
            </div><!-- .nk-block-head -->

            <div class="nk-block">
                <!-- Date Range Filter -->
                <div class="card card-bordered mb-4">
                    <div class="card-inner">
                        <form method="GET" action="{{ route('admin.unified-dashboard') }}">
                            <div class="row g-gs">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="form-label">Date Range</label>
                                        <div class="form-control-wrap">
                                            <div class="input-daterange datepicker-wrap">
                                                <div class="input-group">
                                                    <input type="text" class="form-control date-picker" name="start_date" value="{{ request('start_date', now()->subDays(30)->format('m/d/Y')) }}" placeholder="Start Date">
                                                    <div class="input-group-addon">TO</div>
                                                    <input type="text" class="form-control date-picker" name="end_date" value="{{ request('end_date', now()->format('m/d/Y')) }}" placeholder="End Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label d-none d-md-block">&nbsp;</label>
                                        <div class="form-control-wrap">
                                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Overview Cards -->
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Active Users</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-users text-primary"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($overview['stats']['total_users'] ?? 0) }}</span>
                                    <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em>{{ number_format($overview['stats']['active_users'] ?? 0) }} active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">New Users</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-user-add text-info"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($overview['stats']['new_users'] ?? 0) }}</span>
                                    <span class="sub-title">This period</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Gross Revenue</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-coins text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($overview['stats']['gross_revenue'] ?? 0, 2) }}</span>
                                    <span class="sub-title">Platform: ₦{{ number_format($overview['stats']['platform_revenue'] ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Author Earnings</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-user-c text-warning"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($overview['stats']['author_earnings'] ?? 0, 2) }}</span>
                                    <span class="sub-title">Payouts: ₦{{ number_format(abs($overview['stats']['payouts_paid'] ?? 0), 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row g-gs mb-4">
                    <div class="col-lg-8">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Platform Analytics</h6>
                                        <p>Users, Authors & Books</p>
                                    </div>
                                </div>
                                <div class="nk-ck">
                                    <canvas class="analytics-chart" id="analyticsChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Sales Metrics</h6>
                                        <p>Key performance indicators</p>
                                    </div>
                                </div>
                                <div class="nk-ck">
                                    <canvas class="sales-chart" id="salesChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Tables Section -->
                <div class="row g-gs">
                    <div class="col-lg-6">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Top Performing Authors</h6>
                                        <p>By total earnings</p>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Author</th>
                                                <th>Books</th>
                                                <th>Earnings</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($topAuthors as $author)
                                                <tr>
                                                    <td>
                                                        <div class="user-card">
                                                            <div class="user-avatar bg-primary-dim">
                                                                <span>{{ strtoupper(substr($author->name, 0, 2)) }}</span>
                                                            </div>
                                                            <div class="user-info">
                                                                <span class="tb-lead">{{ $author->name }}</span>
                                                                <span>{{ $author->email }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $author->books_count }}</td>
                                                    <td>₦{{ number_format($author->total_earnings, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">No data available</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Top Selling Books</h6>
                                        <p>By revenue generated</p>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Book</th>
                                                <th>Author</th>
                                                <th>Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($topBooks as $book)
                                                <tr>
                                                    <td>
                                                        <span class="tb-lead">{{ Str::limit($book->title, 30) }}</span>
                                                        <span class="small text-muted">{{ $book->genre }}</span>
                                                    </td>
                                                    <td>{{ $book->user->name }}</td>
                                                    <td>₦{{ number_format($book->total_revenue, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">No data available</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Analytics Chart
    var analyticsCtx = document.getElementById('analyticsChart').getContext('2d');
    var analyticsChart = new Chart(analyticsCtx, {
        type: 'line',
        data: {
            labels: @json($analytics['chartData']['labels'] ?? []),
            datasets: [{
                label: 'Users',
                data: @json($analytics['chartData']['users'] ?? []),
                borderColor: '#559bfb',
                backgroundColor: 'rgba(85, 155, 251, 0.1)',
                borderWidth: 2,
                fill: true
            }, {
                label: 'Authors',
                data: @json($analytics['chartData']['authors'] ?? []),
                borderColor: '#1ee0ac',
                backgroundColor: 'rgba(30, 224, 172, 0.1)',
                borderWidth: 2,
                fill: true
            }, {
                label: 'Books',
                data: @json($analytics['chartData']['books'] ?? []),
                borderColor: '#f4bd0e',
                backgroundColor: 'rgba(244, 189, 14, 0.1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Sales Chart
    var salesCtx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: ['Revenue', 'Sales', 'AOV'],
            datasets: [{
                label: 'Current Period',
                data: [
                    {{ $sales['metrics']['total_revenue'] ?? 0 }},
                    {{ $sales['metrics']['total_sales'] ?? 0 }},
                    {{ $sales['metrics']['avg_order_value'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(85, 155, 251, 0.7)',
                    'rgba(30, 224, 172, 0.7)',
                    'rgba(244, 189, 14, 0.7)'
                ],
                borderColor: [
                    'rgba(85, 155, 251, 1)',
                    'rgba(30, 224, 172, 1)',
                    'rgba(244, 189, 14, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush
@endsection