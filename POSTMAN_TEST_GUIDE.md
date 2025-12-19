# 🔧 ERPREV API - Postman Test Guide

## 📋 **Postman Configuration**

### **Basic Setup**

**URL**: `https://y301y.erprev.com/api/1.0/register-product/json/`

**Method**: Try both POST and GET

---

## 🔐 **Authentication**

### **Headers Tab**

Add these headers:

```
Authorization: Basic [YOUR_BASE64_ENCODED_CREDENTIALS]
Content-Type: application/json
Accept: application/json
```

### **How to Get Authorization Header**

Your credentials need to be Base64 encoded in format: `API_KEY:API_SECRET`

**Option 1: Use Postman's Built-in Auth**
1. Go to **Authorization** tab
2. Select **Type**: Basic Auth
3. Username: `your_api_key_here`
4. Password: `your_api_secret_here`
5. Postman will auto-generate the header

**Option 2: Manual Base64 Encoding**

Use this online tool: https://www.base64encode.org/

Encode: `your_api_key:your_api_secret`

Then add header:
```
Authorization: Basic [encoded_string]
```

---

## 📝 **Test Data - JSON Body**

### **Test 1: POST with JSON Body**

**Method**: POST  
**URL**: `https://y301y.erprev.com/api/1.0/register-product/json/`

**Headers**:
```
Authorization: Basic [your_encoded_credentials]
Content-Type: application/json
Accept: application/json
```

**Body** (select "raw" and "JSON"):
```json
{
    "product_name": "Test Book from Postman",
    "product_code": "ISBN-POSTMAN-001",
    "category": "Books",
    "description": "This is a test book",
    "unit_price": "25.00",
    "product_type": "physical",
    "author": "Test Author",
    "genre": "Fiction"
}
```

---

### **Test 2: POST with Form Data**

**Method**: POST  
**URL**: `https://y301y.erprev.com/api/1.0/register-product/json/`

**Headers**:
```
Authorization: Basic [your_encoded_credentials]
```

**Body** (select "x-www-form-urlencoded"):
```
product_name: Test Book from Postman
product_code: ISBN-POSTMAN-002
category: Books
description: This is a test book
unit_price: 25.00
product_type: physical
author: Test Author
genre: Fiction
```

---

### **Test 3: GET with Query Parameters**

**Method**: GET  
**URL**: `https://y301y.erprev.com/api/1.0/register-product/json/`

**Headers**:
```
Authorization: Basic [your_encoded_credentials]
Accept: application/json
```

**Params** (in Postman's Params tab):
```
product_name: Test Book from Postman
product_code: ISBN-POSTMAN-003
category: Books
description: This is a test book
unit_price: 25.00
product_type: physical
author: Test Author
genre: Fiction
```

---

### **Test 4: POST without /json/ suffix**

**Method**: POST  
**URL**: `https://y301y.erprev.com/api/1.0/register-product/`

**Headers**:
```
Authorization: Basic [your_encoded_credentials]
Content-Type: application/json
```

**Body** (JSON):
```json
{
    "product_name": "Test Book from Postman",
    "product_code": "ISBN-POSTMAN-004",
    "category": "Books",
    "description": "This is a test book",
    "unit_price": "25.00",
    "product_type": "physical",
    "author": "Test Author",
    "genre": "Fiction"
}
```

---

## 🧪 **Additional Tests to Try**

### **Test 5: Minimal Data**

Try with just required fields:
```json
{
    "product_name": "Minimal Test",
    "product_code": "ISBN-MIN-001"
}
```

### **Test 6: Different Parameter Names**

Try alternative field names:
```json
{
    "name": "Test Book",
    "code": "ISBN-ALT-001",
    "price": "25.00"
}
```

### **Test 7: Nested Structure**

Try nested parameters:
```json
{
    "product": {
        "name": "Test Book",
        "code": "ISBN-NEST-001",
        "category": "Books",
        "price": "25.00"
    }
}
```

---

## 📊 **What to Look For**

### **Success Response** (Expected):
```json
{
    "status": "1",
    "product_id": "12345",
    "message": "Product registered successfully"
}
```

### **Error Response** (Current):
```json
{
    "status": "0",
    "error": "No parameters given"
}
```

### **Other Possible Responses**:
- Authentication error
- Missing required field
- Duplicate product code
- Invalid format

---

## 🎯 **Step-by-Step Postman Test**

1. **Open Postman**

2. **Create New Request**
   - Click "New" → "HTTP Request"

3. **Set Method and URL**
   - Method: POST
   - URL: `https://y301y.erprev.com/api/1.0/register-product/json/`

4. **Add Authorization**
   - Go to "Authorization" tab
   - Type: Basic Auth
   - Username: [Your ERPREV API Key]
   - Password: [Your ERPREV API Secret]

5. **Add Headers**
   - Go to "Headers" tab
   - Add: `Content-Type: application/json`
   - Add: `Accept: application/json`

6. **Add Body**
   - Go to "Body" tab
   - Select "raw"
   - Select "JSON" from dropdown
   - Paste the JSON test data

7. **Send Request**
   - Click "Send"
   - Check response

8. **Try Different Variations**
   - If it fails, try Test 2 (form data)
   - Then try Test 3 (GET)
   - Then try Test 4 (without /json/)

---

## 📝 **Sample Data for Real Book**

Use one of your actual books:

```json
{
    "product_name": "Delectus excepteur",
    "product_code": "ISBN-1234560000",
    "category": "Books",
    "description": "Voluptatem minus non",
    "unit_price": "180.00",
    "product_type": "digital",
    "author": "Charissa David",
    "genre": "Fiction"
}
```

---

## 🔍 **Debugging Tips**

1. **Check Response Status Code**
   - 200 = Success
   - 401 = Authentication failed
   - 400 = Bad request
   - 500 = Server error

2. **Check Response Headers**
   - Look for any hints about expected format

3. **Check Response Body**
   - Error messages might give clues

4. **Enable Postman Console**
   - View → Show Postman Console
   - See full request/response details

---

## 📞 **What to Share After Testing**

Once you test in Postman, share with me:

1. **Which test worked** (if any)
2. **The exact request** that succeeded:
   - Method (GET/POST)
   - Headers used
   - Body format (JSON/form/query)
   - Parameter names
3. **The successful response**
4. **Any error messages** if none worked

---

## 🎓 **Expected Outcome**

If one of these tests works, you'll see:
```json
{
    "status": "1",
    "product_id": "SOME_ID",
    "message": "Success"
}
```

Then I can update the code to match the working format!

---

## 💡 **Quick Reference**

**Your ERPREV Account**: y301y.erprev.com  
**API Base URL**: https://y301y.erprev.com/api/1.0  
**Endpoint**: /register-product/json/  
**Auth Type**: Basic Authentication  
**API Key**: [From your .env file]  
**API Secret**: [From your .env file]

---

**Good luck with testing! Share the results and I'll fix the code immediately!** 🚀
