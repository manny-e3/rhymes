<?php
// Simple database check without Laravel
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=rhymes_platform', 'root', '');
    echo "Connected successfully to MySQL\n";
    
    // Check all relevant tables
    $tables = ['jobs', 'failed_jobs'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM {$table}");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "{$table}: " . $result['count'] . " records\n";
            
            if ($result['count'] > 0 && $table === 'jobs') {
                echo "Sample jobs:\n";
                $stmt = $pdo->query("SELECT id, queue, attempts, created_at FROM {$table} LIMIT 5");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "  - ID: " . $row['id'] . ", Queue: " . $row['queue'] . ", Attempts: " . $row['attempts'] . ", Created: " . date('Y-m-d H:i:s', $row['created_at']) . "\n";
                }
            }
        } catch (PDOException $e) {
            echo "Error querying {$table}: " . $e->getMessage() . "\n";
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}