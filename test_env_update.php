<?php
// Test script to check .env file update functionality

$envFile = __DIR__.'/.env';
echo "Testing .env file update...\n";
echo "File path: " . $envFile . "\n";

// Check if file exists and is writable
if (!file_exists($envFile)) {
    die("Environment file (.env) not found\n");
}

if (!is_writable($envFile)) {
    die("Environment file (.env) is not writable. Please check file permissions.\n");
}

echo "File exists and is writable.\n";

// Read file contents
$str = file_get_contents($envFile);

if ($str === false) {
    die("Failed to read environment file\n");
}

echo "File read successfully.\n";

// Test data to update
$data = [
    'APP_NAME' => '"Test App Name"',
    'APP_URL' => 'http://localhost/test'
];

// Update the file contents
foreach ($data as $key => $value) {
    // Escape special characters in the value
    $escapedValue = str_replace(['$', '"'], ['\$', '\"'], $value);
    
    // Check if the key already exists
    if (preg_match("/^{$key}=.*/m", $str)) {
        $str = preg_replace("/^{$key}=.*/m", "{$key}={$escapedValue}", $str);
        echo "Updated existing key: {$key}\n";
    } else {
        // If key doesn't exist, append it to the file
        $str .= "\n{$key}={$escapedValue}";
        echo "Added new key: {$key}\n";
    }
}

// Write back to file
$result = file_put_contents($envFile, $str);

if ($result === false) {
    die("Failed to write to environment file\n");
}

echo "File updated successfully!\n";
?>