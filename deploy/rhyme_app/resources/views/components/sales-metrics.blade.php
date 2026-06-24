<div class="row g-gs">
    <div class="col-xxl-3 col-sm-6">
        <div class="card">
            <div class="nk-ecwg nk-ecwg3">
                <div class="card-inner pb-0">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title">{{ $title1 ?? 'Total Revenue' }}</h6>
                        </div>
                    </div>
                    <div class="data my-3">
                        <div class="fs-2 fw-bold">{{ $value1 ?? '$0.00' }}</div>
                        @if(isset($change1))
                            <div class="fw-medium {{ $change1 >= 0 ? 'text-success' : 'text-danger' }}">
                                @if($change1 >= 0)
                                    <em class="icon ni ni-arrow-long-up"></em> 
                                @else
                                    <em class="icon ni ni-arrow-long-down"></em> 
                                @endif
                                {{ abs($change1) }}%
                            </div>
                        @endif
                    </div>
                </div>
                @if(isset($chartId1))
                    <div class="card-inner py-0">
                        <div class="chart-container" style="height: 60px;">
                            <canvas id="{{ $chartId1 }}"></canvas>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-xxl-3 col-sm-6">
        <div class="card">
            <div class="nk-ecwg nk-ecwg3">
                <div class="card-inner pb-0">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title">{{ $title2 ?? 'Total Sales' }}</h6>
                        </div>
                    </div>
                    <div class="data my-3">
                        <div class="fs-2 fw-bold">{{ $value2 ?? '0' }}</div>
                        @if(isset($change2))
                            <div class="fw-medium {{ $change2 >= 0 ? 'text-success' : 'text-danger' }}">
                                @if($change2 >= 0)
                                    <em class="icon ni ni-arrow-long-up"></em> 
                                @else
                                    <em class="icon ni ni-arrow-long-down"></em> 
                                @endif
                                {{ abs($change2) }}%
                            </div>
                        @endif
                    </div>
                </div>
                @if(isset($chartId2))
                    <div class="card-inner py-0">
                        <div class="chart-container" style="height: 60px;">
                            <canvas id="{{ $chartId2 }}"></canvas>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-xxl-3 col-sm-6">
        <div class="card">
            <div class="nk-ecwg nk-ecwg3">
                <div class="card-inner pb-0">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title">{{ $title3 ?? 'Avg. Order Value' }}</h6>
                        </div>
                    </div>
                    <div class="data my-3">
                        <div class="fs-2 fw-bold">{{ $value3 ?? '$0.00' }}</div>
                        @if(isset($change3))
                            <div class="fw-medium {{ $change3 >= 0 ? 'text-success' : 'text-danger' }}">
                                @if($change3 >= 0)
                                    <em class="icon ni ni-arrow-long-up"></em> 
                                @else
                                    <em class="icon ni ni-arrow-long-down"></em> 
                                @endif
                                {{ abs($change3) }}%
                            </div>
                        @endif
                    </div>
                </div>
                @if(isset($chartId3))
                    <div class="card-inner py-0">
                        <div class="chart-container" style="height: 60px;">
                            <canvas id="{{ $chartId3 }}"></canvas>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-xxl-3 col-sm-6">
        <div class="card">
            <div class="nk-ecwg nk-ecwg3">
                <div class="card-inner pb-0">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title">{{ $title4 ?? 'Conversion Rate' }}</h6>
                        </div>
                    </div>
                    <div class="data my-3">
                        <div class="fs-2 fw-bold">{{ $value4 ?? '0%' }}</div>
                        @if(isset($change4))
                            <div class="fw-medium {{ $change4 >= 0 ? 'text-success' : 'text-danger' }}">
                                @if($change4 >= 0)
                                    <em class="icon ni ni-arrow-long-up"></em> 
                                @else
                                    <em class="icon ni ni-arrow-long-down"></em> 
                                @endif
                                {{ abs($change4) }}%
                            </div>
                        @endif
                    </div>
                </div>
                @if(isset($chartId4))
                    <div class="card-inner py-0">
                        <div class="chart-container" style="height: 60px;">
                            <canvas id="{{ $chartId4 }}"></canvas>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(isset($miniCharts) && $miniCharts)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mini chart configurations
    const miniChartConfig = (ctx, data, color) => {
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['', '', '', '', '', ''],
                datasets: [{
                    data: data,
                    borderColor: color,
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    pointRadius: 0,
                    tension: 0.4,
                    fill: false
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
                        enabled: false
                    }
                },
                scales: {
                    x: {
                        display: false
                    },
                    y: {
                        display: false
                    }
                }
            }
        });
    };
    
    @if(isset($chartId1))
        const ctx1 = document.getElementById('{{ $chartId1 }}').getContext('2d');
        miniChartConfig(ctx1, [65, 59, 80, 81, 56, 55], '#559bfb');
    @endif
    
    @if(isset($chartId2))
        const ctx2 = document.getElementById('{{ $chartId2 }}').getContext('2d');
        miniChartConfig(ctx2, [28, 48, 40, 19, 86, 27], '#1ee0ac');
    @endif
    
    @if(isset($chartId3))
        const ctx3 = document.getElementById('{{ $chartId3 }}').getContext('2d');
        miniChartConfig(ctx3, [12, 19, 3, 5, 2, 3], '#f4bd0e');
    @endif
    
    @if(isset($chartId4))
        const ctx4 = document.getElementById('{{ $chartId4 }}').getContext('2d');
        miniChartConfig(ctx4, [8, 12, 5, 15, 10, 7], '#854fff');
    @endif
});
</script>
@endpush
@endif