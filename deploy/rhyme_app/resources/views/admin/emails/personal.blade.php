@extends('layouts.admin')

@section('title', 'Send Personal Email')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Send Personal Email</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.emails.index') }}">Email Management</a></li>
                        <li class="breadcrumb-item active">Send Personal Email</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Compose Personal Email</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.emails.personal.send') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Recipient</label>
                            <select name="user_id" id="user_id" class="form-control select2" required>
                                <option value="">Select a recipient</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @if($selectedUser && $selectedUser->id == $user->id) selected @endif>
                                        {{ $user->name }} ({{ $user->email }}) @if($user->hasRole('author')) - Author @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required value="{{ old('subject') }}">
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="10" required>{{ old('message') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="template" class="form-label">Email Template (Optional)</label>
                            <select name="template" id="template" class="form-control">
                                <option value="">Default Template</option>
                                <option value="emails.otp-code">OTP Code Template</option>
                                <option value="emails.book-status-changed">Book Status Changed</option>
                                <option value="emails.payout-status-changed">Payout Status Changed</option>
                                <option value="emails.verify-email">Verify Email</option>
                            </select>
                            <div class="form-text">Select an existing email template or use the default</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.emails.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Email Management
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-envelope me-2"></i>Send Personal Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Recent Users</h4>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($users->take(5) as $user)
                            <a href="{{ route('admin.emails.personal.form', ['userId' => $user->id]) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $user->name }}</h5>
                                    <small>{{ $user->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $user->email }}</p>
                                <small>
                                    @if($user->hasRole('author')) 
                                        <span class="badge bg-success">Author</span> 
                                    @elseif($user->hasRole('admin'))
                                        <span class="badge bg-primary">Admin</span>
                                    @else
                                        <span class="badge bg-secondary">User</span>
                                    @endif
                                </small>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize select2 if available
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('#user_id').select2({
            placeholder: 'Select a recipient...',
            allowClear: true
        });
    }
});
</script>
@endsection