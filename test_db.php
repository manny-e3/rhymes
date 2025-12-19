<?php
require_once 'vendor/autoload.php';

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=rhymes_platform', 'root', '');
    echo "Connected successfully to MySQL\n";
    
    // Check if we have any admins
    $stmt = $pdo->query("SELECT u.id, u.name, u.email FROM users u JOIN model_has_roles mhr ON u.id = mhr.model_id JOIN roles r ON mhr.role_id = r.id WHERE r.name = 'admin'");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($admins) . " admins:\n";
    foreach ($admins as $admin) {
        echo "- ID: " . $admin['id'] . ", Name: " . $admin['name'] . ", Email: " . $admin['email'] . "\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}