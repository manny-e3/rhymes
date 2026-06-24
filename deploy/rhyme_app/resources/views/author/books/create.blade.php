@extends('layouts.author')
@section('title', 'Create Book | Rhymes Author Platform')
@section('page-title', 'Create New Book')
@section('page-description', 'Submit your book for review')

@section('content')
<style>
    .fancy-upload-zone {
        border: 2px dashed #dbdfea;
        background: #f5f6fa;
        padding: 30px;
        text-align: center;
        border-radius: 8px;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .fancy-upload-zone:hover, .fancy-upload-zone.dragover {
        border-color: #6576ff;
        background: #ebeefc;
    }
    .fancy-upload-zone .icon {
        font-size: 44px;
        color: #8094ae;
        margin-bottom: 8px;
        display: block;
    }
    .fancy-upload-zone h5 {
        margin-bottom: 4px;
        font-size: 16px;
        color: #364a63;
    }
    .fancy-upload-zone p {
        font-size: 12px;
        color: #8094ae;
        margin-bottom: 0;
    }
    #image-preview-container {
        display: none;
        margin-top: 20px;
        position: relative;
        width: fit-content;
        margin-left: auto;
        margin-right: auto;
        text-align: center;
    }
    #image-preview {
        max-width: 100%;
        max-height: 250px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        display: inline-block;
        border: 1px solid #e5e9f2;
    }
    .remove-image {
        position: absolute;
        top: -12px;
        right: -12px;
        background: #e85347;
        color: #fff;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(232, 83, 71, 0.4);
        border: 2px solid #fff;
        z-index: 10;
        transition: transform 0.2s;
    }
    .remove-image:hover {
        background: #cf372b;
        transform: scale(1.1);
    }
    .remove-image .icon {
        font-size: 16px;
    }
</style>
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
                                <form action="{{ route('author.books.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    
                                    <div class="nk-block-head">
                                        <h5 class="title">Book Information</h5>
                                    </div>
                                    
                                    <div class="row gy-3">
                                       
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="isbn">ISBN <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" inputmode="numeric" pattern="[0-9]*"
                                                           class="form-control @error('isbn') error @enderror" 
                                                           id="isbn" name="isbn" value="{{ old('isbn') }}"
                                                           oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
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
                                                <label class="form-label" for="price">Price (₦) <span class="text-danger">*</span></label>
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
                                                        <option value="paper_back" {{ old('book_type') == 'paper_back' ? 'selected' : '' }}>Paper back</option>
                                                        <option value="hard_back" {{ old('book_type') == 'hard_back' ? 'selected' : '' }}>Hard back</option>
                                                        <option value="both" {{ old('book_type') == 'both' ? 'selected' : '' }}>Both</option>
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
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label" for="image">Book Image <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <div class="fancy-upload-zone" id="upload-zone" onclick="document.getElementById('image').click()">
                                                        <div class="upload-content">
                                                            <em class="icon ni ni-cloud-upload"></em>
                                                            <h5>Click or Drag & Drop to Upload</h5>
                                                            <p>High-quality cover image (Max 5MB)</p>
                                                        </div>
                                                        <input type="file" class="d-none @error('image') error @enderror" id="image" name="image" accept="image/*" required>
                                                    </div>
                                                    @error('image')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                    
                                                    <div id="image-preview-container">
                                                        <img id="image-preview" src="#" alt="Preview">
                                                        <div class="remove-image" id="remove-image" title="Remove Image" style="display: none;">
                                                            <em class="icon ni ni-cross-sm"></em>
                                                        </div>
                                                    </div>
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

        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');
        const previewContainer = document.getElementById('image-preview-container');
        const removeButton = document.getElementById('remove-image');
        const uploadZone = document.getElementById('upload-zone');

        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    previewContainer.style.display = 'block';
                    removeButton.style.display = 'flex'; // Changed to flex to match CSS
                    uploadZone.style.display = 'none'; // Hide upload zone when image is present
                }
                reader.readAsDataURL(file);
            }
        });

        removeButton.addEventListener('click', function(e) {
            e.stopPropagation();
            imageInput.value = '';
            imagePreview.src = '#';
            previewContainer.style.display = 'none';
            removeButton.style.display = 'none';
            uploadZone.style.display = 'block'; // Show upload zone again
        });

        // Basic Drag & Drop visual feedback
        uploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                const files = e.dataTransfer.files;
                if (files[0].type.startsWith('image/')) {
                    imageInput.files = files;
                    const changeEvent = new Event('change');
                    imageInput.dispatchEvent(changeEvent);
                }
            }
        });
    });
</script>
@endpush