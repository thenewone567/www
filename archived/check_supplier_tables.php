<?php
require_once 'bootstrap.php';

echo "🔍 INVESTIGATING SUPPLIER TABLES\n";
echo "=================================\n\n";

try {
    $db = new Database();

    echo "1️⃣ Checking product_suppliers table (used by UI)...\n";

    $db->query("SELECT COUNT(*) as total FROM product_suppliers");
    $db->execute();
    $psCount = $db->single();
    echo "   📊 Total records in product_suppliers: {$psCount->total}\n";

    // Check for one of the out-of-stock products
    $db->query("SELECT * FROM product_suppliers WHERE product_id = 11 LIMIT 5");
    $db->execute();
    $psRecords = $db->resultSet();
    echo "   📋 Records for product ID 11 (Minimal Test Product):\n";
    if (count($psRecords) > 0) {
        foreach ($psRecords as $record) {
            echo "      • Supplier ID: {$record->supplier_id}, Purchase Price: $" . number_format($record->purchase_price, 2) . ", Primary: " . ($record->is_primary ? 'Yes' : 'No') . "\n";
        }
    } else {
        echo "      • No records found\n";
    }

    echo "\n2️⃣ Checking supplier_products table (used by purchase bot)...\n";

    $db->query("SELECT COUNT(*) as total FROM supplier_products");
    $db->execute();
    $spCount = $db->single();
    echo "   📊 Total records in supplier_products: {$spCount->total}\n";

    // Check for the same product
    $db->query("SELECT * FROM supplier_products WHERE product_id = 11 LIMIT 5");
    $db->execute();
    $spRecords = $db->resultSet();
    echo "   📋 Records for product ID 11 (Minimal Test Product):\n";
    if (count($spRecords) > 0) {
        foreach ($spRecords as $record) {
            echo "      • Supplier ID: {$record->supplier_id}, Purchase Price: $" . number_format($record->purchase_price, 2) . ", Min Qty: " . ($record->min_order_quantity ?? 'N/A') . "\n";
        }
    } else {
        echo "      • No records found\n";
    }

    echo "\n3️⃣ Comparing table structures...\n";

    $db->query("DESCRIBE product_suppliers");
    $db->execute();
    $psColumns = $db->resultSet();
    echo "   📋 product_suppliers columns:\n";
    foreach ($psColumns as $col) {
        echo "      • {$col->Field}: {$col->Type}\n";
    }

    $db->query("DESCRIBE supplier_products");
    $db->execute();
    $spColumns = $db->resultSet();
    echo "\n   📋 supplier_products columns:\n";
    foreach ($spColumns as $col) {
        echo "      • {$col->Field}: {$col->Type}\n";
    }

    echo "\n4️⃣ Checking which table the purchase bot should use...\n";

    // Check if supplier_products has the data the purchase bot needs
    $db->query("
        SELECT COUNT(*) as count_with_price
        FROM supplier_products
        WHERE purchase_price > 0
    ");
    $db->execute();
    $priceCount = $db->single();
    echo "   📊 supplier_products records with valid prices: {$priceCount->count_with_price}\n";

    // Check product_suppliers
    $db->query("
        SELECT COUNT(*) as count_with_price
        FROM product_suppliers
        WHERE purchase_price > 0
    ");
    $db->execute();
    $psPriceCount = $db->single();
    echo "   📊 product_suppliers records with valid prices: {$psPriceCount->count_with_price}\n";

    echo "\n5️⃣ Testing purchase bot with correct table...\n";

    // Check if the purchase bot's getAllSuppliersForProduct method uses the right table
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'admin';

    $controller = new BotController();
    $reflection = new ReflectionClass($controller);
    $getAllSuppliersMethod = $reflection->getMethod('getAllSuppliersForProduct');
    $getAllSuppliersMethod->setAccessible(true);

    $suppliers = $getAllSuppliersMethod->invoke($controller, 11);
    echo "   📊 getAllSuppliersForProduct(11) returned: " . count($suppliers) . " suppliers\n";

    if (count($suppliers) > 0) {
        echo "   ✅ Purchase bot CAN find suppliers for this product!\n";
        echo "   💡 The issue might be in the table being used\n";
    } else {
        echo "   ❌ Purchase bot still can't find suppliers\n";
        echo "   🔧 Need to update purchase bot to use product_suppliers table\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>