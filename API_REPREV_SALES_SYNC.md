# ERPREV Sales Sync API Implementation

This document describes the API implementation that replicates the functionality of the [SyncRevSales](file:///c%3A/xampp%2Fhtdocs%2Frhyme_app%2Fapp%2FConsole%2FCommands%2FSyncRevSales.php%23L12-L200) console command.

## API Endpoints

### 1. Sync Sales Data
**Endpoint:** `GET|POST /api/erprev/sync-sales`
**Controller:** [RevSalesSyncController::syncSales()](file:///c%3A/xampp%2Fhtdocs%2Frhyme_app%2Fapp%2FHttp%2FControllers%2FApi%2FRevSalesSyncController.php%23L35-L212)

#### Description
This endpoint fetches sales data from the ERPREV API and creates wallet transactions for authors based on book sales, similar to the console command.

#### Parameters
- `since` (optional, date) - Start date for fetching sales data
- `days` (optional, integer) - Number of days back to fetch sales data (defaults to 7)
- `book_id` (optional, integer) - Specific book ID to sync sales for
- `debug` (optional, boolean) - Enable debug mode to see sample data without processing

#### Response Format
```json
{
  "success": true,
  "message": "Sales sync completed successfully",
  "statistics": {
    "processed": 5,
    "duplicates": 0,
    "books_not_found": 0,
    "other_errors": 0,
    "total_records": 5
  },
  "filters": {
    "date_from": "2025-12-02",
    "date_to": "2025-12-09"
  },
  "execution_time_ms": 125.45
}
```

### 2. Get Sync Status
**Endpoint:** `GET /api/erprev/sync-sales/status`
**Controller:** [RevSalesSyncController::syncStatus()](file:///c%3A/xampp%2Fhtdocs%2Frhyme_app%2Fapp%2FHttp%2FControllers%2FApi%2FRevSalesSyncController.php%23L219-L243)

#### Description
Retrieves the status of recent sync operations from the [RevSyncLog](file:///c%3A/xampp%2Fhtdocs%2Frhyme_app%2Fapp%2FModels%2FRevSyncLog.php%23L8-L35) model.

#### Response Format
```json
{
  "success": true,
  "latest_sync_logs": [
    {
      "id": 15,
      "area": "sales",
      "status": "success",
      "message": "Sales items fetched",
      "payload": {
        "count": 3
      },
      "created_at": "2025-12-09T14:30:22.000000Z",
      "updated_at": "2025-12-09T14:30:22.000000Z"
    }
  ]
}
```

## Implementation Details

### Authentication
The API endpoints are protected with the `auth:sanctum` middleware, requiring authenticated users with valid API tokens.

### Key Features Replicated from Console Command
1. Date range filtering for sales data
2. Book-specific syncing capability
3. Debug mode for inspecting data structures
4. Duplicate detection and prevention
5. Error handling and logging
6. Performance metrics
7. Comprehensive statistics reporting

### Differences from Console Command
1. Returns structured JSON responses instead of console output
2. Uses HTTP request validation instead of command-line options
3. Provides execution time metrics
4. Accessible via HTTP requests instead of command-line interface

## Usage Examples

### Basic Sync Request
```bash
# Using POST method
curl -X POST "https://your-domain.com/api/erprev/sync-sales" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"days": 30}'

# Using GET method with query parameters
curl -X GET "https://your-domain.com/api/erprev/sync-sales?days=30" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

### Debug Mode Request
```bash
# Using POST method
curl -X POST "https://your-domain.com/api/erprev/sync-sales" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"debug": true, "days": 7}'

# Using GET method with query parameters
curl -X GET "https://your-domain.com/api/erprev/sync-sales?debug=true&days=7" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

### Check Sync Status
```bash
curl -X GET "https://your-domain.com/api/erprev/sync-sales/status" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

## Files Created

1. `routes/api.php` - API route definitions
2. `app/Http/Controllers/Api/RevSalesSyncController.php` - API controller implementation

## Dependencies

This implementation relies on:
- [RevService](file:///c%3A/xampp%2Fhtdocs%2Frhyme_app%2Fapp%2FServices%2FRevService.php%23L7-L659) for ERPREV API communication
- [Book](file:///c%3A/xampp%2Fhtdocs%2Frhyme_app%2Fapp%2FModels%2FBook.php%23L8-L77) model for book lookups
- [WalletTransaction](file:///c%3A/xampp%2Fhtdocs%2Frhyme_app%2Fapp%2FModels%2FWalletTransaction.php%23L7-L36) model for recording sales
- [RevSyncLog](file:///c%3A/xampp%2Fhtdocs%2Frhyme_app%2Fapp%2FModels%2FRevSyncLog.php%23L8-L35) model for sync logging