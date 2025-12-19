# ERPREV Webhook Implementation

This document explains how to use the webhook functionality implemented for the Rhymes platform to receive real-time updates from ERPREV.

## Overview

The webhook system allows the Rhymes platform to receive real-time notifications from ERPREV when certain events occur, such as:

- New sales (`sale.created`)
- Inventory updates (`inventory.updated`)
- Product creation (`product.created`)

## Webhook Endpoint

The webhook endpoint is available at:
```
POST /webhook/erprev
```

## Available Commands

### Register Webhooks
```bash
php artisan rev:register-webhooks
```

### List Registered Webhooks
```bash
php artisan rev:register-webhooks --list
```

### Test Webhooks
```bash
# Test a sale created webhook
php artisan rev:test-webhook sale.created

# Test an inventory updated webhook
php artisan rev:test-webhook inventory.updated

# Test with custom data
php artisan rev:test-webhook sale.created --data='{"custom":"data"}'
```

## Webhook Events

### 1. Sale Created (`sale.created`)

Triggered when a new sale is recorded in ERPREV.

**Payload Structure:**
```json
{
  "event": "sale.created",
  "data": {
    "sale_id": "SALE123",
    "product_id": "PRODUCT456",
    "quantity_sold": 2,
    "unit_price": 1500.00,
    "total_amount": 3000.00,
    "sale_date": "2025-10-15T10:30:00Z",
    "invoice_id": "INV-789",
    "location": "Online Store"
  }
}
```

**Processing:**
- Finds the corresponding book in Rhymes using the `product_id`
- Creates a wallet transaction for the author (70% of total amount)
- Updates author's wallet balance

### 2. Inventory Updated (`inventory.updated`)

Triggered when inventory levels change in ERPREV.

**Payload Structure:**
```json
{
  "event": "inventory.updated",
  "data": {
    "product_id": "PRODUCT456",
    "quantity_on_hand": 15,
    "warehouse_id": "WH001",
    "last_updated": "2025-10-15T10:30:00Z"
  }
}
```

**Processing:**
- Finds the corresponding book in Rhymes using the `product_id`
- Updates book status to 'stocked' if previously 'accepted' and quantity > 0
- Sends notifications to authors when status changes

### 3. Product Created (`product.created`)

Triggered when a new product is created in ERPREV.

**Payload Structure:**
```json
{
  "event": "product.created",
  "data": {
    "product_id": "PRODUCT789",
    "name": "New Book Title",
    "description": "A great new book",
    "price": 2500.00,
    "category": "Fiction",
    "created_at": "2025-10-15T10:30:00Z"
  }
}
```

**Processing:**
- Links newly created ERPREV products with books in Rhymes
- Updates book records with ERPREV product IDs

## Security

Webhooks are secured using HMAC signatures:

1. A shared secret is configured in `.env` as `ERPREV_WEBHOOK_SECRET`
2. Each webhook request includes an `X-ERPREV-Signature` header
3. The signature is generated using HMAC-SHA256 of the payload with the secret
4. Incoming webhooks are verified before processing

## Scheduling

The webhook system works in conjunction with scheduled sync jobs:

- `rev:sync-sales` - Runs every 30 minutes to sync sales data
- `rev:sync-inventory` - Runs every 6 hours to sync inventory data
- Webhooks provide real-time updates between scheduled syncs

## Testing

You can test the webhook implementation using the provided test command:

```bash
php artisan rev:test-webhook sale.created --debug
```

This sends a test webhook to your application and shows the response.

## Troubleshooting

### Webhook Not Received
1. Check that the webhook URL is correctly registered with ERPREV
2. Verify that your server is accessible from the internet
3. Check firewall settings

### Signature Verification Failed
1. Ensure `ERPREV_WEBHOOK_SECRET` is correctly configured
2. Verify that ERPREV is using the same secret for signing

### Processing Errors
1. Check Laravel logs for detailed error messages
2. Use the `--debug` flag with test commands for verbose output
3. Verify that the book/product exists in Rhymes before the webhook

## Extending

To add support for new webhook events:

1. Add the event handler method in `ERPRevWebhookController`
2. Update the switch statement in `handleWebhook` method
3. Register the webhook with ERPREV using the register command