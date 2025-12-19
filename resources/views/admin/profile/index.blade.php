@extends('layouts.admin')

@section('title', 'Admin Profile | Admin Panel')

@section('page-title', 'Admin Profile')

@section('page-description', 'Manage your admin account settings')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Admin Profile</h3>
                        <div class="nk-block-des text-soft">
                            <p>Manage your admin account information and security settings.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- Profile Information -->
                    <div class="col-lg-8">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Profile Information</h6>
                                    </div>
                                </div>
                                
                                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Full Name</label>
                                                <input type="text" class="form-control @error('name') error @enderror" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                                @error('name')
                                                    <span class="form-note-error">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Email Address</label>
                                                <input type="email" class="form-control @error('email') error @enderror" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                                                @error('email')
                                                    <span class="form-note-error">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Phone Number</label>
                                                <input type="text" class="form-control @error('phone') error @enderror" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                                                @error('phone')
                                                    <span class="form-note-error">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Timezone</label>
                                                <select class="form-select @error('timezone') error @enderror" name="timezone">
                                                    <option value="UTC" {{ (auth()->user()->timezone ?? 'UTC') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                                    <option value="America/New_York" {{ (auth()->user()->timezone ?? 'UTC') === 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                                    <option value="America/Chicago" {{ (auth()->user()->timezone ?? 'UTC') === 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                                    <option value="America/Denver" {{ (auth()->user()->timezone ?? 'UTC') === 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                                    <option value="America/Los_Angeles" {{ (auth()->user()->timezone ?? 'UTC') === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                                    <option value="Europe/London" {{ (auth()->user()->timezone ?? 'UTC') === 'Europe/London' ? 'selected' : '' }}>London</option>
                                                    <option value="Europe/Paris" {{ (auth()->user()->timezone ?? 'UTC') === 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                                                </select>
                                                @error('timezone')
                                                    <span class="form-note-error">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Bio</label>
                                                <textarea class="form-control @error('bio') error @enderror" name="bio" rows="4">{{ old('bio', auth()->user()->bio) }}</textarea>
                                                @error('bio')
                                                    <span class="form-note-error">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">Update Profile</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Security Settings -->
                        <div class="card card-bordered mt-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Security Settings</h6>
                                    </div>
                                </div>
                                
                                <form action="{{ route('admin.profile.password') }}" method="POST" id="passwordForm">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Current Password</label>
                                                <div class="form-control-wrap">
                                                    <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="current_password">
                                                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                    </a>
                                                    <input type="password" class="form-control @error('current_password') error @enderror" name="current_password" id="current_password">
                                                    @error('current_password')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6"></div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">New Password</label>
                                                <div class="form-control-wrap">
                                                    <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                    </a>
                                                    <input type="password" class="form-control @error('password') error @enderror" name="password" id="password">
                                                    @error('password')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Confirm New Password</label>
                                                <div class="form-control-wrap">
                                                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-warning">Change Password</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Notification Preferences -->
                        {{-- <div class="card card-bordered mt-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Notification Preferences</h6>
                                    </div>
                                </div>
                                
                                <form action="{{ route('admin.profile.notifications') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="email_notifications" id="email_notifications" {{ (auth()->user()->email_notifications ?? true) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="email_notifications">Email Notifications</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="system_alerts" id="system_alerts" {{ (auth()->user()->system_alerts ?? true) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="system_alerts">System Alerts</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="security_alerts" id="security_alerts" {{ (auth()->user()->security_alerts ?? true) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="security_alerts">Security Alerts</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="marketing_emails" id="marketing_emails" {{ (auth()->user()->marketing_emails ?? false) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="marketing_emails">Marketing Emails</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-outline-primary">Save Preferences</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div> --}}
                    </div>

                    <!-- Profile Sidebar -->
                    <div class="col-lg-4">
                        <!-- Profile Card -->
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="user-card user-card-s2">
                                    <div class="user-avatar lg bg-primary">
                                        <span>{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="user-info">
                                        <h5>{{ auth()->user()->name }}</h5>
                                        <span class="sub-text">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                                
                                <div class="user-meta">
                                    <ul class="nk-list-meta">
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Role:</span>
                                            <span class="nk-list-meta-value">
                                                @foreach(auth()->user()->roles as $role)
                                                    <span class="badge badge-dim bg-outline-primary">{{ ucfirst($role->name) }}</span>
                                                @endforeach
                                            </span>
                                        </li>
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Member Since:</span>
                                            <span class="nk-list-meta-value">{{ auth()->user()->created_at->format('M d, Y') }}</span>
                                        </li>
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Last Login:</span>
                                            <span class="nk-list-meta-value">
                                                @if(auth()->user()->last_login_at)
                                                    {{ auth()->user()->last_login_at->diffForHumans() }}
                                                @else
                                                    Never
                                                @endif
                                            </span>
                                        </li>
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Status:</span>
                                            <span class="nk-list-meta-value">
                                                @if(auth()->user()->email_verified_at)
                                                    <span class="badge badge-success">Verified</span>
                                                @else
                                                    <span class="badge badge-warning">Unverified</span>
                                                @endif
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Summary -->
                        <div class="card card-bordered mt-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Activity Summary</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-4">
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-primary-dim">
                                                        <em class="icon ni ni-users"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Users Managed</p>
                                                    <h4 class="inbox-item-title">{{ $stats['users_managed'] ?? 0 }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-success-dim">
                                                        <em class="icon ni ni-book"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Books Reviewed</p>
                                                    <h4 class="inbox-item-title">{{ $stats['books_reviewed'] ?? 0 }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-warning-dim">
                                                        <em class="icon ni ni-tranx"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Payouts Processed</p>
                                                    <h4 class="inbox-item-title">{{ $stats['payouts_processed'] ?? 0 }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-info-dim">
                                                        <em class="icon ni ni-clock"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Hours Online</p>
                                                    <h4 class="inbox-item-title">{{ $stats['hours_online'] ?? 0 }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        {{-- <div class="card card-bordered mt-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Quick Actions</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-2">
                                    <div class="col-12">
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-block">
                                            <em class="icon ni ni-dashboard"></em><span>Dashboard</span>
                                        </a>
                                    </div>
                                    <div class="col-12">
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-block">
                                            <em class="icon ni ni-users"></em><span>Manage Users</span>
                                        </a>
                                    </div>
                                    <div class="col-12">
                                        <a href="{{ route('admin.settings') }}" class="btn btn-outline-primary btn-block">
                                            <em class="icon ni ni-setting"></em><span>System Settings</span>
                                        </a>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-outline-danger btn-block" onclick="downloadData()">
                                            <em class="icon ni ni-download"></em><span>Export My Data</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Password form validation
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        Swal.fire('Error!', 'Passwords do not match.', 'error');
        return false;
    }
    
    if (password.length < 8) {
        e.preventDefault();
        Swal.fire('Error!', 'Password must be at least 8 characters long.', 'error');
        return false;
    }
});

function downloadData() {
    Swal.fire({
        title: 'Export Personal Data?',
        text: 'This will generate a file containing all your admin activity data.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, export!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Generating Export...',
                text: 'Please wait while we prepare your data.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/admin/profile/export-data', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            })
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = `admin-data-${new Date().toISOString().split('T')[0]}.json`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                Swal.close();
            })
            .catch(error => {
                Swal.fire('Error!', 'Failed to export data.', 'error');
            });
        }
    });
}
</script>
@endpush
@endsection
