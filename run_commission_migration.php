<?php
/**
 * Run Commission System Database Migration
 * This script creates the necessary tables for the customer references and commission system
 */

require_once 'bootstrap.php';

echo "Starting Commission System Migration...\n";

try {
    $db = new Database();
    
    // Read the migration file
    $migrationFile = __DIR__ . '/database/migrations/create_references_commission_system.sql';
    
    if (!file_exists($migrationFile)) {
        throw new Exception("Migration file not found: $migrationFile");
    }
    
    $sql = file_get_contents($migrationFile);
    
    if (!$sql) {
        throw new Exception("Failed to read migration file");
    }
    
    echo "Executing migration SQL...\n";
    
    // Split SQL by semicolons and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue; // Skip empty statements and comments
        }
        
        echo "Executing: " . substr($statement, 0, 50) . "...\n";
        
        $db->query($statement);
        if (!$db->execute()) {
            throw new Exception("Failed to execute statement: " . substr($statement, 0, 100));
        }
    }
    
    echo "\n✅ Commission System Migration completed successfully!\n";
    echo "\nTables created:\n";
    echo "- customer_references\n";
    echo "- commissions\n";
    echo "- commission_rates\n";
    echo "- commission_summary (view)\n";
    echo "\nYou can now use the References feature in the admin panel.\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
