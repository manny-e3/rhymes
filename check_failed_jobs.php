<?php
require_once 'vendor/autoload.php';

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=rhymes_platform', 'root', '');
    echo "Connected successfully to MySQL\n";
    
    // Check failed jobs
    $stmt = $pdo->query("SELECT id, uuid, connection, queue, payload, exception FROM failed_jobs ORDER BY failed_at DESC LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . "\n";
        echo "Connection: " . $row['connection'] . "\n";
        echo "Queue: " . $row['queue'] . "\n";
        echo "Payload preview: " . substr($row['payload'], 0, 100) . "\n";
        echo "Exception preview: " . substr($row['exception'], 0, 200) . "\n";
        echo "---\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}