<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "<h3>Fix PO-250905-140201 for Receiving Workflow</h3>";

    // Check current status
    echo "<h4>1. Current Status Check</h4>";
    $db->query("SELECT * FROM purchases WHERE po_number = ?");
    $db->bind(1, 'PO-250905-140201');
    $db->execute();
    $po = $db->single();

    if ($po) {
        $currentStatus = $po->status ?? 'BLANK';
        echo "<p><strong>Current Status:</strong> <span style='color: " . (empty($po->status) ? 'red' : 'blue') . ";'>{$currentStatus}</span></p>";

        // List of statuses that work with receiving interface
        $receivingStatuses = ["ready_to_receive", "receiving_in_progress", "partially_received", "received", "at_dock", "sent", "in_transit"];

        echo "<h4>2. Valid Receiving Statuses</h4>";
        echo "<p>The receiving interface accepts these statuses:</p>";
        echo "<ul>";
        foreach ($receivingStatuses as $status) {
            $current = ($status === $currentStatus) ? " <strong>(CURRENT)</strong>" : "";
            echo "<li style='color: green;'>{$status}{$current}</li>";
        }
        echo "</ul>";

        // Determine what status to set
        $newStatus = 'ready_to_receive'; // Best status for receiving workflow

        if (!in_array($currentStatus, $receivingStatuses)) {
            echo "<h4>3. Updating Status for Receiving Compatibility</h4>";
            echo "<p style='color: orange;'>Current status '{$currentStatus}' is not compatible with receiving interface.</p>";
            echo "<p>Setting status to '<strong>{$newStatus}</strong>' to make it available for receiving...</p>";

            try {
                $db->query("UPDATE purchases SET status = ?, updated_at = NOW() WHERE po_number = ?");
                $db->bind(1, $newStatus);
                $db->bind(2, 'PO-250905-140201');
                $result = $db->execute();

                if ($result) {
                    echo "<p style='color: green;'>✓ Successfully updated status to '{$newStatus}'</p>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to update status</p>";
                }

            } catch (Exception $e) {
                echo "<p style='color: red;'>Update Error: " . $e->getMessage() . "</p>";
            }

        } else {
            echo "<h4>3. Status Check Result</h4>";
            echo "<p style='color: green;'>✓ Current status '{$currentStatus}' is already compatible with receiving interface</p>";
        }

        // Verify the final status
        echo "<h4>4. Final Status Verification</h4>";
        $db->query("SELECT * FROM purchases WHERE po_number = ?");
        $db->bind(1, 'PO-250905-140201');
        $db->execute();
        $updatedPO = $db->single();

        if ($updatedPO) {
            $finalStatus = $updatedPO->status;
            echo "<p><strong>Final Status:</strong> <span style='color: blue;'>{$finalStatus}</span></p>";

            if (in_array($finalStatus, $receivingStatuses)) {
                echo "<p style='color: green; font-weight: bold;'>🎉 PO is now ready for receiving workflow!</p>";

                echo "<h5>Next Steps:</h5>";
                echo "<ol>";
                echo "<li>Go to the <strong>Receiving Interface</strong> (app/views/inventory/receiving.php)</li>";
                echo "<li>Search for PO-250905-140201</li>";
                echo "<li>The PO should now appear and be selectable</li>";
                echo "<li>Complete the receiving process</li>";
                echo "</ol>";

            } else {
                echo "<p style='color: red;'>❌ Status still not compatible</p>";
            }
        }

        // Show receiving interface test
        echo "<h4>5. Test Receiving Interface Compatibility</h4>";

        // Simulate the getPODetails query
        $db->query('
            SELECT p.purchase_id, p.po_number, p.status, p.total_amount,
                   p.dock_location_id, p.receiving_area_id, s.supplier_name,
                   dl.location_name as dock_name, rl.location_name as receiving_area_name
            FROM purchases p
            LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
            LEFT JOIN locations dl ON p.dock_location_id = dl.location_id
            LEFT JOIN locations rl ON p.receiving_area_id = rl.location_id
            WHERE p.po_number = ? 
            AND (p.status IN ("ready_to_receive", "receiving_in_progress", "partially_received", "received", "at_dock", "sent", "in_transit"))
            LIMIT 1
        ');
        $db->bind(1, 'PO-250905-140201');
        $db->execute();
        $receivingPO = $db->single();

        if ($receivingPO) {
            echo "<p style='color: green;'>✓ PO passes receiving interface filter</p>";
            echo "<p>The PO will appear in the receiving interface search results</p>";

            echo "<h6>PO Details for Receiving:</h6>";
            echo "<ul>";
            echo "<li><strong>PO Number:</strong> {$receivingPO->po_number}</li>";
            echo "<li><strong>Status:</strong> {$receivingPO->status}</li>";
            echo "<li><strong>Supplier:</strong> {$receivingPO->supplier_name}</li>";
            echo "<li><strong>Total Amount:</strong> \${$receivingPO->total_amount}</li>";
            echo "<li><strong>Dock Location:</strong> " . ($receivingPO->dock_name ?? 'Not assigned') . "</li>";
            echo "<li><strong>Receiving Area:</strong> " . ($receivingPO->receiving_area_name ?? 'Not assigned') . "</li>";
            echo "</ul>";

        } else {
            echo "<p style='color: red;'>❌ PO still does not pass receiving interface filter</p>";
            echo "<p>The PO will NOT appear in receiving interface - additional debugging needed</p>";
        }

    } else {
        echo "<p style='color: red;'>❌ PO-250905-140201 not found</p>";
    }

    // Also check what other POs are available for receiving
    echo "<h4>6. Other POs Available for Receiving</h4>";
    $db->query('
        SELECT p.po_number, p.status, s.supplier_name, p.purchase_date
        FROM purchases p
        LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
        WHERE p.status IN ("ready_to_receive", "receiving_in_progress", "partially_received", "received", "at_dock", "sent", "in_transit")
        ORDER BY p.purchase_date DESC
        LIMIT 10
    ');
    $db->execute();
    $availablePOs = $db->resultSet();

    if (!empty($availablePOs)) {
        echo "<p>Found " . count($availablePOs) . " POs available for receiving:</p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th style='padding: 8px;'>PO Number</th>";
        echo "<th style='padding: 8px;'>Status</th>";
        echo "<th style='padding: 8px;'>Supplier</th>";
        echo "<th style='padding: 8px;'>Date</th>";
        echo "</tr>";

        foreach ($availablePOs as $availablePO) {
            $highlight = ($availablePO->po_number === 'PO-250905-140201') ? "background-color: #d4edda;" : "";
            echo "<tr style='{$highlight}'>";
            echo "<td style='padding: 8px;'>{$availablePO->po_number}</td>";
            echo "<td style='padding: 8px;'>{$availablePO->status}</td>";
            echo "<td style='padding: 8px;'>{$availablePO->supplier_name}</td>";
            echo "<td style='padding: 8px;'>{$availablePO->purchase_date}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>No POs currently available for receiving</p>";
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
</style>