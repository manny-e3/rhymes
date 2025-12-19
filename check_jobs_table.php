<?php
require_once 'vendor/autoload.php';

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=rhymes_platform', 'root', '');
    echo "Connected successfully to MySQL\n";
    
    // Check jobs table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM jobs");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Jobs in table: " . $result['count'] . "\n";
    
    if ($result['count'] > 0) {
        echo "Listing jobs:\n";
        $stmt = $pdo->query("SELECT * FROM jobs");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- ID: " . $row['id'] . ", Queue: " . $row['queue'] . ", Attempts: " . $row['attempts'] . "\n";
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}