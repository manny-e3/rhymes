@extends('layouts.admin')

@section('title', 'ERPREV Endpoint Tester | Rhymes Platform')

@section('page-title', 'ERPREV Endpoint Tester')

@section('page-description', 'Test correct ERPREV sales and order endpoints with dynamic pagination')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">ERPREV Endpoint Tester</h3>
                        <div class="nk-block-des text-soft">
                            <p>Verify and fetch records directly from correct ERPREV API endpoints</p>
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
                                    <li><a href="{{ route('admin.erprev.monitoring') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-activity-alt"></em><span>Sync Monitoring</span></a></li>
                                    <li><a href="{{ route('admin.erprev.sync-stocked-books-sales') }}" target="_blank" class="btn btn-white btn-dim btn-outline-primary"><em class="icon ni ni-reload"></em><span>Sync Stocked Books Sales</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuration Card -->
            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <h5 class="card-title">Request Configuration</h5>
                        <form method="POST" action="{{ route('admin.erprev.run-test-endpoint') }}" class="mt-4">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="endpoint">API Endpoint <span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <select name="endpoint" id="endpoint" class="form-select select2" required>
                                                <option value="">-- Select Endpoint to Test --</option>
                                                @foreach($endpoints as $path => $label)
                                                    <option value="{{ $path }}" {{ (isset($selectedEndpoint) && $selectedEndpoint == $path) ? 'selected' : '' }}>
                                                        {{ $label }} ({{ $path }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span class="form-note">Select the specific Sales or Order endpoint to query.</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="lastupdated">Last Updated Filter (Relative or Custom Date Y-m-d)</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="lastupdated" name="lastupdated" list="lastupdated-presets" placeholder="e.g. 2026-04-01 or 100d" value="{{ $selectedLastUpdated ?? '100d' }}">
                                            <datalist id="lastupdated-presets">
                                                <option value="all">All Records</option>
                                                <option value="2026-04-01">April 2026 till date</option>
                                                <option value="5m">Last 5 Minutes</option>
                                                <option value="10m">Last 10 Minutes</option>
                                                <option value="30m">Last 30 Minutes</option>
                                                <option value="1h">Last 1 Hour</option>
                                                <option value="4h">Last 4 Hours</option>
                                                <option value="6h">Last 6 Hours</option>
                                                <option value="24h">Last 24 Hours</option>
                                                <option value="7d">Last 7 Days</option>
                                                <option value="30d">Last 30 Days</option>
                                                <option value="60d">Last 60 Days</option>
                                                <option value="100d">Last 100 Days</option>
                                            </datalist>
                                        </div>
                                        <span class="form-note">Pass dynamic lastupdated threshold value (e.g. 100d or custom date string like 2026-04-01). <strong class="text-warning">Warning:</strong> Querying 'all' without other filters on large tables can result in timeouts.</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="Product">Product Name (Deep Search Filter)</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="Product" name="Product" placeholder="e.g. Sanya" value="{{ $selectedProduct ?? '' }}">
                                        </div>
                                        <span class="form-note">Filter by specific Product name query parameter on the server side.</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="ProductID">Product ID (Deep Search Filter)</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="ProductID" name="ProductID" placeholder="e.g. 30585" value="{{ $selectedProductID ?? '' }}">
                                        </div>
                                        <span class="form-note">Filter by specific ProductID query parameter on the server side.</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="startRow">Pagination: startRow</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" id="startRow" name="startRow" placeholder="e.g. 1000" value="{{ $selectedStartRow ?? '' }}">
                                        </div>
                                        <span class="form-note">The start row index for subsequent pagination requests. Leave empty for the first page.</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="TotalRecords">Pagination: TotalRecords</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" id="TotalRecords" name="TotalRecords" placeholder="e.g. 500000" value="{{ $selectedTotalRecords ?? '' }}">
                                        </div>
                                        <span class="form-note">The total records count returned from the previous pagination object. Leave empty for the first page.</span>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-lg btn-primary">
                                            <em class="icon ni ni-play-fill"></em><span>Send Request to ERPREV API</span>
                                        </button>
                                        <a href="{{ route('admin.erprev.test-endpoints') }}" class="btn btn-lg btn-light">Reset Form</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            @if(isset($result))
                <div class="nk-block mt-4">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="nk-block-between align-start mb-3">
                                <div class="nk-block-head-content">
                                    <h5 class="card-title">API Response Results</h5>
                                </div>
                                <div class="nk-block-head-content">
                                    @if($result['success'])
                                        <span class="badge bg-success px-3 py-2 fs-6">SUCCESS (HTTP OK)</span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2 fs-6">FAILED (API ERROR)</span>
                                    @endif
                                </div>
                            </div>

                            @if(isset($result['url']))
                                <div class="alert alert-fill alert-light alert-icon mb-4">
                                    <em class="icon ni ni-globe"></em>
                                    <strong>Requested URL:</strong> <code class="text-dark bg-white p-1 rounded">{{ $result['url'] }}</code>
                                </div>
                            @endif

                            @if($result['success'])
                                @php
                                    $dataPayload = $result['data'] ?? [];
                                    $pagenation = $dataPayload['pagenation'] ?? $dataPayload['pagination'] ?? null;
                                    $records = $dataPayload['records'] ?? $dataPayload['data'] ?? $dataPayload['records_view'] ?? [];
                                @endphp

                                <!-- Pagination Details Card -->
                                <div class="card card-bordered bg-light mb-4">
                                    <div class="card-inner">
                                        <h6 class="title text-primary"><em class="icon ni ni-swap-alt-h me-1"></em>Pagination Object (Returned by API)</h6>
                                        @if($pagenation)
                                            <div class="row g-3 mt-2">
                                                <div class="col-md-3 col-6">
                                                    <div class="bg-white p-3 rounded border text-center">
                                                        <span class="text-soft fs-11px text-uppercase">Total Records</span>
                                                        <h4 class="mt-1 text-dark">{{ $pagenation['TotalRecords'] ?? $pagenation['totalRecords'] ?? 'N/A' }}</h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6">
                                                    <div class="bg-white p-3 rounded border text-center">
                                                        <span class="text-soft fs-11px text-uppercase">Page Limit</span>
                                                        <h4 class="mt-1 text-dark">{{ $pagenation['PageLimit'] ?? $pagenation['pageLimit'] ?? 'N/A' }}</h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6">
                                                    <div class="bg-white p-3 rounded border text-center">
                                                        <span class="text-soft fs-11px text-uppercase">Start Row</span>
                                                        <h4 class="mt-1 text-dark">{{ $pagenation['startRow'] ?? $pagenation['StartRow'] ?? 'N/A' }}</h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6">
                                                    <div class="bg-white p-3 rounded border text-center">
                                                        <span class="text-soft fs-11px text-uppercase">End Row</span>
                                                        <h4 class="mt-1 text-dark">{{ $pagenation['endRow'] ?? $pagenation['EndRow'] ?? 'N/A' }}</h4>
                                                    </div>
                                                </div>
                                            </div>

                                            @if(isset($pagenation['TotalRecords']) && isset($pagenation['endRow']))
                                                @php
                                                    $nextStart = (int)($pagenation['endRow']) + 1;
                                                    $totalRecs = $pagenation['TotalRecords'];
                                                @endphp
                                                @if($nextStart <= $totalRecs)
                                                    <div class="alert alert-fill alert-info mt-3 mb-0">
                                                        <em class="icon ni ni-info-fill"></em>
                                                        <strong>Next Page Query:</strong> To fetch the next block of records, copy the following values into the input fields above:
                                                        <ul class="mt-2 mb-0 pl-3">
                                                            <li><strong>startRow:</strong> <code class="bg-white text-info px-1 rounded">{{ $nextStart }}</code></li>
                                                            <li><strong>TotalRecords:</strong> <code class="bg-white text-info px-1 rounded">{{ $totalRecs }}</code></li>
                                                        </ul>
                                                    </div>
                                                @else
                                                    <div class="alert alert-fill alert-success mt-3 mb-0">
                                                        <em class="icon ni ni-check-circle-fill"></em>
                                                        <strong>End of Records:</strong> You have reached the end of the available records.
                                                    </div>
                                                @endif
                                            @endif
                                        @else
                                            <div class="text-muted mt-2">
                                                No `pagenation` object returned in this response. The API might have returned all records at once or has empty data.
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Records Rendered Table -->
                                <div class="mb-4">
                                    <h6 class="title mb-3"><em class="icon ni ni-list-index me-1"></em>Records Found ({{ count($records) }})</h6>
                                    @if(count($records) > 0)
                                        <div class="table-responsive border rounded bg-white">
                                            <table class="table table-striped mb-0">
                                                <thead>
                                                    <tr>
                                                        @php
                                                            $sampleRecord = $records[0];
                                                            $displayKeys = array_slice(array_keys($sampleRecord), 0, 8); // Show first 8 columns for neatness
                                                        @endphp
                                                        @foreach($displayKeys as $key)
                                                            <th>{{ $key }}</th>
                                                        @endforeach
                                                        @if(count(array_keys($sampleRecord)) > 8)
                                                            <th>Actions</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($records as $index => $record)
                                                        <tr>
                                                            @foreach($displayKeys as $key)
                                                                <td>
                                                                    @if(is_array($record[$key]))
                                                                        <span class="badge bg-light text-dark">Array ({{ count($record[$key]) }})</span>
                                                                    @else
                                                                        {{ Str::limit((string)$record[$key], 50) }}
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                            @if(count(array_keys($sampleRecord)) > 8)
                                                                <td>
                                                                    <button class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#recordDetails{{ $index }}">
                                                                        Full Detail
                                                                    </button>

                                                                    <!-- Record Details Modal -->
                                                                    <div class="modal fade" id="recordDetails{{ $index }}" tabindex="-1" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title">Record Details - Row #{{ $index + 1 }}</h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <div class="table-responsive">
                                                                                        <table class="table table-bordered mb-0">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th width="30%">Field Name</th>
                                                                                                    <th>Value</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                @foreach($record as $field => $val)
                                                                                                    <tr>
                                                                                                        <td><strong>{{ $field }}</strong></td>
                                                                                                        <td>
                                                                                                            @if(is_array($val))
                                                                                                                <pre class="bg-light p-2 rounded"><code>{{ json_encode($val, JSON_PRETTY_PRINT) }}</code></pre>
                                                                                                            @else
                                                                                                                {{ $val }}
                                                                                                            @endif
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                @endforeach
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-fill alert-warning">
                                            <em class="icon ni ni-alert-circle"></em>
                                            The API successfully connected but returned 0 records.
                                        </div>
                                    @endif
                                </div>

                                <!-- Raw JSON Payload Explorer -->
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="title mb-0"><em class="icon ni ni-code me-1"></em>Raw Response JSON Payload</h6>
                                        <button class="btn btn-xs btn-outline-secondary" onclick="copyRawJson()">
                                            <em class="icon ni ni-copy"></em><span>Copy JSON</span>
                                        </button>
                                    </div>
                                    <div class="bg-dark p-3 rounded" style="max-height: 500px; overflow-y: auto;">
                                        <pre><code id="rawJsonCode" class="text-success">{{ json_encode($dataPayload, JSON_PRETTY_PRINT) }}</code></pre>
                                    </div>
                                </div>

                            @else
                                <!-- Error Section -->
                                <div class="alert alert-fill alert-danger mb-4">
                                    <em class="icon ni ni-cross-circle-fill"></em>
                                    <strong>API Connection Error:</strong> {{ $result['message'] }}
                                </div>
                                <p class="text-soft">Check your API credentials, network connection, or parameters. You can inspect more details inside the System Sync logs.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyRawJson() {
        const copyText = document.getElementById("rawJsonCode").innerText;
        navigator.clipboard.writeText(copyText).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Raw JSON payload copied to clipboard.',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }).catch(err => {
            console.error('Failed to copy text: ', err);
        });
    }
</script>
@endsection
