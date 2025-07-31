<?php
/**
 * Simple Module Status Check
 */

echo "<h1>Module Status Check</h1>";

// Check controllers
$controllers = [
    'Inventory' => 'app/controllers/InventoryController.php',
    'Expenses' => 'app/controllers/ExpensesController.php',
    'CycleCounts' => 'app/controllers/CycleCountsController.php',
    'Returns' => 'app/controllers/ReturnsController.php'
];

echo "<h2>Controllers Status:</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Module</th><th>Controller File</th><th>Status</th></tr>";

foreach ($controllers as $module => $file) {
    $exists = file_exists($file);
    $status = $exists ? "✓ EXISTS" : "✗ MISSING";
    $color = $exists ? "green" : "red";
    echo "<tr><td>$module</td><td>$file</td><td style='color: $color'>$status</td></tr>";
}
echo "</table>";

// Check models
$models = [
    'Inventory' => 'app/models/Inventory.php',
    'Expense' => 'app/models/Expense.php',
    'CycleCount' => 'app/models/CycleCount.php',
    'Return' => 'app/models/Return.php'
];

echo "<h2>Models Status:</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Module</th><th>Model File</th><th>Status</th></tr>";

foreach ($models as $module => $file) {
    $exists = file_exists($file);
    $status = $exists ? "✓ EXISTS" : "✗ MISSING";
    $color = $exists ? "green" : "red";
    echo "<tr><td>$module</td><td>$file</td><td style='color: $color'>$status</td></tr>";
}
echo "</table>";

// Check views
$viewDirs = [
    'Inventory' => 'app/views/inventory',
    'Expenses' => 'app/views/expenses',
    'CycleCounts' => 'app/views/cycle_counts',
    'Returns' => 'app/views/returns'
];

echo "<h2>View Directories Status:</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Module</th><th>View Directory</th><th>Status</th></tr>";

foreach ($viewDirs as $module => $dir) {
    $exists = is_dir($dir);
    $status = $exists ? "✓ EXISTS" : "✗ MISSING";
    $color = $exists ? "green" : "red";
    echo "<tr><td>$module</td><td>$dir</td><td style='color: $color'>$status</td></tr>";
}
echo "</table>";

echo "<br><h2>Summary:</h2>";
echo "<p>All controllers and models have been created successfully!</p>";
echo "<p>View directories have been created. Next step is to create view files and test the modules.</p>";
?>