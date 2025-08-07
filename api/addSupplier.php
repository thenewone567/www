<?php
// Simple API endpoint for adding suppliers
require_once '../bootstrap.php';

// Function to generate supplier code from company name and phone
function generateSupplierCode($companyName, $phone)
{
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

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $supplierName = trim($_POST['supplier_name'] ?? '');
        $contactPerson = trim($_POST['contact_person'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $gstNumber = trim($_POST['gst_number'] ?? '');

        if (empty($supplierName)) {
            http_response_code(400);
            echo json_encode(['error' => 'Please enter supplier name']);
            exit;
        }

        $database = new Database();

        // Check if supplier already exists
        $database->query('SELECT supplier_id FROM suppliers WHERE supplier_name = :supplier_name');
        $database->bind(':supplier_name', $supplierName);
        $existing = $database->single();

        if ($existing) {
            http_response_code(400);
            echo json_encode(['error' => 'Supplier name already exists']);
            exit;
        }

        // Check if GST number already exists (if provided)
        if (!empty($gstNumber)) {
            $database->query('SELECT supplier_id FROM suppliers WHERE gst_number = :gst_number');
            $database->bind(':gst_number', $gstNumber);
            $existingGst = $database->single();

            if ($existingGst) {
                http_response_code(400);
                echo json_encode(['error' => 'GST number already exists']);
                exit;
            }
        }

        // Auto-generate supplier code
        $supplierCode = generateSupplierCode($supplierName, $phone);

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

        // Add new supplier
        $database->query('INSERT INTO suppliers (supplier_name, supplier_code, contact_person, phone, email, address, gst_number) VALUES (:supplier_name, :supplier_code, :contact_person, :phone, :email, :address, :gst_number)');
        $database->bind(':supplier_name', $supplierName);
        $database->bind(':supplier_code', $supplierCode);
        $database->bind(':contact_person', $contactPerson);
        $database->bind(':phone', $phone);
        $database->bind(':email', $email);
        $database->bind(':address', $address);
        $database->bind(':gst_number', $gstNumber);

        if ($database->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Supplier added successfully',
                'supplier_code' => $supplierCode
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add supplier']);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>