<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<!-- Unified CSS -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/app-unified.css">

<div class="container-fluid">
    <!-- Back button and page header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= URLROOT ?>/returns" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Returns
            </a>
        </div>
    </div>

    <!-- Main content card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="theme-card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="h3 mb-2 text-primary">
                            <i class="fas fa-undo-alt mr-2"></i>Create Purchase Return
                        </h2>
                        <p class="text-muted">Return items from a received purchase order</p>
                    </div>

                    <form action="<?= URLROOT ?>/returns/addpurchase" method="post">
                        <!-- Purchase Selection -->
                        <div class="form-group">
                            <label for="purchase_id" class="form-label">
                                <i class="fas fa-shopping-cart mr-1"></i>Select Purchase Order <sup
                                    class="text-danger">*</sup>
                            </label>
                            <select name="purchase_id" id="purchase_id"
                                class="form-control form-control-lg <?= (!empty($data['purchase_id_err'])) ? 'is-invalid' : ''; ?>">
                                <option value="">Choose a purchase order to return...</option>
                                <?php if (!empty($data['available_purchases'])): ?>
                                    <?php foreach ($data['available_purchases'] as $purchase): ?>
                                        <option value="<?= $purchase->purchase_id ?>"
                                            <?= ($data['purchase_id'] == $purchase->purchase_id) ? 'selected' : '' ?>>
                                            PO: <?= $purchase->po_number ?> - <?= $purchase->supplier_name ?>
                                            (₹<?= number_format($purchase->total_amount, 2) ?>) -
                                            <?= date('M j, Y', strtotime($purchase->purchase_date)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No returnable purchases found</option>
                                <?php endif; ?>
                            </select>
                            <?php if (!empty($data['purchase_id_err'])): ?>
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle mr-1"></i><?= $data['purchase_id_err'] ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">
                                Only received purchase orders that haven't been returned are shown
                            </small>
                        </div>

                        <!-- Return Date -->
                        <div class="form-group">
                            <label for="return_date" class="form-label">
                                <i class="fas fa-calendar mr-1"></i>Return Date <sup class="text-danger">*</sup>
                            </label>
                            <input type="date" name="return_date" id="return_date"
                                class="form-control form-control-lg <?= (!empty($data['return_date_err'])) ? 'is-invalid' : ''; ?>"
                                value="<?= $data['return_date'] ?>" max="<?= date('Y-m-d') ?>">
                            <?php if (!empty($data['return_date_err'])): ?>
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle mr-1"></i><?= $data['return_date_err'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Reason -->
                        <div class="form-group">
                            <label for="reason" class="form-label">
                                <i class="fas fa-comment mr-1"></i>Return Reason
                            </label>
                            <textarea name="reason" id="reason" class="form-control" rows="4"
                                placeholder="Describe why you're returning this purchase order..."><?= $data['reason'] ?></textarea>
                            <small class="form-text text-muted">
                                Optional: Provide details about the return reason for record keeping
                            </small>
                        </div>

                        <!-- Action buttons -->
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success btn-lg mr-3">
                                <i class="fas fa-check mr-2"></i>Create Return
                            </button>
                            <a href="<?= URLROOT ?>/returns" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help section -->
            <?php if (empty($data['available_purchases'])): ?>
                <div class="alert alert-info mt-4">
                    <h5><i class="fas fa-info-circle mr-2"></i>No Returnable Purchases</h5>
                    <p class="mb-2">You can only return purchase orders that have been received and haven't already been
                        returned.</p>
                    <p class="mb-0">
                        <strong>To return items:</strong> Ensure the purchase order status is "received" and hasn't been
                        returned previously.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Auto-focus on purchase selection dropdown
    document.addEventListener('DOMContentLoaded', function () {
        const purchaseSelect = document.getElementById('purchase_id');
        if (purchaseSelect && purchaseSelect.options.length > 1) {
            purchaseSelect.focus();
        }
    });
</script>