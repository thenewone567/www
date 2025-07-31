<?php
/**
 * PHP Syntax and Formatting Check
 * Checks for common syntax issues that could cause formatting problems
 */

echo "<h1>PHP Syntax Check Results</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

$filesToCheck = [
    'app/views/inventory/index.php',
    'app/views/expenses/index.php',
    'app/views/cycle_counts/index.php',
    'app/models/Inventory.php',
    'app/models/Expense.php',
    'app/models/CycleCount.php',
    'app/controllers/InventoryController.php',
    'app/controllers/ExpensesController.php',
    'app/controllers/CycleCountsController.php'
];

echo "<h2>File Syntax Check</h2>";
echo "<table>";
echo "<tr><th>File</th><th>Status</th><th>Issues</th></tr>";

foreach ($filesToCheck as $file) {
    $fullPath = __DIR__ . '/' . $file;
    $issues = [];

    if (!file_exists($fullPath)) {
        echo "<tr><td>$file</td><td class='error'>MISSING</td><td>File not found</td></tr>";
        continue;
    }

    // Check for syntax errors
    $output = [];
    $returnCode = 0;
    exec("php -l \"$fullPath\" 2>&1", $output, $returnCode);

    if ($returnCode !== 0) {
        $issues[] = "Syntax error: " . implode(" ", $output);
    }

    // Check file contents
    $content = file_get_contents($fullPath);

    // Check for mixed line endings
    if (strpos($content, "\r\n") !== false && strpos($content, "\n") !== false) {
        $issues[] = "Mixed line endings detected";
    }

    // Check for BOM
    if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
        $issues[] = "UTF-8 BOM detected";
    }

    // Check for trailing whitespace on lines
    $lines = explode("\n", $content);
    $trailingWhitespaceLines = [];
    foreach ($lines as $lineNum => $line) {
        if (rtrim($line) !== $line && trim($line) !== '') {
            $trailingWhitespaceLines[] = $lineNum + 1;
        }
    }

    if (!empty($trailingWhitespaceLines)) {
        $issues[] = "Trailing whitespace on lines: " . implode(", ", array_slice($trailingWhitespaceLines, 0, 5));
    }

    // Check for non-printable characters (excluding common ones)
    if (preg_match('/[^\x20-\x7E\x09\x0A\x0D]/', $content)) {
        $issues[] = "Non-printable characters detected";
    }

    $status = empty($issues) ? "OK" : "ISSUES";
    $statusClass = empty($issues) ? "success" : "warning";
    $issueText = empty($issues) ? "None" : implode("<br>", $issues);

    echo "<tr><td>$file</td><td class='$statusClass'>$status</td><td>$issueText</td></tr>";
}

echo "</table>";

echo "<h2>Recommendations</h2>";
echo "<ul>";
echo "<li>Use consistent indentation (spaces or tabs, not mixed)</li>";
echo "<li>Ensure files use consistent line endings (LF or CRLF)</li>";
echo "<li>Remove trailing whitespace from lines</li>";
echo "<li>Save files in UTF-8 without BOM</li>";
echo "<li>Check for unclosed PHP tags or HTML elements</li>";
echo "</ul>";

echo "<h2>VS Code PHP Formatter Settings</h2>";
echo "<p>To fix PHP formatting issues in VS Code, try these settings:</p>";
echo "<pre>";
echo '{
  "php.format.rules.spaces_before_semicolon": false,
  "php.format.rules.spaces_after_semicolon": true,
  "php.format.rules.spaces_around_operators": true,
  "php.format.rules.spaces_around_concat": true
}';
echo "</pre>";
?>