@extends('layouts.admin')

@section('title', 'Book Review Logs | Rhymes Platform')

@section('page-title', 'Book Review Process Logs')

@section('page-description', 'Monitor book review activities and system events')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Book Review Process Logs</h3>
                        <div class="nk-block-des text-soft">
                            <p>Monitor all book review activities and system events</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <a href="{{ route('admin.books.index') }}" class="btn btn-primary">
                            <em class="icon ni ni-arrow-left"></em>
                            <span>Back to Books</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="card card-bordered card-preview">
                    <div class="card-inner">
                        <div class="preview-block">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">How to Monitor Book Review Logs</h6>
                                <p>All book review activities are logged with detailed information including:</p>
                                <ul>
                                    <li>Book review process start and completion</li>
                                    <li>Validation successes and failures</li>
                                    <li>Database updates</li>
                                    <li>ERPREV integration attempts</li>
                                    <li>User promotions to author status</li>
                                    <li>Notification sending</li>
                                    <li>Error conditions and exceptions</li>
                                </ul>
                                
                                <h6 class="alert-heading mt-3">Log File Location</h6>
                                <p>Logs are stored in: <code>{{ storage_path('logs/laravel.log') }}</code></p>
                                
                                <h6 class="alert-heading mt-3">Quick Commands</h6>
                                <p>To view recent book-related logs, you can use these commands in your terminal:</p>
                                <pre><code>cd {{ base_path() }}
tail -f storage/logs/laravel.log | grep "BookReviewService"
# Or to see all book-related logs:
tail -f storage/logs/laravel.log | grep -i "book"</code></pre>
                                
                                <h6 class="alert-heading mt-3">Log Levels</h6>
                                <p>Different types of events are logged at different levels:</p>
                                <ul>
                                    <li><strong>INFO</strong>: Normal operational events (successful reviews, notifications sent)</li>
                                    <li><strong>DEBUG</strong>: Detailed information for diagnosing problems</li>
                                    <li><strong>WARNING</strong>: Events that might indicate problems (validation failures, service returns false)</li>
                                    <li><strong>ERROR</strong>: Error events that might still allow the application to continue</li>
                                </ul>
                            </div>
                            
                            <div class="alert alert-light">
                                <h6 class="alert-heading">Example Log Entries</h6>
                                <p>Successful book review:</p>
                                <pre>[2025-11-27 14:30:45] local.INFO: Book review successful {
    "book_id": 123,
    "book_title": "Sample Book Title",
    "old_status": "pending",
    "new_status": "accepted",
    "admin_id": 1,
    "admin_name": "Admin User",
    "timestamp": "2025-11-27T14:30:45.123456Z"
}</pre>
                                
                                <p>Failed validation:</p>
                                <pre>[2025-11-27 14:30:45] local.WARNING: Book review validation failed {
    "book_id": 123,
    "book_title": "Sample Book Title",
    "errors": {
        "status": ["The selected status is invalid."]
    },
    "admin_id": 1
}</pre>
                                
                                <p>Exception during review:</p>
                                <pre>[2025-11-27 14:30:45] local.ERROR: Book review process failed with exception {
    "book_id": 123,
    "exception_message": "Database connection failed",
    "admin_id": 1
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection