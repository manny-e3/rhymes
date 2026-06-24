<div class="card h-100">
    <div class="card-inner">
        <h6 class="card-title">{{ $title ?? 'Sales Data' }}</h6>
        <div class="chart-container">
            <canvas id="{{ $id ?? 'salesChart' }}" class="chart-canvas"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('{{ $id ?? 'salesChart' }}').getContext('2d');
    
    // Chart data from component attributes
    const chartData = @json($data ?? []);
    const chartType = '{{ $type ?? 'bar' }}';
    const chartLabels = @json($labels ?? []);
    
    new Chart(ctx, {
        type: chartType,
        data: {
            labels: chartLabels,
            datasets: chartData
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: {{ $showLegend ?? 'true' }}
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                // Format currency if it's a revenue chart
                                if (context.dataset.label && context.dataset.label.toLowerCase().includes('revenue')) {
                                    label += new Intl.NumberFormat('en-US', {
                                        style: 'currency',
                                        currency: 'USD'
                                    }).format(context.parsed.y);
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
                    ticks: {
                        callback: function(value) {
                            // Format currency on Y axis for revenue charts
                            if ('{{ $type ?? 'bar' }}' === 'bar' && 
                                (('{{ $title ?? 'Sales Data' }}'.toLowerCase().includes('revenue')) ||
                                ('{{ $title ?? 'Sales Data' }}'.toLowerCase().includes('sales')))) {
                                return '$' + value;
                            }
                            return value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush