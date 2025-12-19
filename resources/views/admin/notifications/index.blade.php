@extends('layouts.admin')

@section('title', 'Notifications | Admin Panel')

@section('page-title', 'Notification Management')

@section('page-description', 'Manage system notifications and announcements')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Notifications</h3>
                        <div class="nk-block-des text-soft">
                            <p>Send announcements and manage system notifications.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createNotificationModal"><em class="icon ni ni-plus"></em><span>Create Notification</span></a></li>
                                    <li><a href="#" class="btn btn-white btn-dim btn-outline-light" onclick="markAllAsRead()"><em class="icon ni ni-check-circle"></em><span>Mark All Read</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Stats -->
            <div class="nk-block">
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Notifications</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-bell text-primary"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ $stats['total'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Unread</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-bell-off text-warning"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ $stats['unread'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Sent Today</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-send text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ $stats['today'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Active Users</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-users text-info"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ $stats['active_users'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- Notifications List -->
                    <div class="col-lg-8">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Recent Notifications</h6>
                                    </div>
                                    <div class="card-tools">
                                        <div class="form-wrap w-150px">
                                            <select class="form-select form-select-sm" onchange="filterNotifications(this.value)">
                                                <option value="">All Types</option>
                                                <option value="announcement">Announcements</option>
                                                <option value="system">System</option>
                                                <option value="promotion">Promotions</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                @if(isset($notifications) && count($notifications) > 0)
                                    <div class="nk-notification">
                                        @foreach($notifications as $notification)
                                            <div class="nk-notification-item {{ $notification->read_at ? '' : 'is-unread' }}">
                                                <div class="nk-notification-icon">
                                                    <div class="icon-circle 
                                                        @if($notification->type === 'announcement') bg-primary-dim
                                                        @elseif($notification->type === 'system') bg-info-dim
                                                        @elseif($notification->type === 'promotion') bg-success-dim
                                                        @elseif($notification->type === 'maintenance') bg-warning-dim
                                                        @else bg-secondary-dim @endif">
                                                        <em class="icon ni ni-
                                                            @if($notification->type === 'announcement') bell
                                                            @elseif($notification->type === 'system') setting
                                                            @elseif($notification->type === 'promotion') gift
                                                            @elseif($notification->type === 'maintenance') tool
                                                            @else info @endif"></em>
                                                    </div>
                                                </div>
                                                <div class="nk-notification-content">
                                                    <div class="nk-notification-text">
                                                        <h6>{{ $notification->title }}</h6>
                                                        <p>{{ Str::limit($notification->message, 100) }}</p>
                                                    </div>
                                                    <div class="nk-notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                                </div>
                                                <div class="nk-notification-actions">
                                                    <div class="dropdown">
                                                        <a href="#" class="btn btn-sm btn-icon btn-trigger dropdown-toggle" data-bs-toggle="dropdown">
                                                            <em class="icon ni ni-more-h"></em>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li><a href="#" onclick="viewNotification({{ $notification->id }})"><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                @if(!$notification->read_at)
                                                                    <li><a href="#" onclick="markAsRead({{ $notification->id }})"><em class="icon ni ni-check"></em><span>Mark as Read</span></a></li>
                                                                @endif
                                                                <li><a href="#" onclick="editNotification({{ $notification->id }})"><em class="icon ni ni-edit"></em><span>Edit</span></a></li>
                                                                <li class="divider"></li>
                                                                <li><a href="#" class="text-danger" onclick="deleteNotification({{ $notification->id }})"><em class="icon ni ni-trash"></em><span>Delete</span></a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <em class="icon ni ni-bell" style="font-size: 3rem; opacity: 0.3;"></em>
                                        <p class="text-soft mt-2">No notifications found</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-lg-4">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Quick Actions</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-2">
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-block" data-bs-toggle="modal" data-bs-target="#createNotificationModal">
                                            <em class="icon ni ni-plus"></em><span>New Notification</span>
                                        </button>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-outline-primary btn-block" onclick="sendBulkNotification()">
                                            <em class="icon ni ni-send"></em><span>Bulk Notification</span>
                                        </button>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-outline-info btn-block" onclick="scheduleNotification()">
                                            <em class="icon ni ni-clock"></em><span>Schedule Notification</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Templates -->
                        <div class="card card-bordered card-full mt-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Quick Templates</h6>
                                    </div>
                                </div>
                                
                                <div class="nk-tb-list nk-tb-orders">
                                    <div class="nk-tb-item" onclick="useTemplate('maintenance')">
                                        <div class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-avatar bg-warning-dim">
                                                    <em class="icon ni ni-tool"></em>
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead">Maintenance Notice</span>
                                                    <span class="tb-sub">System maintenance alert</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="nk-tb-item" onclick="useTemplate('welcome')">
                                        <div class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-avatar bg-success-dim">
                                                    <em class="icon ni ni-user-add"></em>
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead">Welcome Message</span>
                                                    <span class="tb-sub">New user welcome</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="nk-tb-item" onclick="useTemplate('promotion')">
                                        <div class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary-dim">
                                                    <em class="icon ni ni-gift"></em>
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead">Promotion Alert</span>
                                                    <span class="tb-sub">Special offers</span>
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
    </div>
</div>

<!-- Create Notification Modal -->
<div class="modal fade" tabindex="-1" id="createNotificationModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Notification</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form id="notificationForm">
                    @csrf
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Notification Type</label>
                                <select class="form-select" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="announcement">Announcement</option>
                                    <option value="system">System</option>
                                    <option value="promotion">Promotion</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Target Audience</label>
                                <select class="form-select" name="audience" required>
                                    <option value="all">All Users</option>
                                    <option value="authors">Authors Only</option>
                                    <option value="readers">Readers Only</option>
                                    <option value="admins">Admins Only</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Message</label>
                                <textarea class="form-control" name="message" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="priority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Send Method</label>
                                <select class="form-select" name="send_method">
                                    <option value="in_app">In-App Only</option>
                                    <option value="email">Email Only</option>
                                    <option value="both" selected>Both</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendNotification()">Send Notification</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function sendNotification() {
    const form = document.getElementById('notificationForm');
    const formData = new FormData(form);
    
    fetch('/admin/notifications', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success!', 'Notification sent successfully.', 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error!', data.message || 'Failed to send notification.', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error!', 'Something went wrong.', 'error');
    });
}

