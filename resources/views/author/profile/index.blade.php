@extends('layouts.author')
@section('title', 'My Profile | Rhymes Author Platform')
@section('page-title', 'My Profile')
@section('page-description', 'Manage your profile here')
@section('content')

@if(session('payment-success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('payment-success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    });
</script>
@endif

@if(session('payment-error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('payment-error') }}',
            timer: 3000,
            showConfirmButton: false
        });
    });
</script>
@endif

<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block">
                <div class="card">
                    <div class="card-aside-wrap">
                        <div class="card-inner card-inner-lg">
                            <div class="nk-block-head nk-block-head-lg">
                                <div class="nk-block-between">
                                    <div class="nk-block-head-content">
                                        <h4 class="nk-block-title">Personal Information</h4>
                                        <div class="nk-block-des">
                                            <p>Basic info, like your name and address, that you use on Rhymes Platform.</p>
                                        </div>
                                    </div>
                                    <div class="nk-block-head-content align-self-start d-lg-none">
                                        <a href="#" class="toggle btn btn-icon btn-trigger mt-n1" data-target="userAside"><em class="icon ni ni-menu-alt-r"></em></a>
                                    </div>
                                </div>
                            </div><!-- .nk-block-head -->
                            <div class="nk-block">
                                <div class="nk-data data-list">
                                    <div class="data-head">
                                        <h6 class="overline-title">Basics</h6>
                                    </div>
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#profile-edit">
                                        <div class="data-col">
                                            <span class="data-label">Full Name</span>
                                            <span class="data-value">{{ $user->name }}</span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                    </div><!-- data-item -->
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#profile-edit">
                                        <div class="data-col">
                                            <span class="data-label">Display Name</span>
                                            <span class="data-value {{ !isset($user->profile_data['display_name']) ? 'text-soft' : '' }}">{{ $user->profile_data['display_name'] ?? 'Not set' }}</span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                    </div><!-- data-item -->
                                    <div class="data-item">
                                        <div class="data-col">
                                            <span class="data-label">Email</span>
                                            <span class="data-value">{{ $user->email }}</span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more disable"><em class="icon ni ni-lock-alt"></em></span></div>
                                    </div><!-- data-item -->
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#profile-edit">
                                        <div class="data-col">
                                            <span class="data-label">Phone Number</span>
                                            <span class="data-value {{ !$user->phone ? 'text-soft' : '' }}">{{ $user->phone ?? 'Not added yet' }}</span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                    </div><!-- data-item -->
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#profile-edit">
                                        <div class="data-col">
                                            <span class="data-label">Date of Birth</span>
                                            <span class="data-value {{ !isset($user->profile_data['date_of_birth']) ? 'text-soft' : '' }}">
                                                {{ isset($user->profile_data['date_of_birth']) ? \Carbon\Carbon::parse($user->profile_data['date_of_birth'])->format('M d, Y') : 'Not added yet' }}
                                            </span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                    </div><!-- data-item -->
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#profile-edit">
                                        <div class="data-col">
                                            <span class="data-label">Address</span>
                                            <span class="data-value {{ !isset($user->profile_data['address']) ? 'text-soft' : '' }}">
                                                {{ $user->profile_data['address'] ?? 'Not added yet' }}
                                            </span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                    </div><!-- data-item -->
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#profile-edit">
                                        <div class="data-col">
                                            <span class="data-label">Bio</span>
                                            <span class="data-value {{ !isset($user->profile_data['bio']) ? 'text-soft' : '' }}">
                                                {{ $user->profile_data['bio'] ?? 'Not added yet' }}
                                            </span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                    </div><!-- data-item -->
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#profile-edit">
                                        <div class="data-col">
                                            <span class="data-label">Website</span>
                                            <span class="data-value {{ !isset($user->profile_data['website']) ? 'text-soft' : '' }}">
                                                @if(isset($user->profile_data['website']))
                                                    <a href="{{ $user->profile_data['website'] }}" target="_blank" class="link link-primary">{{ $user->profile_data['website'] }}</a>
                                                @else
                                                    Not added yet
                                                @endif
                                            </span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                    </div><!-- data-item -->
                                </div><!-- data-list -->
                                
                                <div class="nk-data data-list">
                                    <div class="data-head">
                                        <h6 class="overline-title">Author Statistics</h6>
                                    </div>
                                    <div class="data-item">
                                        <div class="data-col">
                                            <span class="data-label">Total Books</span>
                                            <span class="data-value">{{ $totalBooks }}</span>
                                        </div>
                                        <div class="data-col data-col-end"><a href="{{ route('author.books.index') }}" class="link link-primary">View Books</a></div>
                                    </div><!-- data-item -->
                                    <div class="data-item">
                                        <div class="data-col">
                                            <span class="data-label">Published Books</span>
                                            <span class="data-value">{{ $publishedBooks }}</span>
                                        </div>
                                        <div class="data-col data-col-end"><a href="{{ route('author.books.index') }}?status=published" class="link link-primary">View Published</a></div>
                                    </div><!-- data-item -->
                                    <div class="data-item">
                                        <div class="data-col">
                                            <span class="data-label">Wallet Balance</span>
                                            <span class="data-value">${{ number_format($walletBalance, 2) }}</span>
                                        </div>
                                        <div class="data-col data-col-end"><a href="{{ route('author.wallet.index') }}" class="link link-primary">View Wallet</a></div>
                                    </div><!-- data-item -->
                                    <div class="data-item">
                                        <div class="data-col">
                                            <span class="data-label">Member Since</span>
                                            <span class="data-value">{{ $user->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <div class="data-col data-col-end">
                                            @if($user->promoted_to_author_at)
                                                <span class="badge badge-success">Author since {{ $user->promoted_to_author_at->format('M Y') }}</span>
                                            @endif
                                        </div>
                                    </div><!-- data-item -->
                                </div><!-- data-list -->

                                @if(isset($user->profile_data['social_links']) && !empty(array_filter($user->profile_data['social_links'])))
                                <div class="nk-data data-list">
                                    <div class="data-head">
                                        <h6 class="overline-title">Social Links</h6>
                                    </div>
                                    @foreach($user->profile_data['social_links'] as $platform => $url)
                                        @if($url)
                                        <div class="data-item" data-bs-toggle="modal" data-bs-target="#profile-edit">
                                            <div class="data-col">
                                                <span class="data-label">{{ ucfirst($platform) }}</span>
                                                <span class="data-value">
                                                    <a href="{{ $url }}" target="_blank" class="link link-primary">{{ $url }}</a>
                                                </span>
                                            </div>
                                            <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                        </div><!-- data-item -->
                                        @endif
                                    @endforeach
                                </div><!-- data-list -->
                                @endif
                                
                                <div class="nk-data data-list">
                                    <div class="data-head">
                                        <h6 class="overline-title">Payment Details</h6>
                                    </div>
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#payment-details">
                                        <div class="data-col">
                                            <span class="data-label">Payment Method</span>
                                            <span class="data-value {{ !isset($user->payment_details['payment_method']) ? 'text-soft' : '' }}">
                                                @if(isset($user->payment_details['payment_method']))
                                                    {{ ucwords(str_replace('_', ' ', $user->payment_details['payment_method'])) }}
                                                @else
                                                    Not configured
                                                @endif
                                            </span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                    </div><!-- data-item -->
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#payment-details">
                                        <div class="data-col">
                                            <span class="data-label">Account Holder</span>
                                            <span class="data-value {{ !isset($user->payment_details['account_holder_name']) ? 'text-soft' : '' }}">
                                                {{ $user->payment_details['account_holder_name'] ?? 'Not set' }}
                                            </span>
                                        </div>
                                        <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                    </div><!-- data-item -->
                                    @if(isset($user->payment_details['payment_method']))
                                        @if($user->payment_details['payment_method'] === 'bank_transfer')
                                            <div class="data-item" data-bs-toggle="modal" data-bs-target="#payment-details">
                                                <div class="data-col">
                                                    <span class="data-label">Bank Name</span>
                                                    <span class="data-value">{{ $user->payment_details['bank_name'] ?? 'Not set' }}</span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item" data-bs-toggle="modal" data-bs-target="#payment-details">
                                                <div class="data-col">
                                                    <span class="data-label">Account Number</span>
                                                    <span class="data-value">{{ $user->payment_details['account_number'] ?? 'Not set' }}</span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                        @elseif($user->payment_details['payment_method'] === 'paypal')
                                            <div class="data-item" data-bs-toggle="modal" data-bs-target="#payment-details">
                                                <div class="data-col">
                                                    <span class="data-label">PayPal Email</span>
                                                    <span class="data-value">{{ $user->payment_details['paypal_email'] ?? 'Not set' }}</span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                        @elseif($user->payment_details['payment_method'] === 'stripe')
                                            <div class="data-item" data-bs-toggle="modal" data-bs-target="#payment-details">
                                                <div class="data-col">
                                                    <span class="data-label">Stripe Account</span>
                                                    <span class="data-value">{{ $user->payment_details['stripe_account_id'] ?? 'Not set' }}</span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                        @endif
                                    @endif
                                    <div class="data-item" data-bs-toggle="modal" data-bs-target="#payment-details">
                                        <div class="data-col">
                                            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#payment-details">Update Payment Details</button>
                                        </div>
                                    </div><!-- data-item -->
                                </div><!-- data-list -->
                                
                              
                            </div><!-- .nk-block -->
                        </div>
                        
                        <!-- Sidebar -->
                        <div class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg" data-toggle-body="true" data-content="userAside" data-toggle-screen="lg" data-toggle-overlay="true">
                            <div class="card-inner-group" data-simplebar>
                                <div class="card-inner">
                                    <div class="user-card">
                                        <div class="user-avatar {{ $user->avatar ? '' : 'bg-primary' }}">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/images/avatar/' . $user->avatar) }}" alt="{{ $user->name }}">
                                            @else
                                                <span>{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                            @endif
                                        </div>
                                        <div class="user-info">
                                            <span class="lead-text">{{ $user->name }}</span>
                                            <span class="sub-text">{{ $user->email }}</span>
                                        </div>
                                        <div class="user-action">
                                            <div class="dropdown">
                                                <a class="btn btn-icon btn-trigger me-n2" data-bs-toggle="dropdown" href="#"><em class="icon ni ni-more-v"></em></a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#avatar-upload"><em class="icon ni ni-camera-fill"></em><span>Change Photo</span></a></li>
                                                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#profile-edit"><em class="icon ni ni-edit-fill"></em><span>Update Profile</span></a></li>
                                                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#password-change"><em class="icon ni ni-lock-alt-fill"></em><span>Change Password</span></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- .user-card -->
                                </div><!-- .card-inner -->
                                <div class="card-inner">
                                    <div class="user-account-info py-0">
                                        <h6 class="overline-title-alt">Author Wallet</h6>
                                        <div class="user-balance">${{ number_format($walletBalance, 2) }} <small class="currency">USD</small></div>
                                        <div class="user-balance-sub">Total Books <span>{{ $totalBooks }}</span></div>
                                    </div>
                                </div><!-- .card-inner -->
                                <div class="card-inner p-0">
                                    <ul class="link-list-menu">
                                        <li><a class="active" href="{{ route('author.profile.edit') }}"><em class="icon ni ni-user-fill-c"></em><span>Personal Information</span></a></li>
                                        {{-- <li><a href="{{ route('author.books.index') }}"><em class="icon ni ni-book-fill"></em><span>My Books</span></a></li>
                                        <li><a href="{{ route('author.wallet.index') }}"><em class="icon ni ni-wallet-fill"></em><span>Wallet & Earnings</span></a></li>
                                        <li><a href="{{ route('author.payouts.index') }}"><em class="icon ni ni-tranx"></em><span>Payouts</span></a></li>
                                        <li><a href="{{ route('dashboard') }}"><em class="icon ni ni-dashboard-fill"></em><span>Dashboard</span></a></li>
                                    </ul> --}}
                                </div><!-- .card-inner -->
                            </div><!-- .card-inner-group -->
                        </div><!-- card-aside -->
                    </div><!-- .card-aside-wrap -->
                </div><!-- .card -->
            </div><!-- .nk-block -->
        </div>
    </div>
</div>

@include('author.profile.modals.edit-profile')
@include('author.profile.modals.change-password')
@include('author.profile.modals.upload-avatar')
@include('author.profile.modals.payment-details')
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Handle all profile forms (personal, address, social)
    $('#profile-form, #address-form, #social-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("author.profile.update") }}',
            method: 'PATCH',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = 'Please fix the following errors:\n';
                    Object.keys(errors).forEach(key => {
                        errorMessage += `• ${errors[key][0]}\n`;
                    });
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: errorMessage
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating profile.'
                    });
                }
            }
        });
    });

    // Password change form
    $('#password-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("author.profile.password.update") }}',
            method: 'PUT',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                    $('#password-change').modal('hide');
                    $('#password-form')[0].reset();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'An error occurred while updating password.'
                });
            }
        });
    });

    // Avatar upload form
    $('#avatar-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("author.profile.avatar.update") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                let errorMessage = 'An error occurred while uploading avatar.';
                
                if (response?.errors?.avatar) {
                    errorMessage = response.errors.avatar[0];
                } else if (response?.message) {
                    errorMessage = response.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });

    // Password visibility toggle
    $('.passcode-switch').on('click', function(e) {
        e.preventDefault();
        const target = $(this).attr('data-target');
        const input = $('#' + target);
        const type = input.attr('type');
        
        if (type === 'password') {
            input.attr('type', 'text');
            $(this).find('.icon-show').hide();
            $(this).find('.icon-hide').show();
        } else {
            input.attr('type', 'password');
            $(this).find('.icon-show').show();
            $(this).find('.icon-hide').hide();
        }
    });
    
    // Handle payment details form submission for non-JavaScript users
    // This is just for consistency - the form will submit normally without JavaScript
});
</script>
@endpush