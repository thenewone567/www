<?php
require_once 'bootstrap.php';

echo "🔧 TESTING PURCHASE BOT FIX FOR 0-STOCK\n";
echo "========================================\n\n";

try {
    // Bypass authentication for testing
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'admin';

    echo "1️⃣ Testing new getOutOfStockProducts() method...\n";

    $controller = new BotController();

    // Use reflection to access private methods
    $reflection = new ReflectionClass($controller);
    $getOutOfStockMethod = $reflection->getMethod('getOutOfStockProducts');
    $getOutOfStockMethod->setAccessible(true);

    $outOfStockProducts = $getOutOfStockMethod->invoke($controller);

    echo "   📊 getOutOfStockProducts() returned " . count($outOfStockProducts) . " products\n";

    if (count($outOfStockProducts) == 16) {
        echo "   ✅ Perfect! All 16 out-of-stock products are now included\n";

        echo "\n   📋 Out-of-stock products found:\n";
        foreach (array_slice($outOfStockProducts, 0, 5) as $product) {
            echo "      • {$product->product_name} (Stock: {$product->stock_quantity})\n";
        }
        if (count($outOfStockProducts) > 5) {
            echo "      • ... and " . (count($outOfStockProducts) - 5) . " more\n";
        }
    } else {
        echo "   ❌ Expected 16, got " . count($outOfStockProducts) . "\n";
    }

    echo "\n2️⃣ Testing modified executePurchaseBot() method...\n";

    $executePurchaseMethod = $reflection->getMethod('executePurchaseBot');
    $executePurchaseMethod->setAccessible(true);

    echo "   🤖 Executing purchase bot...\n";
    $result = $executePurchaseMethod->invoke($controller);

    echo "   📊 Purchase bot result:\n";
    echo "      • Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
    echo "      • Message: " . ($result['message'] ?? 'N/A') . "\n";
    echo "      • Action: " . ($result['action'] ?? 'N/A') . "\n";

    if (isset($result['product_name'])) {
        echo "      • Product selected: " . $result['product_name'] . "\n";
        echo "      • Current stock: " . $result['current_stock'] . "\n";
        echo "      • Order quantity: " . $result['quantity'] . "\n";

        if ($result['current_stock'] == 0) {
            echo "   ✅ SUCCESS! Purchase bot selected a 0-stock product!\n";
        } else {
            echo "   ℹ️  Purchase bot selected a low-stock product (not 0-stock)\n";
        }
    }

    echo "\n3️⃣ Multiple execution test (should prioritize 0-stock)...\n";

    $zeroStockSelected = 0;
    $totalRuns = 5;

    for ($i = 1; $i <= $totalRuns; $i++) {
        $result = $executePurchaseMethod->invoke($controller);
        if (isset($result['current_stock']) && $result['current_stock'] == 0) {
            $zeroStockSelected++;
        }
        echo "   Run {$i}: " . ($result['success'] ? '✅' : '❌') . " - ";
        if (isset($result['product_name'])) {
            echo $result['product_name'] . " (Stock: " . $result['current_stock'] . ")\n";
        } else {
            echo $result['message'] . "\n";
        }
    }

    echo "\n   📊 Summary: {$zeroStockSelected}/{$totalRuns} runs selected 0-stock products\n";

    if ($zeroStockSelected > 0) {
        echo "   ✅ SUCCESS! Purchase bot is now targeting 0-stock products!\n";
    } else {
        echo "   ⚠️  Purchase bot may not be prioritizing 0-stock products properly\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>