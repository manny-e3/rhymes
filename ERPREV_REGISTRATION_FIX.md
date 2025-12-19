# 🔧 ERPREV Book Registration Troubleshooting Guide

## Problem
Books approved/accepted by admin are not being registered in ERPREV automatically.

## Root Causes

The book registration code **IS already implemented** in the system. The issue is likely one of the following:

### 1. ERPREV Sync is Disabled
**Check**: Look in your `.env` file for `ERPREV_SYNC_ENABLED`

**Solution**:
```env
ERPREV_SYNC_ENABLED=true
```

### 2. ERPREV Credentials Missing or Incorrect
**Check**: Look in your `.env` file for ERPREV credentials

**Solution**: Add or update these values in `.env`:
```env
ERPREV_ACCOUNT_URL=your-account.erprev.com
ERPREV_API_KEY=your_actual_api_key
ERPREV_API_SECRET=your_actual_api_secret
ERPREV_SYNC_ENABLED=true
```

### 3. ERPREV API Connection Issues
**Check**: Network connectivity or API endpoint issues

**Solution**: Test the connection:
```bash
php artisan rev:test-connection
```

Or visit in browser:
```
http://localhost:8000/test-erprev
```

### 4. Books Were Accepted Before Integration Was Configured
**Check**: Books with `status='accepted'` but `rev_book_id IS NULL`

**Solution**: Use the diagnostic tool or manual registration command

---

## Quick Diagnostic

Run this command to diagnose the issue:

```bash
php diagnose_erprev.php
```

This will:
- ✅ Check ERPREV configuration
- ✅ Test ERPREV connection
- ✅ Find unregistered books
- ✅ Offer to register them automatically
- ✅ Show recent sync logs

---

## Manual Solutions

### Solution 1: Register a Specific Book

```bash
php artisan rev:register-book {book_id}
```

Example:
```bash
php artisan rev:register-book 1
```

### Solution 2: Register All Unregistered Books

Use Laravel Tinker:

```bash
php artisan tinker
```

Then run:
```php
$revService = app(\App\Services\RevService::class);
$books = \App\Models\Book::where('status', 'accepted')
    ->whereNull('rev_book_id')
    ->get();

foreach ($books as $book) {
    echo "Registering: {$book->title}...\n";
    $result = $revService->registerProduct($book);
    
    if ($result['success']) {
        $book->update(['rev_book_id' => $result['product_id']]);
        echo "✅ Success! Product ID: {$result['product_id']}\n";
    } else {
        echo "❌ Failed: {$result['message']}\n";
    }
}
```

### Solution 3: Check Sync Logs

```bash
php artisan tinker
```

```php
// View recent product sync logs
\App\Models\RevSyncLog::where('area', 'products')
    ->latest()
    ->take(10)
    ->get()
    ->each(function($log) {
        echo "[{$log->status}] {$log->created_at}: {$log->message}\n";
    });
```

---

## How the Auto-Registration Works

When an admin approves a book, the following happens:

1. **Controller** (`BookReviewController@review`) receives the approval
2. **Service** (`BookReviewService@reviewBook`) is called
3. **If status is 'accepted'**:
   - Calls `RevService->registerProduct($book)`
   - Sends book data to ERPREV API
   - Receives `product_id` from ERPREV
   - Updates book with `rev_book_id`
4. **User is promoted** to Author (if first book)
5. **Notification** is sent to the author

**Code Location**: `app/Services/Admin/BookReviewService.php` (lines 84-129)

---

## Verification Steps

### Step 1: Check Configuration

```bash
php artisan tinker --execute="dd(config('services.erprev'));"
```

Expected output:
```php
array:4 [
  "account_url" => "your-account.erprev.com"
  "api_key" => "your_api_key"
  "api_secret" => "your_api_secret"
  "enabled" => true  // ← Must be true!
]
```

### Step 2: Test Connection

```bash
php artisan rev:test-connection
```

Expected output:
```
✅ ERPREV connection successful!
```

### Step 3: Check Unregistered Books

