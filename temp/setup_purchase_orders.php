<?php
// Setup Purchase Orders tables
require_once 'app/config.php';

echo "<h2>Setting up Purchase Orders System</h2>";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Connected to database successfully!<br>";

    // Read and execute the SQL file
    $sql = file_get_contents('create_purchase_orders_tables.sql');

    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    $successCount = 0;
    $errorCount = 0;

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                $successCount++;
                echo "✅ Executed: " . substr($statement, 0, 50) . "...<br>";
            } catch (PDOException $e) {
                $errorCount++;
                echo "❌ Error: " . $e->getMessage() . "<br>";
                echo "Statement: " . substr($statement, 0, 100) . "...<br>";
            }
        }
    }

    echo "<br><h3>Setup Summary:</h3>";
    echo "✅ Successful statements: $successCount<br>";
    echo "❌ Failed statements: $errorCount<br>";

    // Test the new tables
    echo "<br><h3>Testing Tables:</h3>";

    // Check purchase_orders table
    $stmt = $pdo->query("SHOW TABLES LIKE 'purchase_orders'");
    if ($stmt->rowCount() > 0) {
        echo "✅ purchase_orders table created<br>";

        $stmt = $pdo->query("SELECT COUNT(*) as count FROM purchase_orders");
        $count = $stmt->fetch()['count'];
        echo "📊 Purchase orders count: $count<br>";
    }

    // Check purchase_order_items table
    $stmt = $pdo->query("SHOW TABLES LIKE 'purchase_order_items'");
    if ($stmt->rowCount() > 0) {
        echo "✅ purchase_order_items table created<br>";
    }

    // Check suppliers table
    $stmt = $pdo->query("SHOW TABLES LIKE 'suppliers'");
    if ($stmt->rowCount() > 0) {
        echo "✅ suppliers table exists<br>";

        $stmt = $pdo->query("SELECT COUNT(*) as count FROM suppliers");
        $count = $stmt->fetch()['count'];
        echo "📊 Suppliers count: $count<br>";

        if ($count > 0) {
            echo "<h4>Sample Suppliers:</h4>";
            $stmt = $pdo->query("SELECT supplier_name, contact_info FROM suppliers LIMIT 3");
            $suppliers = $stmt->fetchAll();
            echo "<ul>";
            foreach ($suppliers as $supplier) {
                echo "<li>{$supplier['supplier_name']}</li>";
            }
            echo "</ul>";
        }
    }

    echo "<br>🎉 <strong>Purchase Orders system is now ready!</strong><br>";
    echo "<a href='test_purchase_system.php'>→ Test Purchase System</a><br>";
    echo "<a href='" . URLROOT . "/purchases'>→ Go to Purchases Page</a>";

} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}
?>