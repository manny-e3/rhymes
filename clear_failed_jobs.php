<?php
require_once 'vendor/autoload.php';

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=rhymes_platform', 'root', '');
    echo "Connected successfully to MySQL\n";
    
    // Clear failed jobs
    $stmt = $pdo->query("DELETE FROM failed_jobs");
    echo "Failed jobs cleared\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}