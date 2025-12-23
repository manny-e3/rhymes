# Pusher Notification Fix for Subdirectory Installation

## Problem
Pusher notifications were not working when accessing the app via `http://localhost/rhyme_app/` but worked fine with `http://127.0.0.1:8000/`.

## Root Cause
The issue was caused by:
1. **Relative authEndpoint**: The Pusher configuration used a relative path `/broadcasting/auth` which resolved incorrectly in subdirectory installations
2. **Incorrect base tag**: The `<base href="../">` tag used a relative path instead of an absolute URL

## Solution Applied

### 1. Fixed Pusher Authentication Endpoint
**File**: `public/js/notifications.js`

Added a helper function to get the correct base URL from the `<base>` tag:
```javascript
const getBaseUrl = () => {
    const base = document.querySelector('base');
    if (base && base.href) {
        return base.href.replace(/\/$/, ''); // Remove trailing slash
    }
    return window.location.origin;
};
```

Updated the Pusher config to use absolute path:
```javascript
authEndpoint: `${getBaseUrl()}/broadcasting/auth`
```

**Also updated all notification API endpoints to use absolute URLs:**
- `/notifications/unread` → `${getBaseUrl()}/notifications/unread`
- `/notifications/mark-all-read` → `${getBaseUrl()}/notifications/mark-all-read`
- `/notifications/${id}/mark-read` → `${getBaseUrl()}/notifications/${id}/mark-read`
- `/toggle-dark-mode` → `${getBaseUrl()}/toggle-dark-mode`

### 2. Fixed Base Tag in Layout Files
Updated the following files to use Laravel's `url()` helper:
- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/author.blade.php`

Changed from:
```html
<base href="../">
```

To:
```html
<base href="{{ url('/') }}/">
```

## How to Test

### 1. Clear Browser Cache
- Press `Ctrl + Shift + Delete` (Windows/Linux) or `Cmd + Shift + Delete` (Mac)
- Clear cached images and files
- Or use hard refresh: `Ctrl + F5` (Windows/Linux) or `Cmd + Shift + R` (Mac)

### 2. Test with Subdirectory URL
1. Access your app at `http://localhost/rhyme_app/`
2. Open browser DevTools (F12)
3. Go to the Console tab
4. Look for these messages:
   - `Pusher connected` - indicates successful connection
   - No errors about `broadcasting/auth` endpoint

### 3. Test Notifications
1. Trigger a notification (e.g., submit a book for review)
2. Check if the notification badge updates

3. Click the bell icon to see notifications

### 4. Verify WebSocket Connection
In the browser console, check:
```javascript
console.log(window.pusherKey);
console.log(window.pusherCluster);
console.log(window.userId);
```

All values should be properly set.

## Debugging

If notifications still don't work, check the following:

### 1. Check Browser Console
Look for errors related to:
- Pusher connection
- Authentication endpoint
- WebSocket connection

### 2. Check Network Tab
- Filter by "WS" (WebSocket) to see Pusher connections
- Check if `/broadcasting/auth` requests are successful (200 status)

### 3. Verify Pusher Configuration
Check your `.env` file has:
```env
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

### 4. Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

Look for any errors related to broadcasting or Pusher.

### 5. Test Authentication Endpoint Directly
In browser console:
```javascript
fetch(`${window.location.origin}/rhyme_app/broadcasting/auth`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
        socket_id: 'test',
        channel_name: 'private-notifications.1'
    })
})
.then(r => r.json())
.then(console.log)
.catch(console.error);
```

Should return authentication data, not a 404 error.

## Additional Notes

### Why This Fix Works
- **Absolute URLs**: Using absolute URLs ensures the correct endpoint is called regardless of the app's location (root or subdirectory)
- **Base Tag**: The `<base>` tag sets the base URL for all relative URLs in the document, ensuring consistent URL resolution
- **Laravel url() Helper**: Automatically generates the correct URL based on your `APP_URL` in `.env`

### APP_URL Configuration
Make sure your `.env` file has the correct `APP_URL`:

For subdirectory installation:
```env
APP_URL=http://localhost/rhyme_app
```

For root installation:
```env
APP_URL=http://127.0.0.1:8000
```

### Production Deployment
When deploying to production, update `APP_URL` to your production domain:
```env
APP_URL=https://yourdomain.com
```

## Common Issues

### Issue: Still getting 404 on /broadcasting/auth
**Solution**: Clear Laravel's config cache:
```bash
php artisan config:clear
php artisan cache:clear
```

### Issue: Pusher connection fails
**Solution**: Verify Pusher credentials and check if the Pusher service is running

### Issue: Notifications work on one URL but not another
**Solution**: This fix should resolve this. Make sure you've cleared browser cache and hard-refreshed the page.
