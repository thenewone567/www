<?php
/**
 * Module Error Checker
 * Tests inventory, returns, expenses, and cycle count modules
 */

require_once 'bootstrap.php';

echo "<h1>🔍 Module Error Checker</h1>";

// Test modules
$modules = [
    'inventory' => 'InventoryController',
    'returns' => 'ReturnsController',
    'expenses' => 'ExpensesController',
    'cycle_count' => 'CycleCountController'
];

echo "<h2>🧪 Testing Module Controllers</h2>";

foreach ($modules as $module => $controller) {
    echo "<h3>Testing $module module ($controller)</h3>";

    // Check if controller file exists
    $controllerFile = "app/controllers/$controller.php";
    if (file_exists($controllerFile)) {
        echo "✅ Controller file exists: $controllerFile<br>";

        // Check for PHP syntax errors
        $output = [];
        $return = 0;
        exec("\"c:\\wamp64\\bin\\php\\php8.2.13\\php.exe\" -l \"$controllerFile\" 2>&1", $output, $return);

        if ($return === 0) {
            echo "✅ No syntax errors<br>";
        } else {
            echo "❌ Syntax errors found:<br>";
            foreach ($output as $line) {
                echo "  - $line<br>";
            }
        }

        // Try to instantiate controller
        try {
            if (class_exists($controller)) {
                echo "✅ Controller class exists<br>";

                // Test accessing the module via URL
                echo "🔗 <a href='http://localhost/$module' target='_blank'>Test $module module</a><br>";
            } else {
                echo "❌ Controller class not found<br>";
            }
        } catch (Exception $e) {
            echo "❌ Error instantiating controller: " . $e->getMessage() . "<br>";
        }

    } else {
        echo "❌ Controller file not found: $controllerFile<br>";
    }

    // Check if corresponding model exists
    $modelName = ucfirst($module);
    if ($module === 'cycle_count') {
        $modelName = 'CycleCount';
    }

    $modelFile = "app/models/$modelName.php";
    if (file_exists($modelFile)) {
        echo "✅ Model file exists: $modelFile<br>";

        // Check model syntax
        $output = [];
        $return = 0;
        exec("\"c:\\wamp64\\bin\\php\\php8.2.13\\php.exe\" -l \"$modelFile\" 2>&1", $output, $return);

        if ($return === 0) {
            echo "✅ Model syntax OK<br>";
        } else {
            echo "❌ Model syntax errors:<br>";
            foreach ($output as $line) {
                echo "  - $line<br>";
            }
        }
    } else {
        echo "⚠️ Model file not found: $modelFile<br>";
    }

    echo "<br>";
}

// Check database tables
echo "<h2>🗄️ Database Table Check</h2>";

try {
    $db = new Database();

    $expectedTables = [
        'inventory' => ['stock', 'stock_adjustments', 'stock_movements'],
        'returns' => ['sale_returns', 'purchase_returns'],
        'expenses' => ['expenses', 'expense_categories'],
        'cycle_count' => ['cycle_counts', 'cycle_count_items']
    ];

    // Get all existing tables
    $db->query("SHOW TABLES");
    $existingTables = array_column($db->resultSet(), "Tables_in_" . DB_NAME);

    foreach ($expectedTables as $module => $tables) {
        echo "<h3>$module module tables:</h3>";
        foreach ($tables as $table) {
            if (in_array($table, $existingTables)) {
                // Check table structure and data
                $db->query("SELECT COUNT(*) as count FROM `$table`");
                $count = $db->single()->count;
                echo "✅ $table - $count records<br>";

                // Show sample structure
                $db->query("DESCRIBE `$table`");
                $columns = $db->resultSet();
                echo "<details><summary>Table structure ($table)</summary>";
                foreach ($columns as $col) {
                    echo "- {$col->Field} ({$col->Type})<br>";
                }
                echo "</details>";
            } else {
                echo "❌ Missing table: $table<br>";
            }
        }
        echo "<br>";
    }

} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Check view files
echo "<h2>👁️ View Files Check</h2>";

$viewDirs = [
    'inventory' => 'app/views/inventory/',
    'returns' => 'app/views/returns/',
    'expenses' => 'app/views/expenses/',
    'cycle_count' => 'app/views/cycle_counts/'
];

foreach ($viewDirs as $module => $dir) {
    echo "<h3>$module views:</h3>";

    if (is_dir($dir)) {
        $files = array_filter(scandir($dir), function ($f) {
            return $f !== '.' && $f !== '..'; });
        echo "✅ View directory exists with " . count($files) . " files<br>";

        foreach ($files as $file) {
            echo "- $file<br>";
        }
    } else {
        echo "❌ View directory not found: $dir<br>";
    }
    echo "<br>";
}

// Test routing
echo "<h2>🛣️ Route Testing</h2>";

foreach (array_keys($modules) as $module) {
    echo "<a href='http://localhost/$module' target='_blank' style='background: #007bff; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; margin-right: 10px;'>Test $module</a>";
}

echo "<br><br>";

echo "<h2>📋 Summary & Recommendations</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px;'>";
echo "<h3>Common Issues to Check:</h3>";
echo "<ul>";
echo "<li>Missing controller files</li>";
echo "<li>Missing model files</li>";
echo "<li>Database table structure issues</li>";
echo "<li>Missing view files</li>";
echo "<li>Routing configuration problems</li>";
echo "<li>Deprecated code in controllers/models</li>";
echo "</ul>";
echo "</div>";

?>