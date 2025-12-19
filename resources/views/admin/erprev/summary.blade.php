@extends('layouts.admin')

@section('title', 'ERPREV Sales Summary | Rhymes Platform')

@section('page-title', 'ERPREV Sales Summary')

@section('page-description', 'Sales summary from ERPREV system')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">ERPREV Sales Summary</h3>
                        <div class="nk-block-des text-soft">
                            <p>Sales summary synchronized from ERPREV system</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="{{ route('admin.erprev.sales') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-tranx"></em><span>Sales</span></a></li>
                                    <li><a href="{{ route('admin.erprev.inventory') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-package"></em><span>Inventory</span></a></li>
                                    <li><a href="{{ route('admin.erprev.products') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-grid-add"></em><span>Products</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="card card-bordered card-full">
                    <div class="card-inner">
                        @if($paginator->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Barcode</th>
                                            <th>Category</th>
                                            <th>Units Sold</th>
                                            <th>Price</th>
                                            <th>Total Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paginator as $item)
                                            <tr>
                                                <td>{{ $item['SN'] ?? 'N/A' }}</td>
                                                <td>
                                                    <strong>{{ $item['Name'] ?? 'N/A' }}</strong>
                                                </td>
                                                <td>{{ $item['Barcode'] ?? 'N/A' }}</td>
                                                <td>{{ $item['Category'] ?? 'N/A' }}</td>
                                                <td>{{ number_format((float)($item['UnitsInStock'] ?? 0)) }}</td>
                                                <td>{!! $item['CurrencySymbol'] ?? '&#x20A6;' !!}{{ number_format((float)($item['SellingPrice'] ?? 0), 2) }}</td>
                                                <td>{!! $item['CurrencySymbol'] ?? '&#x20A6;' !!}{{ number_format(((float)($item['SellingPrice'] ?? 0)) * ((float)($item['UnitsInStock'] ?? 0)), 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination Links -->
                            <div class="card-inner">
                                <div class="nk-block-between-md g-3">
                                    <div class="g">
                                        {{ $paginator->links() }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <em class="icon ni ni-bar-chart" style="font-size: 48px; opacity: 0.3;"></em>
                                <p class="mt-3">No sales summary data found</p>
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