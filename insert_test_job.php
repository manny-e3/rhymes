<?php
require_once 'vendor/autoload.php';

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=rhymes_platform', 'root', '');
    echo "Connected successfully to MySQL\n";
    
    // Insert a test job
    $payload = json_encode([
        'displayName' => 'TestJob',
        'job' => 'TestJob',
        'data' => ['message' => 'Test']
    ]);
    
    $stmt = $pdo->prepare("INSERT INTO jobs (queue, payload, attempts, reserved_at, available_at, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([
        'default',
        $payload,
        0,
        null,
        time(),
        time()
    ]);
    
    if ($result) {
        echo "Job inserted successfully\n";
    } else {
        echo "Failed to insert job\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}