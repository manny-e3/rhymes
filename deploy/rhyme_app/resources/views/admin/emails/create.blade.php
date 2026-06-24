@extends('layouts.admin')

@section('title', 'Compose Email')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    @if($type == 'newsletter') Send Newsletter
                    @elseif($type == 'announcement') Send Announcement
                    @elseif($type == 'sales_report') Send Sales Report
                    @else Send Bulk Email
                    @endif
                </h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.emails.index') }}">Email Management</a></li>
                        <li class="breadcrumb-item active">Compose</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        @if($type == 'newsletter') Compose Newsletter
                        @elseif($type == 'announcement') Compose Announcement
                        @elseif($type == 'sales_report') Sales Report Configuration
                        @else Compose Bulk Email
                        @endif
                    </h4>
                </div>
                <div class="card-body">
                    
                    {{-- Newsletter Form --}}
                    @if($type == 'newsletter')
                    <form action="{{ route('admin.emails.newsletter.send') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Newsletter Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                            <div class="form-text">You can use HTML tags for formatting.</div>
                        </div>

                        <div class="mb-3">
                            <label for="author_ids" class="form-label">Recipients (Optional)</label>
                            <select name="author_ids[]" id="author_ids" class="form-control select2" multiple>
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}">{{ $author->name }} ({{ $author->email }})</option>
                                @endforeach
                            </select>
                            <div class="form-text">Leave empty to send to ALL authors.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.emails.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Send Newsletter
                            </button>
                        </div>
                    </form>

                    {{-- Announcement Form --}}
                    @elseif($type == 'announcement')
                    <form action="{{ route('admin.emails.announcement.send') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Announcement Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Announcement Content</label>
                            <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                            <div class="form-text">This will be highlighted as an important announcement.</div>
                        </div>

                        <div class="mb-3">
                            <label for="author_ids" class="form-label">Recipients (Optional)</label>
                            <select name="author_ids[]" id="author_ids" class="form-control select2" multiple>
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}">{{ $author->name }} ({{ $author->email }})</option>
                                @endforeach
                            </select>
                            <div class="form-text">Leave empty to send to ALL authors.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.emails.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-bullhorn me-2"></i>Send Announcement
                            </button>
                        </div>
                    </form>

                    {{-- Sales Report Form --}}
                    @elseif($type == 'sales_report')
                    <ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#single_author" role="tab">Single Author</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#bulk_authors" role="tab">Bulk Authors</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- Single Author Report --}}
                        <div class="tab-pane active" id="single_author" role="tabpanel">
                            <form action="{{ route('admin.emails.sales-report.send') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="author_id" class="form-label">Select Author</label>
                                    <select name="author_id" id="author_id" class="form-control select2" required>
                                        <option value="">Select an author...</option>
                                        @foreach($authors as $author)
                                            <option value="{{ $author->id }}">{{ $author->name }} ({{ $author->email }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="period" class="form-label">Report Period</label>
                                    <select name="period" id="period" class="form-control">
                                        <option value="This Month">This Month</option>
                                        <option value="Last Month">Last Month</option>
                                        <option value="This Quarter">This Quarter</option>
                                        <option value="This Year">This Year</option>
                                        <option value="All Time">All Time</option>
                                    </select>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>The email will automatically include the author's total sales, revenue, and book-by-book performance breakdown.
                                </div>

                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-paper-plane me-2"></i>Send Report
                                </button>
                            </form>
                        </div>

                        {{-- Bulk Authors Report --}}
                        <div class="tab-pane" id="bulk_authors" role="tabpanel">
                            <form action="{{ route('admin.emails.bulk-sales-reports.send') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="period_bulk" class="form-label">Report Period</label>
                                    <select name="period" id="period_bulk" class="form-control">
                                        <option value="This Month">This Month</option>
                                        <option value="Last Month">Last Month</option>
                                        <option value="This Quarter">This Quarter</option>
                                        <option value="This Year">This Year</option>
                                        <option value="All Time">All Time</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="bulk_author_ids" class="form-label">Filter Authors (Optional)</label>
                                    <select name="author_ids[]" id="bulk_author_ids" class="form-control select2" multiple>
                                        @foreach($authors as $author)
                                            <option value="{{ $author->id }}">{{ $author->name }} ({{ $author->email }})</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Leave empty to send to ALL authors.</div>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Warning: This will send individual emails to multiple authors. Each author will receive ONLY their own private sales data.
                                </div>

                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-mail-bulk me-2"></i>Send Bulk Reports
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Traditional Bulk Email Form (Default) --}}
                    @else
                    <form action="{{ route('admin.emails.bulk.send') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="recipients" class="form-label">Recipients</label>
                            <select name="recipients[]" id="recipients" class="form-control select2" multiple required>
                                @foreach($allUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }}) @if($user->hasRole('author')) - Author @endif</option>
                                @endforeach
                            </select>
                            <div class="form-text">Select one or more recipients for this email</div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="10" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="template" class="form-label">Email Template (Optional)</label>
                            <select name="template" id="template" class="form-control">
                                <option value="">Default Template</option>
                                @if(isset($templates))
                                    @foreach($templates as $tpl)
                                        <option value="{{ $tpl->id }}">{{ $tpl->name }}</option>
                                    @endforeach
                                @endif
                                <option value="emails.otp-code">OTP Code Template</option>
                                <option value="emails.book-status-changed">Book Status Changed</option>
                                <option value="emails.payout-status-changed">Payout Status Changed</option>
                                <option value="emails.verify-email">Verify Email</option>
                            </select>
                            <div class="form-text">Select an existing email template or use the default</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.emails.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Send Bulk Email
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Variables Cheat Sheet</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted mb-2">You can use these variables in your content:</p>
                        
                        @if($type == 'newsletter')
                            <code class="d-block mb-1">@{{newsletter_title}}</code>
                            <small class="text-muted d-block mb-2">The title of the newsletter</small>
                            <code class="d-block mb-1">@{{newsletter_content}}</code>
                            <small class="text-muted d-block mb-2">The main content body</small>
                        
                        @elseif($type == 'announcement')
                            <code class="d-block mb-1">@{{announcement_title}}</code>
                            <small class="text-muted d-block mb-2">The title of the announcement</small>
                            <code class="d-block mb-1">@{{announcement_content}}</code>
                            <small class="text-muted d-block mb-2">The announcement text</small>
                        
                        @else
                            <code class="d-block mb-1">@{{name}}</code>
                            <small class="text-muted d-block mb-2">Recipient's full name</small>
                            <code class="d-block mb-1">@{{email}}</code>
                            <small class="text-muted d-block mb-2">Recipient's email address</small>
                        @endif
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
        $('.select2').select2({
            placeholder: 'Select recipients...',
            allowClear: true,
            width: '100%'
        });
    }
});
</script>
@endsection