function useTemplate(type) {
    const templates = {
        maintenance: {
            type: 'maintenance',
            title: 'Scheduled Maintenance Notice',
            message: 'We will be performing scheduled maintenance on our platform. During this time, some features may be temporarily unavailable.'
        },
        welcome: {
            type: 'announcement',
            title: 'Welcome to Rhymes Platform!',
            message: 'Thank you for joining our community of readers and authors. Explore amazing books and start your literary journey today!'
        },
        promotion: {
            type: 'promotion',
            title: 'Special Offer - Limited Time!',
            message: 'Don\'t miss out on our exclusive promotion. Get amazing discounts on featured books this week only!'
        }
    };
    
    const template = templates[type];
    if (template) {
        document.querySelector('[name="type"]').value = template.type;
        document.querySelector('[name="title"]').value = template.title;
        document.querySelector('[name="message"]').value = template.message;
        
        const modal = new bootstrap.Modal(document.getElementById('createNotificationModal'));
        modal.show();
    }
}

function markAllAsRead() {
    Swal.fire({
        title: 'Mark All as Read?',
        text: 'This will mark all notifications as read.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, mark all!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', 'All notifications marked as read.', 'success').then(() => {
                        location.reload();
                    });
                }
            });
        }
    });
}

function filterNotifications(type) {
    const url = new URL(window.location);
    if (type) {
        url.searchParams.set('type', type);
    } else {
        url.searchParams.delete('type');
    }
    window.location.href = url.toString();
}
</script>
@endpush
@endsection
