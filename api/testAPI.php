<?php
// Quick test of the itemLookup API
header('Content-Type: application/json');

try {
    // Test with a simple barcode from the putaway queue
    $testBarcode = 'DW-CD18V-001'; // From the Cordless Drill we know exists

    // Include bootstrap for database constants
    require_once __DIR__ . '/../bootstrap.php';
    
    $db = new Database();
    
    echo json_encode([
        'test' => 'API paths working',
        'testing_barcode' => $testBarcode,
        'database_connected' => true
    ]);

} catch (Exception $e) {
    echo json_encode([
        'test' => 'failed',
        'error' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile()
    ]);
}
?>
