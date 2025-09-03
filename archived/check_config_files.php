<?php
echo "🔍 Configuration File Analysis\n";
echo "==============================\n\n";

// Test what happens when we load bootstrap.php
require_once 'bootstrap.php';

echo "📋 Defined Constants After Bootstrap:\n";
echo "=====================================\n";

$dbConstants = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME', 'DB_CHARSET', 'DB_COLLATION'];
$appConstants = ['APP_NAME', 'APP_VERSION', 'APP_ENV', 'APPROOT', 'URLROOT', 'SITENAME'];

echo "\n🗄️  Database Constants:\n";
echo "----------------------\n";
foreach ($dbConstants as $constant) {
    if (defined($constant)) {
        echo "✅ $constant = " . constant($constant) . "\n";
    } else {
        echo "❌ $constant = NOT DEFINED\n";
    }
}

echo "\n🏢 Application Constants:\n";
echo "------------------------\n";
foreach ($appConstants as $constant) {
    if (defined($constant)) {
        echo "✅ $constant = " . constant($constant) . "\n";
    } else {
        echo "❌ $constant = NOT DEFINED\n";
    }
}

echo "\n📂 File Existence Check:\n";
echo "========================\n";
$configFiles = [
    'config/app.php',
    'config/database.php',
    'app/config.php'
];

foreach ($configFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "✅ $file EXISTS\n";
        echo "   Size: " . filesize($fullPath) . " bytes\n";
        echo "   Modified: " . date('Y-m-d H:i:s', filemtime($fullPath)) . "\n\n";
    } else {
        echo "❌ $file DOES NOT EXIST\n\n";
    }
}

// Test Database class instantiation
echo "🔌 Database Connection Test:\n";
echo "============================\n";

try {
    $db = new Database();
    echo "✅ Database class instantiated successfully\n";

    // Test a simple query to verify connection
    $db->query("SELECT 'test' as result");
    $db->execute();
    $result = $db->single();

    if ($result && $result->result === 'test') {
        echo "✅ Database connection working\n";
        echo "📊 Connected to database: " . DB_NAME . "\n";
    } else {
        echo "❌ Database query failed\n";
    }

} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}
?>