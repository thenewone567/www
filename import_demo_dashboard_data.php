<?php
require_once 'app/config.php';
require_once 'app/Database.php';

$db = new Database();
$db->query("USE master_hardware");
$db->execute();

$sql = file_get_contents('demo_dashboard_data.sql');
foreach (explode(';', $sql) as $statement) {
    $statement = trim($statement);
    if ($statement) {
        try {
            $db->query($statement);
            $db->execute();
        } catch (Exception $e) {
            echo "<div style='color:red'>Error: " . $e->getMessage() . "</div>";
        }
    }
}
echo "<h2>Demo data imported successfully!</h2>";
?>