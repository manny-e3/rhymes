# ERPREV Debugging Implementation Summary

## Overview
This document summarizes the debugging enhancements added to help troubleshoot issues with ERPREV data retrieval.

## Debugging Enhancements Added

### 1. Enhanced RevService with Detailed Logging
Updated `app\Services\RevService.php` to include comprehensive logging for all API interactions:

#### Request Logging
- Added logging of API requests including:
  - URL being called
  - Headers (with sensitive data masked)
  - Request payload/filters

#### Response Logging
- Added logging of API responses including:
  - HTTP status code
  - Success status
  - Full response body

#### Error Logging
- Added detailed error logging for all exceptions
- Included context information for easier troubleshooting

### 2. Enhanced ErpRevController with Debugging
Updated `app\Http\Controllers\Admin\ErpRevController.php` to include:

#### Method Entry/Exit Logging
- Added logging at the beginning of each method with input parameters
- Added logging at the end of each method with results

#### Data Processing Logging
- Added logging of data extraction and processing steps
- Included record counts and data structure information

#### Error State Logging
- Added detailed error logging when operations fail
- Included error messages and context

### 3. New Test Command
Created `app\Console\Commands\TestErpRevData.php` - a comprehensive test command that:

#### Connection Testing
- Tests basic connectivity to ERPREV API
- Verifies authentication credentials

#### Data Retrieval Testing
- Tests retrieval of products data
- Tests retrieval of inventory data
- Tests retrieval of sales data

#### Detailed Output
- Provides detailed output of test results
- Shows record counts and sample data
- Indicates success/failure of each test step

### 4. Browser-Based Test Route
Added a new test route `/test-erprev` that:

#### Quick Testing
- Provides a quick way to test ERPREV integration in the browser
- Returns JSON response with detailed test results

#### Connection Verification
- Tests API connection
- Verifies authentication

#### Data Retrieval Verification
- Tests retrieval of products and inventory data
- Returns sample data for inspection

## How to Use the Debugging Tools

### 1. Check Laravel Logs
Look in `storage/logs/laravel.log` for detailed ERPREV API interaction logs:
```
tail -f storage/logs/laravel.log | grep ERPREV
```

### 2. Run the Test Command
Execute the comprehensive test command:
```bash
php artisan rev:test-data
```

### 3. Check Database Logs
Look at the `rev_sync_logs` table for sync operation logs:
```sql
SELECT * FROM rev_sync_logs ORDER BY created_at DESC LIMIT 10;
```

### 4. Use Browser Test Route
Visit `http://your-domain/test-erprev` to see JSON test results

## Common Issues to Look For

### 1. Authentication Issues
- Check if API credentials are correctly configured in `.env`
- Look for 401/403 errors in logs

### 2. Network/Connectivity Issues
- Check if the ERPREV server is reachable
- Look for connection timeout errors

### 3. Data Structure Issues
- Verify that the API response structure matches expectations
- Check for missing or unexpected fields

### 4. Filter/Parameter Issues
- Verify that filters are correctly formatted
- Check for invalid parameter errors

## Configuration Verification

### 1. Environment Variables
Ensure these variables are correctly set in `.env`:
```
ERPREV_ACCOUNT_URL=your-account.erprev.com
ERPREV_API_KEY=your_api_key_here
ERPREV_API_SECRET=your_api_secret_here
ERPREV_SYNC_ENABLED=true
```

### 2. Service Configuration
Verify `config/services.php` includes:
```php
'erprev' => [
    'account_url' => env('ERPREV_ACCOUNT_URL'),
    'api_key' => env('ERPREV_API_KEY'),
    'api_secret' => env('ERPREV_API_SECRET'),
    'enabled' => env('ERPREV_SYNC_ENABLED', false),
],
```

## Troubleshooting Steps

### 1. Run the Test Command
```bash
php artisan rev:test-data
```

### 2. Check Laravel Logs
```bash
tail -f storage/logs/laravel.log | grep ERPREV
```

### 3. Check Database Logs
```sql
SELECT * FROM rev_sync_logs ORDER BY created_at DESC LIMIT 20;
```

### 4. Test in Browser
Visit `http://your-domain/test-erprev`

### 5. Verify Configuration
Check `.env` and `config/services.php` settings

## Expected Log Output

When everything is working correctly, you should see logs like:
```
[2023-xx-xx xx:xx:xx] local.INFO: ERPREV API Request - getProductsList {"url":"https://your-account.erprev.com/api/1.0/get-products-list/json","headers":{"Authorization":"Basic xxx...","Content-Type":"application/json"},"filters":{"limit":5}} 
[2023-xx-xx xx:xx:xx] local.INFO: ERPREV API Response - getProductsList {"status":200,"successful":true,"body":"{\"status\":\"1\",\"records\":[...]}"} 
[2023-xx-xx xx:xx:xx] local.INFO: ERPREV Sync [books] success: Products list fetched {"count":5,"filters":{"limit":5}} 
```

If there are issues, you'll see error logs that help identify the problem.