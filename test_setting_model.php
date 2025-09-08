<?php
require_once 'bootstrap.php';

echo "Testing Setting model...\n";

try {
    $setting = new Setting();
    
    // Test getSettings
    echo "Testing getSettings()...\n";
    $settings = $setting->getSettings();
    
    if ($settings) {
        echo "Settings found:\n";
        print_r($settings);
    } else {
        echo "No settings found or empty array\n";
    }
    
    // Test updateSettings
    echo "\nTesting updateSettings...\n";
    $testUpdate = $setting->updateSettings(['test_key' => 'test_value']);
    
    if ($testUpdate) {
        echo "Update successful!\n";
        
        // Verify the update
        $updated = $setting->getSettings();
        if (isset($updated['test_key'])) {
            echo "Verified: test_key = " . $updated['test_key'] . "\n";
        }
    } else {
        echo "Update failed!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
