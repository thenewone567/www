<?php
// Direct PDO connection test
echo "<h2>Direct PDO Connection Test</h2>";

try {
    // Test connection without database first
    $dsn1 = 'mysql:host=localhost;charset=utf8mb4';
    $pdo1 = new PDO($dsn1, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
    echo "✅ MySQL connection successful<br>";

    // Check available databases
    $stmt = $pdo1->query("SHOW DATABASES LIKE 'master%'");
    $databases = $stmt->fetchAll();
    echo "📋 Available databases:<br>";
    foreach ($databases as $db) {
        echo "- " . current((array) $db) . "<br>";
    }
    echo "<br>";

    // Test connection with database
    $dsn2 = 'mysql:host=localhost;dbname=master_hardware;charset=utf8mb4';
    $pdo2 = new PDO($dsn2, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
    echo "✅ Database connection successful<br>";

    // Check current database
    $stmt2 = $pdo2->query("SELECT DATABASE() as current_db");
    $result = $stmt2->fetch();
    echo "📋 Current database: <strong>" . ($result->current_db ?? 'NULL') . "</strong><br><br>";

    // Count records
    $stmt3 = $pdo2->query("SELECT COUNT(*) as count FROM products");
    $products = $stmt3->fetch();
    echo "📊 Products count: " . $products->count . "<br>";

    $stmt4 = $pdo2->query("SELECT COUNT(*) as count FROM sales");
    $sales = $stmt4->fetch();
    echo "📊 Sales count: " . $sales->count . "<br>";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>