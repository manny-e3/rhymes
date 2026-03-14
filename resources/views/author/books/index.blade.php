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
                                        <a href="{{route('author.books.create')}}" class="btn btn-icon btn-primary d-inline-flex d-sm-none"><em class="icon ni ni-plus"></em></a>
                                    </div>
                                </div>
                            </div><!-- .nk-block-head -->
                                    <div class="card card-preview">
                                        <div class="card-inner">
                                              <table class="datatable-init-export nowrap table" data-export-title="Export">
                                                <thead>
                                                    <tr>
                                                        <th>S/N</th>
                                                        <th>Cover</th>
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
                                                            <div class="user-avatar bg-light border">
                                                                @if($book->image)
                                                                    <img src="{{ asset('storage/' . $book->image) }}" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                                @else
                                                                    <em class="icon ni ni-book-read text-soft"></em>
                                                                @endif
                                                            </div>
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
                                                            {{ ucwords(str_replace('_', ' ', $book->book_type)) }}
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
                                            @elseif($book->status === 'edited_pending_approval')
                                                <span class="badge badge-sm badge-dim bg-outline-warning">Edited - Awaiting Approval</span>
                                            @elseif($book->status === 'retrieval_requested')
                                                <span class="badge badge-sm badge-dim bg-outline-warning">Retrieval Requested</span>
                                            @elseif($book->status === 'retrieved')
                                                <span class="badge badge-sm badge-dim bg-outline-danger">Retrieved</span>
                                            @endif
                                           
                                            @if($book->trashed())
                                                <span class="badge badge-sm badge-dim bg-outline-secondary">Deleted</span>
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
                                                                                        <span>View Book</span>
                                                                                    </a>
                                                                                </li>

                                                                                 @if($book->status === 'stocked')
                                                                                 <li>
                                                                                             <a href="{{ route('author.books.edit', $book->id) }}">
                                                                                                 <em class="icon ni ni-edit-fill"></em>
                                                                                                 <span>Edit Book</span>
                                                                                             </a>
                                                                                         </li>
                                                                                        @endif
                                                                                        @if($book->status === 'stocked' &&  $book->status !== 'retrieval_requested' && $book->status !== 'retrieved')
                                                                                        <li class="divider"></li>
                                                                                        <li>
                                                                                            <a href="#" onclick="requestBookRetrieval({{ $book->id }}); return false;">
                                                                                                <em class="icon ni ni-exclamation-circle"></em>
                                                                                                <span>Retrieval of Book</span>
                                                                                            </a>
                                                                                        </li>
                                                                                        @endif

                                                                                @if($book->trashed())
                                                                                    <li>
                                                                                        <a href="#" onclick="restoreBook({{ $book->id }}, '{{ $book->title }}'); return false;">
                                                                                            <em class="icon ni ni-reload"></em>
                                                                                            <span>Restore</span>
                                                                                        </a>
                                                                                    </li>
                                                                                @else
                                                                                    @if($book->status === 'pending_review')
                                                    
                                                                                    @endif
                                                                                         @if($book->status === 'rejected')
                                                                                         <li>
                                                                                             <a href="{{ route('author.books.edit', $book->id) }}">
                                                                                                 <em class="icon ni ni-edit"></em>
                                                                                                 <span>Edit & Resubmit</span>
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
                        <div class="col-lg-4">
                            <div class="card card-bordered h-100">
                                <div class="card-inner d-flex align-items-center justify-content-center p-2 bg-light" style="min-height: 250px;">
                                    @if($book->image)
                                        <img src="{{ asset('storage/' . $book->image) }}" class="rounded shadow-sm" alt="{{ $book->title }}" style="max-width: 100%; max-height: 350px; object-fit: contain;">
                                    @else
                                        <div class="text-center text-soft">
                                            <em class="icon ni ni-book-read" style="font-size: 64px;"></em>
                                            <p class="mt-2">No Cover Available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card card-bordered h-100">
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
                                                    <input type="text" class="form-control" value="{{ ucwords(str_replace('_', ' ', $book->book_type)) }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Status</label>
                                                <div class="form-control-wrap">
                                                    <span class="badge badge-sm 
                                                        @switch($book->status)
                                                            @case('pending_review') badge-dim bg-warning @break
                                                            @case('send_review_copy') badge-dim bg-warning @break
                                                            @case('approved_awaiting_delivery') badge-dim bg-success @break
                                                            @case('stocked') badge-dim bg-success @break
                                                            @case('edited_pending_approval') badge-dim bg-warning @break
                                                            @case('retrieval_requested') badge-dim bg-warning @break
                                                            @case('retrieved') badge-dim bg-secondary @break
                                                            @case('rejected') badge-dim bg-danger @break
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
                    {{-- @if($book->status === 'pending_review' || $book->status === 'rejected' || $book->status === 'edited_pending_approval')
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editBook-{{$book->id}}">Edit Book</button>
                    @endif --}}
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
    
    /**
     * Request book retrieval
     */
    function requestBookRetrieval(bookId) {
        Swal.fire({
            title: 'Retrieval of Book',
            html: `
                <div class="text-start mb-3">
                    <label class="form-label" for="retrieval_location">Location for Retrieval <span class="text-danger">*</span></label>
                    <input type="text" id="retrieval_location" class="swal2-input m-0 w-100" placeholder="e.g. Lagos Warehouse">
                </div>
                <div class="text-start mb-3">
                    <label class="form-label" for="retrieval_quantity">Quantity to Retrieve <span class="text-danger">*</span></label>
                    <input type="number" id="retrieval_quantity" class="swal2-input m-0 w-100" placeholder="Quantity" min="1">
                </div>
                <div class="text-start">
                    <label class="form-label" for="recall_reason">Reason (Optional)</label>
                    <textarea id="recall_reason" class="swal2-textarea m-0 w-100" placeholder="Optional reason..."></textarea>
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Submit Request',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const location = document.getElementById('retrieval_location').value;
                const quantity = document.getElementById('retrieval_quantity').value;
                const reason = document.getElementById('recall_reason').value;
                
                if (!location) {
                    Swal.showValidationMessage('Retrieval location is required');
                    return false;
                }
                if (!quantity || quantity < 1) {
                    Swal.showValidationMessage('A valid quantity is required');
                    return false;
                }
                
                return {
                    retrieval_location: location,
                    retrieval_quantity: quantity,
                    recall_reason: reason
                };
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable all recall buttons
                const buttons = document.querySelectorAll('a[onclick^="requestBookRetrieval"]');
                buttons.forEach(button => {
                    button.disabled = true;
                    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                });
                
                fetch(`/author/books/${bookId}/retrieval`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(result.value)
                })
                .then(response => response.json())
                .then(data => {
                    // Re-enable buttons
                    buttons.forEach(button => {
                        button.disabled = false;
                        button.innerHTML = 'Retrieval of Book';
                    });
                    
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                        }).then(() => {
                            // Reload the page on success
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'An error occurred while requesting the retrieval.',
                            icon: 'error',
                            confirmButtonColor: '#d33',
                        });
                    }
                })
                .catch(error => {
                    console.error('Retrieval request error:', error);
                    
                    // Re-enable buttons
                    buttons.forEach(button => {
                        button.disabled = false;
                        button.innerHTML = 'Retrieval of Book';
                    });
                    
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while sending the retrieval request.',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                    });
                });
            }
        });
    }
</script>
@endpush