<?php
// Assign unique IDs to existing records
$pdo = new PDO('mysql:host=localhost;dbname=master_hardware', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

require_once 'SimpleUniqueIdGenerator.php';
$generator = new SimpleUniqueIdGenerator();

echo "=== Assigning Unique IDs to Existing Records ===\n\n";

// Users without unique IDs
$stmt = $pdo->query("SELECT user_id FROM users WHERE unique_id IS NULL OR unique_id = ''");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
$userCount = 0;

foreach ($users as $user) {
    $uniqueId = $generator->generateUniqueId('user');
    $updateStmt = $pdo->prepare("UPDATE users SET unique_id = ? WHERE user_id = ?");
    $updateStmt->execute([$uniqueId, $user['user_id']]);
    $userCount++;
    echo "Assigned {$uniqueId} to user ID {$user['user_id']}\n";
}

// Customers without unique IDs
$stmt = $pdo->query("SELECT customer_id FROM customers WHERE unique_id IS NULL OR unique_id = ''");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
$customerCount = 0;

foreach ($customers as $customer) {
    $uniqueId = $generator->generateUniqueId('customer');
    $updateStmt = $pdo->prepare("UPDATE customers SET unique_id = ? WHERE customer_id = ?");
    $updateStmt->execute([$uniqueId, $customer['customer_id']]);
    $customerCount++;
    echo "Assigned {$uniqueId} to customer ID {$customer['customer_id']}\n";
}

// Contractors without unique IDs
$stmt = $pdo->query("SELECT contractor_id FROM contractors WHERE unique_id IS NULL OR unique_id = ''");
$contractors = $stmt->fetchAll(PDO::FETCH_ASSOC);
$contractorCount = 0;

foreach ($contractors as $contractor) {
    $uniqueId = $generator->generateUniqueId('contractor');
    $updateStmt = $pdo->prepare("UPDATE contractors SET unique_id = ? WHERE contractor_id = ?");
    $updateStmt->execute([$uniqueId, $contractor['contractor_id']]);
    $contractorCount++;
    echo "Assigned {$uniqueId} to contractor ID {$contractor['contractor_id']}\n";
}

echo "\n=== Assignment Complete ===\n";
echo "Users updated: {$userCount}\n";
echo "Customers updated: {$customerCount}\n";
echo "Contractors updated: {$contractorCount}\n";

// Final statistics
$stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN unique_id IS NOT NULL AND unique_id != '' THEN 1 ELSE 0 END) as with_ids FROM users");
$users = $stmt->fetch(PDO::FETCH_ASSOC);
echo "\nFinal Status:\n";
echo "  Users: {$users['with_ids']}/{$users['total']} have unique IDs\n";

$stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN unique_id IS NOT NULL AND unique_id != '' THEN 1 ELSE 0 END) as with_ids FROM customers");
$customers = $stmt->fetch(PDO::FETCH_ASSOC);
echo "  Customers: {$customers['with_ids']}/{$customers['total']} have unique IDs\n";

$stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN unique_id IS NOT NULL AND unique_id != '' THEN 1 ELSE 0 END) as with_ids FROM contractors");
$contractors = $stmt->fetch(PDO::FETCH_ASSOC);
echo "  Contractors: {$contractors['with_ids']}/{$contractors['total']} have unique IDs\n";

echo "\n✅ All existing records now have unique tracking IDs!\n";
?>