<?php
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';

// Replicate the exact putaway.php logic for debugging
$putawayQueue = [];

try {
    require_once APPROOT . DS . 'app' . DS . 'Database.php';
    $db = new Database();

    // Get putaway queue data - items currently in receiving areas waiting for putaway
    try {
        // Query actual inventory in receiving locations - group by product and PO to reduce duplicates
        $db->query("SELECT 
            p.product_id,
            p.product_name,
            p.sku,
            SUM(i.quantity) as total_pending_quantity,
            GROUP_CONCAT(CONCAT(l.location_name, ': ', i.quantity, ' units') ORDER BY i.quantity DESC SEPARATOR ' | ') as location_breakdown,
            (SELECT l_first.location_name FROM inventory i_first 
             JOIN locations l_first ON i_first.location_id = l_first.location_id 
             WHERE i_first.product_id = p.product_id AND l_first.location_type = 'receiving' AND i_first.quantity > 0
             ORDER BY i_first.quantity DESC LIMIT 1) as primary_location,
            (SELECT i_first.quantity FROM inventory i_first 
             JOIN locations l_first ON i_first.location_id = l_first.location_id 
             WHERE i_first.product_id = p.product_id AND l_first.location_type = 'receiving' AND i_first.quantity > 0
             ORDER BY i_first.quantity DESC LIMIT 1) as primary_quantity,
            (SELECT pi_inner.received_quantity FROM purchase_items pi_inner 
             JOIN purchases pu_inner ON pi_inner.purchase_id = pu_inner.purchase_id
             WHERE pi_inner.product_id = p.product_id 
             ORDER BY pi_inner.received_at DESC LIMIT 1) as total_received,
            (SELECT IFNULL(pi_inner.putaway_quantity, 0) FROM purchase_items pi_inner 
             JOIN purchases pu_inner ON pi_inner.purchase_id = pu_inner.purchase_id
             WHERE pi_inner.product_id = p.product_id 
             ORDER BY pi_inner.received_at DESC LIMIT 1) as putaway_quantity,
            (SELECT pu_inner.po_number FROM purchase_items pi_inner 
             JOIN purchases pu_inner ON pi_inner.purchase_id = pu_inner.purchase_id
             WHERE pi_inner.product_id = p.product_id 
             ORDER BY pi_inner.received_at DESC LIMIT 1) as po_number
            FROM inventory i
            JOIN locations l ON i.location_id = l.location_id
            JOIN products p ON i.product_id = p.product_id
            WHERE l.location_type = 'receiving' 
            AND i.quantity > 0
            GROUP BY p.product_id, p.product_name, p.sku
            ORDER BY p.product_name, SUM(i.quantity) DESC
            LIMIT 10");

        if ($db->execute()) {
            $rawItems = $db->resultSet() ?? [];

            foreach ($rawItems as $item) {
                $productName = $item->product_name ?? ('Product #' . $item->product_id);
                $sku = $item->sku ?? ('SKU' . str_pad($item->product_id, 4, '0', STR_PAD_LEFT));
                $primaryLocation = $item->primary_location ?? 'Receiving';

                $totalReceived = $item->total_received ?? 0;
                $putawayQuantity = $item->putaway_quantity ?? 0;
                $remainingQuantity = $totalReceived > 0 ? ($totalReceived - $putawayQuantity) : 0;

                $putawayQueue[] = (object) [
                    'product_name' => $productName,
                    'sku' => $sku,
                    'pending_quantity' => $item->total_pending_quantity,
                    'primary_quantity' => $item->primary_quantity,
                    'location_breakdown' => $item->location_breakdown,
                    'total_received' => $totalReceived,
                    'putaway_quantity' => $putawayQuantity,
                    'remaining_quantity' => $remainingQuantity,
                    'receiving_area' => $primaryLocation,
                    'hours_waiting' => rand(4, 72),
                    'priority_class' => 'warning',
                    'po_number' => $item->po_number ?? ('INV-' . $item->product_id),
                    'suggested_location' => 'S1-A1-A1',
                    'product_id' => $item->product_id
                ];
            }
        }

        if (empty($putawayQueue)) {
            $putawayQueue = [
                (object) [
                    'product_name' => 'FALLBACK DATA - Database query failed or returned no results',
                    'sku' => 'ERROR',
                    'pending_quantity' => 0,
                    'po_number' => 'NO-DATA',
                    'receiving_area' => 'Debug Mode',
                    'hours_waiting' => 0,
                    'priority_class' => 'danger'
                ]
            ];
        }

    } catch (Exception $e) {
        $putawayQueue = [
            (object) [
                'product_name' => 'ERROR: ' . $e->getMessage(),
                'sku' => 'EXCEPTION',
                'pending_quantity' => 0,
                'po_number' => 'ERROR',
                'receiving_area' => 'Exception Caught',
                'hours_waiting' => 0,
                'priority_class' => 'danger'
            ]
        ];
    }

} catch (Exception $e) {
    $putawayQueue = [
        (object) [
            'product_name' => 'OUTER ERROR: ' . $e->getMessage(),
            'sku' => 'OUTER-ERR',
            'pending_quantity' => 0,
            'po_number' => 'OUTER-ERROR',
            'receiving_area' => 'Outer Exception',
            'hours_waiting' => 0,
            'priority_class' => 'danger'
        ]
    ];
}
?>

<link rel="stylesheet" href="<?= URLROOT ?>/public/css/app-unified.css">

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-bug"></i> Putaway Queue Debug Test</h4>
                    <p class="text-muted mb-0">This page shows exactly what the putaway.php logic produces</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h5><i class="fas fa-list"></i> Priority Queue Results</h5>
                            <p>Total Items: <strong><?= count($putawayQueue) ?></strong></p>

                            <?php if (!empty($putawayQueue)): ?>
                                <div class="queue-items">
                                    <?php foreach ($putawayQueue as $index => $item): ?>
                                        <div class="queue-item mb-3 p-3 border rounded bg-light">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h6 class="text-primary mb-2">
                                                        <i class="fas fa-cube"></i>
                                                        <?= htmlspecialchars($item->product_name) ?>
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <small class="text-muted d-block">
                                                                <strong>SKU:</strong> <?= htmlspecialchars($item->sku) ?>
                                                            </small>
                                                            <small class="text-muted d-block">
                                                                <strong>Quantity:</strong> <?= $item->pending_quantity ?> units
                                                            </small>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <small class="text-success d-block">
                                                                <strong>PO Number:</strong>
                                                                <?= htmlspecialchars($item->po_number ?? 'NO PO') ?>
                                                            </small>
                                                            <small class="text-info d-block">
                                                                <strong>Location:</strong>
                                                                <?php if (!empty($item->location_breakdown)): ?>
                                                                    <?= htmlspecialchars($item->location_breakdown) ?>
                                                                <?php else: ?>
                                                                    <?= htmlspecialchars($item->receiving_area ?? 'No Location') ?>
                                                                <?php endif; ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    <span class="badge badge-<?= $item->priority_class ?? 'secondary' ?> mb-2">
                                                        Item #<?= $index + 1 ?>
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">
                                                        Debug:
                                                        <?= isset($item->hours_waiting) ? $item->hours_waiting . 'h' : 'No time' ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    No items found in putaway queue
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>