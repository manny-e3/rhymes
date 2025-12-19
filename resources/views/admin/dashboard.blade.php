@extends('layouts.admin')

@section('title', 'Admin Dashboard | Rhymes Platform')

@section('page-title', 'Admin Dashboard')

@section('page-description', 'Platform Overview & Analytics')

@section('content')
<!-- main header @e -->
<!-- content @s -->
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Admin Dashboard</h3>
                        <div class="nk-block-des text-soft">
                            <p>Platform overview, analytics, and management tools.</p>
                        </div>
                    </div><!-- .nk-block-head-content -->
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="{{ route('admin.reports.sales-dashboard') }}" class="btn btn-primary"><em class="icon ni ni-dashboard"></em><span>Sales Dashboard</span></a></li>
                                    <li><a href="{{ route('admin.books.index') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-eye"></em><span>Review Books</span></a></li>
                                    <li><a href="{{ route('admin.payouts.index') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-tranx"></em><span>Manage Payouts</span></a></li>
                                    <li><a href="{{ route('admin.erprev.sales') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-swap"></em><span>ERPREV Data</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- .nk-block-head-content -->
                </div><!-- .nk-block-between -->
            </div><!-- .nk-block-head -->
            
            <!-- SweetAlert Test Buttons -->
         
            
            <div class="nk-block">
                <!-- Overview Stats Cards -->
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Users</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-users text-primary"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($analytics['stats']['total_users']) }} </span>
                                    <span class="sub-title">Authors: {{ number_format($analytics['stats']['total_authors']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Revenue</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-coins text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($analytics['stats']['total_revenue'], 2) }}</span>
                                    @if($analytics['stats']['revenue_growth'] > 0)
                                        <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em>{{ number_format($analytics['stats']['revenue_growth'], 1) }}% this month</span>
                                    @elseif($analytics['stats']['revenue_growth'] < 0)
                                        <span class="sub-title text-danger"><em class="icon ni ni-arrow-long-down"></em>{{ number_format(abs($analytics['stats']['revenue_growth']), 1) }}% this month</span>
                                    @else
                                      

                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Books</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-book text-info"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($analytics['stats']['total_books']) }}</span>
                                    <span class="sub-title">Published: {{ number_format($analytics['stats']['published_books']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Pending Reviews</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-clock text-warning"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($analytics['stats']['pending_books']) }}</span>
                                    <span class="sub-title">Need Attention</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Secondary Stats -->
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Pending Payouts</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-tranx text-warning"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($analytics['stats']['pending_payouts']) }}</span>
                                    <span class="sub-title">₦{{ number_format($analytics['stats']['pending_payout_amount'], 2) }} pending</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">This Month Revenue</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-growth text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($analytics['stats']['this_month_revenue'], 2) }}</span>
                                    <span class="sub-title">vs ₦{{ number_format($analytics['stats']['last_month_revenue'], 2) }} last month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Payouts</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-check-circle text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($analytics['stats']['total_payout_amount'], 2) }}</span>
                                    <span class="sub-title">{{ number_format($analytics['stats']['approved_payouts']) }} completed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-gs">
                    <!-- Recent Books Requiring Review -->
                    <div class="col-xxl-8">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Recent Book Submissions</h6>
                                        <p>Latest books submitted for review</p>
                                    </div>
                                    {{-- <div class="card-tools">
                                        <a href="#" class="link">Review All</a>
                                    </div> --}}
                                </div>
                                @if(count($analytics['recent']['books']) > 0)
                                    <div class="nk-tb-list nk-tb-orders">
                                        <div class="nk-tb-item nk-tb-head">
                                            <div class="nk-tb-col"><span>Book Title</span></div>
                                            <div class="nk-tb-col tb-col-md"><span>Author</span></div>
                                            <div class="nk-tb-col"><span>Status</span></div>
                                            <div class="nk-tb-col"><span>Submitted</span></div>
                                        </div>
                                        @foreach($analytics['recent']['books'] as $book)
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col">
                                                    <span class="tb-product">
                                                        <span class="title">{{ Str::limit($book->title, 25) }}</span>
                                                    </span>
                                                </div>
                                                <div class="nk-tb-col tb-col-md">
                                                    <span>{{ $book->user->name }}</span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span class="badge bg-{{ $book->status === 'accepted' ? 'success' : ($book->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($book->status) }}</span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span>{{ $book->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center p-3">
                                        <em class="icon ni ni-book" style="font-size: 2rem;"></em>
                                        <p class="text-soft mt-2">No recent book submissions</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent User Registrations -->
                    <div class="col-xxl-4">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Recent Users</h6>
                                        <p>New platform registrations</p>
                                    </div>
                                </div>
                                @if(count($analytics['recent']['users']) > 0)
                                    <div class="nk-tb-list is-alt">
                                        @foreach($analytics['recent']['users'] as $user)
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col">
                                                    <div class="user-card">
                                                        <div class="user-avatar bg-primary-dim">
                                                            <span>{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                                        </div>
                                                        <div class="user-info">
                                                            <span class="tb-lead">{{ $user->name }}</span>
                                                            <span>{{ $user->email }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span class="sub-text">{{ $user->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center p-3">
                                        <em class="icon ni ni-user" style="font-size: 2rem;"></em>
                                        <p class="text-soft mt-2">No recent user registrations</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection