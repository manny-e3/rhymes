@extends('layouts.admin')

@section('title', 'ERPREV Sync Monitoring | Rhymes Platform')

@section('page-title', 'ERPREV Sync Monitoring')

@section('page-description', 'Monitor synchronization operations with ERPREV system')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">ERPREV Sync Monitoring</h3>
                        <div class="nk-block-des text-soft">
                            <p>Monitor synchronization operations with ERPREV system</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="{{ route('admin.erprev.sales') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-swap"></em><span>Sales Data</span></a></li>
                                    <li><a href="{{ route('admin.erprev.inventory') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-package"></em><span>Inventory</span></a></li>
                                    <li><a href="{{ route('admin.erprev.products') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-grid-add"></em><span>Products</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Total Sync Operations</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount">{{ number_format($summary['total']) }}</span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Successful Syncs</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount text-success">{{ number_format($summary['successful']) }}</span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-success" style="width: {{ $summary['successful'] > 0 ? ($summary['successful'] / max($summary['total'], 1)) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Failed Syncs</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount text-danger">{{ number_format($summary['failed']) }}</span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-danger" style="width: {{ $summary['failed'] > 0 ? ($summary['failed'] / max($summary['total'], 1)) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Success Rate</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount">{{ $summary['success_rate'] }}%</span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-info" style="width: {{ $summary['success_rate'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Sync Operations by Area</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-3 gx-1">
                                    @foreach($summary['by_area'] as $area => $data)
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">{{ ucfirst($area) }}</span>
                                            <span>{{ $data->count }}</span>
                                        </div>
                                        <div class="progress progress-md mt-1">
                                            <div class="progress-bar bg-primary" style="width: {{ ($data->count / max($summary['total'], 1)) * 100 }}%"></div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Recent Sync Operations (24h)</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-3 gx-1">
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Successful</span>
                                            <span>{{ $summary['recent']->get('success', (object)['count' => 0])->count ?? 0 }}</span>
                                        </div>
                                        <div class="progress progress-md mt-1">
                                            <div class="progress-bar bg-success" style="width: {{ ($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) > 0 ? (($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) / max(($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) + ($summary['recent']->get('error', (object)['count' => 0])->count ?? 0), 1)) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Failed</span>
                                            <span>{{ $summary['recent']->get('error', (object)['count' => 0])->count ?? 0 }}</span>
                                        </div>
                                        <div class="progress progress-md mt-1">
                                            <div class="progress-bar bg-danger" style="width: {{ ($summary['recent']->get('error', (object)['count' => 0])->count ?? 0) > 0 ? (($summary['recent']->get('error', (object)['count' => 0])->count ?? 0) / max(($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) + ($summary['recent']->get('error', (object)['count' => 0])->count ?? 0), 1)) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Errors -->
            @if(count($recentErrors) > 0)
            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-3">
                            <div class="card-title">
                                <h6 class="title">Recent Sync Errors</h6>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Area</th>
                                        <th>Message</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentErrors as $error)
                                    <tr>
                                        <td><span class="badge badge-dot bg-danger">{{ ucfirst($error->area) }}</span></td>
                                        <td>{{ Str::limit($error->message, 100) }}</td>
                                        <td>{{ $error->created_at->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Sync Logs -->
            <div class="nk-block">
                <div class="card card-bordered card-full">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-3">
                            <div class="card-title">
                                <h6 class="title">Sync Operation Logs</h6>
                            </div>
                        </div>

                        <!-- Filters -->
                        <form method="GET" action="{{ route('admin.erprev.monitoring') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="area">Area</label>
                                        <select class="form-select" id="area" name="area">
                                            <option value="">All Areas</option>
                                            <option value="books" {{ request('area') == 'books' ? 'selected' : '' }}>Books</option>
                                            <option value="sales" {{ request('area') == 'sales' ? 'selected' : '' }}>Sales</option>
                                            <option value="inventory" {{ request('area') == 'inventory' ? 'selected' : '' }}>Inventory</option>
                                            <option value="products" {{ request('area') == 'products' ? 'selected' : '' }}>Products</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="status">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="">All Statuses</option>
                                            <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                                            <option value="error" {{ request('status') == 'error' ? 'selected' : '' }}>Error</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label" for="date_from">From Date</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label" for="date_to">To Date</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary me-2"><em class="icon ni ni-search"></em><span>Filter</span></button>
                                            <a href="{{ route('admin.erprev.monitoring') }}" class="btn btn-light"><em class="icon ni ni-reload"></em><span>Reset</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @if(count($logs) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Area</th>
                                            <th>Status</th>
                                            <th>Message</th>
                                            <th>Timestamp</th>
                                            <th>Payload</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($logs as $log)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-dot bg-{{ $log->area == 'books' ? 'primary' : ($log->area == 'sales' ? 'success' : ($log->area == 'inventory' ? 'warning' : 'info')) }}">
                                                        {{ ucfirst($log->area) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($log->status == 'success')
                                                        <span class="badge bg-success">Success</span>
                                                    @else
                                                        <span class="badge bg-danger">Error</span>
                                                    @endif
                                                </td>
                                                <td>{{ Str::limit($log->message, 80) }}</td>
                                                <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                                <td>
                                                    @if($log->payload)
                                                        <button class="btn btn-xs btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#payloadModal{{ $log->id }}">
                                                            View Details
                                                        </button>
                                                        
                                                        <!-- Payload Modal -->
                                                        <div class="modal fade" id="payloadModal{{ $log->id }}" tabindex="-1" aria-labelledby="payloadModalLabel{{ $log->id }}" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="payloadModalLabel{{ $log->id }}">Payload Details</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <pre><code>{{ json_encode($log->payload, JSON_PRETTY_PRINT) }}</code></pre>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No payload</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} entries
                                </div>
                                <div>
                                    {{ $logs->appends(request()->except('page'))->links() }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <em class="icon ni ni-file-text" style="font-size: 48px; opacity: 0.3;"></em>
                                <p class="mt-3">No sync logs found</p>
                                @if(empty(request()->except('page')))
                                    <p class="text-muted">There are no sync operations recorded yet</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection@extends('layouts.admin')

@section('title', 'ERPREV Sync Monitoring | Rhymes Platform')

@section('page-title', 'ERPREV Sync Monitoring')

@section('page-description', 'Monitor synchronization operations with ERPREV system')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">ERPREV Sync Monitoring</h3>
                        <div class="nk-block-des text-soft">
                            <p>Monitor synchronization operations with ERPREV system</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="{{ route('admin.erprev.sales') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-swap"></em><span>Sales Data</span></a></li>
                                    <li><a href="{{ route('admin.erprev.inventory') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-package"></em><span>Inventory</span></a></li>
                                    <li><a href="{{ route('admin.erprev.products') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-grid-add"></em><span>Products</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Total Sync Operations</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount">{{ number_format($summary['total']) }}</span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Successful Syncs</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount text-success">{{ number_format($summary['successful']) }}</span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-success" style="width: {{ $summary['successful'] > 0 ? ($summary['successful'] / max($summary['total'], 1)) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Failed Syncs</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount text-danger">{{ number_format($summary['failed']) }}</span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-danger" style="width: {{ $summary['failed'] > 0 ? ($summary['failed'] / max($summary['total'], 1)) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg6">
                                <div class="card-inner">
                                    <div class="nk-ecwg6-title">
                                        <h6 class="title">Success Rate</h6>
                                    </div>
                                    <div class="nk-ecwg6-amount">
                                        <span class="amount">{{ $summary['success_rate'] }}%</span>
                                    </div>
                                    <div class="nk-ecwg6-graph">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-info" style="width: {{ $summary['success_rate'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Sync Operations by Area</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-3 gx-1">
                                    @foreach($summary['by_area'] as $area => $data)
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">{{ ucfirst($area) }}</span>
                                            <span>{{ $data->count }}</span>
                                        </div>
                                        <div class="progress progress-md mt-1">
                                            <div class="progress-bar bg-primary" style="width: {{ ($data->count / max($summary['total'], 1)) * 100 }}%"></div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Recent Sync Operations (24h)</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-3 gx-1">
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Successful</span>
                                            <span>{{ $summary['recent']->get('success', (object)['count' => 0])->count ?? 0 }}</span>
                                        </div>
                                        <div class="progress progress-md mt-1">
                                            <div class="progress-bar bg-success" style="width: {{ ($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) > 0 ? (($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) / max(($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) + ($summary['recent']->get('error', (object)['count' => 0])->count ?? 0), 1)) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Failed</span>
                                            <span>{{ $summary['recent']->get('error', (object)['count' => 0])->count ?? 0 }}</span>
                                        </div>
                                        <div class="progress progress-md mt-1">
                                            <div class="progress-bar bg-danger" style="width: {{ ($summary['recent']->get('error', (object)['count' => 0])->count ?? 0) > 0 ? (($summary['recent']->get('error', (object)['count' => 0])->count ?? 0) / max(($summary['recent']->get('success', (object)['count' => 0])->count ?? 0) + ($summary['recent']->get('error', (object)['count' => 0])->count ?? 0), 1)) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Errors -->
            @if(count($recentErrors) > 0)
            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-3">
                            <div class="card-title">
                                <h6 class="title">Recent Sync Errors</h6>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Area</th>
                                        <th>Message</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentErrors as $error)
                                    <tr>
                                        <td><span class="badge badge-dot bg-danger">{{ ucfirst($error->area) }}</span></td>
                                        <td>{{ Str::limit($error->message, 100) }}</td>
                                        <td>{{ $error->created_at->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Sync Logs -->
            <div class="nk-block">
                <div class="card card-bordered card-full">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-3">
                            <div class="card-title">
                                <h6 class="title">Sync Operation Logs</h6>
                            </div>
                        </div>

                        <!-- Filters -->
                        <form method="GET" action="{{ route('admin.erprev.monitoring') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="area">Area</label>
                                        <select class="form-select" id="area" name="area">
                                            <option value="">All Areas</option>
                                            <option value="books" {{ request('area') == 'books' ? 'selected' : '' }}>Books</option>
                                            <option value="sales" {{ request('area') == 'sales' ? 'selected' : '' }}>Sales</option>
                                            <option value="inventory" {{ request('area') == 'inventory' ? 'selected' : '' }}>Inventory</option>
                                            <option value="products" {{ request('area') == 'products' ? 'selected' : '' }}>Products</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="status">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="">All Statuses</option>
                                            <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                                            <option value="error" {{ request('status') == 'error' ? 'selected' : '' }}>Error</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label" for="date_from">From Date</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label" for="date_to">To Date</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary me-2"><em class="icon ni ni-search"></em><span>Filter</span></button>
                                            <a href="{{ route('admin.erprev.monitoring') }}" class="btn btn-light"><em class="icon ni ni-reload"></em><span>Reset</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @if(count($logs) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Area</th>
                                            <th>Status</th>
                                            <th>Message</th>
                                            <th>Timestamp</th>
                                            <th>Payload</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($logs as $log)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-dot bg-{{ $log->area == 'books' ? 'primary' : ($log->area == 'sales' ? 'success' : ($log->area == 'inventory' ? 'warning' : 'info')) }}">
                                                        {{ ucfirst($log->area) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($log->status == 'success')
                                                        <span class="badge bg-success">Success</span>
                                                    @else
                                                        <span class="badge bg-danger">Error</span>
                                                    @endif
                                                </td>
                                                <td>{{ Str::limit($log->message, 80) }}</td>
                                                <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                                <td>
                                                    @if($log->payload)
                                                        <button class="btn btn-xs btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#payloadModal{{ $log->id }}">
                                                            View Details
                                                        </button>
                                                        
                                                        <!-- Payload Modal -->
                                                        <div class="modal fade" id="payloadModal{{ $log->id }}" tabindex="-1" aria-labelledby="payloadModalLabel{{ $log->id }}" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="payloadModalLabel{{ $log->id }}">Payload Details</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <pre><code>{{ json_encode($log->payload, JSON_PRETTY_PRINT) }}</code></pre>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No payload</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} entries
                                </div>
                                <div>
                                    {{ $logs->appends(request()->except('page'))->links() }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <em class="icon ni ni-file-text" style="font-size: 48px; opacity: 0.3;"></em>
                                <p class="mt-3">No sync logs found</p>
                                @if(empty(request()->except('page')))
                                    <p class="text-muted">There are no sync operations recorded yet</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection