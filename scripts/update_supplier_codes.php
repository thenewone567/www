<?php
// Script to update existing suppliers with auto-generated supplier codes
require_once '../bootstrap.php';

// Function to generate supplier code from company name and phone
function generateSupplierCode($companyName, $phone) {
    // Get first 4 letters from company name (remove spaces and special characters)
    $cleanName = preg_replace('/[^A-Za-z]/', '', $companyName);
    $nameCode = strtoupper(substr($cleanName, 0, 4));
    
    // Pad with X if less than 4 characters
    $nameCode = str_pad($nameCode, 4, 'X');
    
    // Get last 4 digits from phone number
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
    $phoneCode = substr($cleanPhone, -4);
    
    // Pad with 0 if less than 4 digits
    $phoneCode = str_pad($phoneCode, 4, '0', STR_PAD_LEFT);
    
    return $nameCode . $phoneCode;
}

try {
    $database = new Database();
    
    echo "Connecting to database...\n";
    
    // Get all suppliers without supplier codes
    $database->query('SELECT supplier_id, supplier_name, phone FROM suppliers WHERE supplier_code IS NULL');
    $database->execute();
    $suppliers = $database->resultSet();
    
    echo "Found " . count($suppliers) . " suppliers without codes\n";
    
    if (count($suppliers) > 0) {
        echo "Suppliers found:\n";
        foreach ($suppliers as $supplier) {
            echo "- ID: {$supplier->supplier_id}, Name: {$supplier->supplier_name}, Phone: {$supplier->phone}\n";
        }
    }
    
    foreach ($suppliers as $supplier) {
        $supplierCode = generateSupplierCode($supplier->supplier_name, $supplier->phone ?? '');
        
        // Check if supplier code already exists and make it unique
        $originalCode = $supplierCode;
        $counter = 1;
        while (true) {
            $database->query('SELECT supplier_id FROM suppliers WHERE supplier_code = :supplier_code');
            $database->bind(':supplier_code', $supplierCode);
            $existingCode = $database->single();
            
            if (!$existingCode) {
                break; // Code is unique
            }
            
            // If code exists, append a number
            $supplierCode = $originalCode . $counter;
            $counter++;
        }
        
        // Update the supplier with the generated code
        $database->query('UPDATE suppliers SET supplier_code = :supplier_code WHERE supplier_id = :supplier_id');
        $database->bind(':supplier_code', $supplierCode);
        $database->bind(':supplier_id', $supplier->supplier_id);
        
        if ($database->execute()) {
            echo "Updated supplier '{$supplier->supplier_name}' with code: {$supplierCode}\n";
        } else {
            echo "Failed to update supplier '{$supplier->supplier_name}'\n";
        }
    }
    
    echo "Supplier code update completed!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
