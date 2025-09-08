<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "<h3>Permanent Fix for PO-250906-024518 Status Issue</h3>";

    // Step 1: Check current status
    echo "<h4>1. Current Status</h4>";
    $db->query("SELECT purchase_id, po_number, status, dock_location_id FROM purchases WHERE po_number = 'PO-250906-024518'");
    $db->execute();
    $po = $db->single();

    if ($po) {
        echo "<p><strong>Purchase ID:</strong> {$po->purchase_id}</p>";
        echo "<p><strong>PO Number:</strong> {$po->po_number}</p>";
        echo "<p><strong>Current Status:</strong> " . (empty($po->status) ? '<span style="color: red;">EMPTY/NULL</span>' : $po->status) . "</p>";
        echo "<p><strong>Dock Location ID:</strong> " . ($po->dock_location_id ?: 'NULL') . "</p>";

        // Step 2: Force update to pending status
        echo "<h4>2. Forcing Status to 'pending'</h4>";

        // Use a more robust update
        $db->query("UPDATE purchases SET status = 'pending', updated_at = NOW() WHERE purchase_id = ? AND po_number = ?");
        $db->bind(1, $po->purchase_id);
        $db->bind(2, 'PO-250906-024518');
        $result = $db->execute();

        if ($result) {
            echo "<p style='color: green;'>✅ Status updated to 'pending'</p>";

            // Verify the update immediately
            $db->query("SELECT status, updated_at FROM purchases WHERE purchase_id = ?");
            $db->bind(1, $po->purchase_id);
            $db->execute();
            $verified = $db->single();

            echo "<p><strong>Verified Status:</strong> <span style='color: green; font-weight: bold;'>{$verified->status}</span></p>";
            echo "<p><strong>Updated At:</strong> {$verified->updated_at}</p>";

            // Step 3: Check if there are any constraints or triggers affecting this
            echo "<h4>3. Checking for Database Constraints</h4>";

            // Check if the status column has any constraints
            $db->query("DESCRIBE purchases");
            $db->execute();
            $columns = $db->resultSet();

            foreach ($columns as $column) {
                if ($column->Field === 'status') {
                    echo "<p><strong>Status Column Info:</strong></p>";
                    echo "<ul>";
                    echo "<li>Type: {$column->Type}</li>";
                    echo "<li>Null: {$column->Null}</li>";
                    echo "<li>Default: " . ($column->Default ?: 'None') . "</li>";
                    echo "<li>Extra: " . ($column->Extra ?: 'None') . "</li>";
                    echo "</ul>";
                    break;
                }
            }

            // Step 4: Test workflow readiness
            echo "<h4>4. Workflow Readiness Test</h4>";

            $finalStatus = strtolower($verified->status ?? '');
            $canOffload = in_array($finalStatus, ['pending', 'sent', 'in_transit', 'shipped']);

            if ($canOffload) {
                echo "<div style='color: green; background-color: #d4edda; padding: 15px; border-radius: 5px;'>";
                echo "<h5 style='margin-top: 0;'>✅ READY FOR OFF-LOADING!</h5>";
                echo "<p><strong>Status:</strong> {$verified->status}</p>";
                echo "<p><strong>Action:</strong> You can now click 'Assign Dock & Start Off-loading'</p>";
                echo "<p><strong>Expected Flow:</strong></p>";
                echo "<ol>";
                echo "<li>Click 'Assign Dock & Start Off-loading' → Status becomes 'arrived_at_facility'</li>";
                echo "<li>Wait for timer or manually click 'Ready to Receive' → Status becomes 'ready_to_receive'</li>";
                echo "<li>Use receiving interface to complete the process</li>";
                echo "</ol>";
                echo "</div>";
            } else {
                echo "<p style='color: red;'>❌ Still not ready for off-loading</p>";
            }

        } else {
            echo "<p style='color: red;'>❌ Failed to update status</p>";
        }

    } else {
        echo "<p style='color: red;'>❌ PO not found</p>";
    }

    // Step 5: Additional safety check
    echo "<h4>5. Final Safety Check</h4>";
    $db->query("SELECT po_number, status FROM purchases WHERE po_number = 'PO-250906-024518'");
    $db->execute();
    $final = $db->single();

    if ($final) {
        echo "<p><strong>Final Status Check:</strong> " . ($final->status ?: 'STILL EMPTY') . "</p>";

        if (empty($final->status)) {
            echo "<div style='color: red; background-color: #f8d7da; padding: 15px; border-radius: 5px;'>";
            echo "<h5>⚠️ WARNING: Status Still Empty</h5>";
            echo "<p>The status update didn't persist. This could be due to:</p>";
            echo "<ul>";
            echo "<li>Database transaction rollback</li>";
            echo "<li>Triggers or constraints</li>";
            echo "<li>Concurrent updates</li>";
            echo "</ul>";
            echo "<p><strong>Manual SQL Fix:</strong> Run this query directly in your database:</p>";
            echo "<code>UPDATE purchases SET status = 'pending' WHERE po_number = 'PO-250906-024518';</code>";
            echo "</div>";
        }
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
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
        color: #333;
    }

    ul,
    ol {
        margin: 10px 0;
    }

    li {
        margin: 5px 0;
    }

    code {
        background-color: #f8f9fa;
        padding: 2px 5px;
        border-radius: 3px;
        font-family: monospace;
    }
</style>