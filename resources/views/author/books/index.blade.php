@extends('layouts.author')
@section('title', 'My Books | Rhymes Author Platform')
@section('page-title', 'My Books')
@section('page-description', 'Manage your books here')
@section('content')
                <!-- main header @e -->
                <!-- content @s -->
                <div class="nk-content nk-content-fluid">
                    <div class="container-xl wide-xl">
                        <div class="nk-content-body">
                            <div class="components-preview wide-xl mx-auto">
                                <div class="nk-block-head nk-block-head-lg wide-sm">
                                   
                                </div><!-- .nk-block-head -->
                                <div class="nk-block nk-block-lg">
                                     <div class="nk-block-head nk-block-head-sm">
                                <div class="nk-block-between g-3">
                                    <div class="nk-block-head-content">
                                        <h3 class="nk-block-title page-title">Books </h3>
                                        <div class="nk-block-des text-soft">
                                            <p>List of books you have created.</p>
                                        </div>
                                    </div>
                                    <div class="nk-block-head-content">
                                        <a href="{{route('author.books.create')}}"  class="btn btn-primary d-none d-sm-inline-flex"><em class="icon ni ni-plus"></em><span>Create New</span></a>
                                        <a href="#" class="btn btn-icon btn-primary d-inline-flex d-sm-none"><em class="icon ni ni-plus"></em></a>
                                    </div>
                                </div>
                            </div><!-- .nk-block-head -->
                                    <div class="card card-preview">
                                        <div class="card-inner">
                                              <table class="datatable-init-export nowrap table" data-export-title="Export">
                                                <thead>
                                                    <tr>
                                                        <th>S/N</th>
                                                        <th>Book Details</th>
                                                        <th>ISBN</th>
                                                        <th>Type</th>
                                        
                                                        <th>Price</th>
                                                        <th>Status</th>
                                                        <th>Quantity</th>
                                                        <th>Submitted</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($books as $book)
                                                    <tr class="nk-tb-item">
                                                        <td class="nk-tb-col">
                                                            <span>{{ $loop->iteration }}</span>
                                                        </td>
                                            
                                                        <td class="nk-tb-col">
                                                            <div class="user-card">
                                                                <div class="user-info">
                                                                    <span class="tb-lead">{{ $book->title }} <span class="dot dot-success d-md-none ms-1"></span></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                            
                                                        <td class="nk-tb-col tb-col-mb">
                                                            {{ $book->isbn }}
                                                        </td>
                                            
                                                        <td class="nk-tb-col tb-col-mb">
                                                            {{ $book->book_type }}
                                                        </td>
                                            
                                                        {{-- <td class="nk-tb-col tb-col-mb">
                                                            {{ $book->genre }}
                                                        </td> --}}
                                            
                                                        <td class="nk-tb-col tb-col-md">
                                                            <span class="tb-amount">₦{{ number_format($book->price, 2) }}</span>
                                                        </td>
                                            
                                                        <td class="nk-tb-col tb-col-lg">
                                                            <ul class="list-status">
                                                                @if($book->status === 'pending_review')
                                                                        <span class="badge badge-sm badge-dim bg-outline-warning">Pending Review</span>
                                                                    @elseif($book->status === 'send_review_copy')
                                                                        <span class="badge badge-sm badge-dim bg-outline-info">Send Review Copy</span>
                                                                    @elseif($book->status === 'approved_awaiting_delivery')
                                                                        <span class="badge badge-sm badge-dim bg-outline-success">Approved - Awaiting Delivery</span>
                                                                    @elseif($book->status === 'rejected')
                                                                        <span class="badge badge-sm badge-dim bg-outline-danger">Rejected</span>
                                                                    @elseif($book->status === 'stocked')
                                                                        <span class="badge badge-sm badge-dim bg-outline-info">Stocked</span>
                                                                    @endif
                                                                                                    @if($book->trashed())
                                                                    <li><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Deleted</span></li>
                                                                @endif
                                                            </ul>
                                                        </td>
                                            
                                                        <td class="nk-tb-col tb-col-lg">
                                                            @if($book->status === 'stocked' && $book->quantity)
                                                                <span class="tb-amount">{{ $book->quantity }}</span>
                                                            @else
                                                                <span class="tb-sub">N/A</span>
                                                            @endif
                                                        </td>
                                            
                                                        <td class="nk-tb-col tb-col-lg">
                                                            <span>{{ optional($book->created_at)->format('M d, Y') }}</span>
                                                        </td>
                                            
                                                        <td class="nk-tb-col nk-tb-col-tools">
                                                            <ul class="nk-tb-actions gx-1">
                                                                <li>
                                                                    <div class="dropdown">
                                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                                            <em class="icon ni ni-more-h"></em>
                                                                        </a>
                                                                        <div class="dropdown-menu dropdown-menu-end">
                                                                            <ul class="link-list-opt no-bdr">
                                                                                <li>
                                                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#viewBook-{{ $book->id }}">
                                                                                        <em class="icon ni ni-eye"></em>
                                                                                        <span>View Details</span>
                                                                                    </a>
                                                                                </li>

                                                                                @if($book->trashed())
                                                                                    <li>
                                                                                        <a href="#" onclick="restoreBook({{ $book->id }}, '{{ $book->title }}'); return false;">
                                                                                            <em class="icon ni ni-reload"></em>
                                                                                            <span>Restore</span>
                                                                                        </a>
                                                                                    </li>
                                                                                @else
                                                                                    @if($book->status === 'pending_review' || $book->status === 'rejected')
                                                                                        <li>
                                                                                            <a href="#" data-bs-toggle="modal" data-bs-target="#editBook-{{ $book->id }}">
                                                                                                <em class="icon ni ni-repeat"></em>
                                                                                                <span>Edit</span>
                                                                                            </a>
                                                                                        </li>

                                                                                        <li class="divider"></li>
                                                                                        <li>
                                                                                            <a href="#" onclick="deleteBook({{ $book->id }}, '{{ $book->title }}'); return false;">
                                                                                                <em class="icon ni ni-trash"></em>
                                                                                                <span>Delete</span>
                                                                                            </a>
                                                                                        </li>
                                                                                    @endif
                                                                                @endif
                                                                            </ul> 
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            
                                        </div>
                                    </div><!-- .card-preview -->
                                </div> <!-- nk-block -->
                               
                               
                            </div><!-- .components-preview -->
                        </div>
                    </div>
                </div>
                <!-- content @e -->
                <!-- footer @s -->


                <div class="modal fade" id="addBook" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Book</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('author.books.store') }}">
                        @csrf
                        <div class="row g-4">
                            <!-- ISBN -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="isbn">ISBN</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="isbn" name="isbn" value="{{ old('isbn') }}" required>
                                        @error('isbn')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <span class="form-note">Enter the 13-digit ISBN of your book</span>
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="title">Book Title</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                                        @error('title')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Genre -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="genre">Genre</label>
                                    <div class="form-control-wrap">
                                        <select class="form-select form-select-search" id="genre" name="genre" required>
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
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="price">Price (₦)</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required>
                                        @error('price')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Book Type -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Book Type</label>
                                    <ul class="custom-control-group g-3 align-center">
                                        <li>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="book_type" value="physical" id="book_type_physical" {{ old('book_type') == 'physical' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="book_type_physical">Physical Book Only</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="book_type" value="digital" id="book_type_digital" {{ old('book_type') == 'digital' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="book_type_digital">Digital Book Only</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="book_type" value="both" id="book_type_both" {{ old('book_type') == 'both' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="book_type_both">Both Physical and Digital</label>
                                            </div>
                                        </li>
                                    </ul>
                                    @error('book_type')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="description">Book Description</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <span class="form-note">Provide a detailed description of your book, including plot summary, target audience, and key themes.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-lg btn-primary">Submit Book for Review</button>
                            <a href="{{ route('author.books.index') }}" class="btn btn-lg btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <span class="sub-text">Modal Footer Text</span>
                </div>
            </div>
        </div>
    </div>

    @foreach($books as $book)
    <!-- Edit Book Modal -->
    <div class="modal fade" id="editBook-{{$book->id}}" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Book</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('author.books.update', $book->id) }}" id="editBookForm-{{$book->id}}">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <!-- ISBN -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="edit_isbn_{{$book->id}}">ISBN</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="edit_isbn_{{$book->id}}" name="isbn" value="{{$book->isbn}}" required>
                                        @error('isbn')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <span class="form-note">13-digit ISBN of your book</span>
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="edit_title_{{$book->id}}">Book Title</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="edit_title_{{$book->id}}" name="title" value="{{$book->title}}" required>
                                        @error('title')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Genre -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="edit_genre_{{$book->id}}">Genre</label>
                                    <div class="form-control-wrap">
                                        <select class="form-select form-select-search" id="edit_genre_{{$book->id}}" name="genre" required>
                                            <option value="">Select Genre</option>
                                            @foreach($categories as $category)
                                                @if(is_array($category))
                                                    <option value="{{ $category['name'] }}" {{ $book->genre == $category['name'] ? 'selected' : '' }} data-id="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                                @else
                                                    <option value="{{ $category }}" {{ $book->genre == $category ? 'selected' : '' }}>{{ $category }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('genre')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="edit_price_{{$book->id}}">Price ($)</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control" id="edit_price_{{$book->id}}" name="price" value="{{$book->price}}" step="0.01" min="0" required>
                                        @error('price')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Book Type -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Book Type</label>
                                    <ul class="custom-control-group g-3 align-center">
                                        <li>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="book_type" value="physical" id="edit_book_type_physical_{{$book->id}}" {{ $book->book_type == 'physical' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="edit_book_type_physical_{{$book->id}}">Physical Book Only</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="book_type" value="digital" id="edit_book_type_digital_{{$book->id}}" {{ $book->book_type == 'digital' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="edit_book_type_digital_{{$book->id}}">Digital Book Only</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="book_type" value="both" id="edit_book_type_both_{{$book->id}}" {{ $book->book_type == 'both' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="edit_book_type_both_{{$book->id}}">Both Physical and Digital</label>
                                            </div>
                                        </li>
                                    </ul>
                                    @error('book_type')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="edit_description_{{$book->id}}">Book Description</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" id="edit_description_{{$book->id}}" name="description" rows="6" required>{{$book->description}}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <span class="form-note">Provide a detailed description of your book, including plot summary, target audience, and key themes.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-lg btn-primary">Update Book</button>
                            <a href="#" class="btn btn-lg btn-light" data-bs-dismiss="modal">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @foreach($books as $book)
    <!-- View Book Modal -->
    <div class="modal fade" id="viewBook-{{$book->id}}" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book Details</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-inner">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">ISBN</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="{{$book->isbn}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Book Title</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="{{$book->title}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Genre</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="{{$book->genre}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Price</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="₦{{number_format($book->price, 2)}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Book Type</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="{{ucfirst($book->book_type)}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Status</label>
                                                <div class="form-control-wrap">
                                                    <span class="badge badge-sm 
                                                        @switch($book->status)
                                                            @case('pending_review') badge-warning @break
                                                            @case('send_review_copy') badge-info @break
                                                            @case('approved_awaiting_delivery') badge-success @break
                                                            @case('stocked') badge-info @break
                                                            @case('rejected') badge-danger @break
                                                        @endswitch
                                                    ">{{ucfirst(str_replace('_', ' ', $book->status))}}</span>
                                                </div>
                                            </div>
                                        </div>
                                        @if($book->status === 'stocked' && $book->quantity)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Quantity</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="{{$book->quantity}} copies" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Description</label>
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control" rows="6" readonly>{{$book->description}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Submitted Date</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="{{$book->created_at->format('M d, Y h:i A')}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Last Updated</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" value="{{$book->updated_at->format('M d, Y h:i A')}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        @if($book->admin_notes)
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Admin Notes</label>
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control bg-light" rows="3" readonly>{{$book->admin_notes}}</textarea>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    @if($book->status === 'pending_review' || $book->status === 'rejected')
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editBook-{{$book->id}}">Edit Book</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

 @endsection
  {{-- Scripts --}}
@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for all genre dropdowns
        $('#genre').select2({
            placeholder: "Select Genre",
            allowClear: true,
            width: '100%'
        });
        
        $('[id^="edit_genre_"]').select2({
            placeholder: "Select Genre",
            allowClear: true,
            width: '100%'
        });
    });
    
    // Delete book function with SweetAlert confirmation
    function deleteBook(bookId, bookTitle) {
        confirmAction(`Are you sure you want to delete the book "${bookTitle}"? This action cannot be undone.`, function() {
            const form = document.getElementById('deleteForm');
            form.action = `/author/books/${bookId}`;
            form.submit();
        });
    }
    
    // Restore book function with SweetAlert confirmation
    function restoreBook(bookId, bookTitle) {
        confirmAction(`Are you sure you want to restore the book "${bookTitle}"?`, function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/author/books/${bookId}/restore`;
            
            const csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'POST';
            
            form.appendChild(csrfField);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        });
    }
</script>
@endpush