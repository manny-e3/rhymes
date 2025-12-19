<div class="{{ $class ?? 'card' }}">
    <div class="{{ isset($class) ? '' : 'card-inner' }}">
        <div class="card-title-group {{ $titleClass ?? 'mb-3' }}">
            @if(isset($title))
                <div class="card-title">
                    <h6 class="title">{{ $title }}</h6>
                    @if(isset($subtitle))
                        <p>{{ $subtitle }}</p>
                    @endif
                </div>
            @endif
            
            @if(isset($actions) || isset($dropdown))
                <div class="card-tools">
                    @if(isset($actions))
                        {!! $actions !!}
                    @endif
                    
                    @if(isset($dropdown))
                        <div class="dropdown">
                            <a href="#" class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">{{ $dropdown['label'] ?? 'Options' }}</a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <ul class="link-list-opt no-bdr">
                                    @foreach($dropdown['items'] as $item)
                                        <li><a href="{{ $item['url'] ?? '#' }}" onclick="{{ $item['onclick'] ?? '' }}"><span>{{ $item['label'] }}</span></a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
        
        <div class="chart-container" style="{{ isset($height) ? 'height: ' . $height . ';' : 'height: 300px;' }}">
            <canvas id="{{ $id ?? 'chart' }}"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('{{ $id ?? 'chart' }}').getContext('2d');
    
    // Chart configuration from component attributes
    const config = {
        type: '{{ $type ?? 'line' }}',
        data: {
            labels: @json($labels ?? []),
            datasets: @json($datasets ?? [])
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
                                // Format currency if specified
                                if ({{ isset($formatCurrency) && $formatCurrency ? 'true' : 'false' }}) {
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
                    beginAtZero: {{ $beginAtZero ?? 'true' }},
                    ticks: {
                        callback: function(value) {
                            // Format currency on Y axis if specified
                            if ({{ isset($formatCurrency) && $formatCurrency ? 'true' : 'false' }}) {
                                return '$' + value;
                            }
                            return value;
                        }
                    }
                }
            }
        }
    };
    
    // Extend config with additional options if provided
    @if(isset($options))
        Object.assign(config.options, @json($options));
    @endif
    
    new Chart(ctx, config);
});
</script>
@endpush