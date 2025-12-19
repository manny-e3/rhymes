@extends('layouts.admin')

@section('title', 'Charting Components Test')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Charting Components Test</h3>
                        <div class="nk-block-des text-soft">
                            <p>Testing all charting components</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Sales Metrics Component -->
            <div class="nk-block">
                <h5 class="mb-3">Sales Metrics Component</h5>
                <x-sales-metrics 
                    title1="Total Revenue" 
                    value1="$24,560.00" 
                    change1="3.2"
                    chartId1="revenueMiniChart"
                    
                    title2="Total Sales" 
                    value2="1,248" 
                    change2="2.5"
                    chartId2="salesMiniChart"
                    
                    title3="Avg. Order Value" 
                    value3="$42.50" 
                    change3="-1.2"
                    chartId3="aovMiniChart"
                    
                    title4="Conversion Rate" 
                    value4="4.8%" 
                    change4="0.8"
                    chartId4="conversionMiniChart"
                    mini-charts="true"
                />
            </div>

            <!-- Test Chart Container Component -->
            <div class="nk-block">
                <h5 class="mb-3">Chart Container Component</h5>
                <div class="row g-gs">
                    <div class="col-lg-6">
                        <x-chart-container
                            id="lineChart"
                            title="Line Chart Example"
                            type="line"
                            :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
                            :datasets="[
                                [
                                    'label' => 'Revenue',
                                    'data' => [12000, 19000, 15000, 18000, 22000, 25000],
                                    'borderColor' => '#559bfb',
                                    'backgroundColor' => 'rgba(85, 155, 251, 0.1)',
                                    'borderWidth' => 2,
                                    'fill' => true,
                                    'tension' => 0.4
                                ]
                            ]"
                            :format-currency="true"
                        />
                    </div>
                    
                    <div class="col-lg-6">
                        <x-chart-container
                            id="barChart"
                            title="Bar Chart Example"
                            type="bar"
                            :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
                            :datasets="[
                                [
                                    'label' => 'Sales',
                                    'data' => [120, 190, 150, 180, 220, 250],
                                    'backgroundColor' => 'rgba(30, 224, 172, 0.7)',
                                    'borderColor' => 'rgba(30, 224, 172, 1)',
                                    'borderWidth' => 1
                                ]
                            ]"
                        />
                    </div>
                </div>
            </div>

            <!-- Test Sales Chart Component -->
            <div class="nk-block">
                <h5 class="mb-3">Sales Chart Component</h5>
                <x-sales-chart 
                    id="testSalesChart"
                    title="Test Sales Data"
                    type="doughnut"
                    :labels="['Fiction', 'Non-Fiction', 'Sci-Fi', 'Romance', 'Mystery']"
                    :data="[
                        [
                            'label' => 'Books Sold',
                            'data' => [35, 25, 15, 15, 10],
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
                        ]
                    ]"
                />
            </div>

            <!-- Test Sales Overview Component -->
            <div class="nk-block">
                <h5 class="mb-3">Sales Overview Component</h5>
                <x-sales-overview 
                    :revenue-data="[
                        'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        'values' => [12000, 19000, 15000, 18000, 22000, 25000]
                    ]"
                    :metrics-data="[
                        'labels' => ['Orders', 'Customers', 'Conversion'],
                        'values' => [125, 89, 4.2]
                    ]"
                    :performance-data="[
                        'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        'units' => [1200, 1900, 1500, 1800, 2200, 2500],
                        'prices' => [25.50, 27.20, 26.80, 28.10, 29.30, 30.50]
                    ]"
                />
            </div>
        </div>
    </div>
</div>
@endsection