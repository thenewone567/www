<?php
// Fix deprecated FILTER_SANITIZE_STRING constant across all controllers

$controllerFiles = [
    'app/controllers/CustomersController.php',
    'app/controllers/UsersController.php',
    'app/controllers/SuppliersController.php',
    'app/controllers/StockController.php',
    'app/controllers/SettingsController.php',
    'app/controllers/ReturnsController.php',
    'app/controllers/ReportsController.php'
];

echo "<h2>Fixing deprecated FILTER_SANITIZE_STRING constant</h2>";

$totalFiles = 0;
$totalReplacements = 0;

foreach ($controllerFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $originalContent = $content;

        // Replace FILTER_SANITIZE_STRING with FILTER_SANITIZE_FULL_SPECIAL_CHARS
        $content = str_replace('FILTER_SANITIZE_STRING', 'FILTER_SANITIZE_FULL_SPECIAL_CHARS', $content);

        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            $replacements = substr_count($originalContent, 'FILTER_SANITIZE_STRING');
            $totalReplacements += $replacements;
            $totalFiles++;
            echo "✅ Fixed $file - $replacements replacements<br>";
        } else {
            echo "⏭️ No changes needed in $file<br>";
        }
    } else {
        echo "❌ File not found: $file<br>";
    }
}

echo "<br><strong>Summary:</strong><br>";
echo "📁 Files updated: $totalFiles<br>";
echo "🔧 Total replacements: $totalReplacements<br>";
echo "<br>✅ All deprecated FILTER_SANITIZE_STRING constants have been fixed!";
?>