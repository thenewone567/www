<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "<h3>Deep Database Investigation for PO-250906-024518</h3>";

    // 1. Search with LIKE and get full details
    echo "<h4>1. Search with LIKE Pattern</h4>";
    $db->query("SELECT purchase_id, po_number, CHAR_LENGTH(po_number) as po_length, status, CHAR_LENGTH(status) as status_length, HEX(po_number) as po_hex FROM purchases WHERE po_number LIKE '%250906%'");
    $db->execute();
    $likeResults = $db->resultSet();

    foreach ($likeResults as $result) {
        echo "<p><strong>Found PO:</strong></p>";
        echo "<ul>";
        echo "<li>Purchase ID: {$result->purchase_id}</li>";
        echo "<li>PO Number: '{$result->po_number}' (Length: {$result->po_length})</li>";
        echo "<li>Status: '" . ($result->status ?: 'NULL') . "' (Length: {$result->status_length})</li>";
        echo "<li>PO Hex: {$result->po_hex}</li>";
        echo "</ul>";

        // Try direct update using this exact purchase_id
        echo "<h4>2. Direct Fix Using Purchase ID {$result->purchase_id}</h4>";
        try {
            $db->query("UPDATE purchases SET status = 'pending', updated_at = NOW() WHERE purchase_id = ?");
            $db->bind(1, $result->purchase_id);
            $updateResult = $db->execute();

            if ($updateResult) {
                echo "<p style='color: green;'>✅ Successfully updated status to 'pending' using purchase_id {$result->purchase_id}</p>";

                // Verify the update
                $db->query("SELECT status, updated_at FROM purchases WHERE purchase_id = ?");
                $db->bind(1, $result->purchase_id);
                $db->execute();
                $updated = $db->single();

                if ($updated) {
                    echo "<p><strong>New Status:</strong> <span style='color: green; font-weight: bold;'>{$updated->status}</span></p>";
                    echo "<p><strong>Updated At:</strong> {$updated->updated_at}</p>";

                    echo "<div style='color: green; background-color: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
                    echo "<h4 style='margin-top: 0;'>🎉 FIXED!</h4>";
                    echo "<p><strong>PO-250906-024518 is now unstuck!</strong></p>";
                    echo "<p><strong>Status changed from NULL/EMPTY to 'pending'</strong></p>";
                    echo "<p><strong>You can now use the off-loading workflow:</strong></p>";
                    echo "<ol>";
                    echo "<li>Go to Purchase Orders page</li>";
                    echo "<li>Find PO-250906-024518 in the Active Orders table</li>";
                    echo "<li>Click 'Assign Dock & Start Off-loading'</li>";
                    echo "<li>Wait for timer to complete</li>";
                    echo "<li>Click 'Ready to Receive'</li>";
                    echo "</ol>";
                    echo "</div>";
                } else {
                    echo "<p style='color: red;'>❌ Could not verify update</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ Update failed</p>";
            }

        } catch (Exception $e) {
            echo "<p style='color: red;'>Update Error: " . $e->getMessage() . "</p>";
        }
    }

    if (empty($likeResults)) {
        echo "<p style='color: red;'>No records found with LIKE pattern</p>";
    }

    // 3. Final verification
    echo "<h4>3. Final Status Verification</h4>";
    $db->query("SELECT po_number, status FROM purchases WHERE po_number LIKE '%250906%'");
    $db->execute();
    $finalCheck = $db->resultSet();

    foreach ($finalCheck as $check) {
        echo "<p><strong>PO:</strong> {$check->po_number} - <strong>Status:</strong> " . ($check->status ?: 'EMPTY') . "</p>";
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

    ul,
    ol {
        margin: 10px 0;
    }

    li {
        margin: 5px 0;
    }

    div {
        margin: 10px 0;
    }
</style>