// Get the base URL from the current page (handles subdirectory installations)
const getBaseUrl = () => {
    const base = document.querySelector('base');
    if (base && base.href) {
        return base.href.replace(/\/$/, ''); // Remove trailing slash
    }
    return window.location.origin;
};

// Pusher configuration - these should match your .env values
const pusherConfig = {
    cluster: window.pusherCluster || 'mt1', // Use cluster from Laravel config or default
    forceTLS: true,
    authEndpoint: `${getBaseUrl()}/broadcasting/auth`, // Use absolute path for auth endpoint
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
};

// Initialize Pusher
const pusher = new Pusher(window.pusherKey || 'your-pusher-key', pusherConfig);

// Notifications and UI functionality
class NotificationManager {
    constructor() {
        this.unreadCount = 0;
        this.darkMode = localStorage.getItem('darkMode') === 'true';
        this.pusherChannel = null;
        this.init();
    }

    init() {
        this.loadUnreadNotifications();
        this.setupEventListeners();
        this.applyDarkMode();
        this.setupPusher();

        // Refresh notifications every 30 seconds as fallback
        setInterval(() => this.loadUnreadNotifications(), 30000);
    }

    setupPusher() {
        if (!window.userId) {
            console.warn('User ID not available for Pusher channel');
            return;
        }

        // Subscribe to the user's private notification channel
        // Using the same channel name format as defined in routes/channels.php
        // Laravel automatically prefixes with 'private-' for private channels
        this.pusherChannel = pusher.subscribe(`private-notifications.${window.userId}`);

        // Listen for the notification.created event
        this.pusherChannel.bind('notification.created', (data) => {
            console.log('New notification received via Pusher:', data);
            // Update the UI with the new notification
            this.handleNewNotification(data);
        });

        // Handle Pusher connection events
        pusher.connection.bind('connected', () => {
            console.log('Pusher connected');
        });

        pusher.connection.bind('disconnected', () => {
            console.log('Pusher disconnected');
        });

        // Handle authentication failures
        this.pusherChannel.bind('pusher:subscription_error', (status) => {
            console.error('Pusher subscription error:', status);
        });
    }

    handleNewNotification(notification) {
        // Update the unread count
        this.unreadCount++;
        this.updateNotificationBadge();

        // Update the notification dropdown with the new notification
        this.prependNotificationToDropdown(notification);

        // Show a desktop notification if permissions are granted
        this.showDesktopNotification(notification);
    }

    prependNotificationToDropdown(notification) {
        const container = document.getElementById('notificationsList');
        if (!container) return;

        // Try both approaches for getting notification data
        let data;
        if (notification.formatted_data) {
            data = notification.formatted_data;
        } else {
            data = this.formatNotificationData(notification);
        }

        // Create the notification element
        const notificationElement = document.createElement('div');
        notificationElement.className = 'nk-notification-item dropdown-inner notification-item';
        notificationElement.dataset.notificationId = notification.id;
        notificationElement.innerHTML = `
            <div class="nk-notification-icon">
                <em class="icon icon-circle bg-${data.type}-dim ${data.icon}"></em>
            </div>
            <div class="nk-notification-content">
                <div class="nk-notification-text">${data.title || 'Notification'}</div>
                <div class="nk-notification-text text-muted small">${data.message || ''}</div>
                <div class="nk-notification-time">${data.time || ''}</div>
            </div>
        `;

        // Prepend to the container (add to the top)
        if (container.firstChild) {
            container.insertBefore(notificationElement, container.firstChild);
        } else {
            container.appendChild(notificationElement);
        }
    }

    showDesktopNotification(notification) {
        if (!("Notification" in window)) {
            return; // Notifications not supported
        }

        if (Notification.permission === "granted") {
            const data = notification.formatted_data || this.formatNotificationData(notification);
            new Notification(data.title || 'New Notification', {
                body: data.message || '',
                icon: '/favicon.ico' // Use your app's favicon or notification icon
            });
        } else if (Notification.permission !== "denied") {
            Notification.requestPermission().then(permission => {
                if (permission === "granted") {
                    const data = notification.formatted_data || this.formatNotificationData(notification);
                    new Notification(data.title || 'New Notification', {
                        body: data.message || '',
                        icon: '/favicon.ico'
                    });
                }
            });
        }
    }

