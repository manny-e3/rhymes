@extends('layouts.admin')

@section('title', 'System Settings | Admin Panel')

@section('page-title', 'System Settings')

@section('page-description', 'Configure platform settings and preferences')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">System Settings</h3>
                        <div class="nk-block-des text-soft">
                            <p>Configure platform settings, payment options, and system preferences.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- General Settings -->
                    <div class="col-lg-8">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">General Settings</h6>
                                    </div>
                                </div>
                                
                                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Site Name</label>
                                                <input type="text" class="form-control" name="site_name" value="{{ config('app.name') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Site URL</label>
                                                <input type="url" class="form-control" name="site_url" value="{{ config('app.url') }}">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Site Description</label>
                                                <textarea class="form-control" name="site_description" rows="3">{{ $settings['site_description'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Contact Email</label>
                                                <input type="email" class="form-control" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Support Email</label>
                                                <input type="email" class="form-control" name="support_email" value="{{ $settings['support_email'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Payment Settings -->
                        <div class="card card-bordered mt-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Payment Settings</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Platform Commission (%)</label>
                                            <input type="number" class="form-control" name="platform_commission" value="{{ $settings['platform_commission'] ?? 15 }}" min="0" max="100" step="0.1">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Minimum Payout Amount</label>
                                            <input type="number" class="form-control" name="min_payout_amount" value="{{ $settings['min_payout_amount'] ?? 50 }}" min="1" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Payout Processing Fee</label>
                                            <input type="number" class="form-control" name="payout_fee" value="{{ $settings['payout_fee'] ?? 2.50 }}" min="0" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Currency</label>
                                            <select class="form-select" name="currency">
                                                <option value="USD" {{ ($settings['currency'] ?? 'USD') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                                <option value="EUR" {{ ($settings['currency'] ?? 'USD') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                                <option value="GBP" {{ ($settings['currency'] ?? 'USD') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Book Settings -->
                        <div class="card card-bordered mt-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Book Management</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Auto-approve Books</label>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="auto_approve_books" id="auto_approve_books" {{ ($settings['auto_approve_books'] ?? false) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="auto_approve_books">Automatically approve new book submissions</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Max File Size (MB)</label>
                                            <input type="number" class="form-control" name="max_file_size" value="{{ $settings['max_file_size'] ?? 50 }}" min="1" max="500">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Allowed File Types</label>
                                            <input type="text" class="form-control" name="allowed_file_types" value="{{ $settings['allowed_file_types'] ?? 'pdf,epub,mobi' }}" placeholder="pdf,epub,mobi">
                                            <div class="form-note">Comma-separated list of allowed file extensions</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Quick Actions</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-2">
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-block" onclick="saveSettings()">
                                            <em class="icon ni ni-save"></em><span>Save All Settings</span>
                                        </button>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-outline-primary btn-block" onclick="clearCache()">
                                            <em class="icon ni ni-reload"></em><span>Clear Cache</span>
                                        </button>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-outline-info btn-block" onclick="testEmail()">
                                            <em class="icon ni ni-mail"></em><span>Test Email</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Information -->
                        <div class="card card-bordered mt-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">System Information</h6>
                                    </div>
                                </div>
                                
                                <ul class="nk-list-meta">
                                    <li class="nk-list-meta-item">
                                        <span class="nk-list-meta-label">PHP Version:</span>
                                        <span class="nk-list-meta-value">{{ PHP_VERSION }}</span>
                                    </li>
                                    <li class="nk-list-meta-item">
                                        <span class="nk-list-meta-label">Laravel Version:</span>
                                        <span class="nk-list-meta-value">{{ app()->version() }}</span>
                                    </li>
                                    <li class="nk-list-meta-item">
                                        <span class="nk-list-meta-label">Environment:</span>
                                        <span class="nk-list-meta-value">{{ config('app.env') }}</span>
                                    </li>
                                    <li class="nk-list-meta-item">
                                        <span class="nk-list-meta-label">Debug Mode:</span>
                                        <span class="nk-list-meta-value">{{ config('app.debug') ? 'Enabled' : 'Disabled' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function saveSettings() {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    
    Swal.fire({
        title: 'Saving Settings...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success!', 'Settings have been saved successfully.', 'success');
        } else {
            Swal.fire('Error!', data.message || 'Failed to save settings.', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error!', 'Something went wrong.', 'error');
    });
}

function clearCache() {
    Swal.fire({
        title: 'Clear Cache?',
        text: 'This will clear all application cache.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, clear cache!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/settings/clear-cache', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', 'Cache cleared successfully.', 'success');
                } else {
                    Swal.fire('Error!', 'Failed to clear cache.', 'error');
                }
            });
        }
    });
}

function testEmail() {
    Swal.fire({
        title: 'Test Email Configuration',
        input: 'email',
        inputPlaceholder: 'Enter test email address',
        showCancelButton: true,
        confirmButtonText: 'Send Test Email'
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            fetch('/admin/settings/test-email', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: result.value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', 'Test email sent successfully.', 'success');
                } else {
                    Swal.fire('Error!', 'Failed to send test email.', 'error');
                }
            });
        }
    });
}
</script>
@endpush
@endsection
