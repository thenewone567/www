<?php
require_once 'bootstrap.php';

try {
    echo "<h3>Check for Stuck Off-loading POs</h3>";

    $db = new Database();

    // Find POs stuck in off-loading status
    echo "<h4>1. Finding Stuck Off-loading POs</h4>";
    $db->query("
        SELECT 
            purchase_id,
            po_number, 
            status, 
            dock_arrival_time,
            dock_location_id,
            TIMESTAMPDIFF(MINUTE, dock_arrival_time, NOW()) as minutes_elapsed,
            TIME_FORMAT(TIMEDIFF(NOW(), dock_arrival_time), '%i:%s') as time_elapsed_formatted
        FROM purchases 
        WHERE status = 'pending_arrival' 
        AND dock_arrival_time IS NOT NULL
        ORDER BY dock_arrival_time ASC
    ");
    $db->execute();
    $stuckPOs = $db->resultSet();

    if (!empty($stuckPOs)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th style='padding: 8px;'>PO Number</th>";
        echo "<th style='padding: 8px;'>Status</th>";
        echo "<th style='padding: 8px;'>Started At</th>";
        echo "<th style='padding: 8px;'>Elapsed Time</th>";
        echo "<th style='padding: 8px;'>Minutes</th>";
        echo "<th style='padding: 8px;'>Action</th>";
        echo "</tr>";

        foreach ($stuckPOs as $po) {
            $isStuck = $po->minutes_elapsed > 10; // Consider stuck if longer than 10 minutes
            $rowColor = $isStuck ? 'style="background-color: #fff3cd;"' : '';

            echo "<tr {$rowColor}>";
            echo "<td style='padding: 8px; font-weight: bold;'>{$po->po_number}</td>";
            echo "<td style='padding: 8px;'><span style='background-color: #ffc107; color: black; padding: 2px 6px; border-radius: 3px;'>Off-loading</span></td>";
            echo "<td style='padding: 8px;'>{$po->dock_arrival_time}</td>";
            echo "<td style='padding: 8px; font-family: monospace; font-weight: bold;'>{$po->time_elapsed_formatted}</td>";
            echo "<td style='padding: 8px;'>{$po->minutes_elapsed} min</td>";

            if ($isStuck) {
                echo "<td style='padding: 8px;'>";
                echo "<span style='color: orange;'>⚠️ Potentially Stuck</span><br>";
                echo "<small>Consider completing manually</small>";
                echo "</td>";
            } else {
                echo "<td style='padding: 8px;'><span style='color: green;'>✅ Normal</span></td>";
            }

            echo "</tr>";
        }
        echo "</table>";

        echo "<h4>2. Recommendations</h4>";
        $stuckCount = count(array_filter($stuckPOs, function ($po) {
            return $po->minutes_elapsed > 10; }));

        if ($stuckCount > 0) {
            echo "<div style='color: orange; background-color: #fff3cd; padding: 15px; border-radius: 5px;'>";
            echo "<h5>⚠️ Found {$stuckCount} Potentially Stuck PO(s)</h5>";
            echo "<p><strong>Reasons for stuck status:</strong></p>";
            echo "<ul>";
            echo "<li>User closed browser/tab during off-loading</li>";
            echo "<li>Network interruption</li>";
            echo "<li>Session timeout</li>";
            echo "<li>JavaScript errors</li>";
            echo "</ul>";
            echo "<p><strong>Solution:</strong> Add resume functionality to the main purchases page</p>";
            echo "</div>";
        } else {
            echo "<div style='color: green; background-color: #d4edda; padding: 15px; border-radius: 5px;'>";
            echo "<h5>✅ All Off-loading Processes Normal</h5>";
            echo "<p>No POs have been stuck in off-loading status for an unusually long time.</p>";
            echo "</div>";
        }

    } else {
        echo "<p style='color: blue;'>ℹ️ No POs currently in off-loading status</p>";
    }

    // Check our specific PO
    echo "<h4>3. Check PO-250906-024518 Specifically</h4>";
    $db->query("
        SELECT 
            po_number, 
            status, 
            dock_arrival_time,
            TIMESTAMPDIFF(MINUTE, dock_arrival_time, NOW()) as minutes_elapsed
        FROM purchases 
        WHERE po_number = 'PO-250906-024518'
    ");
    $db->execute();
    $specificPO = $db->single();

    if ($specificPO) {
        echo "<p><strong>PO Number:</strong> {$specificPO->po_number}</p>";
        echo "<p><strong>Status:</strong> " . ($specificPO->status ?: 'NULL') . "</p>";
        echo "<p><strong>Dock Arrival Time:</strong> " . ($specificPO->dock_arrival_time ?: 'Not started') . "</p>";

        if ($specificPO->status === 'pending_arrival' && $specificPO->dock_arrival_time) {
            echo "<p><strong>Elapsed Time:</strong> {$specificPO->minutes_elapsed} minutes</p>";

            if ($specificPO->minutes_elapsed > 10) {
                echo "<div style='color: orange; background-color: #fff3cd; padding: 10px; border-radius: 5px;'>";
                echo "<p>⚠️ This PO has been in off-loading status for over 10 minutes</p>";
                echo "<p><strong>Recommended Action:</strong> Add resume functionality or manually complete</p>";
                echo "</div>";
            } else {
                echo "<p style='color: green;'>✅ Off-loading time is normal</p>";
            }
        } else {
            echo "<p>ℹ️ PO is not currently in off-loading status</p>";
        }
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    table {
        margin: 10px 0;
        width: 100%;
    }

    th,
    td {
        text-align: left;
        border: 1px solid #ddd;
    }

    h3 {
        color: #333;
        border-bottom: 2px solid #007bff;
        padding-bottom: 5px;
    }

    h4 {
        color: #555;
        margin-top: 25px;
    }

    h5 {
        margin-top: 0;
    }
</style>