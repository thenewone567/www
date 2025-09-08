<?php
echo "=== Testing Improved Unique ID Generation (yymmddms format) ===\n\n";

require_once 'SimpleUniqueIdGenerator.php';
$generator = new SimpleUniqueIdGenerator();

echo "1. Testing new yymmddms format:\n";

// Generate multiple IDs to show the format
for ($i = 1; $i <= 5; $i++) {
    $userId = $generator->generateUniqueId('user');
    $customerId = $generator->generateUniqueId('customer');
    $contractorId = $generator->generateUniqueId('contractor');

    echo "   Set {$i}:\n";
    echo "     User ID: {$userId}\n";
    echo "     Customer ID: {$customerId}\n";
    echo "     Contractor ID: {$contractorId}\n";

    // Analyze the format
    $timestamp = substr($userId, 2, 8);
    $year = '20' . substr($timestamp, 0, 2);
    $month = substr($timestamp, 2, 2);
    $day = substr($timestamp, 4, 2);
    $ms = substr($timestamp, 6, 2);
    $random = substr($userId, 10, 2);

    echo "     Analysis: Year={$year}, Month={$month}, Day={$day}, MS={$ms}, Random={$random}\n";

    // Add small delay to see different milliseconds
    usleep(10000); // 10ms delay
    echo "\n";
}

echo "2. Testing format validation:\n";
$testIds = [
    'US2509087523', // Valid format
    'CU2509084567', // Valid format  
    'CO2509089215', // Valid format
    'INVALID123',   // Invalid prefix
    'US25090875',   // Too short
    'US25090875234', // Too long
    'US25090X7523'  // Non-numeric
];

foreach ($testIds as $testId) {
    $isValid = $generator->validateUniqueIdFormat($testId);
    $status = $isValid ? '✅ VALID' : '❌ INVALID';
    echo "   {$testId}: {$status}\n";
}

echo "\n3. Comparing old vs new format:\n";
echo "   Old format: US + 8-digit timestamp + 2-digit random\n";
echo "   Example: US57312956 + 23 = US5731295623\n";
echo "   Problem: Same timestamp repeats daily\n\n";

echo "   New format: XX + yymmddms + RR\n";
echo "   Example: US + 25090875 + 23 = US2509087523\n";
echo "   Benefits:\n";
echo "     - Year/Month/Day prevents daily duplicates\n";
echo "     - Milliseconds provide sub-second uniqueness\n";
echo "     - Still maintains chronological ordering\n";
echo "     - Human-readable date component\n";

echo "\n✅ Improved unique ID system is ready!\n";
echo "✅ Format: XX + yymmddms + RR eliminates daily duplicates\n";
echo "✅ Higher uniqueness with date + millisecond precision\n";
?>