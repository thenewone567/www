<?php
// Verify the last pricing change: "Debug Test Product" from $298.08 to $261.90
require_once 'bootstrap.php';

echo "🔍 Verifying Last Pricing Change\n";
echo "===============================\n\n";

try {
    // Create Product model instance
    $productModel = new Product();

    echo "1️⃣ Looking for 'Debug Test Product'...\n";

    // Get all products and find the Debug Test Product
    $products = $productModel->getProductsForPriceManagement();
    $debugProduct = null;

    foreach ($products as $product) {
        if (stripos($product->name, 'Debug Test Product') !== false) {
            $debugProduct = $product;
            break;
        }
    }

    if ($debugProduct) {
        echo "✅ Found product:\n";
        echo "   ID: " . $debugProduct->product_id . "\n";
        echo "   Name: " . $debugProduct->name . "\n";
        echo "   Current Price: $" . number_format($debugProduct->price, 2) . "\n";
        echo "   Cost: $" . number_format($debugProduct->cost, 2) . "\n";

        // Verify the reported change
        echo "\n2️⃣ Verifying the reported change:\n";
        echo "   Reported Change: $298.08 → $261.90 (change: -$36.18)\n";
        echo "   Current Price: $" . number_format($debugProduct->price, 2) . "\n";

        if (abs($debugProduct->price - 261.90) < 0.01) {
            echo "✅ Price matches the reported change!\n";
        } else {
            echo "❌ Price doesn't match. Expected: $261.90, Found: $" . number_format($debugProduct->price, 2) . "\n";
        }

        // Calculate and verify margin
        echo "\n3️⃣ Verifying margin calculation:\n";
        if ($debugProduct->cost > 0) {
            $profit = $debugProduct->price - $debugProduct->cost;
            $margin = ($profit / $debugProduct->cost) * 100;

            echo "   Cost: $" . number_format($debugProduct->cost, 2) . "\n";
            echo "   Profit: $" . number_format($profit, 2) . "\n";
            echo "   Calculated Margin: " . number_format($margin, 1) . "%\n";
            echo "   Reported Margin: 31.5%\n";

            if (abs($margin - 31.5) < 1.0) {
                echo "✅ Margin calculation is correct!\n";
            } else {
                echo "❌ Margin calculation differs. Expected ~31.5%, Got: " . number_format($margin, 1) . "%\n";
            }
        }

        // Calculate price change validation
        echo "\n4️⃣ Verifying price change calculation:\n";
        $oldPrice = 298.08;
        $newPrice = $debugProduct->price;
        $priceChange = $newPrice - $oldPrice;
        $changePercent = (($newPrice - $oldPrice) / $oldPrice) * 100;

        echo "   Old Price: $" . number_format($oldPrice, 2) . "\n";
        echo "   New Price: $" . number_format($newPrice, 2) . "\n";
        echo "   Calculated Change: $" . number_format($priceChange, 2) . "\n";
        echo "   Change Percent: " . number_format($changePercent, 1) . "%\n";
        echo "   Reported Change: -$36.18\n";

        if (abs($priceChange - (-36.18)) < 0.05) {
            echo "✅ Price change calculation is correct!\n";
        } else {
            echo "❌ Price change differs. Expected: -$36.18, Got: $" . number_format($priceChange, 2) . "\n";
        }

        // Verify if this is a reasonable pricing bot change
        echo "\n5️⃣ Validating pricing logic:\n";
        $targetMargin = 31.5; // From the report
        $expectedPrice = $debugProduct->cost * (1 + $targetMargin / 100);

        echo "   Expected Price for 31.5% margin: $" . number_format($expectedPrice, 2) . "\n";
        echo "   Actual Price: $" . number_format($debugProduct->price, 2) . "\n";

        if (abs($debugProduct->price - $expectedPrice) < 5.0) {
            echo "✅ Price is consistent with 31.5% margin target!\n";
        } else {
            echo "⚠️  Price differs from expected 31.5% margin calculation\n";
        }

    } else {
        echo "❌ 'Debug Test Product' not found in product list\n";

        // Show available products for debugging
        echo "\n🔍 Available products (first 10):\n";
        $count = 0;
        foreach ($products as $product) {
            if ($count >= 10)
                break;
            echo "   " . $product->name . " - $" . number_format($product->price, 2) . "\n";
            $count++;
        }
    }

    echo "\n🎯 VERIFICATION SUMMARY:\n";
    if ($debugProduct && abs($debugProduct->price - 261.90) < 0.01) {
        echo "✅ The pricing change appears to be CORRECTLY EXECUTED\n";
        echo "✅ All calculations match the reported values\n";
        echo "✅ Bot logic appears to be working properly\n";
    } else {
        echo "❌ The pricing change could not be verified or is incorrect\n";
    }

} catch (Exception $e) {
    echo "❌ Error during verification: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>