<!-- <div class="row g-gs">
    <div class="col-xxl-6">
        <div class="card h-100">
            <div class="card-inner">
                <div class="card-title-group mb-2">
                    <div class="card-title">
                        <h6 class="title">Revenue Overview</h6>
                    </div>
                </div>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="revenueOverviewChart"></canvas>
                </div>
            </div>
        </div>
    </div> -->
    
    <!-- <div class="col-xxl-6">
        <div class="card h-100">
            <div class="card-inner">
                <div class="card-title-group mb-2">
                    <div class="card-title">
                        <h6 class="title">Sales Metrics</h6>
                    </div>
                </div>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="salesMetricsOverviewChart"></canvas>
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
                    <canvas id="performanceOverviewChart"></canvas>
                </div>
            </div>
        </div>
    </div> -->
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php
        $revenueLabels = $revenueData['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $revenueValues = $revenueData['values'] ?? [12000, 19000, 15000, 18000, 22000, 25000];
        
        $metricsLabels = $metricsData['labels'] ?? ['Orders', 'Customers', 'Conversion'];
        $metricsValues = $metricsData['values'] ?? [125, 89, 4.2];
        
        $performanceLabels = $performanceData['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $performanceUnits = $performanceData['units'] ?? [1200, 1900, 1500, 1800, 2200, 2500];
        $performancePrices = $performanceData['prices'] ?? [25.50, 27.20, 26.80, 28.10, 29.30, 30.50];
    ?>
    
    // Revenue Chart (Line)
    const revenueCtx = document.getElementById('revenueOverviewChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($revenueLabels, 15, 512) ?>,
            datasets: [{
                label: 'Revenue',
                data: <?php echo json_encode($revenueValues, 15, 512) ?>,
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
    const metricsCtx = document.getElementById('salesMetricsOverviewChart').getContext('2d');
    new Chart(metricsCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($metricsLabels, 15, 512) ?>,
            datasets: [{
                label: 'Current Period',
                data: <?php echo json_encode($metricsValues, 15, 512) ?>,
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
    const performanceCtx = document.getElementById('performanceOverviewChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($performanceLabels, 15, 512) ?>,
            datasets: [{
                label: 'Units Sold',
                data: <?php echo json_encode($performanceUnits, 15, 512) ?>,
                backgroundColor: 'rgba(85, 155, 251, 0.7)',
                borderColor: 'rgba(85, 155, 251, 1)',
                borderWidth: 1
            }, {
                label: 'Avg. Price',
                data: <?php echo json_encode($performancePrices, 15, 512) ?>,
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
<?php $__env->stopPush(); ?><?php /**PATH C:\xampp\htdocs\authors_portal\resources\views/components/sales-overview.blade.php ENDPATH**/ ?>