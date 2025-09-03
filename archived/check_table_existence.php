<?php
require_once 'bootstrap.php';

echo "🔍 CHECKING SUPPLIER_PRODUCTS TABLE EXISTENCE\n";
echo "=============================================\n\n";

try {
    $db = new Database();

    echo "1️⃣ Checking if supplier_products table exists...\n";

    $db->query("SHOW TABLES LIKE 'supplier_products'");
    $db->execute();
    $tableExists = $db->resultSet();

    if (count($tableExists) > 0) {
        echo "   ✅ supplier_products table exists\n";

        // Check if it has any data
        $db->query("SELECT COUNT(*) as total FROM supplier_products");
        $result = $db->execute();
        if ($result) {
            $count = $db->single();
            echo "   📊 Records in supplier_products: " . ($count ? $count->total : '0') . "\n";
        } else {
            echo "   ❌ Error querying supplier_products table\n";
        }
    } else {
        echo "   ❌ supplier_products table does NOT exist\n";
        echo "   💡 This explains why purchase bot finds no suppliers!\n";
    }

    echo "\n2️⃣ Checking what tables actually exist...\n";

    $db->query("SHOW TABLES");
    $db->execute();
    $allTables = $db->resultSet();

    $supplierTables = [];
    foreach ($allTables as $table) {
        $tableName = $table->{'Tables_in_' . DB_NAME} ?? '';
        if (stripos($tableName, 'supplier') !== false || stripos($tableName, 'product') !== false) {
            $supplierTables[] = $tableName;
        }
    }

    echo "   📋 Tables with 'supplier' or 'product' in name:\n";
    foreach ($supplierTables as $table) {
        echo "      • {$table}\n";
    }

    echo "\n3️⃣ Checking purchase bot supplier methods...\n";

    // Check what the BotController's getAllSuppliersForProduct method does
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'admin';

    $controller = new BotController();
    $reflection = new ReflectionClass($controller);

    // Check the getAllSuppliersForProduct method
    $getAllSuppliersMethod = $reflection->getMethod('getAllSuppliersForProduct');
    $getAllSuppliersMethod->setAccessible(true);

    echo "   🤖 Testing getAllSuppliersForProduct method...\n";
    $suppliers = $getAllSuppliersMethod->invoke($controller, 11);
    echo "   📊 Method returned " . count($suppliers) . " suppliers\n";

    if (count($suppliers) > 0) {
        echo "   📋 Supplier details:\n";
        foreach ($suppliers as $supplier) {
            echo "      • ID: {$supplier->supplier_id}, Name: {$supplier->supplier_name}, Price: $" . number_format($supplier->purchase_price, 2) . "\n";
        }
    }

    echo "\n4️⃣ The solution...\n";

    if (count($tableExists) == 0) {
        echo "   🔧 SOLUTION: Update purchase bot to use 'product_suppliers' table instead of 'supplier_products'\n";
        echo "   📋 The UI correctly uses 'product_suppliers' table\n";
        echo "   📋 Purchase bot should use the same table for consistency\n";
    } else {
        echo "   🤔 Both tables exist - need to check data synchronization\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>