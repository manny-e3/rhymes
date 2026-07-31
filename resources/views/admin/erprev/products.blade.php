@extends('layouts.admin')

@section('title', 'ERPREV Product Listings | Rhymes Platform')

@section('page-title', 'ERPREV Product Listings')

@section('page-description', 'Product catalog from ERPREV system')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">ERPREV Product Listings</h3>
                        <div class="nk-block-des text-soft">
                            <p>Product catalog synchronized from ERPREV system</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <!-- <ul class="nk-block-tools g-3">
                                    <li><a href="{{ route('admin.erprev.sales') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-tranx"></em><span>Sales</span></a></li>
                                    <li><a href="{{ route('admin.erprev.inventory') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-package"></em><span>Inventory</span></a></li>
                                    <li><a href="{{ route('admin.erprev.summary') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-bar-chart"></em><span>Summary</span></a></li>
                                    <li><a href="{{ route('admin.erprev.test-endpoints') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-play-fill"></em><span>Test Endpoints</span></a></li>
                                    <li><a href="{{ route('admin.erprev.sync-stocked-books-sales') }}" target="_blank" class="btn btn-white btn-dim btn-outline-primary"><em class="icon ni ni-reload"></em><span>Sync Stocked Books Sales</span></a></li>
                                </ul> -->
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
                                    <h6 class="nk-block-title">Filter Product Listings</h6>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-inner mb-2">
                            <form method="GET" action="{{ route('admin.erprev.products') }}" class="row g-2 align-items-end">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label small mb-1" for="id">Product ID</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control form-control-sm" id="id" name="id" placeholder="Enter product ID" value="{{ request('id') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label small mb-1" for="name">Product Name</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Enter product name" value="{{ request('name') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label small mb-1" for="barcode">Barcode/ISBN</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control form-control-sm" id="barcode" name="barcode" placeholder="Enter barcode" value="{{ request('barcode') }}">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <button type="submit" class="btn btn-primary me-2"><em class="icon ni ni-filter-alt"></em><span>Apply</span></button>
                                            @if(request('id') || request('name') || request('barcode'))
                                                <a href="{{ route('admin.erprev.products') }}" class="btn btn-secondary"><em class="icon ni ni-reload"></em><span>Clear</span></a>
                                            @endif
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
                                            <th>Name</th>
                                            <th>Barcode</th>
                                            <th>Category</th>
                                          
                                            <th>Units In Stock</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paginator as $product)
                                            <tr>
                                                <td>{{ $product['ID'] }}</td>
                                                <td>
                                                    <strong>{{ $product['Name'] ?? 'N/A' }}</strong>
                                                </td>
                                                <td>{{ $product['Barcode'] ?? 'N/A' }}</td>
                                                <td>{{ $product['Category'] ?? 'N/A' }}</td>
                                               
                                                
                                                <td>{{ number_format((float)($product['UnitsInStock'] ?? 0)) }}</td>
                                              
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
                                           'id' => request('id'),
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
                                <em class="icon ni ni-grid-add" style="font-size: 48px; opacity: 0.3;"></em>
                                <p class="mt-3">No products found</p>
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