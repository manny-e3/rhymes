<?php
require_once 'vendor/autoload.php';

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=rhymes_platform', 'root', '');
    echo "Connected successfully to MySQL\n";
    
    // Check jobs table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM jobs");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Pending jobs: " . $result['count'] . "\n";
    
    // Check failed jobs table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM failed_jobs");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Failed jobs: " . $result['count'] . "\n";
    
    // If there are pending jobs, try to process one
    if ($result['count'] > 0) {
        echo "Processing one job...\n";
        // We can't easily process jobs from raw PHP, so we'll just show the command to run
        echo "Run: php artisan queue:work --max-jobs=1 --once\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}