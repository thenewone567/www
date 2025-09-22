<?php
/**
 * Setup Unique Tracking IDs System
 * This script runs the migration and sets up the unique ID system
 */

require_once 'bootstrap.php';
require_once APPROOT . DS . 'app' . DS . 'helpers' . DS . 'UniqueIdGenerator.php';

// Function to run SQL file
function runSqlFile($filePath)
{
    if (!file_exists($filePath)) {
        echo "Error: SQL file not found: $filePath\n";
        return false;
    }

    try {
        $db = new Database();
        $sql = file_get_contents($filePath);

        // Split SQL by delimiter and execute each statement
        $statements = explode(';', $sql);

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement) || substr($statement, 0, 2) === '--') {
                continue;
            }

            try {
                $db->query($statement);
                $db->execute();
            } catch (Exception $e) {
                // Some statements might fail if they already exist, that's ok
                if (
                    strpos($e->getMessage(), 'already exists') === false &&
                    strpos($e->getMessage(), 'Duplicate') === false
                ) {
                    echo "Warning: " . $e->getMessage() . "\n";
                }
            }
        }

        return true;

    } catch (Exception $e) {
        echo "Error running SQL file: " . $e->getMessage() . "\n";
        return false;
    }
}

echo "=== Setting up Unique Tracking IDs System ===\n\n";

// Step 1: Run the migration
echo "Step 1: Running database migration...\n";
$migrationFile = APPROOT . DS . 'database' . DS . 'migrations' . DS . 'add_unique_tracking_ids.sql';

if (runSqlFile($migrationFile)) {
    echo "✅ Migration completed successfully!\n\n";
} else {
    echo "❌ Migration failed!\n\n";
    exit(1);
}

// Step 2: Initialize the UniqueIdGenerator
echo "Step 2: Initializing Unique ID Generator...\n";
try {
    $generator = new UniqueIdGenerator();
    echo "✅ UniqueIdGenerator initialized successfully!\n\n";
} catch (Exception $e) {
    echo "❌ Failed to initialize UniqueIdGenerator: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Step 3: Assign unique IDs to existing records
echo "Step 3: Assigning unique IDs to existing records...\n";
try {
    $summary = $generator->assignUniqueIdsToExistingRecords();

    echo "Results:\n";
    echo "- Users updated: " . $summary['users'] . "\n";
    echo "- Customers updated: " . $summary['customers'] . "\n";
    echo "- Contractors updated: " . $summary['contractors'] . "\n";

    if (!empty($summary['errors'])) {
        echo "Errors:\n";
        foreach ($summary['errors'] as $error) {
            echo "- " . $error . "\n";
        }
    }

    echo "\n✅ Unique ID assignment completed!\n\n";

} catch (Exception $e) {
    echo "❌ Failed to assign unique IDs: " . $e->getMessage() . "\n\n";
}

// Step 4: Display statistics
echo "Step 4: System statistics...\n";
try {
    $stats = $generator->getUniqueIdStatistics();

    echo "Current system status:\n";
    echo "- Users: " . $stats['users_with_ids'] . "/" . $stats['total_users'] . " have unique IDs\n";
    echo "- Customers: " . $stats['customers_with_ids'] . "/" . $stats['total_customers'] . " have unique IDs\n";
    echo "- Contractors: " . $stats['contractors_with_ids'] . "/" . $stats['total_contractors'] . " have unique IDs\n\n";

} catch (Exception $e) {
    echo "❌ Failed to get statistics: " . $e->getMessage() . "\n\n";
}

// Step 5: Test unique ID generation
echo "Step 5: Testing unique ID generation...\n";
try {
    echo "Sample generated IDs:\n";
    echo "- User ID: " . $generator->generateUniqueId('user') . "\n";
    echo "- Customer ID: " . $generator->generateUniqueId('customer') . "\n";
    echo "- Contractor ID: " . $generator->generateUniqueId('contractor') . "\n\n";

    echo "✅ ID generation test completed!\n\n";

} catch (Exception $e) {
    echo "❌ ID generation test failed: " . $e->getMessage() . "\n\n";
}

// Step 6: Verify some existing records
echo "Step 6: Verifying existing records...\n";
try {
    $db = new Database();

    // Check users
    $db->query("SELECT unique_id, username FROM users WHERE unique_id IS NOT NULL LIMIT 3");
    $users = $db->resultSet();

    if (!empty($users)) {
        echo "Sample users with unique IDs:\n";
        foreach ($users as $user) {
            echo "- {$user->username}: {$user->unique_id}\n";
        }
    }

    // Check customers
    $db->query("SELECT unique_id, customer_name FROM customers WHERE unique_id IS NOT NULL LIMIT 3");
    $customers = $db->resultSet();

    if (!empty($customers)) {
        echo "Sample customers with unique IDs:\n";
        foreach ($customers as $customer) {
            echo "- {$customer->customer_name}: {$customer->unique_id}\n";
        }
    }

    // Check contractors
    $db->query("SELECT unique_id, contractor_name FROM contractors WHERE unique_id IS NOT NULL LIMIT 3");
    $contractors = $db->resultSet();

    if (!empty($contractors)) {
        echo "Sample contractors with unique IDs:\n";
        foreach ($contractors as $contractor) {
            echo "- {$contractor->contractor_name}: {$contractor->unique_id}\n";
        }
    }

    echo "\n✅ Record verification completed!\n\n";

} catch (Exception $e) {
    echo "❌ Record verification failed: " . $e->getMessage() . "\n\n";
}

echo "=== Setup Complete! ===\n";
echo "The unique tracking ID system is now ready to use.\n\n";

echo "Usage Examples:\n";
echo "1. Generate new ID: \$generator = new UniqueIdGenerator(); \$id = \$generator->generateUniqueId('user');\n";
echo "2. Look up entity: \$entity = \$generator->getEntityByUniqueId('US1625097600123');\n";
echo "3. Validate ID format: \$isValid = \$generator->validateUniqueIdFormat('US1625097600123');\n\n";

echo "ID Format: XX + 8-digit timestamp + 2-digit random = 12 characters\n";
echo "Prefixes: US=Users, CU=Customers, CO=Contractors\n\n";
