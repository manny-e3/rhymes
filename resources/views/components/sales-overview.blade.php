<div class="row g-gs">
    <div class="col-xxl-6">
        <div class="card h-100">
            <div class="card-inner">
                <div class="card-title-group mb-2">
                    <div class="card-title">
                        <h6 class="title">Revenue Overview</h6>
                    </div>
                </div>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xxl-6">
        <div class="card h-100">
            <div class="card-inner">
                <div class="card-title-group mb-2">
                    <div class="card-title">
                        <h6 class="title">Sales Metrics</h6>
                    </div>
                </div>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="salesMetricsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xxl-12">
        <div class="card mt-4">
            <div class="card-inner">
                <div class="card-title-group mb-2">
                    <div class="card-title">
                        <h6 class="title">Sales Performance</h6>
                    </div>
                </div>
                <div class="chart-container" style="height: 350px;">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart (Line)
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json($revenueData['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']),
            datasets: [{
                label: 'Revenue',
                data: @json($revenueData['values'] ?? [12000, 19000, 15000, 18000, 22000, 25000]),
                borderColor: '#559bfb',
                backgroundColor: 'rgba(85, 155, 251, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: $' + new Intl.NumberFormat('en-US').format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
    
    // Sales Metrics Chart (Bar)
    const metricsCtx = document.getElementById('salesMetricsChart').getContext('2d');
    new Chart(metricsCtx, {
        type: 'bar',
        data: {
            labels: @json($metricsData['labels'] ?? ['Orders', 'Customers', 'Conversion']),
            datasets: [{
                label: 'Current Period',
                data: @json($metricsData['values'] ?? [125, 89, 4.2]),
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
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Performance Chart (Combo - Line & Bar)
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'bar',
        data: {
            labels: @json($performanceData['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']),
            datasets: [{
                label: 'Units Sold',
                data: @json($performanceData['units'] ?? [1200, 1900, 1500, 1800, 2200, 2500]),
                backgroundColor: 'rgba(85, 155, 251, 0.7)',
                borderColor: 'rgba(85, 155, 251, 1)',
                borderWidth: 1
            }, {
                label: 'Avg. Price',
                data: @json($performanceData['prices'] ?? [25.50, 27.20, 26.80, 28.10, 29.30, 30.50]),
                type: 'line',
                borderColor: '#1ee0ac',
                backgroundColor: 'rgba(30, 224, 172, 0.1)',
                borderWidth: 2,
                fill: true,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                if (context.dataset.label === 'Avg. Price') {
                                    label += '$' + context.parsed.y.toFixed(2);
                                } else {
                                    label += context.parsed.y;
                                }
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Units Sold'
                    }
                },
                y1: {
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Average Price ($)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    }
                }
            }
        }
    });
});
</script>
@endpush