@extends('layouts.admin')

@section('title', 'Analytics Dashboard | Admin Panel')

@section('page-title', 'Analytics Dashboard')

@section('page-description', 'Platform analytics and performance insights')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Analytics Dashboard</h3>
                        <div class="nk-block-des text-soft">
                            <p>Comprehensive platform analytics, user behavior, and performance insights.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li>
                                        <div class="form-control-wrap">
                                            <select class="form-select" onchange="changePeriod(this.value)">
                                                <option value="7">Last 7 Days</option>
                                                <option value="30" selected>Last 30 Days</option>
                                                <option value="90">Last 90 Days</option>
                                                <option value="365">Last Year</option>
                                            </select>
                                        </div>
                                    </li>
                                    <li><a href="#" class="btn btn-primary" onclick="refreshData()"><em class="icon ni ni-reload"></em><span>Refresh</span></a></li>
                                    <li><a href="{{ route('admin.unified-dashboard') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-dashboard"></em><span>Unified Dashboard</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Key Performance Indicators -->
            <div class="nk-block">
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
                                    <span class="amount">{{ number_format($analytics['active_users']) }}</span>
                                    <span class="sub-title">{{ number_format($analytics['new_users']) }} new this period</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Book Views</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-eye text-info"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($analytics['book_views']) }}</span>
                                    @if($analytics['views_change'] > 0)
                                        <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em>{{ number_format($analytics['views_change'], 1) }}%</span>
                                    @elseif($analytics['views_change'] < 0)
                                        <span class="sub-title text-danger"><em class="icon ni ni-arrow-long-down"></em>{{ number_format(abs($analytics['views_change']), 1) }}%</span>
                                    @else
                                        <span class="sub-title">No change</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Conversion Rate</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-bar-chart text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($analytics['conversion_rate'], 2) }}%</span>
                                    <span class="sub-title">Views to purchases</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Author Retention</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-user-check text-warning"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($analytics['author_retention'], 1) }}%</span>
                                    <span class="sub-title">Active authors</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- User Growth Chart -->
                    <div class="col-xxl-8">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">User Growth & Activity</h6>
                                        <p>User registration and activity trends</p>
                                    </div>
                                    <div class="card-tools">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">Metrics</a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="#" onclick="toggleMetric('users')"><span>New Users</span></a></li>
                                                    <li><a href="#" onclick="toggleMetric('authors')"><span>New Authors</span></a></li>
                                                    <li><a href="#" onclick="toggleMetric('books')"><span>Books Published</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="nk-chart-canvas">
                                    <canvas id="userGrowthChart" height="400"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Genre Performance -->
                    <div class="col-xxl-4">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Genre Performance</h6>
                                        <p>Sales by book genre</p>
                                    </div>
                                </div>
                                <div class="nk-chart-canvas">
                                    <canvas id="genreChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Platform Statistics -->
            <div class="nk-block">
                <div class="row g-gs">
                    <!-- Top Authors -->
                    <div class="col-lg-6">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Top Authors</h6>
                                        <p>Most successful authors by revenue</p>
                                    </div>
                                </div>
                                
                                @if(count($topAuthors) > 0)
                                    <div class="nk-tb-list nk-tb-orders">
                                        @foreach($topAuthors as $index => $author)
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col">
                                                    <div class="user-card">
                                                        <div class="user-avatar bg-primary">
                                                            <span>{{ $index + 1 }}</span>
                                                        </div>
                                                        <div class="user-info">
                                                            <span class="tb-lead">{{ $author->name }}</span>
                                                            <span class="tb-sub">{{ $author->books_count }} books</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="nk-tb-col tb-col-end">
                                                    <span class="tb-lead">${{ number_format($author->total_earnings, 2) }}</span>
                                                    <span class="tb-sub">Total earnings</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <em class="icon ni ni-users" style="font-size: 2rem; opacity: 0.3;"></em>
                                        <p class="text-soft mt-2">No author data available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="col-lg-6">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Platform Activity</h6>
                                        <p>Recent platform events</p>
                                    </div>
                                </div>
                                
                                @if(count($recentActivity) > 0)
                                    <ul class="nk-activity">
                                        @foreach($recentActivity as $activity)
                                            <li class="nk-activity-item">
                                                <div class="nk-activity-media user-avatar 
                                                    @if($activity['type'] === 'user_registered') bg-primary
                                                    @elseif($activity['type'] === 'book_published') bg-success
                                                    @elseif($activity['type'] === 'book_purchased') bg-info
                                                    @elseif($activity['type'] === 'payout_requested') bg-warning
                                                    @else bg-secondary @endif">
                                                    <em class="icon ni ni-
                                                        @if($activity['type'] === 'user_registered') user-add
                                                        @elseif($activity['type'] === 'book_published') book
                                                        @elseif($activity['type'] === 'book_purchased') cart
                                                        @elseif($activity['type'] === 'payout_requested') tranx
                                                        @else activity @endif"></em>
                                                </div>
                                                <div class="nk-activity-data">
                                                    <div class="label">{{ $activity['description'] }}</div>
                                                    <span class="time">{{ $activity['time'] }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-center py-4">
                                        <em class="icon ni ni-activity" style="font-size: 2rem; opacity: 0.3;"></em>
                                        <p class="text-soft mt-2">No recent activity</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Analytics Tables -->
            <div class="nk-block">
                <div class="row g-gs">
                    <!-- User Engagement -->
                    <div class="col-lg-6">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">User Engagement Metrics</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-4">
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-primary-dim">
                                                        <em class="icon ni ni-clock"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Avg. Session</p>
                                                    <h4 class="inbox-item-title">{{ $analytics['avg_session_duration'] }}m</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-success-dim">
                                                        <em class="icon ni ni-eye"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Pages/Session</p>
                                                    <h4 class="inbox-item-title">{{ number_format($analytics['pages_per_session'], 1) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-warning-dim">
                                                        <em class="icon ni ni-signin"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Bounce Rate</p>
                                                    <h4 class="inbox-item-title">{{ number_format($analytics['bounce_rate'], 1) }}%</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-info-dim">
                                                        <em class="icon ni ni-repeat"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Return Rate</p>
                                                    <h4 class="inbox-item-title">{{ number_format($analytics['return_rate'], 1) }}%</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Breakdown -->
                    <div class="col-lg-6">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Revenue Breakdown</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-4">
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-success-dim">
                                                        <em class="icon ni ni-coins"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Gross Revenue</p>
                                                    <h4 class="inbox-item-title">${{ number_format($analytics['gross_revenue'], 0) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-primary-dim">
                                                        <em class="icon ni ni-growth"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Platform Fee</p>
                                                    <h4 class="inbox-item-title">${{ number_format($analytics['platform_revenue'], 0) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-info-dim">
                                                        <em class="icon ni ni-user-check"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Author Earnings</p>
                                                    <h4 class="inbox-item-title">${{ number_format($analytics['author_earnings'], 0) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-warning-dim">
                                                        <em class="icon ni ni-tranx"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Payouts Paid</p>
                                                    <h4 class="inbox-item-title">${{ number_format($analytics['payouts_paid'], 0) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// User Growth Chart
const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
const userGrowthChart = new Chart(userGrowthCtx, {
    type: 'line',
    data: {
        labels: @json($chartData['labels']),
        datasets: [
            {
                label: 'New Users',
                data: @json($chartData['users']),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            },
            {
                label: 'New Authors',
                data: @json($chartData['authors']),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1
            },
            {
                label: 'Books Published',
                data: @json($chartData['books']),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.1
            }
        ]
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

// Genre Performance Chart
const genreCtx = document.getElementById('genreChart').getContext('2d');
const genreChart = new Chart(genreCtx, {
    type: 'doughnut',
    data: {
        labels: @json($genreData['labels']),
        datasets: [{
            data: @json($genreData['data']),
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40',
                '#FF6384',
                '#C9CBCF'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

function changePeriod(days) {
    const url = new URL(window.location);
    url.searchParams.set('period', days);
    window.location.href = url.toString();
}

function toggleMetric(metric) {
    // Toggle dataset visibility
    const datasets = userGrowthChart.data.datasets;
    const dataset = datasets.find(d => d.label.toLowerCase().includes(metric));
    if (dataset) {
        dataset.hidden = !dataset.hidden;
        userGrowthChart.update();
    }
}

function refreshData() {
    Swal.fire({
        title: 'Refreshing Data...',
        text: 'Please wait while we update the analytics.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        location.reload();
    }, 1000);
}
</script>
@endpush

@section('scripts')
    @stack('scripts')
@endsection