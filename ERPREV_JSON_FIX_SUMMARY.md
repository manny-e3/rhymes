# ERPREV JSON Request Fix Summary

## Issue Description
The ERPREV API was returning an error:
```
{
  "success": true,
  "data": {
    "status": "0",
    "error": "A JSONObject text must begin with '{' at 1 [character 2 line 1]"
  }
}
```

This error was occurring when trying to access the ERPREV sales data view (`/admin/erprev/sales`) and other ERPREV views.

## Root Cause
The issue was that the `RevService` methods were not properly sending request data as JSON to the ERPREV API. According to the ERPREV API documentation, all POST requests should send data as JSON in the request body with the `Content-Type: application/json` header.

However, the methods were:
1. Not setting the `Content-Type: application/json` header (except for `registerProduct`)
2. Sending filter parameters as form parameters instead of JSON in the request body

This caused the ERPREV API to receive malformed data, resulting in the JSON parsing error.

## Solution Implemented
Updated all `RevService` methods to properly send requests as JSON:

### Files Modified
1. `app\Services\RevService.php` - Added `Content-Type: application/json` header to all HTTP requests and ensured filter parameters are sent as JSON

### Methods Updated
1. `getProductsList()` - Added Content-Type header
2. `getStockList()` - Added Content-Type header
3. `getSalesItems()` - Added Content-Type header
4. `getSoldProductsSummary()` - Added Content-Type header
5. `testConnection()` - Added Content-Type header

## Changes Made
- Added `'Content-Type' => 'application/json'` to the headers in all HTTP POST requests
- This ensures that the filter parameters are properly sent as JSON in the request body
- The ERPREV API can now correctly parse the incoming requests

## Verification
After implementing the fix:
1. The ERPREV sales data view (`/admin/erprev/sales`) should now load correctly
2. All other ERPREV views (`/admin/erprev/inventory`, `/admin/erprev/products`, `/admin/erprev/summary`) should also work properly
3. The API requests will now be properly formatted according to the ERPREV API specification

## Technical Details
The Laravel HTTP client automatically converts arrays to JSON when the `Content-Type: application/json` header is present. By adding this header to all requests, we ensure that:

1. Filter parameters are properly serialized as JSON
2. The ERPREV API receives well-formed JSON requests
3. The API can correctly parse and process the requests

This fix aligns the implementation with the ERPREV API documentation requirements and resolves the JSON parsing errors.