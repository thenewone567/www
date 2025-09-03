<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=master_hardware', 'root', '');
    $stmt = $pdo->query('DESCRIBE users');
    echo "Users table structure:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']} ({$row['Type']})\n";
    }
    
    echo "\nSample users:\n";
    $stmt = $pdo->query('SELECT * FROM users LIMIT 3');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
