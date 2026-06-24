@extends('layouts.admin')

@section('title', 'ERPREV Sales Data | Rhymes Platform')

@section('page-title', 'ERPREV Sales Data')

@section('page-description', 'Sales transactions from ERPREV system')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">ERPREV Sales Data</h3>
                        <div class="nk-block-des text-soft">
                            <p>Sales transactions synchronized from ERPREV system</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li>
                                        <button id="btn-sync-sales" class="btn btn-primary">
                                            <em class="icon ni ni-reload-alt"></em>
                                            <span>Sync Sales</span>
                                        </button>
                                    </li>
                                    <li><a href="{{ route('admin.erprev.inventory') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-package"></em><span>Inventory</span></a></li>
                                    <li><a href="{{ route('admin.erprev.products') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-grid-add"></em><span>Products</span></a></li>
                                    <li><a href="{{ route('admin.erprev.summary') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-bar-chart"></em><span>Summary</span></a></li>
                                    <li><a href="{{ route('admin.erprev.test-endpoints') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-play-fill"></em><span>Test Endpoints</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="card card-bordered card-full">
                    <div class="card-inner">
                        <!-- Filter Section -->
                        <div class="nk-block-head nk-block-head-sm">
                            <div class="nk-block-between">
                                <div class="nk-block-head-content">
                                    <h6 class="nk-block-title">Filter Sales Data</h6>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-inner mb-3">
                            <form method="GET" action="{{ route('admin.erprev.sales') }}" class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="lastupdated">Last Updated</label>
                                        <div class="form-control-wrap">
                                            <select name="lastupdated" id="lastupdated" class="form-select">
                                                <option value="" {{ request('lastupdated', $filters['lastupdated'] ?? '100d') == '' ? 'selected' : '' }}>All Time</option>
                                                <option value="all" {{ request('lastupdated', $filters['lastupdated'] ?? '100d') == 'all' ? 'selected' : '' }}>All Records</option>
                                                <option value="5m" {{ request('lastupdated', $filters['lastupdated'] ?? '100d') == '5m' ? 'selected' : '' }}>Last 5 Minutes</option>
                                                <option value="10m" {{ request('lastupdated', $filters['lastupdated'] ?? '100d') == '10m' ? 'selected' : '' }}>Last 10 Minutes</option>
                                                <option value="30m" {{ request('lastupdated', $filters['lastupdated'] ?? '100d') == '30m' ? 'selected' : '' }}>Last 30 Minutes</option>
                                                <option value="1h" {{ request('lastupdated', $filters['lastupdated'] ?? '100d') == '1h' ? 'selected' : '' }}>Last 1 Hour</option>
                                                <option value="4h" {{ request('lastupdated', $filters['lastupdated'] ?? '100d') == '4h' ? 'selected' : '' }}>Last 4 Hours</option>
                                                <option value="6h" {{ request('lastupdated', $filters['lastupdated'] ?? '100d') == '6h' ? 'selected' : '' }}>Last 6 Hours</option>
                                                <option value="24h" {{ request('lastupdated', $filters['lastupdated'] ?? '100d') == '24h' ? 'selected' : '' }}>Last 24 Hours</option>
                                                <option value="7d" {{ request('lastupdated', $filters['lastupdated'] ?? '100d') == '7d' ? 'selected' : '' }}>Last 7 Days</option>
                                                <option value="30d" {{ request('lastupdated', $filters['lastupdated'] ?? '100d') == '30d' ? 'selected' : '' }}>Last 30 Days</option>
                                                <option value="60d" {{ request('lastupdated', $filters['lastupdated'] ?? '100d') == '60d' ? 'selected' : '' }}>Last 60 Days</option>
                                                <option value="100d" {{ request('lastupdated', $filters['lastupdated'] ?? '100d') == '100d' ? 'selected' : '' }}>Last 100 Days</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="name">Product Name</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter product name" value="{{ request('name') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="barcode">Barcode/ISBN/Product ID</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Enter barcode or ID" value="{{ request('barcode') }}">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <button type="submit" class="btn btn-primary me-2"><em class="icon ni ni-filter-alt"></em><span>Apply Filter</span></button>
                                            <!-- @if(request('lastupdated') || request('name') || request('barcode'))
                                                <a href="{{ route('admin.erprev.sales') }}" class="btn btn-secondary"><em class="icon ni ni-reload"></em><span>Clear Filter</span></a>
                                            @endif -->
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @if($paginator->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Invoice ID</th>
                                            <th>Sale ID</th>
                                            <th>Date/Time</th>
                                            <th>Product Name</th>
                                            <th>Category</th>
                                            <th>Warehouse</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                            <th>Customer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paginator as $sale)
                                            <tr>
                                                <td>{{ ($paginator->currentPage() - 1) * $paginator->perPage() + $loop->iteration }}</td>
                                                <td>{{ $sale['InvoiceID'] ?? 'N/A' }}</td>
                                                <td>{{ $sale['ID'] ?? 'N/A' }}</td>
                                                <td>{{ $sale['DateTime'] ?? 'N/A' }}</td>
                                                <td>
                                                    <strong>{{ $sale['Product'] ?? 'N/A' }}</strong>
                                                    @if(isset($sale['ProductID']))
                                                        <br><small class="text-muted">ID: {{ $sale['ProductID'] }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $sale['Category'] ?? 'N/A' }}</td>
                                                <td>{{ $sale['WareHouse'] ?? 'N/A' }}</td>
                                                <td>{{ number_format((float)($sale['Qty'] ?? 0)) }}</td>
                                                <td>{!! $sale['Currency'] ?? $sale['CurrencySymbol'] ?? '&#x20A6;' !!}{{ number_format((float)str_replace(',', '', $sale['UnitPrice'] ?? 0), 2) }}</td>
                                                <td>{!! $sale['Currency'] ?? $sale['CurrencySymbol'] ?? '&#x20A6;' !!}{{ number_format((float)str_replace(',', '', $sale['Amount'] ?? 0), 2) }}</td>
                                                <td>
                                                    {{ $sale['CustomerName'] ?? 'N/A' }}
                                                    @if(!empty($sale['CustomerMobile']))
                                                        <br><small class="text-muted">{{ $sale['CustomerMobile'] }}</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination Links -->
                            <div class="card-inner">
                                <div class="nk-block-between-md g-3">
                                    <div class="g">
                                        @if ($paginator->hasPages())
                                    <div>
                                        {{ $paginator->appends([
                                           'lastupdated' => request('lastupdated', $filters['lastupdated'] ?? '100d'),
                                           'name' => request('name'),
                                           'barcode' => request('barcode')
                                        ])->links('vendor.pagination.bootstrap-4') }}
                                    </div>
                                @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <em class="icon ni ni-file-text" style="font-size: 48px; opacity: 0.3;"></em>
                                <p class="mt-3">No sales data found</p>
                                <p class="text-muted">Try adjusting your filters or check the ERPREV connection</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('btn-sync-sales').addEventListener('click', function() {
        Swal.fire({
            title: 'Synchronizing Sales Data...',
            text: 'Performing deep search of sales from April 2026 to update wallets. Please wait.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('{{ route("api.erprev.sync-sales-deep") }}?lastupdated=2026-04-01&dry_run=0&status=all')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const stats = data.statistics;
                    Swal.fire({
                        icon: 'success',
                        title: 'Sales Sync Completed',
                        html: `
                            <p>${data.message}</p>
                            <div class="text-start mt-3">
                                <ul>
                                    <li><strong>Processed Sales:</strong> ${stats.processed}</li>
                                    <li><strong>Skipped (Duplicates):</strong> ${stats.duplicates}</li>
                                    <li><strong>Books Not Found:</strong> ${stats.books_not_found}</li>
                                    <li><strong>Total Records Received:</strong> ${stats.total_records_received}</li>
                                    <li><strong>Errors:</strong> ${stats.errors}</li>
                                </ul>
                            </div>
                        `,
                        confirmButtonText: 'Reload Page',
                        confirmButtonColor: '#099fff',
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sync Failed',
                        text: data.message || 'An error occurred during synchronization.',
                        confirmButtonColor: '#e85347'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Request Failed',
                    text: 'Unable to communicate with the synchronization endpoint. ' + error.message,
                    confirmButtonColor: '#e85347'
                });
            });
    });
</script>
@endsection