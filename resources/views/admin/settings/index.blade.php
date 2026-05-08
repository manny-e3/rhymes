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

            @if(session('success'))
                <div class="alert alert-success">
                    <em class="icon ni ni-check-circle"></em>
                    <strong>Success!</strong> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <em class="icon ni ni-cross-circle"></em>
                    <strong>Error!</strong> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <em class="icon ni ni-cross-circle"></em>
                    <strong>Error!</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- General Settings -->
                    <div class="col-lg-8">
                        <form id="settingsForm" action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">General Settings</h6>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Site Name</label>
                                                <input type="text" class="form-control" name="site_name" value="{{ config('app.name') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Site URL</label>
                                                <input type="url" class="form-control" name="site_url" value="{{ config('app.url') }}" required>
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
                                                <input type="email" class="form-control" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Support Email</label>
                                                <input type="email" class="form-control" name="support_email" value="{{ $settings['support_email'] ?? '' }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payout Settings -->
                            <div class="card card-bordered mt-4">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">Payout Settings</h6>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Minimum Payout Amount (₦)</label>
                                                <input type="number" class="form-control" name="min_payout_amount" value="{{ $settings['min_payout_amount'] ?? 50000 }}" min="1" step="0.01" required>
                                                <div class="form-note">Authors must have at least this amount to request a payout</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Author Commission Percentage (%)</label>
                                                <input type="number" class="form-control" name="author_commission_percentage" value="{{ $settings['author_commission_percentage'] ?? 75 }}" min="0" max="100" step="0.1" required>
                                                <div class="form-note">Percentage of sales that goes to authors (platform gets the remainder)</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Payout Frequency Limit (Days)</label>
                                                <input type="number" class="form-control" name="payout_frequency_days" value="{{ $settings['payout_frequency_days'] ?? 1 }}" min="1" max="365" required>
                                                <div class="form-note">Authors can only request payouts once every X days</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Processing Time (Min Days)</label>
                                                <input type="number" class="form-control" name="payout_processing_time_min" value="{{ $settings['payout_processing_time_min'] ?? 3 }}" min="1" max="30" required>
                                                <div class="form-note">Minimum number of days for payout processing</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Processing Time (Max Days)</label>
                                                <input type="number" class="form-control" name="payout_processing_time_max" value="{{ $settings['payout_processing_time_max'] ?? 5 }}" min="1" max="30" required>
                                                <div class="form-note">Maximum number of days for payout processing</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="card card-bordered mt-4">
                                <div class="card-inner">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <em class="icon ni ni-save"></em>
                                            <span>Save All Settings</span>
                                        </button>
                                        <!-- <button type="button" class="btn btn-outline-primary" onclick="clearCache()">
                                            <em class="icon ni ni-reload"></em>
                                            <span>Clear Cache</span>
                                        </button> -->
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-lg-4">
                        {{-- <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Quick Actions</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-2">
                                    <div class="col-12">
                                        <button class="btn btn-outline-info btn-block" onclick="testEmail()">
                                            <em class="icon ni ni-mail"></em><span>Test Email</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Payout Information Preview -->
                        <div class="card card-bordered mt-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Current Payout Settings</h6>
                                    </div>
                                </div>
                                
                                <ul class="nk-list-meta">
                                    <li class="nk-list-meta-item">
                                        <span class="nk-list-meta-label">Minimum Payout:</span>
                                        <span class="nk-list-meta-value">₦{{ number_format($settings['min_payout_amount'] ?? 50000, 2) }}</span>
                                    </li>
                                    <li class="nk-list-meta-item">
                                        <span class="nk-list-meta-label">Author Commission:</span>
                                        <span class="nk-list-meta-value">{{ $settings['author_commission_percentage'] ?? 75 }}% (Platform: {{ 100 - ($settings['author_commission_percentage'] ?? 75) }}%)</span>
                                    </li>
                                    <li class="nk-list-meta-item">
                                        <span class="nk-list-meta-label">Processing Time:</span>
                                        <span class="nk-list-meta-value">{{ $settings['payout_processing_time_min'] ?? 3 }}-{{ $settings['payout_processing_time_max'] ?? 5 }} days</span>
                                    </li>
                                    <li class="nk-list-meta-item">
                                        <span class="nk-list-meta-label">Frequency Limit:</span>
                                        <span class="nk-list-meta-value">Once every {{ $settings['payout_frequency_days'] ?? 1 }} day(s)</span>
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

<!-- JavaScript for non-form actions -->
<script>
function clearCache() {
    Swal.fire({
        title: 'Clear Cache?',
        text: "This will clear all cached data including settings.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e85347',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, clear it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route('admin.settings.clear-cache') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cache Cleared!',
                        text: data.message,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An unexpected error occurred. Please try again.',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

function testEmail() {
    Swal.fire({
        title: 'Test Email',
        text: 'Enter an email address to send a test message:',
        input: 'email',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Send Test',
        showLoaderOnConfirm: true,
        preConfirm: (email) => {
            return fetch('{{ route('admin.settings.test-email') }}', {
                method: 'POST',
                body: JSON.stringify({email: email}),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message);
                }
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Email Sent!',
                text: result.value.message,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        }
    });
}
</script>
@endsection