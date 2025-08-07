<?php
// Direct PDO test
require_once 'config/app.php';
require_once 'config/database.php';

try {
    echo "Direct PDO Test\n";
    echo "===============\n\n";
    
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
    
    echo "✓ Direct PDO connection successful\n";
    
    // Create test product
    $stmt = $pdo->prepare("INSERT INTO products (product_name, sku) VALUES (?, ?)");
    $stmt->execute(['PDO Test Product', 'PDO001']);
    $productId = $pdo->lastInsertId();
    echo "✓ Created product ID: $productId\n";
    
    // Add stock
    $stmt = $pdo->prepare("INSERT INTO stock (product_id, quantity, batch_number) VALUES (?, ?, ?)");
    $stmt->execute([$productId, 20, 'PDO-BATCH-001']);
    echo "✓ Added stock record\n";
    
    // Check stock
    $stmt = $pdo->prepare("SELECT * FROM stock WHERE product_id = ?");
    $stmt->execute([$productId]);
    $stockRecords = $stmt->fetchAll();
    
    echo "Stock records found: " . count($stockRecords) . "\n";
    foreach ($stockRecords as $record) {
        echo "- Stock ID: " . $record->stock_id . ", Quantity: " . $record->quantity . "\n";
    }
    
    // Check total
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) as total_stock FROM stock WHERE product_id = ?");
    $stmt->execute([$productId]);
    $totalStock = $stmt->fetch();
    echo "Total stock: " . $totalStock->total_stock . "\n";
    
    // Test adjustment with our custom model
    require_once 'app/Database.php';
    require_once 'app/models/Inventory.php';
    
    echo "\nTesting custom Inventory model...\n";
    $inventoryModel = new Inventory();
    $result = $inventoryModel->adjustStockSimple([
        'product_id' => $productId,
        'quantity_change' => 5,
        'reason' => 'PDO test adjustment'
    ]);
    
    echo "Adjustment result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    // Check stock again with direct PDO
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) as total_stock FROM stock WHERE product_id = ?");
    $stmt->execute([$productId]);
    $newTotalStock = $stmt->fetch();
    echo "New total stock: " . $newTotalStock->total_stock . "\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
