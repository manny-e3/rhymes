# In-App Notification System - User Guide

## 🔔 Overview

The Rhyme App has a fully functional in-app notification system that keeps admins and authors informed about important events in real-time.

## 📍 Where to Find Notifications

### Notification Bell Icon
Look for the **bell icon** (🔔) in the top navigation bar:
- **Admin Panel**: Top right corner, next to the dark mode toggle
- **Author Dashboard**: Top right corner, next to user profile
- **User Dashboard**: Top right corner, next to user profile

### Notification Badge
- A **red badge** with a number appears on the bell icon when you have unread notifications
- The number shows how many unread notifications you have
- Example: 🔔 **3** means you have 3 unread notifications

## 🎯 Notification Types

### For Admins

#### 1. **New Book Submission** 📚
**When**: An author submits a new book for review
**Icon**: Blue book icon
**Message**: "New book submission: '[Book Title]' by [Author Name]"
**Action**: Click "View Details" to review the book

**Example:**
```
🔔 New Book Submission
New book submission: "The Great Adventure" by John Doe
2 minutes ago
View Details →
```

#### 2. **Payout Request** 💰
**When**: An author requests a payout
**Icon**: Yellow wallet icon
**Message**: "New payout request of ₦[Amount] from [Author Name]"
**Action**: Click "View Details" to review the payout request

**Example:**
```
🔔 New Payout Request
New payout request of ₦500,000.00 from Jane Smith
5 minutes ago
View Details →
```

### For Authors

#### 3. **Book Status Changed** ✅
**When**: Admin changes your book's status
**Icon**: Blue book icon
**Messages** (varies by status):
- **Pending Review**: "Your book has been submitted and is pending review"
- **Send Review Copy**: "A review copy has been requested. Please check your dashboard"
- **Rejected**: "Your book was not accepted. You can edit and resubmit"
- **Approved Awaiting Delivery**: "Send Review Copy status - deliver physical copies"
- **Stocked**: "Your book is now available in inventory. Sales tracking is active"

**Example:**
```
🔔 Book Status Changed
Your book "The Great Adventure" approved. Great News! Your book is now 
available in our inventory. Sales tracking is now active and you can 
monitor your earnings.
10 minutes ago
View Details →
```

#### 4. **Payout Status Changed** 💳
**When**: Admin approves or denies your payout request
**Icon**: Blue wallet icon
**Messages**:
- **Approved**: "Your payout request of ₦[Amount] has been approved and will be processed soon"
- **Denied**: "Your payout request of ₦[Amount] was denied. You can submit a new request"
- **Completed**: "Your payout of ₦[Amount] has been completed and sent to your account"

**Example:**
```
🔔 Payout Status Changed
Your payout request of ₦500,000.00 has been approved and will be 
processed soon.
1 hour ago
View Details →
```

## 📖 How to Use Notifications

### Viewing Notifications

1. **Click the bell icon** in the top navigation
2. A dropdown will appear showing your recent notifications
3. Each notification shows:
   - Icon indicating the type
   - Title and message
   - Time (e.g., "2 minutes ago")
   - "View Details" link (if applicable)

### Reading Notifications

- **Unread notifications** appear with a white/light background
- **Read notifications** appear with a slightly darker background
- Click on a notification to mark it as read
- Click "View Details" to go to the relevant page

### Managing Notifications

#### Mark All as Read
1. Click the bell icon to open notifications
2. Click **"Mark All as Read"** at the bottom
3. All unread notifications will be marked as read
4. The badge number will reset to 0

#### Mark Single Notification as Read
- Simply click on the notification
- It will automatically be marked as read

### Auto-Refresh
- Notifications automatically refresh every **30 seconds**
- You don't need to manually refresh the page
- New notifications will appear automatically

## 📧 Email Notifications

In addition to in-app notifications, you also receive **email notifications** for all events:

### Email Features
- Sent to your registered email address
- Contains detailed information about the event
- Includes direct links to take action
- Professional email templates

### Email Types
1. **Book Submitted** - Confirmation for authors, alert for admins
2. **Book Status Changed** - Detailed status update for authors
3. **Payout Requested** - Alert for admins with payout details
4. **Payout Status Changed** - Confirmation for authors with next steps

## 🎨 Notification UI Elements

### Notification Bell
```
┌─────────────────────────┐
│  🔔 (3)                 │  ← Red badge shows unread count
└─────────────────────────┘
```

### Notification Dropdown
```
┌───────────────────────────────────────┐
│ Admin Notifications  Mark All as Read │
├───────────────────────────────────────┤
│ 📚 New Book Submission                │
│    New book submission: "Book Title"  │
│    by Author Name                     │
│    2 minutes ago                      │
│    View Details →                     │
├───────────────────────────────────────┤
│ 💰 New Payout Request                 │
│    New payout request of ₦500,000.00  │
│    from Jane Smith                    │
│    5 minutes ago                      │
│    View Details →                     │
├───────────────────────────────────────┤
│              View All                 │
└───────────────────────────────────────┘
```

## 🔍 Troubleshooting

### Not Receiving Notifications?

#### Check 1: Queue Worker
Notifications are processed via Laravel's queue system. Ensure the queue worker is running:
```bash
php artisan queue:work
```

#### Check 2: Email Configuration
Check your `.env` file for correct email settings:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
```

#### Check 3: Browser Console
Open browser developer tools (F12) and check for JavaScript errors

#### Check 4: Database
Check if notifications are being created in the database:
```sql
SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10;
```

### Notifications Not Updating?

1. **Hard refresh** the page (Ctrl+F5 or Cmd+Shift+R)
2. **Clear browser cache**
3. Check if JavaScript is enabled
4. Check browser console for errors

### Badge Not Showing Correct Count?

1. Click "Mark All as Read"
2. Refresh the page
3. The count should update automatically

## 📊 Notification Statistics

### For Admins
View notification statistics in the admin panel:
- Total notifications sent
- Unread notifications
- Notifications by type
- Recent activity

### For Authors
View your notification history:
- All notifications received
- Unread count
- Filter by type
- Search notifications

## 🚀 Best Practices

### For Admins
1. **Check notifications regularly** - At least once per day
2. **Respond promptly** to book submissions and payout requests
3. **Mark as read** after taking action
4. **Use email notifications** for important alerts when away from dashboard

### For Authors
1. **Enable email notifications** to stay informed
2. **Check notifications** before submitting support tickets
3. **Read status change messages** carefully for next steps
4. **Keep email address updated** in your profile

## 📱 Mobile Responsiveness

The notification system is fully responsive:
- Works on desktop, tablet, and mobile devices
- Touch-friendly interface
- Optimized dropdown for small screens
- Same functionality across all devices

## 🔐 Privacy & Security

- Notifications are **private** - only you can see your notifications
- Notifications are **encrypted** in transit
- Email notifications use **secure SMTP**
- Notification data is **never shared** with third parties

## 📞 Support

If you experience issues with notifications:
1. Check this guide first
2. Check the troubleshooting section
3. Contact support with:
   - Your username
   - Description of the issue
   - Screenshots (if applicable)
   - Browser and device information

---

**Last Updated**: December 22, 2025  
**Version**: 1.0  
**Status**: ✅ Fully Operational
