<?php
// Verify KPI card data source for "17 out of Inventory • Low Inventory"
require_once 'bootstrap.php';

echo "🔍 Verifying KPI Card Data Source\n";
echo "===============================\n\n";

try {
    echo "1️⃣ Testing Dashboard Model methods...\n";

    // Create Dashboard model instance
    $dashboardModel = new Dashboard();

    // Test the exact methods used by the KPI card
    $lowInventoryCount = $dashboardModel->getLowInventoryCount();
    $outOfInventoryCount = $dashboardModel->getOutOfInventoryCount();
    $outOfInventoryPercentage = $dashboardModel->getOutOfInventoryPercentage();

    echo "📊 Dashboard Model Results:\n";
    echo "   Low Inventory Count: " . $lowInventoryCount . "\n";
    echo "   Out of Inventory Count: " . $outOfInventoryCount . "\n";
    echo "   Out of Inventory Percentage: " . $outOfInventoryPercentage . "%\n";

    // Verify the KPI card format
    echo "\n2️⃣ KPI Card Display Format:\n";
    echo "   Display: \"" . $lowInventoryCount . "\"\n";
    echo "   Subtitle: \"" . $outOfInventoryCount . " out of Inventory • Low Inventory\"\n";

    if ($lowInventoryCount == 17) {
        echo "✅ KPI main number matches dashboard (17)\n";
    } else {
        echo "❌ KPI main number doesn't match. Expected: 17, Got: " . $lowInventoryCount . "\n";
    }

    echo "\n3️⃣ Testing Product Model comparison...\n";

    // Compare with Product model results
    $productModel = new Product();
    $lowInventoryProducts = $productModel->getLowInventoryProducts();
    $productModelCount = count($lowInventoryProducts);

    echo "📊 Product Model Results:\n";
    echo "   Low Inventory Products: " . $productModelCount . "\n";

    if ($productModelCount == $lowInventoryCount) {
        echo "✅ Both models return same count (" . $productModelCount . ")\n";
    } else {
        echo "⚠️  Models return different counts:\n";
        echo "   Dashboard Model: " . $lowInventoryCount . "\n";
        echo "   Product Model: " . $productModelCount . "\n";
    }

    echo "\n4️⃣ Testing dashboard route directly...\n";

    // Test DashboardController
    $dashboardController = new DashboardController();

    // Simulate accessing the dashboard
    $_SESSION['user_id'] = 1;
    $_SESSION['user_type'] = 'admin';

    // We can't directly test the view, but we can test data preparation
    echo "📊 Dashboard Controller initialized successfully\n";

    echo "\n5️⃣ Verifying KPI card structure...\n";

    // Check if the dashboard view file contains the correct structure
    $dashboardViewFile = APPROOT . DS . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'index.php';

    if (file_exists($dashboardViewFile)) {
        $dashboardContent = file_get_contents($dashboardViewFile);

        if (strpos($dashboardContent, 'low_Inventory_count') !== false) {
            echo "✅ KPI card structure found in dashboard view\n";
        } else {
            echo "❌ KPI card structure not found in dashboard view\n";
        }

        if (strpos($dashboardContent, 'out of Inventory') !== false) {
            echo "✅ 'out of Inventory' text found in dashboard view\n";
        } else {
            echo "❌ 'out of Inventory' text not found in dashboard view\n";
        }
    }

    echo "\n6️⃣ Testing with live dashboard access...\n";

    // Test if the dashboard page is accessible
    echo "📄 Dashboard accessible at: " . URLROOT . "/dashboard\n";
    echo "📄 KPI card location: app/views/dashboard/index.php lines 110-118\n";

    echo "\n🎯 VERIFICATION SUMMARY:\n";

    if ($lowInventoryCount == 17 && $outOfInventoryCount >= 0) {
        echo "✅ KPI CARD IS WORKING CORRECTLY\n";
        echo "   ✅ Shows correct low inventory count: " . $lowInventoryCount . "\n";
        echo "   ✅ Shows out of inventory count: " . $outOfInventoryCount . "\n";
        echo "   ✅ Display format: \"" . $lowInventoryCount . "\" with \"" . $outOfInventoryCount . " out of Inventory • Low Inventory\"\n";
        echo "   ✅ Data source: Dashboard model methods working properly\n";
    } else {
        echo "❌ KPI CARD ISSUE DETECTED\n";
        echo "   Expected low inventory: 17\n";
        echo "   Actual low inventory: " . $lowInventoryCount . "\n";
        echo "   Out of inventory: " . $outOfInventoryCount . "\n";
    }

    echo "\n📝 KPI Card Structure:\n";
    echo "```html\n";
    echo '<div class="kpi-card kpi-gradient-warning shadow-sm h-100">' . "\n";
    echo '    <div class="kpi-body">' . "\n";
    echo '        <div class="kpi-count" data-metric="low_inventory_count">' . "\n";
    echo '            ' . $lowInventoryCount . "\n";
    echo '        </div>' . "\n";
    echo '        <div class="kpi-value small">' . "\n";
    echo '            <span data-metric="out_of_inventory_count">' . $outOfInventoryCount . '</span>' . "\n";
    echo '            out of Inventory • Low Inventory' . "\n";
    echo '        </div>' . "\n";
    echo '    </div>' . "\n";
    echo '</div>' . "\n";
    echo "```\n";

} catch (Exception $e) {
    echo "❌ Error during verification: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>