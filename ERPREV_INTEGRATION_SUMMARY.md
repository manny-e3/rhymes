# ERPREV Integration Implementation Summary

## Overview
This document summarizes the changes made to integrate the Rhymes Platform with the ERPREV API based on the official API documentation.

## Changes Made

### 1. Updated RevService.php
- Changed authentication method from Bearer token to Basic authentication
- Updated API endpoints to match ERPREV's structure (`https://{account_server_url}/api/1.0/{service-endpoint}/{format}`)
- Implemented the following ERPREV endpoints:
  - `register-product/json` - Register a book as a product
  - `get-products-list/json` - Get list of products
  - `get-stock-list/json` - Get inventory data
  - `get-salesitems/json` - Get sales data
  - `sold-products-summary/json` - Get sales summary
  - `about/json` - Test connection endpoint

### 2. Configuration Updates
- Added ERPREV configuration section to `config/services.php`
- Updated `.env.example` with ERPREV configuration variables:
  ```
  ERPREV_ACCOUNT_URL=your-account.erprev.com
  ERPREV_API_KEY=your_api_key_here
  ERPREV_API_SECRET=your_api_secret_here
  ERPREV_SYNC_ENABLED=true
  ```

### 3. Book Review Service Integration
- Modified `BookReviewService` to automatically register books in ERPREV when their status is changed to "accepted"
- Added dependency injection for `RevService`

### 4. Console Commands
Created several console commands for managing the ERPREV integration:

1. **SyncRevSales** (`rev:sync-sales`)
   - Syncs sales data from ERPREV and updates author wallets
   - Calculates author earnings (70% of sale price)
   - Creates wallet transactions for each sale
   - Prevents duplicate processing of sales

2. **SyncRevInventory** (`rev:sync-inventory`)
   - Syncs inventory data from ERPREV
   - Updates book statuses from "accepted" to "stocked" when inventory is available

3. **TestErpRevConnection** (`rev:test-connection`)
   - Tests the connection to the ERPREV API

4. **RegisterBookInErprev** (`rev:register-book {book_id}`)
   - Manually registers a book in ERPREV

### 5. Console Kernel Updates
- Registered all new console commands
- Set up scheduled tasks:
  - Hourly sales sync
  - Daily inventory sync at 2 AM

### 6. Model Updates
- Enhanced `RevMapping` model with proper fillable fields and casting
- Enhanced `RevSyncLog` model with proper fillable fields and casting

## Data Mapping

### Rhymes Book → ERPREV Product
| Rhymes Field | ERPREV Field | Notes |
|--------------|--------------|-------|
| `isbn` | `product_code` | Unique identifier |
| `title` | `product_name` | Book title |
| `genre` | `category` | Book category |
| `price` | `unit_price` | Selling price |
| `description` | `description` | Book description |
| `book_type` | `product_type` | physical/digital/both |
| `user.name` | `author` | Author name |
| `id` | - | Rhymes internal ID |
| `rev_book_id` | `product_id` | ERPREV product ID |

### ERPREV Sale → Rhymes Wallet Transaction
| ERPREV Field | Rhymes Field | Calculation |
|--------------|--------------|-------------|
| `sale_id` | `meta->erprev_sale_id` | Unique reference |
| `product_id` | `book_id` | Via rev_mappings lookup |
| `quantity_sold` | `meta->quantity_sold` | Used for calculation |
| `unit_price` | `meta->unit_price` | Used for calculation |
| `total_amount` | `meta->total_amount` | quantity * unit_price |
| `sale_date` | `meta->sale_date` | Transaction timestamp |
| - | `type` | Always 'sale' |
| - | `user_id` | Book author's user_id |
| - | `amount` | 70% of total_amount |

## Implementation Workflow

### Book Acceptance Flow
1. Admin accepts book in Rhymes (status: pending → accepted)
2. Rhymes automatically calls `/register-product/json`
3. Maps book details to ERPREV product
4. Stores product_id in book.rev_book_id
5. Book status: pending → accepted

### Stock Arrival Flow
1. Books arrive at Rovingheights warehouse
2. Staff registers stock in ERPREV
3. Daily scheduled job calls `/get-stock-list/json`
4. If stock > 0, update book status: accepted → stocked
5. System could notify author (future enhancement)

### Sales Sync Flow (Hourly)
1. Scheduled command runs: `php artisan rev:sync-sales`
2. Calls `/get-salesitems/json`
   - date_from: last sync time or 24 hours ago
   - date_to: now
3. For each sale:
   - Find book by product_id
   - Calculate author earnings (70% of sale amount)
   - Create wallet_transaction (type: 'sale')
   - Update author wallet balance
4. Log sync in rev_sync_logs
5. Could send notification to author for new sales (future enhancement)

## Testing the Integration

### 1. Test Connection
```bash
php artisan rev:test-connection
```

### 2. Manual Book Registration
```bash
php artisan rev:register-book {book_id}
```

### 3. Manual Sales Sync
```bash
php artisan rev:sync-sales
```

### 4. Manual Inventory Sync
```bash
php artisan rev:sync-inventory
```

## Scheduled Tasks

The following tasks are scheduled automatically:

1. **Hourly Sales Sync**
   - Runs every hour
   - Command: `php artisan rev:sync-sales`

2. **Daily Inventory Sync**
   - Runs daily at 2:00 AM
   - Command: `php artisan rev:sync-inventory`

## Security Considerations

1. **API Credentials**: Stored in `.env`, never committed to version control
2. **HTTPS Only**: All ERPREV API calls use HTTPS
3. **Error Handling**: Errors are logged but API credentials are not exposed in logs
4. **Rate Limiting**: Laravel's HTTP client has built-in retry mechanisms

## Next Steps

1. **Configure Production Environment**
   - Update `.env` with production ERPREV credentials
   - Verify scheduled tasks are running

2. **Monitor Sync Logs**
   - Regularly check `rev_sync_logs` table for errors
   - Set up alerts for sync failures

3. **Enhance Notifications**
   - Implement author notifications for sales
   - Implement author notifications for stock status changes

4. **Add Webhook Support** (Future Enhancement)
   - Implement webhook endpoints to receive real-time updates from ERPREV
   - Update data immediately when changes occur in ERPREV

5. **Performance Optimization**
   - Add caching for frequently accessed data
   - Optimize database queries for large datasets

## Troubleshooting

### Common Issues

1. **Connection Failed**
   - Verify ERPREV credentials in `.env`
   - Check network connectivity to ERPREV servers
   - Ensure firewall allows outbound HTTPS connections

2. **Book Registration Fails**
   - Check that book has all required fields
   - Verify book status is "accepted"
   - Check sync logs for specific error messages

3. **Sales Sync Not Finding Books**
   - Verify books have `rev_book_id` populated
   - Check that product IDs match between systems

### Log Locations

1. **Application Logs**: `storage/logs/laravel.log`
2. **Sync Logs**: `rev_sync_logs` database table
3. **Console Output**: Terminal output when running commands manually

## Support

For issues with the ERPREV integration:
- Check the official ERPREV API documentation: https://erprev.com/doc/api/
- Contact ERPREV support: info@erprev.com
- Review sync logs for error details