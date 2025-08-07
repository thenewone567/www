<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified">
    <!-- Header Section -->
    <div class="row align-items-center theme-header mb-4">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item">
                        <a href="<?php echo URLROOT; ?>/dashboard" class="text-decoration-none">
                            <i class="fa-solid fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?php echo URLROOT; ?>/receiving" class="text-decoration-none">
                            <i class="fa-solid fa-dolly"></i> Receiving
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Purchase Details
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h1 class="theme-page-title">
                <i class="fa-solid fa-file-invoice theme-icon"></i>
                Purchase Order Details
            </h1>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('receive_message'); ?>

    <?php if (isset($data['purchase']) && $data['purchase']): ?>
        <!-- Purchase Information -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h5 class="theme-card-title">
                            <i class="fa-solid fa-file-invoice"></i> Purchase Order Information
                        </h5>
                        <div class="theme-card-actions">
                            <span class="theme-badge theme-badge-primary">
                                PO #<?php echo $data['purchase']->purchase_number ?? $data['purchase']->purchase_id; ?>
                            </span>
                        </div>
                    </div>
                    <div class="theme-card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="theme-info-group">
                                    <label class="theme-info-label">Purchase ID</label>
                                    <div class="theme-info-value">
                                        <strong class="text-primary">#<?php echo $data['purchase']->purchase_id; ?></strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="theme-info-group">
                                    <label class="theme-info-label">Status</label>
                                    <div class="theme-info-value">
                                        <?php
                                        $statusClass = 'theme-badge-secondary';
                                        $status = $data['purchase']->status ?? 'pending';
                                        if ($status === 'received')
                                            $statusClass = 'theme-badge-success';
                                        elseif ($status === 'partially_received')
                                            $statusClass = 'theme-badge-warning';
                                        elseif ($status === 'sent')
                                            $statusClass = 'theme-badge-info';
                                        ?>
                                        <span class="theme-badge <?php echo $statusClass; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="theme-info-group">
                                    <label class="theme-info-label">Supplier</label>
                                    <div class="theme-info-value">
                                        <i class="fa-solid fa-building text-muted me-2"></i>
                                        <?php echo htmlspecialchars($data['purchase']->supplier_name ?? 'Unknown'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="theme-info-group">
                                    <label class="theme-info-label">Purchase Date</label>
                                    <div class="theme-info-value">
                                        <i class="fa-solid fa-calendar text-muted me-2"></i>
                                        <?php echo date('M j, Y g:i A', strtotime($data['purchase']->purchase_date ?? $data['purchase']->created_at)); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="theme-info-group">
                                    <label class="theme-info-label">Total Amount</label>
                                    <div class="theme-info-value">
                                        <i class="fa-solid fa-rupee-sign text-success me-2"></i>
                                        <strong class="text-success">
                                            ₹<?php echo number_format($data['purchase']->total_amount ?? 0, 2); ?>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="theme-info-group">
                                    <label class="theme-info-label">Items Count</label>
                                    <div class="theme-info-value">
                                        <i class="fa-solid fa-boxes text-muted me-2"></i>
                                        <?php echo $data['purchase']->item_count ?? 0; ?> items
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($data['purchase']->notes) && $data['purchase']->notes): ?>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="theme-info-group">
                                        <label class="theme-info-label">Notes</label>
                                        <div class="theme-info-value">
                                            <div class="alert alert-info">
                                                <i class="fa-solid fa-note-sticky me-2"></i>
                                                <?php echo nl2br(htmlspecialchars($data['purchase']->notes)); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Actions -->
                <div class="theme-card mb-4">
                    <div class="theme-card-header">
                        <h5 class="theme-card-title">
                            <i class="fa-solid fa-bolt"></i> Actions
                        </h5>
                    </div>
                    <div class="theme-card-body">
                        <div class="d-grid gap-2">
                            <?php if ($data['purchase']->status !== 'received'): ?>
                                <a href="<?php echo URLROOT; ?>/receiving/process/<?php echo $data['purchase']->purchase_id; ?>"
                                    class="btn theme-btn-success">
                                    <i class="fa-solid fa-dolly"></i> Process Receipt
                                </a>
                            <?php endif; ?>

                            <a href="<?php echo URLROOT; ?>/purchases/edit/<?php echo $data['purchase']->purchase_id; ?>"
                                class="btn theme-btn-outline-warning">
                                <i class="fa-solid fa-edit"></i> Edit Purchase Order
                            </a>

                            <a href="<?php echo URLROOT; ?>/purchases/print/<?php echo $data['purchase']->purchase_id; ?>"
                                class="btn theme-btn-outline-info" target="_blank">
                                <i class="fa-solid fa-print"></i> Print Purchase Order
                            </a>

                            <a href="<?php echo URLROOT; ?>/purchases/duplicate/<?php echo $data['purchase']->purchase_id; ?>"
                                class="btn theme-btn-outline-secondary">
                                <i class="fa-solid fa-copy"></i> Duplicate Order
                            </a>

                            <hr class="theme-divider">

                            <a href="<?php echo URLROOT; ?>/receiving/pending" class="btn theme-btn-outline-primary">
                                <i class="fa-solid fa-arrow-left"></i> Back to Pending
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Receiving Progress -->
                <?php if (isset($data['receiving_progress'])): ?>
                    <div class="theme-card">
                        <div class="theme-card-header">
                            <h5 class="theme-card-title">
                                <i class="fa-solid fa-chart-pie"></i> Receiving Progress
                            </h5>
                        </div>
                        <div class="theme-card-body">
                            <div class="theme-progress-stats">
                                <div class="theme-progress-item">
                                    <span class="theme-progress-label">Items Received</span>
                                    <div class="theme-progress-bar">
                                        <?php
                                        $receivedPercent = ($data['receiving_progress']['total_items'] > 0)
                                            ? ($data['receiving_progress']['received_items'] / $data['receiving_progress']['total_items']) * 100
                                            : 0;
                                        ?>
                                        <div class="progress">
                                            <div class="progress-bar bg-success"
                                                style="width: <?php echo $receivedPercent; ?>%"></div>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo $data['receiving_progress']['received_items']; ?> of
                                            <?php echo $data['receiving_progress']['total_items']; ?> items
                                        </small>
                                    </div>
                                </div>

                                <div class="theme-progress-item">
                                    <span class="theme-progress-label">Value Received</span>
                                    <div class="theme-progress-bar">
                                        <?php
                                        $valuePercent = ($data['receiving_progress']['total_value'] > 0)
                                            ? ($data['receiving_progress']['received_value'] / $data['receiving_progress']['total_value']) * 100
                                            : 0;
                                        ?>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" style="width: <?php echo $valuePercent; ?>%">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            ₹<?php echo number_format($data['receiving_progress']['received_value'], 2); ?> of
                                            ₹<?php echo number_format($data['receiving_progress']['total_value'], 2); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Purchase Items -->
        <div class="row">
            <div class="col-12">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h5 class="theme-card-title">
                            <i class="fa-solid fa-boxes"></i> Purchase Items
                        </h5>
                        <div class="theme-card-actions">
                            <span class="theme-badge theme-badge-info">
                                <?php echo count($data['purchase_items'] ?? []); ?> items
                            </span>
                        </div>
                    </div>
                    <div class="theme-card-body">
                        <?php if (isset($data['purchase_items']) && !empty($data['purchase_items'])): ?>
                            <div class="theme-table-container">
                                <table class="table theme-table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Ordered Qty</th>
                                            <th>Received Qty</th>
                                            <th>Remaining</th>
                                            <th>Unit Price</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['purchase_items'] as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if (isset($item->product_image) && $item->product_image): ?>
                                                            <img src="<?php echo URLROOT; ?>/uploads/products/<?php echo $item->product_image; ?>"
                                                                class="theme-product-thumb me-2" alt="Product">
                                                        <?php else: ?>
                                                            <div class="theme-product-thumb theme-product-thumb-placeholder me-2">
                                                                <i class="fa-solid fa-box"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <div class="fw-medium">
                                                                <?php echo htmlspecialchars($item->product_name ?? 'Unknown Product'); ?>
                                                            </div>
                                                            <?php if (isset($item->product_description)): ?>
                                                                <small class="text-muted">
                                                                    <?php echo htmlspecialchars(substr($item->product_description, 0, 50)); ?>
                                                                    <?php if (strlen($item->product_description) > 50)
                                                                        echo '...'; ?>
                                                                </small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <code class="theme-code">
                                                                <?php echo htmlspecialchars($item->sku ?? 'N/A'); ?>
                                                            </code>
                                                </td>
                                                <td>
                                                    <span class="theme-badge theme-badge-info">
                                                        <?php echo number_format($item->quantity ?? 0, 2); ?>
                                                        <small><?php echo $item->unit ?? ''; ?></small>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="theme-badge theme-badge-success">
                                                        <?php echo number_format($item->received_quantity ?? 0, 2); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php $remaining = ($item->quantity ?? 0) - ($item->received_quantity ?? 0); ?>
                                                    <span
                                                        class="theme-badge <?php echo $remaining > 0 ? 'theme-badge-warning' : 'theme-badge-success'; ?>">
                                                        <?php echo number_format($remaining, 2); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="text-success fw-medium">
                                                        ₹<?php echo number_format($item->unit_price ?? 0, 2); ?>
                                                    </div>
                                                    <small class="text-muted">per <?php echo $item->unit ?? 'unit'; ?></small>
                                                </td>
                                                <td>
                                                    <strong class="text-success">
                                                        ₹<?php echo number_format(($item->quantity ?? 0) * ($item->unit_price ?? 0), 2); ?>
                                                    </strong>
                                                </td>
                                                <td>
                                                    <?php
                                                    $remaining = ($item->quantity ?? 0) - ($item->received_quantity ?? 0);
                                                    if ($remaining <= 0) {
                                                        echo '<span class="theme-badge theme-badge-success">Complete</span>';
                                                    } elseif (($item->received_quantity ?? 0) > 0) {
                                                        echo '<span class="theme-badge theme-badge-warning">Partial</span>';
                                                    } else {
                                                        echo '<span class="theme-badge theme-badge-secondary">Pending</span>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <th colspan="6" class="text-end">Total Order Value:</th>
                                            <th>
                                                <strong class="text-success">
                                                    ₹<?php echo number_format($data['purchase']->total_amount ?? 0, 2); ?>
                                                </strong>
                                            </th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="theme-empty-state">
                                <div class="theme-empty-icon">
                                    <i class="fa-solid fa-box-open"></i>
                                </div>
                                <h5 class="theme-empty-title">No Items Found</h5>
                                <p class="theme-empty-description">
                                    This purchase order doesn't have any items associated with it.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Purchase Not Found -->
        <div class="row">
            <div class="col-12">
                <div class="theme-card">
                    <div class="theme-card-body">
                        <div class="theme-empty-state">
                            <div class="theme-empty-icon">
                                <i class="fa-solid fa-exclamation-triangle text-warning"></i>
                            </div>
                            <h5 class="theme-empty-title">Purchase Order Not Found</h5>
                            <p class="theme-empty-description">
                                The requested purchase order could not be found or may have been deleted.
                            </p>
                            <a href="<?php echo URLROOT; ?>/receiving/pending" class="btn theme-btn-primary">
                                <i class="fa-solid fa-arrow-left"></i> Back to Pending Receipts
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>