```bash
php artisan tinker
```

```php
\App\Models\Book::where('status', 'accepted')
    ->whereNull('rev_book_id')
    ->count();
```

If this returns > 0, you have unregistered books.

### Step 4: Approve a Test Book

1. Create a test book (as a user)
2. Login as admin
3. Go to Admin → Books → Pending
4. Approve the book
5. Check the logs:

```bash
tail -f storage/logs/laravel.log
```

Look for:
```
BookReviewService: Registering book in ERPREV
BookReviewService: ERPREV registration result
```

---

## Common Errors and Solutions

### Error: "ERPREV sync is disabled"

**Cause**: `ERPREV_SYNC_ENABLED=false` or not set in `.env`

**Solution**:
```env
ERPREV_SYNC_ENABLED=true
```

Then restart your server:
```bash
php artisan config:clear
php artisan serve
```

### Error: "Connection timeout" or "Could not resolve host"

**Cause**: Network issue or incorrect ERPREV URL

**Solution**:
1. Check `ERPREV_ACCOUNT_URL` in `.env`
2. Ensure no `http://` or `https://` prefix (it's added automatically)
3. Test connectivity: `ping your-account.erprev.com`

### Error: "Authentication failed" or "401 Unauthorized"

**Cause**: Incorrect API credentials

**Solution**:
1. Verify `ERPREV_API_KEY` and `ERPREV_API_SECRET` in `.env`
2. Contact ERPREV support to verify credentials
3. Clear config cache: `php artisan config:clear`

### Error: "Product already exists"

**Cause**: Book with same ISBN already in ERPREV

**Solution**:
1. Check if book is already registered
2. Update the `rev_book_id` manually in database
3. Or use a different ISBN

---

## Database Queries

### Find all unregistered accepted books:

```sql
SELECT id, title, isbn, status, rev_book_id 
FROM books 
WHERE status = 'accepted' 
AND rev_book_id IS NULL;
```

### Find all registered books:

```sql
SELECT id, title, isbn, status, rev_book_id 
FROM books 
WHERE rev_book_id IS NOT NULL;
```

### Check sync logs:

```sql
SELECT * FROM rev_sync_logs 
WHERE area = 'products' 
ORDER BY created_at DESC 
LIMIT 10;
```

---

## Prevention

To prevent this issue in the future:

1. **Always keep ERPREV sync enabled**:
   ```env
   ERPREV_SYNC_ENABLED=true
   ```

2. **Monitor sync logs regularly**:
   ```bash
   php artisan tinker --execute="
   \App\Models\RevSyncLog::where('status', 'error')
       ->where('created_at', '>', now()->subDays(7))
       ->get();
   "
   ```

3. **Set up monitoring alerts** for failed ERPREV syncs

4. **Test ERPREV connection** after any configuration changes:
   ```bash
   php artisan rev:test-connection
   ```

---

## Support

If the issue persists after trying these solutions:

1. **Check application logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Check ERPREV sync logs** in database:
   ```sql
   SELECT * FROM rev_sync_logs 
   WHERE status = 'error' 
   ORDER BY created_at DESC;
   ```

3. **Contact ERPREV support**:
   - Email: info@erprev.com
   - Verify API credentials and endpoint

4. **Run the diagnostic tool**:
   ```bash
   php diagnose_erprev.php
   ```

---

## Quick Fix Summary

**Most common fix** (90% of cases):

1. Open `.env` file
2. Ensure these lines exist and are correct:
   ```env
   ERPREV_ACCOUNT_URL=your-account.erprev.com
   ERPREV_API_KEY=your_actual_api_key
   ERPREV_API_SECRET=your_actual_api_secret
   ERPREV_SYNC_ENABLED=true
   ```
3. Clear config cache:
   ```bash
   php artisan config:clear
   ```
4. Register existing unregistered books:
   ```bash
   php diagnose_erprev.php
   ```
   (Answer 'yes' when prompted)

5. Test with a new book approval

---

**Last Updated**: November 27, 2025
