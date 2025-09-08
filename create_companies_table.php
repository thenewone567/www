<?php
try {
    require_once 'bootstrap.php';
    $db = new Database();
    
    echo "Creating companies table...\n";
    
    $sql = "CREATE TABLE IF NOT EXISTS `companies` (
        `company_id` int(11) NOT NULL AUTO_INCREMENT,
        `company_name` varchar(255) NOT NULL,
        `company_code` varchar(50) NOT NULL UNIQUE,
        `address` text,
        `phone` varchar(20),
        `email` varchar(100),
        `tax_number` varchar(50),
        `logo_path` varchar(255),
        `is_active` tinyint(1) DEFAULT 1,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`company_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $db->query($sql);
    $result = $db->execute();
    
    if ($result) {
        echo "Companies table created successfully!\n";
        
        // Insert default company
        echo "Inserting default company...\n";
        $db->query("INSERT IGNORE INTO `companies` (`company_id`, `company_name`, `company_code`, `address`) 
                   VALUES (1, 'Hardware Store Chain', 'HSC', 'Head Office Address')");
        $insertResult = $db->execute();
        
        if ($insertResult) {
            echo "Default company inserted successfully!\n";
        } else {
            echo "Failed to insert default company\n";
        }
        
        // Verify the table
        $db->query('SELECT * FROM companies WHERE company_id = 1');
        $company = $db->single();
        if ($company) {
            echo "\nVerification - Company record exists:\n";
            print_r($company);
        }
        
    } else {
        echo "Failed to create companies table\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
