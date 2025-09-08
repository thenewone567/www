<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "<h3>Check Current Status of PO-250906-024518</h3>";

    // Get current status
    $db->query("SELECT purchase_id, po_number, status, dock_location_id, updated_at FROM purchases WHERE po_number = 'PO-250906-024518'");
    $db->execute();
    $po = $db->single();

    if ($po) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th style='padding: 8px;'>Field</th>";
        echo "<th style='padding: 8px;'>Value</th>";
        echo "</tr>";

        echo "<tr>";
        echo "<td style='padding: 8px; font-weight: bold;'>Purchase ID</td>";
        echo "<td style='padding: 8px;'>{$po->purchase_id}</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td style='padding: 8px; font-weight: bold;'>PO Number</td>";
        echo "<td style='padding: 8px;'>{$po->po_number}</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td style='padding: 8px; font-weight: bold;'>Status</td>";
        echo "<td style='padding: 8px;'>";
        if (empty($po->status)) {
            echo "<span style='color: red; font-weight: bold;'>EMPTY/NULL</span>";
        } else {
            echo "<span style='color: blue; font-weight: bold;'>{$po->status}</span>";
        }
        echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td style='padding: 8px; font-weight: bold;'>Dock Location ID</td>";
        echo "<td style='padding: 8px;'>" . ($po->dock_location_id ?: 'NULL') . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td style='padding: 8px; font-weight: bold;'>Last Updated</td>";
        echo "<td style='padding: 8px;'>{$po->updated_at}</td>";
        echo "</tr>";

        echo "</table>";

        echo "<h4>Workflow Analysis</h4>";
        $status = strtolower($po->status ?? '');

        if (empty($status)) {
            echo "<p style='color: red;'>❌ <strong>PROBLEM:</strong> Status is still empty!</p>";
            echo "<p><strong>Action Needed:</strong> Re-run the fix script to set status to 'pending'</p>";
        } elseif ($status === 'pending') {
            echo "<p style='color: green;'>✅ Status is 'pending' - Ready for off-loading</p>";
            echo "<p><strong>Next Step:</strong> Click 'Assign Dock & Start Off-loading' button</p>";
        } elseif ($status === 'arrived_at_facility') {
            echo "<p style='color: orange;'>⏱️ Status is 'arrived_at_facility' - Off-loading in progress</p>";
            echo "<p><strong>Next Step:</strong> Click 'Ready to Receive' button</p>";
        } elseif ($status === 'ready_to_receive') {
            echo "<p style='color: blue;'>📦 Status is 'ready_to_receive' - Ready for receiving</p>";
            echo "<p><strong>Next Step:</strong> Use the receiving interface</p>";
        } else {
            echo "<p style='color: red;'>⚠️ Unexpected status: '{$status}'</p>";
        }

        // Test the off-loading API logic
        echo "<h4>API Logic Test</h4>";

        // Test for starting off-loading (arrived_at_facility)
        $canOffload = in_array($status, ['pending', 'sent', 'in_transit', 'shipped']);
        echo "<p><strong>Can start off-loading (arrived_at_facility):</strong> " . ($canOffload ? '✅ YES' : '❌ NO') . "</p>";

        // Test for completing off-loading (ready_to_receive)
        $canComplete = in_array($status, ['arrived_at_facility']);
        echo "<p><strong>Can complete off-loading (ready_to_receive):</strong> " . ($canComplete ? '✅ YES' : '❌ NO') . "</p>";

        // Show what the API would do
        echo "<h4>Expected API Behavior</h4>";
        if ($canOffload) {
            echo "<p style='color: green;'>✅ 'Assign Dock & Start Off-loading' should work (status: pending → arrived_at_facility)</p>";
        } else {
            echo "<p style='color: red;'>❌ 'Assign Dock & Start Off-loading' will fail</p>";
        }

        if ($canComplete) {
            echo "<p style='color: green;'>✅ 'Ready to Receive' should work (status: arrived_at_facility → ready_to_receive)</p>";
        } else {
            echo "<p style='color: red;'>❌ 'Ready to Receive' will fail - need to start off-loading first</p>";
        }

    } else {
        echo "<p style='color: red;'>❌ PO-250906-024518 not found</p>";
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
</style>