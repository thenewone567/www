<?php
require_once 'bootstrap.php';

echo "=== Testing Unique ID System ===\n\n";

try {
    // Test UniqueIdGenerator
    require_once 'app/helpers/UniqueIdGenerator.php';
    $generator = new UniqueIdGenerator();

    echo "Testing ID generation:\n";

    // Generate test IDs
    $userId = $generator->generateUniqueId('user');
    echo "  User ID: {$userId}\n";

    $customerId = $generator->generateUniqueId('customer');
    echo "  Customer ID: {$customerId}\n";

    $contractorId = $generator->generateUniqueId('contractor');
    echo "  Contractor ID: {$contractorId}\n";

    // Test validation
    echo "\nTesting ID validation:\n";
    echo "  US format valid: " . ($generator->validateUniqueIdFormat($userId) ? 'YES' : 'NO') . "\n";
    echo "  CU format valid: " . ($generator->validateUniqueIdFormat($customerId) ? 'YES' : 'NO') . "\n";
    echo "  CO format valid: " . ($generator->validateUniqueIdFormat($contractorId) ? 'YES' : 'NO') . "\n";
    echo "  Invalid format: " . ($generator->validateUniqueIdFormat('INVALID123') ? 'YES' : 'NO') . "\n";

    // Test statistics (using direct PDO)
    echo "\nDirect database statistics:\n";
    $pdo = new PDO('mysql:host=localhost;dbname=master_hardware', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN unique_id IS NOT NULL AND unique_id != '' THEN 1 ELSE 0 END) as with_ids FROM users");
    $users = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  Users: {$users['with_ids']}/{$users['total']} have unique IDs\n";

    $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN unique_id IS NOT NULL AND unique_id != '' THEN 1 ELSE 0 END) as with_ids FROM customers");
    $customers = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  Customers: {$customers['with_ids']}/{$customers['total']} have unique IDs\n";

    $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN unique_id IS NOT NULL AND unique_id != '' THEN 1 ELSE 0 END) as with_ids FROM contractors");
    $contractors = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  Contractors: {$contractors['with_ids']}/{$contractors['total']} have unique IDs\n";

    echo "\n✅ Unique ID system is working!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>