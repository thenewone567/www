<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "<h3>Fix Empty Status for PO-250906-024518</h3>";

    // Check the current status of the specific PO
    echo "<h4>1. Current Status Check</h4>";
    $db->query("SELECT purchase_id, po_number, status, supplier_name, purchase_date, total_amount FROM purchases WHERE po_number = ?");
    $db->bind(1, 'PO-250906-024518');
    $db->execute();
    $po = $db->single();

    if ($po) {
        $status = $po->status ?? '';
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
        if (empty($status)) {
            echo "<span style='color: red; font-weight: bold;'>EMPTY/BLANK</span>";
        } else {
            echo "<span style='color: blue;'>{$status}</span>";
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

        echo "<h4>2. Status Analysis</h4>";
        if (empty($status)) {
            echo "<p style='color: red;'>❌ <strong>PROBLEM:</strong> Status is empty/blank</p>";
            echo "<p><strong>Impact:</strong> This prevents the off-loading workflow from working</p>";
            echo "<p><strong>Solution:</strong> Set status to 'pending' to enable off-loading</p>";
        } else {
            echo "<p style='color: green;'>✅ Status is set: '{$status}'</p>";
        }

        // Fix the status if it's empty
        if (empty($status)) {
            echo "<h4>3. Fixing Status</h4>";
            echo "<p>Setting status to 'pending' to enable off-loading workflow...</p>";

            try {
                $db->query("UPDATE purchases SET status = 'pending', updated_at = NOW() WHERE purchase_id = ?");
                $db->bind(1, $po->purchase_id);
                $result = $db->execute();

                if ($result) {
                    echo "<p style='color: green;'>✅ Successfully updated status to 'pending'</p>";

                    // Verify the update
                    $db->query("SELECT status FROM purchases WHERE purchase_id = ?");
                    $db->bind(1, $po->purchase_id);
                    $db->execute();
                    $updated = $db->single();

                    echo "<p><strong>New Status:</strong> <span style='color: green; font-weight: bold;'>{$updated->status}</span></p>";
                    echo "<p style='color: green; background-color: #d4edda; padding: 10px; border-radius: 5px;'>";
                    echo "🎉 <strong>SUCCESS!</strong> PO-250906-024518 can now use the off-loading workflow:";
                    echo "<br>1. Click 'Assign Dock & Start Off-loading' to begin";
                    echo "<br>2. Wait for timer to complete";
                    echo "<br>3. Click 'Ready to Receive' to finish off-loading";
                    echo "</p>";

                } else {
                    echo "<p style='color: red;'>❌ Failed to update status</p>";
                }

            } catch (Exception $e) {
                echo "<p style='color: red;'>Update Error: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<h4>3. Status Check Result</h4>";
            echo "<p style='color: green;'>✅ Status is already set - no fix needed</p>";
        }

        echo "<h4>4. Workflow Status Guide</h4>";
        echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff;'>";
        echo "<p><strong>Off-loading Workflow:</strong></p>";
        echo "<ol>";
        echo "<li><strong>pending</strong> → Can start off-loading</li>";
        echo "<li><strong>arrived_at_facility</strong> → Off-loading in progress (timer running)</li>";
        echo "<li><strong>ready_to_receive</strong> → Ready for receiving workflow</li>";
        echo "</ol>";
        echo "<p><strong>Current Status:</strong> ";

        // Get the final status
        $db->query("SELECT status FROM purchases WHERE purchase_id = ?");
        $db->bind(1, $po->purchase_id);
        $db->execute();
        $finalStatus = $db->single();
        $finalStatusValue = $finalStatus->status ?? 'EMPTY';

        if ($finalStatusValue === 'pending') {
            echo "<span style='color: green; font-weight: bold;'>pending</span> - Ready to start off-loading ✅";
        } elseif ($finalStatusValue === 'arrived_at_facility') {
            echo "<span style='color: orange; font-weight: bold;'>arrived_at_facility</span> - Off-loading in progress ⏱️";
        } elseif ($finalStatusValue === 'ready_to_receive') {
            echo "<span style='color: blue; font-weight: bold;'>ready_to_receive</span> - Ready for receiving 📦";
        } else {
            echo "<span style='color: red; font-weight: bold;'>{$finalStatusValue}</span> - Check workflow requirements ⚠️";
        }
        echo "</p>";
        echo "</div>";

    } else {
        echo "<p style='color: red;'>❌ PO-250906-024518 not found in database</p>";

        // Check if there are similar PO numbers
        echo "<h4>Similar PO Numbers</h4>";
        $db->query("SELECT po_number, status FROM purchases WHERE po_number LIKE ? ORDER BY po_number DESC LIMIT 5");
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