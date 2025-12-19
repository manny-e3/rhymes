@extends('layouts.author')
@section('title', 'Edit Book | Rhymes Author Platform')
@section('page-title', 'Edit Book')
@section('page-description', 'Update book information')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between g-3">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Edit Book</h3>
                        <div class="nk-block-des text-soft">
                            <p>Update your book information</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <a href="{{ route('author.books.index') }}" class="btn btn-outline-light">
                            <em class="icon ni ni-arrow-left"></em><span>Back to Books</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-lg-8">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <form action="{{ route('author.books.update', $book) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="nk-block-head">
                                        <h5 class="title">Book Information</h5>
                                    </div>
                                    
                                    <div class="row gy-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control @error('title') error @enderror" 
                                                           id="title" name="title" value="{{ old('title', $book->title) }}" required>
                                                    @error('title')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="isbn">ISBN <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control @error('isbn') error @enderror" 
                                                           id="isbn" name="isbn" value="{{ old('isbn', $book->isbn) }}" required>
                                                    @error('isbn')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="genre">Genre <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <select class="form-select form-select-search @error('genre') error @enderror" id="genre" name="genre" required>
                                                        <option value="">Select Genre</option>
                                                        @foreach($categories as $category)
                                                            @if(is_array($category))
                                                                <option value="{{ $category['name'] }}" {{ old('genre', $book->genre) == $category['name'] ? 'selected' : '' }} data-id="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                                            @else
                                                                <option value="{{ $category }}" {{ old('genre', $book->genre) == $category ? 'selected' : '' }}>{{ $category }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    @error('genre')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="price">Price ($) <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <input type="number" step="0.01" min="0" class="form-control @error('price') error @enderror" 
                                                           id="price" name="price" value="{{ old('price', $book->price) }}" required>
                                                    @error('price')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="book_type">Book Type <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <select class="form-select @error('book_type') error @enderror" id="book_type" name="book_type" required>
                                                        <option value="">Select Type</option>
                                                        <option value="physical" {{ old('book_type', $book->book_type) == 'physical' ? 'selected' : '' }}>Physical Only</option>
                                                        <option value="digital" {{ old('book_type', $book->book_type) == 'digital' ? 'selected' : '' }}>Digital Only</option>
                                                        <option value="both" {{ old('book_type', $book->book_type) == 'both' ? 'selected' : '' }}>Both Physical & Digital</option>
                                                    </select>
                                                    @error('book_type')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label" for="description">Description <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control @error('description') error @enderror" 
                                                              id="description" name="description" rows="4" required>{{ old('description', $book->description) }}</textarea>
                                                    @error('description')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($book->admin_notes)
                                            <div class="col-12">
                                                <div class="alert alert-warning">
                                                    <h6>Admin Notes:</h6>
                                                    <p class="mb-0">{{ $book->admin_notes }}</p>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">
                                                    <em class="icon ni ni-save"></em><span>Update Book</span>
                                                </button>
                                                <a href="{{ route('author.books.show', $book) }}" class="btn btn-outline-light">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="nk-block-head">
                                    <h5 class="title">Current Status</h5>
                                </div>
                                <div class="row gy-3">
                                    <div class="col-12">
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
                                            <label class="form-label">Created</label>
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('#genre').select2({
            placeholder: "Select Genre",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush