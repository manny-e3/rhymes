# 🔴 ERPREV Registration - API Format Issue

**Date**: November 27, 2025  
**Issue**: ERPREV API returning "No parameters given"  
**Status**: ⚠️ **NEEDS ERPREV API DOCUMENTATION**

---

## 🔍 **Problem Summary**

ERPREV is consistently returning:
```json
{
    "status": "0",
    "error": "No parameters given"
}
```

This means ERPREV is **not receiving the parameters** we're sending, regardless of the format we use.

---

## 🧪 **What We've Tried**

### Attempt 1: POST with JSON
```php
Http::withHeaders([
    'Authorization' => $this->getAuthHeader(),
    'Content-Type' => 'application/json',
])->post($url, $payload);
```
**Result**: ❌ "No parameters given"

### Attempt 2: POST with Form Data
```php
Http::withHeaders([
    'Authorization' => $this->getAuthHeader(),
])->asForm()->post($url, $payload);
```
**Result**: ❌ "A JSONObject text must begin with '{'"

### Attempt 3: GET with Query Parameters
```php
Http::withHeaders([
    'Authorization' => $this->getAuthHeader(),
])->get($url, $payload);
```
**Result**: ❌ "No parameters given"

---

## 📋 **Payload Being Sent**

```php
[
    'product_name' => 'Delectus excepteur',
    'product_code' => 'ISBN-1234560000',
    'category' => 'Books',
    'description' => 'Voluptatem minus non',
    'unit_price' => '180.00',
    'product_type' => 'digital',
    'author' => 'Charissa David',
    'genre' => 'Fiction'
]
```

---

## 🎯 **What We Need**

### From ERPREV Support (info@erprev.com):

1. **Correct HTTP Method**: GET or POST?
2. **Content-Type**: JSON, form data, or something else?
3. **Parameter Format**: How should parameters be sent?
4. **Example Request**: A working curl example would be perfect

### Example Questions to Ask:

```
Hi ERPREV Support,

We're integrating with your API to register products via the endpoint:
https://y301y.erprev.com/api/1.0/register-product/json/

We're getting "No parameters given" error. Could you please provide:

1. The correct HTTP method (GET/POST)?
2. The expected Content-Type header?
3. How parameters should be formatted?
4. A working curl example?

Thank you!
```

---

## 📚 **ERPREV API Documentation**

According to https://erprev.com/doc/api/:

The documentation should specify the correct format for `/register-product/json/` endpoint.

**Please check**:
- Request method (GET/POST/PUT)
- Headers required
- Parameter format (JSON body, form data, query string)
- Example request

---

## 🔧 **Temporary Workaround**

Until we get the correct API format, you can manually register books using:

```bash
# Register all unregistered books
php diagnose_erprev.php
```

Or contact ERPREV support to manually register the products and provide you with the Product IDs, which you can then update in the database:

```sql
UPDATE books 
SET rev_book_id = 'ERPREV_PRODUCT_ID' 
WHERE id = BOOK_ID;
```

---

## 📞 **Next Steps**

### Option 1: Contact ERPREV Support
1. Email: info@erprev.com
2. Ask for `/register-product/json/` endpoint documentation
3. Request a working curl example
4. Share the response with me

### Option 2: Check ERPREV Dashboard
1. Login to your ERPREV account
2. Check if there's API documentation
3. Look for example code or curl commands
4. Share any relevant documentation

### Option 3: Use ERPREV's Test Endpoint
If ERPREV has a test/sandbox environment:
1. Try the same request there
2. See if error messages are more detailed
3. Test different formats

---

## 🎓 **What We Know Works**

Other ERPREV endpoints ARE working:

### ✅ Test Connection (Works)
```php
Http::withHeaders([
    'Authorization' => $this->getAuthHeader(),
])->post($baseUrl . '/get-products-list/json', ['limit' => 1]);
```
**Result**: ✅ Success

### ✅ Get Products List (Works)
```php
Http::withHeaders([
    'Authorization' => $this->getAuthHeader(),
])->get($baseUrl . '/get-products-list/json/', $filters);
```
**Result**: ✅ Success

### ❌ Register Product (Doesn't Work)
```php
Http::withHeaders([
    'Authorization' => $this->getAuthHeader(),
])->???($baseUrl . '/register-product/json/', $payload);
```
**Result**: ❌ "No parameters given"

---

## 💡 **Possible Solutions**

### Solution 1: Different Endpoint
Maybe the endpoint is:
- `/register-product/` (without `/json/`)
- `/product/register/json/`
- `/add-product/json/`

### Solution 2: Different Parameter Names
Maybe ERPREV expects:
- `name` instead of `product_name`
- `code` instead of `product_code`
- etc.

### Solution 3: Nested Parameters
Maybe parameters need to be nested:
```json
{
    "product": {
        "name": "...",
        "code": "..."
    }
}
```

### Solution 4: Different Authentication
Maybe this endpoint needs:
- Different auth header format
- API key in parameters
- Different credentials

---

## 📊 **Comparison with Working Endpoints**

| Endpoint | Method | Format | Status |
|----------|--------|--------|--------|
| `/get-products-list/json` | POST | JSON | ✅ Works |
| `/get-products-list/json/` | GET | Query | ✅ Works |
| `/register-product/json/` | POST | JSON | ❌ Fails |
| `/register-product/json/` | POST | Form | ❌ Fails |
| `/register-product/json/` | GET | Query | ❌ Fails |

---

## 🎯 **Recommendation**

**Contact ERPREV support** and ask for the correct format for the `/register-product/json/` endpoint.

Once we have the correct format, the fix will take < 5 minutes to implement.

---

## 📝 **Information to Provide to ERPREV**

When contacting support, provide:

1. **Your Account**: y301y.erprev.com
2. **Endpoint**: `/api/1.0/register-product/json/`
3. **Error**: "No parameters given"
4. **What you've tried**: POST JSON, POST form data, GET query params
5. **Request**: Working curl example or documentation

---

## ✅ **Once We Get the Correct Format**

I'll update the code immediately and the registration will work perfectly!

---

**Status**: Waiting for ERPREV API documentation  
**Blocker**: Unknown parameter format for `/register-product/json/`  
**Action Required**: Contact ERPREV support (info@erprev.com)  
**ETA**: < 5 minutes after receiving correct format
