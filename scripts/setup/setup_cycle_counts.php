<?php
/**
 * Database setup script for cycle counts tables
 * Run this script to create the necessary tables for cycle counts functionality
 */

require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/Database.php';

try {
    $database = new Database();

    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/../../database/migrations/create_cycle_counts_tables.sql');

    if ($sql === false) {
        throw new Exception("Could not read SQL file");
    }

    // Split the SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    $successCount = 0;
    $errorCount = 0;

    echo "<h2>Creating Cycle Counts Tables</h2>\n";
    echo "<pre>\n";

    foreach ($statements as $statement) {
        if (empty($statement) || substr(trim($statement), 0, 2) === '--') {
            continue;
        }

        try {
            $database->query($statement);
            $database->execute();
            $successCount++;
            echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
        } catch (Exception $e) {
            $errorCount++;
            echo "✗ Error: " . $e->getMessage() . "\n";
            echo "  Statement: " . substr($statement, 0, 100) . "...\n";
        }
    }

    echo "\n=== Summary ===\n";
    echo "Successful statements: $successCount\n";
    echo "Failed statements: $errorCount\n";

    if ($errorCount === 0) {
        echo "\n🎉 All tables created successfully!\n";
        echo "You can now access: http://localhost/cycle_counts\n";
    } else {
        echo "\n⚠️  Some errors occurred. Please check the output above.\n";
    }

    echo "</pre>\n";

} catch (Exception $e) {
    echo "<h2>Error</h2>\n";
    echo "<pre>Database connection failed: " . $e->getMessage() . "</pre>\n";
    echo "<p>Please check your database configuration in app/config.php</p>\n";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Database Setup - Cycle Counts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
        }

        h2 {
            color: #333;
        }
    </style>
</head>

<body>
    <h1>Hardware Store - Database Setup</h1>
    <p><a href="../../index.php">← Back to Dashboard</a></p>

    <?php if (isset($successCount) && $errorCount === 0): ?>
        <div
            style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; border-radius: 5px; margin: 20px 0;">
            <strong>Success!</strong> Cycle counts tables have been created. You can now use the cycle counts functionality.
        </div>
        <p><a href="../../cycle_counts" class="btn">Go to Cycle Counts →</a></p>
    <?php endif; ?>

    <style>
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background: #0056b3;
        }
    </style>
</body>

</html>