<?php
// Simple database check
try {
    $pdo = new PDO('mysql:host=localhost;dbname=master_hardware', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Database Connection Successful ===\n\n";

    // Check users table structure
    echo "Users table columns:\n";
    $stmt = $pdo->query("DESCRIBE users");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['Field']}: {$row['Type']}\n";
    }

    echo "\nCustomers table columns:\n";
    $stmt = $pdo->query("DESCRIBE customers");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['Field']}: {$row['Type']}\n";
    }

    echo "\nContractors table columns:\n";
    $stmt = $pdo->query("DESCRIBE contractors");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['Field']}: {$row['Type']}\n";
    }

    // Test counts
    echo "\nTable counts:\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  Users: {$result['count']}\n";

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM customers");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  Customers: {$result['count']}\n";

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM contractors");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  Contractors: {$result['count']}\n";

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>