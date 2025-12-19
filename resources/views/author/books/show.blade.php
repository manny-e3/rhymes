@extends('layouts.author')
@section('title', 'Book Details | Rhymes Author Platform')
@section('page-title', 'Book Details')
@section('page-description', 'View book information')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between g-3">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">{{ $book->title }}</h3>
                        <div class="nk-block-des text-soft">
                            <p>Book details and information</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <a href="{{ route('author.books.index') }}" class="btn btn-outline-light">
                            <em class="icon ni ni-arrow-left"></em><span>Back to Books</span>
                        </a>
                        @if($book->status === 'pending' || $book->status === 'rejected')
                            <a href="{{ route('author.books.edit', $book) }}" class="btn btn-primary">
                                <em class="icon ni ni-edit"></em><span>Edit Book</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-lg-8">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="nk-block-head">
                                    <h5 class="title">Book Information</h5>
                                </div>
                                <div class="row gy-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Title</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" value="{{ $book->title }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">ISBN</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" value="{{ $book->isbn }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Genre</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" value="{{ $book->genre }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Price</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" value="${{ number_format($book->price, 2) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Book Type</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" value="{{ ucfirst($book->book_type) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Status</label>
                                            <div class="form-control-wrap">
                                                @switch($book->status)
                                                    @case('pending')
                                                        <span class="badge badge-warning">Pending Review</span>
                                                        @break
                                                    @case('accepted')
                                                        <span class="badge badge-success">Accepted</span>
                                                        @break
                                                    @case('stocked')
                                                        <span class="badge badge-info">In Stock</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge badge-danger">Rejected</span>
                                                        @break
                                                @endswitch
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Description</label>
                                            <div class="form-control-wrap">
                                                <textarea class="form-control" rows="4" readonly>{{ $book->description }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    @if($book->admin_notes)
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Admin Notes</label>
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control" rows="3" readonly>{{ $book->admin_notes }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="nk-block-head">
                                    <h5 class="title">Book Statistics</h5>
                                </div>
                                <div class="row gy-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Created Date</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" value="{{ $book->created_at->format('M d, Y') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Last Updated</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" value="{{ $book->updated_at->format('M d, Y') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    @if($book->rev_book_id)
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Book ID</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="{{ $book->rev_book_id }}" readonly>
                                                </div>
                                            </div>
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
</div>
@endsection
