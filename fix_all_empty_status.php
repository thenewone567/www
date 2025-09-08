<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "<h3>Fix All POs with Empty Status</h3>";

    // Find all POs with empty status
    echo "<h4>1. Finding POs with Empty Status</h4>";
    $db->query("SELECT purchase_id, po_number, supplier_name, purchase_date FROM purchases WHERE status IS NULL OR status = '' ORDER BY purchase_date DESC");
    $db->execute();
    $emptyStatusPOs = $db->resultSet();

    if (!empty($emptyStatusPOs)) {
        echo "<p style='color: orange;'>Found " . count($emptyStatusPOs) . " POs with empty status:</p>";

        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th style='padding: 8px;'>Purchase ID</th>";
        echo "<th style='padding: 8px;'>PO Number</th>";
        echo "<th style='padding: 8px;'>Supplier</th>";
        echo "<th style='padding: 8px;'>Purchase Date</th>";
        echo "<th style='padding: 8px;'>Action</th>";
        echo "</tr>";

        foreach ($emptyStatusPOs as $po) {
            echo "<tr>";
            echo "<td style='padding: 8px;'>{$po->purchase_id}</td>";
            echo "<td style='padding: 8px;'><strong>{$po->po_number}</strong></td>";
            echo "<td style='padding: 8px;'>" . ($po->supplier_name ?? 'N/A') . "</td>";
            echo "<td style='padding: 8px;'>" . ($po->purchase_date ?? 'N/A') . "</td>";
            echo "<td style='padding: 8px;'><span style='color: red;'>Needs Fix</span></td>";
            echo "</tr>";
        }
        echo "</table>";

        echo "<h4>2. Fixing All Empty Statuses</h4>";
        echo "<p>Setting all empty statuses to 'pending' to enable off-loading workflow...</p>";

        $fixedCount = 0;
        $errorCount = 0;

        foreach ($emptyStatusPOs as $po) {
            try {
                $db->query("UPDATE purchases SET status = 'pending', updated_at = NOW() WHERE purchase_id = ?");
                $db->bind(1, $po->purchase_id);
                $result = $db->execute();

                if ($result) {
                    echo "<p style='color: green;'>✅ Fixed {$po->po_number}</p>";
                    $fixedCount++;
                } else {
                    echo "<p style='color: red;'>❌ Failed to fix {$po->po_number}</p>";
                    $errorCount++;
                }

            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Error fixing {$po->po_number}: " . $e->getMessage() . "</p>";
                $errorCount++;
            }
        }

        echo "<h4>3. Fix Summary</h4>";
        echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
        echo "<p><strong>Results:</strong></p>";
        echo "<ul>";
        echo "<li><strong>Total POs found:</strong> " . count($emptyStatusPOs) . "</li>";
        echo "<li><strong>Successfully fixed:</strong> <span style='color: green;'>{$fixedCount}</span></li>";
        echo "<li><strong>Errors:</strong> <span style='color: red;'>{$errorCount}</span></li>";
        echo "</ul>";

        if ($fixedCount > 0) {
            echo "<p style='color: green; font-weight: bold;'>🎉 All fixed POs can now use the off-loading workflow!</p>";
        }
        echo "</div>";

    } else {
        echo "<p style='color: green;'>✅ No POs found with empty status - all good!</p>";
    }

    // Verify the fixes
    echo "<h4>4. Verification</h4>";
    $db->query("SELECT COUNT(*) as count FROM purchases WHERE status IS NULL OR status = ''");
    $db->execute();
    $remainingEmpty = $db->single();

    if ($remainingEmpty->count == 0) {
        echo "<p style='color: green;'>✅ Verification passed: No POs with empty status remain</p>";
    } else {
        echo "<p style='color: red;'>⚠️ Warning: {$remainingEmpty->count} POs still have empty status</p>";
    }

    // Show current status distribution
    echo "<h4>5. Current Status Distribution</h4>";
    $db->query("SELECT status, COUNT(*) as count FROM purchases GROUP BY status ORDER BY count DESC");
    $db->execute();
    $statusCounts = $db->resultSet();

    echo "<table border='1' style='border-collapse: collapse; width: 50%;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th style='padding: 8px;'>Status</th>";
    echo "<th style='padding: 8px;'>Count</th>";
    echo "</tr>";

    foreach ($statusCounts as $statusCount) {
        $statusDisplay = $statusCount->status ?: 'EMPTY';
        $statusColor = $statusCount->status ? 'black' : 'red';

        echo "<tr>";
        echo "<td style='padding: 8px; color: {$statusColor};'><strong>{$statusDisplay}</strong></td>";
        echo "<td style='padding: 8px;'>{$statusCount->count}</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<h4>6. Next Steps</h4>";
    echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff;'>";
    echo "<p><strong>For any PO with 'pending' status:</strong></p>";
    echo "<ol>";
    echo "<li>Go to the purchases page</li>";
    echo "<li>Find the PO in the list</li>";
    echo "<li>Click 'Copy' to copy the PO number</li>";
    echo "<li>Click 'Paste' to paste it in the search field</li>";
    echo "<li>Select a dock location</li>";
    echo "<li>Click 'Assign Dock & Start Off-loading'</li>";
    echo "<li>Wait for the timer to complete</li>";
    echo "<li>Click 'Ready to Receive' when it appears</li>";
    echo "</ol>";
    echo "</div>";

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