<?php
// Use Product model methods to check actual low inventory
require_once 'bootstrap.php';

echo "🛒 Accurate Low Inventory & Purchase Bot Check\n";
echo "============================================\n\n";

try {
    $productModel = new Product();

    echo "1️⃣ Using getLowInventoryProducts() method...\n";

    // Use the actual low inventory method
    $lowInventoryProducts = $productModel->getLowInventoryProducts();

    if ($lowInventoryProducts && count($lowInventoryProducts) > 0) {
        echo "✅ Found " . count($lowInventoryProducts) . " low inventory products:\n\n";

        foreach ($lowInventoryProducts as $product) {
            $qty = isset($product->quantity) ? $product->quantity : 'N/A';
            $minStock = isset($product->min_stock_level) ? $product->min_stock_level : 'N/A';
            $name = isset($product->name) ? $product->name : (isset($product->product_name) ? $product->product_name : 'Unknown');

            echo "   📦 " . $name . " - Qty: " . $qty . " (Min: " . $minStock . ")\n";
        }

        echo "\n📊 Total Low Inventory Count: " . count($lowInventoryProducts) . "\n";

        if (count($lowInventoryProducts) >= 17) {
            echo "✅ Confirmed: Dashboard showing '" . count($lowInventoryProducts) . " out of Inventory • Low Inventory' is accurate\n";
        }

    } else {
        echo "❌ No low inventory products found using getLowInventoryProducts()\n";

        // Let's try getCurrentInventorySummary
        echo "\n2️⃣ Trying getCurrentInventorySummary()...\n";
        $inventorySummary = $productModel->getCurrentInventorySummary();

        if ($inventorySummary) {
            echo "📊 Inventory Summary:\n";
            print_r($inventorySummary);
        }
    }

    echo "\n3️⃣ Checking BotController purchase bot configuration...\n";

    $botController = new BotController();

    // Check for purchase methods
    $methods = get_class_methods($botController);
    $purchaseMethods = array_filter($methods, function ($method) {
        return stripos($method, 'purchase') !== false;
    });

    echo "🤖 Purchase methods found: " . (count($purchaseMethods) > 0 ? implode(', ', $purchaseMethods) : 'None') . "\n";

    // Check bot configuration
    try {
        $reflection = new ReflectionClass($botController);
        $property = $reflection->getProperty('bots');
        $property->setAccessible(true);
        $bots = $property->getValue($botController);

        echo "\n🤖 Available bots: " . implode(', ', array_keys($bots)) . "\n";

        if (isset($bots['purchase_bot'])) {
            $purchaseBot = $bots['purchase_bot'];
            echo "\n✅ Purchase Bot Configuration:\n";
            echo "   Name: " . $purchaseBot['name'] . "\n";
            echo "   Description: " . $purchaseBot['description'] . "\n";
            echo "   Active: " . ($purchaseBot['active'] ? 'Yes' : 'No') . "\n";
            echo "   Interval: " . $purchaseBot['interval'] . " seconds\n";
        } else {
            echo "\n❌ Purchase bot not found in bot configuration\n";
        }

    } catch (Exception $e) {
        echo "❌ Error accessing bot configuration: " . $e->getMessage() . "\n";
    }

    echo "\n4️⃣ Testing purchase bot execution (if available)...\n";

    if (count($purchaseMethods) > 0) {
        echo "🧪 Purchase methods available for testing\n";

        // Try to execute purchase bot if method exists
        $purchaseMethod = $purchaseMethods[0]; // Get first purchase method
        echo "   Testing method: " . $purchaseMethod . "\n";

        try {
            $methodReflection = new ReflectionMethod($botController, $purchaseMethod);

            if ($methodReflection->isPublic()) {
                echo "   Method is public - attempting execution...\n";
                $result = $botController->$purchaseMethod();
                echo "   Result: " . print_r($result, true) . "\n";
            } else {
                echo "   Method is private/protected - cannot test directly\n";
            }

        } catch (Exception $e) {
            echo "   ❌ Error testing purchase method: " . $e->getMessage() . "\n";
        }

    } else {
        echo "❌ No purchase methods available to test\n";
    }

    echo "\n🎯 CONCLUSION:\n";

    if (isset($lowInventoryProducts) && count($lowInventoryProducts) >= 17) {
        echo "✅ CONFIRMED: There are " . count($lowInventoryProducts) . " products with low inventory\n";

        if (count($purchaseMethods) === 0) {
            echo "❌ PROBLEM: No purchase bot implementation found\n";
            echo "🔧 SOLUTION NEEDED: Implement purchase bot logic in BotController\n";
        } elseif (!isset($bots['purchase_bot']) || !$bots['purchase_bot']['active']) {
            echo "❌ PROBLEM: Purchase bot not active or configured\n";
            echo "🔧 SOLUTION NEEDED: Activate purchase bot or fix configuration\n";
        } else {
            echo "⚠️  PROBLEM: Purchase bot exists but not working properly\n";
            echo "🔧 SOLUTION NEEDED: Debug purchase bot execution logic\n";
        }
    } else {
        echo "🤔 UNCLEAR: Dashboard may be showing different inventory data\n";
        echo "   Possible cache or different inventory calculation method\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>