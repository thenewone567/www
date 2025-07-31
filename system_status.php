<?php
/**
 * System Status Dashboard
 */

require_once __DIR__ . "/bootstrap.php";

echo "<h1>🎛️ System Status Dashboard</h1>";

// Database status
echo "<h2>🗄️ Database Status</h2>";
try {
    $db = new Database();
    $db->query("SELECT COUNT(*) as count FROM purchase_orders");
    $poCount = $db->single()->count;
    
    $db->query("SELECT COUNT(*) as count FROM products");
    $productCount = $db->single()->count;
    
    $db->query("SELECT COUNT(*) as count FROM suppliers");
    $supplierCount = $db->single()->count;
    
    echo "<div style=\"display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 20px 0;\">";
    echo "<div style=\"background: #e8f5e8; padding: 15px; border-radius: 8px; text-align: center;\">";
    echo "<h3>Purchase Orders</h3><h2>$poCount</h2>";
    echo "</div>";
    echo "<div style=\"background: #e8f0ff; padding: 15px; border-radius: 8px; text-align: center;\">";
    echo "<h3>Products</h3><h2>$productCount</h2>";
    echo "</div>";
    echo "<div style=\"background: #fff3e0; padding: 15px; border-radius: 8px; text-align: center;\">";
    echo "<h3>Suppliers</h3><h2>$supplierCount</h2>";
    echo "</div>";
    echo "</div>";
    
    echo "✅ Database connection successful<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// System info
echo "<h2>⚙️ System Information</h2>";
echo "<table border=\"1\" style=\"border-collapse: collapse; width: 100%; margin: 10px 0;\">";
echo "<tr><th style=\"padding: 8px; background: #f5f5f5;\">Component</th><th style=\"padding: 8px; background: #f5f5f5;\">Status</th></tr>";
echo "<tr><td style=\"padding: 8px;\">PHP Version</td><td style=\"padding: 8px;\">" . PHP_VERSION . "</td></tr>";
echo "<tr><td style=\"padding: 8px;\">App Version</td><td style=\"padding: 8px;\">" . (defined("APP_VERSION") ? APP_VERSION : "Unknown") . "</td></tr>";
echo "<tr><td style=\"padding: 8px;\">Environment</td><td style=\"padding: 8px;\">" . (defined("APP_ENV") ? APP_ENV : "Unknown") . "</td></tr>";
echo "<tr><td style=\"padding: 8px;\">Log File</td><td style=\"padding: 8px;\">" . (file_exists("storage/logs/app.log") ? "✅ Exists" : "❌ Not found") . "</td></tr>";
echo "<tr><td style=\"padding: 8px;\">Error Reporting</td><td style=\"padding: 8px;\">" . (error_reporting() > 0 ? "✅ Enabled" : "❌ Disabled") . "</td></tr>";
echo "</table>";

echo "<h2>🔗 Quick Actions</h2>";
echo "<div style=\"margin: 20px 0;\">";
echo "<a href=\"scripts/utilities/migrate.php\" style=\"background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-right: 10px;\">🔄 Run Migrations</a>";
echo "<a href=\"purchases\" style=\"background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-right: 10px;\">📦 Purchases</a>";
echo "<a href=\"products\" style=\"background: #ffc107; color: black; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-right: 10px;\">📦 Products</a>";
echo "</div>";

echo "<h2>📁 Project Structure</h2>";
echo "<pre style=\"background: #f8f9fa; padding: 15px; border-radius: 5px;\">";
echo "✅ app/controllers/ - " . count(glob("app/controllers/*.php")) . " files\n";
echo "✅ app/models/ - " . count(glob("app/models/*.php")) . " files\n";
echo "✅ app/views/ - " . count(glob("app/views/*.php")) . " files\n";
echo "✅ config/ - " . count(glob("config/*.php")) . " files\n";
echo "✅ database/migrations/ - " . count(glob("database/migrations/*.sql")) . " files\n";
echo "✅ scripts/setup/ - " . count(glob("scripts/setup/*")) . " files\n";
echo "✅ temp/ - " . count(glob("temp/*")) . " files\n";
echo "</pre>";
