<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "<h3>Fix PO-250906-024518 Status Issue (Using PO Number)</h3>";

    // Get the current record using PO number
    echo "<h4>1. Finding Record by PO Number</h4>";
    $db->query("SELECT purchase_id, po_number, status, supplier_name, purchase_date, total_amount FROM purchases WHERE po_number = ?");
    $db->bind(1, 'PO-250906-024518');
    $db->execute();
    $po = $db->single();

    if ($po) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th style='padding: 8px;'>Field</th>";
        echo "<th style='padding: 8px;'>Current Value</th>";
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
            echo "<span style='color: red; font-weight: bold;'>NULL/EMPTY</span>";
        } else {
            echo "<span style='color: blue;'>{$po->status}</span>";
        }
        echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td style='padding: 8px; font-weight: bold;'>Supplier</td>";
        echo "<td style='padding: 8px;'>" . ($po->supplier_name ?? 'N/A') . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td style='padding: 8px; font-weight: bold;'>Purchase Date</td>";
        echo "<td style='padding: 8px;'>" . ($po->purchase_date ?? 'N/A') . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td style='padding: 8px; font-weight: bold;'>Total Amount</td>";
        echo "<td style='padding: 8px;'>$" . number_format($po->total_amount ?? 0, 2) . "</td>";
        echo "</tr>";

        echo "</table>";

        // Fix the status if it's empty
        if (empty($po->status)) {
            echo "<h4>2. Fixing Empty Status</h4>";
            echo "<p>Setting status to 'pending' to enable off-loading workflow...</p>";

            try {
                $db->query("UPDATE purchases SET status = 'pending', updated_at = NOW() WHERE po_number = ?");
                $db->bind(1, 'PO-250906-024518');
                $result = $db->execute();

                if ($result) {
                    echo "<p style='color: green;'>✅ Successfully updated status to 'pending'</p>";

                    // Verify the update
                    $db->query("SELECT status, updated_at FROM purchases WHERE po_number = ?");
                    $db->bind(1, 'PO-250906-024518');
                    $db->execute();
                    $updated = $db->single();

                    echo "<p><strong>New Status:</strong> <span style='color: green; font-weight: bold;'>{$updated->status}</span></p>";
                    echo "<p><strong>Updated At:</strong> {$updated->updated_at}</p>";

                    echo "<div style='color: green; background-color: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
                    echo "<h4 style='margin-top: 0;'>🎉 SUCCESS!</h4>";
                    echo "<p><strong>PO-250906-024518 is now ready for the off-loading workflow!</strong></p>";
                    echo "<p><strong>The PO is no longer stuck and you can now:</strong></p>";
                    echo "<ol>";
                    echo "<li>Go to the Purchase Orders page</li>";
                    echo "<li>Find PO-250906-024518 in the Active Orders table</li>";
                    echo "<li>Click <strong>'Assign Dock & Start Off-loading'</strong> button</li>";
                    echo "<li>Wait for the timer to complete (status changes to 'arrived_at_facility')</li>";
                    echo "<li>Click <strong>'Ready to Receive'</strong> button (status changes to 'ready_to_receive')</li>";
                    echo "<li>Now you can proceed with the receiving workflow</li>";
                    echo "</ol>";
                    echo "</div>";

                } else {
                    echo "<p style='color: red;'>❌ Failed to update status</p>";
                }

            } catch (Exception $e) {
                echo "<p style='color: red;'>Update Error: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<h4>2. Status Check Result</h4>";
            echo "<p style='color: green;'>✅ Status is already set to: <strong>{$po->status}</strong></p>";
            echo "<p>No fix needed - PO is ready for workflow!</p>";
        }

        echo "<h4>3. Current Workflow Status</h4>";
        // Get the final status
        $db->query("SELECT status FROM purchases WHERE po_number = ?");
        $db->bind(1, 'PO-250906-024518');
        $db->execute();
        $finalStatus = $db->single();
        $finalStatusValue = $finalStatus->status ?? 'EMPTY';

        echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff;'>";
        echo "<p><strong>Current Status:</strong> ";

        if ($finalStatusValue === 'pending') {
            echo "<span style='color: green; font-weight: bold;'>pending</span> - Ready to start off-loading ✅";
            echo "<br><strong>Action:</strong> Click 'Assign Dock & Start Off-loading' to begin";
        } elseif ($finalStatusValue === 'arrived_at_facility') {
            echo "<span style='color: orange; font-weight: bold;'>arrived_at_facility</span> - Off-loading in progress ⏱️";
            echo "<br><strong>Action:</strong> Wait for timer, then click 'Ready to Receive'";
        } elseif ($finalStatusValue === 'ready_to_receive') {
            echo "<span style='color: blue; font-weight: bold;'>ready_to_receive</span> - Ready for receiving 📦";
            echo "<br><strong>Action:</strong> Start the receiving workflow";
        } else {
            echo "<span style='color: red; font-weight: bold;'>{$finalStatusValue}</span> - Issue detected ⚠️";
        }
        echo "</p>";
        echo "</div>";

    } else {
        echo "<p style='color: red;'>❌ PO-250906-024518 not found in database</p>";

        // Search for similar POs
        echo "<h4>Searching for Similar PO Numbers</h4>";
        $db->query("SELECT po_number, status FROM purchases WHERE po_number LIKE ? ORDER BY po_number DESC LIMIT 10");
        $db->bind(1, '%250906%');
        $db->execute();
        $similarPOs = $db->resultSet();

        if (!empty($similarPOs)) {
            echo "<p>Found similar PO numbers:</p>";
            echo "<ul>";
            foreach ($similarPOs as $similarPO) {
                echo "<li>{$similarPO->po_number} - Status: " . ($similarPO->status ?: 'EMPTY') . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No similar PO numbers found</p>";
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

    ol,
    ul {
        margin: 10px 0;
    }

    li {
        margin: 5px 0;
    }

    div {
        margin: 10px 0;
    }
</style>