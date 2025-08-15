<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/app-unified.css">

<div class="container-fluid theme-container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card theme-card-light shadow mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Purchase Order Details</h3>
                    <div class="btn-group" role="group">
                        <a href="<?php echo URLROOT; ?>/suppliers/view/<?php echo $data['order']->supplier_id ?? ''; ?>"
                            class="btn btn-outline-info btn-sm">
                            <i class="fas fa-building"></i> View Supplier
                        </a>
                        <a href="<?php echo URLROOT; ?>/purchases" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to Purchases
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['order'])):
                        $order = $data['order']; ?>

                        <!-- Order Header Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">PO Number:</th>
                                        <td><strong
                                                class="text-primary"><?php echo htmlspecialchars($order->po_number ?? 'PO-' . $order->purchase_id); ?></strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Supplier:</th>
                                        <td><?php echo htmlspecialchars($order->supplier_name ?? '-'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Contact Person:</th>
                                        <td><?php echo htmlspecialchars($order->contact_person ?? '-'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Created By:</th>
                                        <td><?php echo htmlspecialchars($order->created_by_name ?? '-'); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">Order Date:</th>
                                        <td><?php echo !empty($order->purchase_date) ? date('d-m-Y', strtotime($order->purchase_date)) : '-'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Expected Date:</th>
                                        <td><?php echo !empty($order->expected_date) ? date('d-m-Y', strtotime($order->expected_date)) : '-'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            $status = $order->status ?? 'unknown';
                                            switch (strtolower($status)) {
                                                case 'pending':
                                                    $statusClass = 'bg-warning text-dark';
                                                    break;
                                                case 'sent':
                                                    $statusClass = 'bg-info';
                                                    break;
                                                case 'partially_received':
                                                    $statusClass = 'bg-secondary';
                                                    break;
                                                case 'received':
                                                    $statusClass = 'bg-success';
                                                    break;
                                                case 'cancelled':
                                                    $statusClass = 'bg-danger';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-light text-dark';
                                            }
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?> fs-6">
                                                <?php echo htmlspecialchars(ucfirst($status)); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Total Amount:</th>
                                        <td><strong
                                                class="text-success fs-5">₹<?php echo number_format($order->total_amount ?? 0, 2); ?></strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <?php if (!empty($order->notes)): ?>
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title"><i class="fas fa-sticky-note"></i> Notes:</h6>
                                            <p class="card-text"><?php echo nl2br(htmlspecialchars($order->notes)); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Order Items -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3"><i class="fas fa-list"></i> Order Items</h5>

                                <?php if (!empty($data['order_items'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="20%">Product</th>
                                                    <th width="15%">SKU</th>
                                                    <th width="10%">Quantity</th>
                                                    <th width="15%">Unit Price</th>
                                                    <th width="15%">Line Total</th>
                                                    <th width="10%">Received</th>
                                                    <th width="10%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $totalQuantity = 0;
                                                $totalAmount = 0;
                                                foreach ($data['order_items'] as $index => $item):
                                                    $lineTotal = ($item->quantity ?? 0) * ($item->unit_price ?? 0);
                                                    $totalQuantity += $item->quantity ?? 0;
                                                    $totalAmount += $lineTotal;
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $index + 1; ?></td>
                                                        <td><?php echo htmlspecialchars($item->product_name ?? '-'); ?></td>
                                                        <td><?php echo htmlspecialchars($item->sku ?? '-'); ?></td>
                                                        <td class="text-center"><?php echo number_format($item->quantity ?? 0); ?>
                                                        </td>
                                                        <td class="text-end">
                                                            ₹<?php echo number_format($item->unit_price ?? 0, 2); ?></td>
                                                        <td class="text-end">
                                                            <strong>₹<?php echo number_format($lineTotal, 2); ?></strong>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info">
                                                                <?php echo number_format($item->received_quantity ?? 0); ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php
                                                            $receiveStatus = $item->receive_status ?? 'pending';
                                                            $receiveStatusClass = '';
                                                            switch ($receiveStatus) {
                                                                case 'complete':
                                                                    $receiveStatusClass = 'bg-success';
                                                                    break;
                                                                case 'partial':
                                                                    $receiveStatusClass = 'bg-warning text-dark';
                                                                    break;
                                                                default:
                                                                    $receiveStatusClass = 'bg-secondary';
                                                            }
                                                            ?>
                                                            <span class="badge <?php echo $receiveStatusClass; ?>">
                                                                <?php echo ucfirst($receiveStatus); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot class="table-secondary">
                                                <tr>
                                                    <th colspan="3">Total</th>
                                                    <th class="text-center"><?php echo number_format($totalQuantity); ?></th>
                                                    <th></th>
                                                    <th class="text-end">
                                                        <strong>₹<?php echo number_format($totalAmount, 2); ?></strong>
                                                    </th>
                                                    <th colspan="2"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> No items found for this purchase order.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <?php if (in_array(strtolower($order->status ?? ''), ['pending', 'sent'])): ?>
                                        <a href="<?php echo URLROOT; ?>/purchases/edit/<?php echo $order->purchase_id; ?>"
                                            class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit Order
                                        </a>
                                    <?php endif; ?>

                                    <?php if (in_array(strtolower($order->status ?? ''), ['pending', 'sent', 'in_transit', 'shipped'])): ?>
                                        <a href="<?php echo URLROOT; ?>/purchases/markReceived/<?php echo $order->purchase_id; ?>"
                                            class="btn btn-success">
                                            <i class="fas fa-check-circle"></i> Mark as Received & Staged at Dock
                                        </a>
                                    <?php endif; ?>

                                    <?php if (strtolower($order->status ?? '') === 'sent'): ?>
                                        <a href="<?php echo URLROOT; ?>/purchases/receive/<?php echo $order->purchase_id; ?>"
                                            class="btn btn-primary">
                                            <i class="fas fa-truck"></i> Receive Shipment
                                        </a>
                                    <?php endif; ?>

                                    <button class="btn btn-info" onclick="window.print()">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="alert alert-danger">Purchase order not found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {

        .btn,
        .card-header .btn-group {
            display: none !important;
        }

        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>