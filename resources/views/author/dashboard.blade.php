@extends('layouts.author')

@section('title', 'Author Dashboard | Rhymes Author Platform')

@section('page-title', 'Author Dashboard')

@section('page-description', 'Welcome back!')

@php
    // Safe defaults to prevent undefined variable errors if the view is rendered
    // without the controller-provided $analytics payload.
    $analytics = $analytics ?? [
        'user' => auth()->user(),
        'stats' => [
            'total_books' => 0,
            'pending_books' => 0,
            'published_books' => 0,
            'rejected_books' => 0,
            'wallet_balance' => 0,
            'available_balance' => 0,
            'total_earnings' => 0,
            'monthly_earnings' => 0,
            'monthly_growth' => 0,
            'pending_payouts' => 0,
            'total_payouts' => 0,
        ],
        'recent' => [
            'books' => collect(),
            'transactions' => collect(),
            'payouts' => collect(),
        ],
        'analytics' => [
            'book_sales' => [],
            'wallet_analytics' => [],
        ],
    ];
@endphp

@section('content')
<!-- main header @e -->
<!-- content @s -->
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Author Dashboard</h3>
                        <div class="nk-block-des text-soft">
                            <p>Monitor your books, earnings, and publishing progress.</p>
                        </div>
                    </div><!-- .nk-block-head-content -->
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="{{ route('author.books.create') }}" class="btn btn-primary"><em class="icon ni ni-plus"></em><span>Add New Book</span></a></li>
                                    <li><a href="{{route('author.wallet.index')}}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-wallet"></em><span>View Wallet</span></a></li>
                                    <li><a href="{{route('author.payouts.index')}}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-tranx"></em><span>Payouts</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- .nk-block-head-content -->
                </div><!-- .nk-block-between -->
            </div><!-- .nk-block-head -->
            
            <div class="nk-block">
                <!-- Quick Stats Cards -->
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Books</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-book text-primary"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ $analytics['stats']['total_books'] }}</span>
                                    <span class="sub-title">Published: {{ $analytics['stats']['published_books'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Wallet Balance</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-wallet text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($analytics['stats']['wallet_balance'], 2) }}</span>
                                    <span class="sub-title">Available: ${{ number_format($analytics['stats']['available_balance'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Monthly Earnings</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-growth text-info"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($analytics['stats']['monthly_earnings'], 2) }}</span>
                                    @if($analytics['stats']['monthly_growth'] > 0)
                                        <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em>{{ number_format($analytics['stats']['monthly_growth'], 1) }}%</span>
                                    @elseif($analytics['stats']['monthly_growth'] < 0)
                                        <span class="sub-title text-danger"><em class="icon ni ni-arrow-long-down"></em>{{ number_format(abs($analytics['stats']['monthly_growth']), 1) }}%</span>
                                    @else
                                        <span class="sub-title">No change</span>
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
                                        <h6 class="title">Pending Books</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-clock text-warning"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ $analytics['stats']['pending_books'] }}</span>
                                    <span class="sub-title">Under Review</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-gs">
                    <div class="col-xxl-8">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Book Sales Performance</h6>
                                        <p>Revenue breakdown by your published books</p>
                                    </div>
                                    <div class="card-tools">
                                        <a href="" class="link">View Details</a>
                                    </div>
                                </div>
                                @if(count($analytics['analytics']['book_sales']) > 0)
                                    <div class="nk-tb-list nk-tb-orders">
                                        <div class="nk-tb-item nk-tb-head">
                                            <div class="nk-tb-col"><span>Book Title</span></div>
                                            <div class="nk-tb-col tb-col-md"><span>Sales Count</span></div>
                                            <div class="nk-tb-col tb-col-lg"><span>Total Revenue</span></div>
                                            <div class="nk-tb-col"><span>Avg. Price</span></div>
                                        </div>
                                        @foreach($analytics['analytics']['book_sales'] as $bookSale)
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col">
                                                    <span class="tb-lead">{{ $bookSale['title'] ?? 'Unknown Book' }}</span>
                                                </div>
                                                <div class="nk-tb-col tb-col-md">
                                                    <span class="tb-sub">{{ $bookSale['sales_count'] ?? 0 }}</span>
                                                </div>
                                                <div class="nk-tb-col tb-col-lg">
                                                    <span class="tb-sub text-primary">₦{{ number_format($bookSale['total_revenue'] ?? 0, 2) }}</span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span class="tb-sub">₦{{ number_format($bookSale['avg_price'] ?? 0, 2) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <em class="icon ni ni-book-read" style="font-size: 3rem; opacity: 0.3;"></em>
                                        <p class="text-soft mt-2">No sales data available yet. Start by publishing your first book!</p>
                                       
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div><!-- .col -->
                    
                    <div class="col-xxl-4">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Recent Activity</h6>
                                        <p>Latest updates on your books and transactions</p>
                                    </div>
                                    <div class="card-tools">
                                        <a href="" class="link">View All</a>
                                    </div>
                                </div>
                                
                                @if(count($analytics['recent']['books']) > 0 || count($analytics['recent']['transactions']) > 0)
                                    <ul class="nk-activity">
                                        @foreach($analytics['recent']['books'] as $book)
                                            <li class="nk-activity-item">
                                                <div class="nk-activity-media user-avatar bg-primary">
                                                    <em class="icon ni ni-book"></em>
                                                </div>
                                                <div class="nk-activity-data">
                                                    <div class="label">Book "{{ $book->title }}" is {{ $book->status }}</div>
                                                    <span class="time">{{ $book->created_at->diffForHumans() }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                        
                                        @foreach($analytics['recent']['transactions']->take(3) as $transaction)
                                            <li class="nk-activity-item">
                                                <div class="nk-activity-media user-avatar bg-success">
                                                    <em class="icon ni ni-coins"></em>
                                                </div>
                                                <div class="nk-activity-data">
                                                    <div class="label">{{ ucfirst($transaction->type) }} of ₦{{ number_format($transaction->amount, 2) }}</div>
                                                    <span class="time">{{ $transaction->created_at->diffForHumans() }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-center py-4">
                                        <em class="icon ni ni-activity" style="font-size: 2rem; opacity: 0.3;"></em>
                                        <p class="text-soft mt-2">No recent activity</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- .nk-block -->
        </div>
    </div>
</div>
@endsection