    setupEventListeners() {
        // Dark mode toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        if (darkModeToggle) {
            darkModeToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleDarkMode();
            });
        }

        // Mark all as read
        const markAllReadBtn = document.getElementById('markAllAsRead');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.markAllAsRead();
            });
        }

        // Individual notification clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.notification-item')) {
                const notificationId = e.target.closest('.notification-item').dataset.notificationId;
                if (notificationId) {
                    this.markAsRead(notificationId);
                }
            }
        });

        // Login Activity Link
        const loginActivityLink = document.getElementById('loginActivityLink');
        if (loginActivityLink) {
            loginActivityLink.addEventListener('click', (e) => {
                e.preventDefault();
                this.showLoginActivity();
            });
        }

        // Profile dark mode toggle
        const darkModeToggleProfile = document.getElementById('darkModeToggleProfile');
        if (darkModeToggleProfile) {
            darkModeToggleProfile.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleDarkMode();
            });
        }
    }

    async loadUnreadNotifications() {
        try {
            const response = await fetch(`${getBaseUrl()}/notifications/unread`);
            const data = await response.json();

            this.unreadCount = data.unread_count;
            this.updateNotificationBadge();
            this.updateNotificationDropdown(data.notifications);
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    updateNotificationBadge() {
        const badge = document.querySelector('.notification-badge');
        const headerCount = document.querySelector('.header-notification-count');

        if (this.unreadCount > 0) {
            const countText = this.unreadCount > 99 ? '99+' : this.unreadCount;

            if (badge) {
                badge.textContent = countText;
                badge.style.display = 'flex'; // Use flex to match the inline style
            }

            if (headerCount) {
                headerCount.textContent = countText;
                headerCount.style.display = 'inline-block';
            }
        } else {
            if (badge) badge.style.display = 'none';
            if (headerCount) headerCount.style.display = 'none';
        }
    }

    updateNotificationDropdown(notifications) {
        const container = document.getElementById('notificationsList');
        if (!container) return;

        // Debug: Log notifications to console
        console.log('Notifications received:', notifications);

        if (notifications.length === 0) {
            container.innerHTML = `
                <div class="nk-notification-item text-center py-4">
                    <div class="nk-notification-content">
                        <div class="nk-notification-text text-muted">No new notifications</div>
                    </div>
                </div>
            `;
            return;
        }

        container.innerHTML = notifications.map(notification => {
            // Try both approaches for getting notification data
            let data;
            if (notification.formatted_data) {
                data = notification.formatted_data;
            } else {
                data = this.formatNotificationData(notification);
            }

            // Debug: Log individual notification data
            console.log('Notification data:', notification, 'Formatted data:', data);

            return `
                <div class="nk-notification-item dropdown-inner notification-item" data-notification-id="${notification.id}">
                    <div class="nk-notification-icon">
                        <em class="icon icon-circle bg-${data.type}-dim ${data.icon}"></em>
                    </div>
                    <div class="nk-notification-content">
                        <div class="nk-notification-text">${data.title || 'Notification'}</div>
                        <div class="nk-notification-text text-muted small">${data.message || ''}</div>
                        <div class="nk-notification-time">${data.time || ''}</div>
                    </div>
                </div>
            `;
        }).join('');
    }

    formatNotificationData(notification) {
        const data = notification.data || {};
        return {
            title: notification.title || data.title || 'Notification',
            message: notification.message || data.message || data.text || '',
            icon: notification.icon || data.icon || 'ni ni-bell',
            type: this.getNotificationType(notification.type),
            time: this.formatTime(notification.created_at)
        };
    }

    getNotificationType(type) {
        const typeMap = {
            'App\\Notifications\\BookPublished': 'success',
            'App\\Notifications\\BookSold': 'info',
            'App\\Notifications\\PayoutProcessed': 'success',
            'App\\Notifications\\SystemAlert': 'warning',
            'App\\Notifications\\BookSubmitted': 'info',
            'App\\Notifications\\BookStatusChanged': 'info',
            'App\\Notifications\\PayoutRequested': 'warning',
            'App\\Notifications\\PayoutStatusChanged': 'info',
        };
        return typeMap[type] || 'info';
    }

    formatTime(dateString) {
        if (!dateString) return '';

        const date = new Date(dateString);
        const now = new Date();
        const diffInMinutes = Math.floor((now - date) / (1000 * 60));

        if (diffInMinutes < 1) return 'Just now';
        if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
        if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
        return `${Math.floor(diffInMinutes / 1440)}d ago`;
    }

    async markAllAsRead() {
        try {
            const response = await fetch(`${getBaseUrl()}/notifications/mark-all-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                this.unreadCount = 0;
                this.updateNotificationBadge();
                this.loadUnreadNotifications();
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch(`${getBaseUrl()}/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                this.loadUnreadNotifications();
            }
        } catch (error) {
            console.error('Error marking as read:', error);
        }
    }

    async toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);

        try {
            await fetch(`${getBaseUrl()}/toggle-dark-mode`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ dark_mode: this.darkMode })
            });
        } catch (error) {
            console.error('Error toggling dark mode:', error);
        }

        this.applyDarkMode();
    }

    applyDarkMode() {
        const body = document.body;
        const darkModeIcon = document.getElementById('darkModeIcon');

        if (this.darkMode) {
            body.classList.add('dark-mode');
            if (darkModeIcon) {
                darkModeIcon.className = 'icon ni ni-sun';
            }
        } else {
            body.classList.remove('dark-mode');
            if (darkModeIcon) {
                darkModeIcon.className = 'icon ni ni-moon';
            }
        }
    }

    showLoginActivity() {
        // Create mock login activity data
        const loginActivities = [
            {
                ip: '192.168.1.100',
                location: 'New York, USA',
                device: 'Chrome on Windows',
                time: '2 hours ago',
                status: 'success'
            },
            {
                ip: '10.0.0.1',
                location: 'London, UK',
                device: 'Safari on MacOS',
                time: '1 day ago',
                status: 'success'
            },
            {
                ip: '203.0.113.1',
                location: 'Unknown Location',
                device: 'Firefox on Linux',
                time: '3 days ago',
                status: 'warning'
            }
        ];

        const modalHtml = `
            <div class="modal fade login-activity-modal" id="loginActivityModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Login Activity</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted mb-4">Recent login activity for your account</p>
                            ${loginActivities.map(activity => `
                                <div class="login-activity-item d-flex align-items-center">
                                    <div class="activity-icon activity-${activity.status}">
                                        <em class="icon ni ni-${activity.status === 'success' ? 'check' : 'alert-circle'}"></em>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">${activity.device}</div>
                                        <div class="text-muted small">
                                            ${activity.ip} • ${activity.location}
                                        </div>
                                    </div>
                                    <div class="text-muted small">
                                        ${activity.time}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        const existingModal = document.getElementById('loginActivityModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('loginActivityModal'));
        modal.show();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    window.notificationManager = new NotificationManager();
});