@extends('layouts.admin')

@section('title', 'Sales Dashboard | Admin Panel')

@section('page-title', 'Sales Dashboard')

@section('page-description', 'Real-time sales analytics and performance metrics')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Sales Dashboard</h3>
                        <div class="nk-block-des text-soft">
                            <p>Real-time sales analytics and performance metrics.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="#" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-calendar"></em><span>Last 30 Days</span></a></li>
                                    <li><a href="{{ route('admin.reports.sales') }}" class="btn btn-primary"><em class="icon ni ni-reports"></em><span>Detailed Reports</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Metrics Component -->
            <div class="nk-block">
                <x-sales-metrics 
                    title1="Total Revenue" 
                    value1="${{ number_format($totalRevenue, 2) }}" 
                    change1="{{ number_format($revenueChange, 1) }}"
                    chartId1="revenueMiniChart"
                    
                    title2="Total Sales" 
                    value2="{{ number_format($totalSales) }}" 
                    change2="{{ number_format($salesChange, 1) }}"
                    chartId2="salesMiniChart"
                    
                    title3="Avg. Order Value" 
                    value3="${{ number_format($avgOrderValue, 2) }}" 
                    change3="{{ number_format($aovChange, 1) }}"
                    chartId3="aovMiniChart"
                    
                    title4="Conversion Rate" 
                    value4="{{ number_format(rand(3, 6), 1) }}%" 
                    change4="{{ number_format(rand(0, 2), 1) }}"
                    chartId4="conversionMiniChart"
                    mini-charts="true"
                />
            </div>

            <!-- Chart Components -->
            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-xxl-6">
                        <!-- Revenue Chart -->
                        <x-chart-container
                            id="revenueChart"
                            title="Revenue Trend"
                            subtitle="Daily revenue over the last 30 days"
                            height="350px"
                            type="line"
                            :labels="$chartData['labels']"
                            :datasets="[[
                                'label' => 'Revenue',
                                'data' => $chartData['revenue'],
                                'borderColor' => '#559bfb',
                                'backgroundColor' => 'rgba(85, 155, 251, 0.1)',
                                'borderWidth' => 2,
                                'fill' => true,
                                'tension' => 0.4
                            ]]"
                            :format-currency="true"
                        />
                    </div>
                    
                    <div class="col-xxl-6">
                        <!-- Sales by Category Chart -->
                        <x-chart-container
                            id="categoryChart"
                            title="Sales by Category"
                            subtitle="Distribution of sales across categories"
                            height="350px"
                            type="doughnut"
                            :labels="$genreData->pluck('genre')->toArray()"
                            :datasets="[[
                                'label' => 'Sales',
                                'data' => $genreData->pluck('revenue')->toArray(),
                                'backgroundColor' => [
                                    'rgba(85, 155, 251, 0.7)',
                                    'rgba(30, 224, 172, 0.7)',
                                    'rgba(244, 189, 14, 0.7)',
                                    'rgba(133, 79, 255, 0.7)',
                                    'rgba(224, 30, 126, 0.7)'
                                ],
                                'borderColor' => [
                                    'rgba(85, 155, 251, 1)',
                                    'rgba(30, 224, 172, 1)',
                                    'rgba(244, 189, 14, 1)',
                                    'rgba(133, 79, 255, 1)',
                                    'rgba(224, 30, 126, 1)'
                                ],
                                'borderWidth' => 1
                            ]]"
                        />
                    </div>
                    
                    <div class="col-xxl-12">
                        <!-- Performance Chart -->
                        <x-chart-container
                            id="performanceChart"
                            title="Sales Performance"
                            subtitle="Monthly sales performance comparison"
                            height="400px"
                            type="bar"
                            :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
                            :datasets="[[
                                'label' => '2024',
                                'data' => [12000, 19000, 15000, 18000, 22000, 25000],
                                'backgroundColor' => 'rgba(85, 155, 251, 0.7)',
                                'borderColor' => 'rgba(85, 155, 251, 1)',
                                'borderWidth' => 1
                            ], [
                                'label' => '2023',
                                'data' => [10000, 15000, 12000, 16000, 18000, 20000],
                                'backgroundColor' => 'rgba(30, 224, 172, 0.7)',
                                'borderColor' => 'rgba(30, 224, 172, 1)',
                                'borderWidth' => 1
                            ]]"
                            :format-currency="true"
                            :options="[
                                'plugins' => [
                                    'tooltip' => [
                                        'mode' => 'index',
                                        'intersect' => false
                                    ]
                                ]
                            ]"
                        />
                    </div>
                </div>
            </div>
            
            <!-- Top Selling Books -->
            <div class="nk-block">
                <div class="card card-bordered card-preview">
                    <div class="card-inner">
                        <div class="card-title-group">
                            <div class="card-title">
                                <h6 class="title">Top Selling Books</h6>
                            </div>
                        </div>
                        <div class="nk-tb-list nk-tb-ulist">
                            <div class="nk-tb-item nk-tb-head">
                                <div class="nk-tb-col"><span>Book Title</span></div>
                                <div class="nk-tb-col tb-col-md"><span>Sales</span></div>
                                <div class="nk-tb-col"><span>Revenue</span></div>
                            </div>
                            @foreach($topBooks as $book)
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    <span class="tb-lead">{{ $book->title }}</span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span>{{ $book->sales_count }}</span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-lead">${{ number_format($book->total_revenue, 2) }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Include any additional scripts specific to this page here -->
@endpush

@section('scripts')
    @stack('scripts')
@endsection