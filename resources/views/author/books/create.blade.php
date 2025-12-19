@extends('layouts.author')
@section('title', 'Create Book | Rhymes Author Platform')
@section('page-title', 'Create New Book')
@section('page-description', 'Submit your book for review')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between g-3">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Create New Book</h3>
                        <div class="nk-block-des text-soft">
                            <p>Submit your book for review and approval</p>
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
                                <form action="{{ route('author.books.store') }}" method="POST">
                                    @csrf
                                    
                                    <div class="nk-block-head">
                                        <h5 class="title">Book Information</h5>
                                    </div>
                                    
                                    <div class="row gy-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="isbn">ISBN <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control @error('isbn') error @enderror" 
                                                           id="isbn" name="isbn" value="{{ old('isbn') }}" required>
                                                    @error('isbn')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                    <div class="form-note">Enter the 13-digit ISBN of your book</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="title">Book Title <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control @error('title') error @enderror" 
                                                           id="title" name="title" value="{{ old('title') }}" required>
                                                    @error('title')
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
                                                                <option value="{{ $category['name'] }}" {{ old('genre') == $category['name'] ? 'selected' : '' }} data-id="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                                            @else
                                                                <option value="{{ $category }}" {{ old('genre') == $category ? 'selected' : '' }}>{{ $category }}</option>
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
                                                           id="price" name="price" value="{{ old('price') }}" required>
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
                                                        <option value="physical" {{ old('book_type') == 'physical' ? 'selected' : '' }}>Physical Only</option>
                                                        <option value="digital" {{ old('book_type') == 'digital' ? 'selected' : '' }}>Digital Only</option>
                                                        <option value="both" {{ old('book_type') == 'both' ? 'selected' : '' }}>Both Physical & Digital</option>
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
                                                              id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                                                    @error('description')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                    <div class="form-note">Provide a detailed description of your book</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">
                                                    <em class="icon ni ni-save"></em><span>Submit Book</span>
                                                </button>
                                                <a href="{{ route('author.books.index') }}" class="btn btn-outline-light">Cancel</a>
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
                                    <h5 class="title">Submission Guidelines</h5>
                                </div>
                                <div class="nk-block">
                                    <ul class="list list-sm list-checked">
                                        <li>Ensure your ISBN is valid and unique</li>
                                        <li>Provide an accurate and compelling description</li>
                                        <li>Set a competitive price for your book</li>
                                        <li>Choose the appropriate genre</li>
                                        <li>Your book will be reviewed within 3-5 business days</li>
                                    </ul>
                                </div>
                                <div class="nk-block">
                                    <div class="alert alert-info">
                                        <div class="alert-cta">
                                            <h6>Need Help?</h6>
                                            <p>Contact our support team if you need assistance with your book submission.</p>
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