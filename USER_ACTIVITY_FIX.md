# User Activity Page Fix

## Issue

An `InvalidArgumentException` error was occurring when accessing the User Activity page (`/admin/users/activity`) with the message "View [admin.user-activity] not found."

## Root Cause

The issue was in the `AdminController::userActivity()` method which was trying to load a view named `admin.user-activity`, but the actual view file was located at `resources/views/admin/users/activity.blade.php`.

## Solution

Updated the `userActivity()` method in `app/Http/Controllers/Admin/AdminController.php` to load the correct view path:

```php
public function userActivity()
{
    return view('admin.users.activity');
}
```

## Additional Steps Taken

1. Cleared route cache with `php artisan route:clear`
2. Cleared view cache with `php artisan view:clear`
3. Cleared config cache with `php artisan config:clear`

## Verification

The User Activity page should now load correctly without any errors. The page displays user activities and platform events with filtering options for different time periods.

## Files Modified

1. `app/Http/Controllers/Admin/AdminController.php` - Fixed the view path in the `userActivity()` method

## Related Files

1. `resources/views/admin/users/activity.blade.php` - The actual view file that was being referenced
2. `routes/web.php` - Contains the route definition pointing to the controller method

This fix resolves the navigation issue in the admin panel sidebar where the "User Activities" link under the "Analytics" section was not working.