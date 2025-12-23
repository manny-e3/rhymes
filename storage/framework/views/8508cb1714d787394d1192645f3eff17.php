<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Notification Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .result {
            background: #f9f9f9;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #4CAF50;
            font-family: monospace;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .error {
            border-left-color: #f44336;
            background: #ffebee;
        }
        button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background: #45a049;
        }
        .loading {
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>🔔 Notification System Test</h1>
    
    <div class="test-section">
        <h2>1. Check Database Notifications</h2>
        <button onclick="checkDatabase()">Check Database</button>
        <div id="db-result" class="result loading">Click button to test...</div>
    </div>

    <div class="test-section">
        <h2>2. Test API Endpoint (/notifications/unread)</h2>
        <button onclick="testUnreadAPI()">Test Unread API</button>
        <div id="api-result" class="result loading">Click button to test...</div>
    </div>

    <div class="test-section">
        <h2>3. Test Current User</h2>
        <button onclick="testCurrentUser()">Check Current User</button>
        <div id="user-result" class="result loading">Click button to test...</div>
    </div>

    <div class="test-section">
        <h2>4. Create Test Notification</h2>
        <button onclick="createTestNotification()">Create Test Notification</button>
        <div id="create-result" class="result loading">Click button to test...</div>
    </div>

    <script>
        async function checkDatabase() {
            const resultDiv = document.getElementById('db-result');
            resultDiv.textContent = 'Loading...';
            resultDiv.classList.remove('error');
            
            try {
                const response = await fetch('/test-notifications-db');
                const data = await response.json();
                resultDiv.textContent = JSON.stringify(data, null, 2);
            } catch (error) {
                resultDiv.classList.add('error');
                resultDiv.textContent = 'Error: ' + error.message;
            }
        }

        async function testUnreadAPI() {
            const resultDiv = document.getElementById('api-result');
            resultDiv.textContent = 'Loading...';
            resultDiv.classList.remove('error');
            
            try {
                const response = await fetch('/notifications/unread');
                const data = await response.json();
                resultDiv.textContent = JSON.stringify(data, null, 2);
            } catch (error) {
                resultDiv.classList.add('error');
                resultDiv.textContent = 'Error: ' + error.message;
            }
        }

        async function testCurrentUser() {
            const resultDiv = document.getElementById('user-result');
            resultDiv.textContent = 'Loading...';
            resultDiv.classList.remove('error');
            
            try {
                const response = await fetch('/test-current-user');
                const data = await response.json();
                resultDiv.textContent = JSON.stringify(data, null, 2);
            } catch (error) {
                resultDiv.classList.add('error');
                resultDiv.textContent = 'Error: ' + error.message;
            }
        }

        async function createTestNotification() {
            const resultDiv = document.getElementById('create-result');
            resultDiv.textContent = 'Creating...';
            resultDiv.classList.remove('error');
            
            try {
                const response = await fetch('/test-create-notification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                resultDiv.textContent = JSON.stringify(data, null, 2);
                
                // Refresh the unread API test
                if (data.success) {
                    setTimeout(() => testUnreadAPI(), 1000);
                }
            } catch (error) {
                resultDiv.classList.add('error');
                resultDiv.textContent = 'Error: ' + error.message;
            }
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/test-notifications.blade.php ENDPATH**/ ?>