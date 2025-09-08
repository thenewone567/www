<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-file-alt mr-2"></i>Purchase Order Details</h2>
                <div>
                    <a href="<?php echo URLROOT; ?>/purchases" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>Back to Purchases
                    </a>
                    <a href="<?php echo URLROOT; ?>/purchases/history" class="btn btn-info">
                        <i class="fas fa-history mr-1"></i>View History
                    </a>
                    <?php if (isset($data['purchase']->status) && in_array($data['purchase']->status, ['pending', 'email_received']) && empty($data['purchase']->tracking_number)): ?>
                        <button type="button" class="btn btn-primary"
                            onclick="showTrackingModal(<?php echo $data['purchase']->purchase_id; ?>, '<?php echo htmlspecialchars($data['purchase']->po_number ?? 'N/A'); ?>')">
                            <i class="fas fa-truck mr-1"></i>Add Tracking Number
                        </button>
                    <?php endif; ?>
                    <?php if (isset($data['purchase']->status) && $data['purchase']->status === 'pending'): ?>
                        <button type="button" class="btn btn-warning"
                            onclick="showCancelModal(<?php echo $data['purchase']->purchase_id; ?>)">
                            <i class="fas fa-ban mr-1"></i>Cancel Order
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php flash('purchase_message'); ?>

    <?php if (!empty($data['purchase'])): ?>
        <div class="row">
            <!-- Purchase Order Information -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Purchase Order: <?php echo htmlspecialchars($data['purchase']->po_number ?? 'N/A'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Purchase Items -->
                        <h6 class="mb-3"><i class="fas fa-list mr-2"></i>Ordered Items</h6>
                        <?php if (!empty($data['purchase_items'])): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $totalAmount = 0;
                                        foreach ($data['purchase_items'] as $item):
                                            $itemTotal = $item->quantity * $item->unit_price;
                                            $totalAmount += $itemTotal;
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($item->product_name ?? 'Unknown Product'); ?></strong>
                                                </td>
                                                <td>
                                                    <code><?php echo htmlspecialchars($item->sku ?? 'N/A'); ?></code>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info"><?php echo $item->quantity; ?></span>
                                                </td>
                                                <td>
                                                    ₹<?php echo number_format($item->unit_price, 2); ?>
                                                </td>
                                                <td>
                                                    <strong>₹<?php echo number_format($itemTotal, 2); ?></strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="thead-dark">
                                        <tr>
                                            <th colspan="4" class="text-right">Total Amount:</th>
                                            <th>₹<?php echo number_format($totalAmount, 2); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                No items found for this purchase order.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Purchase Order Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Order Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>PO Number:</strong></td>
                                <td><?php echo htmlspecialchars($data['purchase']->po_number ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Supplier:</strong></td>
                                <td><?php echo htmlspecialchars($data['purchase']->supplier_name ?? 'Unknown'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Purchase Date:</strong></td>
                                <td><?php echo date('M j, Y', strtotime($data['purchase']->purchase_date ?? 'now')); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Expected Date:</strong></td>
                                <td><?php echo date('M j, Y', strtotime($data['purchase']->expected_date ?? 'now')); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Created By:</strong></td>
                                <td>
                                    <?php
                                    $createdBy = $data['purchase']->created_by_fullname ?? $data['purchase']->created_by_username ?? 'Unknown';
                                    echo htmlspecialchars($createdBy);
                                    ?>
                                    <br><small class="text-muted">User ID:
                                        <?php echo $data['purchase']->created_by ?? 'N/A'; ?></small>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Created At:</strong></td>
                                <td>
                                    <?php
                                    if (!empty($data['purchase']->created_at)) {
                                        echo date('M j, Y g:i A', strtotime($data['purchase']->created_at));
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php if (!empty($data['purchase']->updated_at)): ?>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($data['purchase']->updated_at)); ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td><strong>Tracking Number:</strong></td>
                                <td>
                                    <?php if (!empty($data['purchase']->tracking_number)): ?>
                                        <span
                                            class="badge badge-info"><?php echo htmlspecialchars($data['purchase']->tracking_number); ?></span>
                                        <br><small class="text-success"><i class="fas fa-check mr-1"></i>Dispatched</small>
                                    <?php else: ?>
                                        <span class="text-muted">Not dispatched</span>
                                        <?php if ($data['purchase']->status === 'pending'): ?>
                                            <br><small class="text-warning"><i class="fas fa-clock mr-1"></i>Awaiting
                                                dispatch</small>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <?php
                                    $status = $data['purchase']->status ?? 'pending';
                                    $statusClass = '';
                                    $statusDisplay = '';
                                    switch ($status) {
                                        case 'pending':
                                            $statusClass = 'badge-warning';
                                            $statusDisplay = 'Pending';
                                            break;
                                        case 'email_received':
                                            $statusClass = 'badge-info';
                                            $statusDisplay = 'Email Received';
                                            break;
                                        case 'off-loading':
                                            $statusClass = 'badge-offloading';
                                            $statusDisplay = 'Off-loading';
                                            break;
                                        case 'in_transit':
                                            $statusClass = 'badge-primary';
                                            $statusDisplay = 'In Transit';
                                            break;
                                        case 'ready_to_receive':
                                            $statusClass = 'badge-primary';
                                            $statusDisplay = 'Ready for Receiving';
                                            break;
                                        case 'receiving_in_progress':
                                            $statusClass = 'badge-warning';
                                            $statusDisplay = 'Receiving in Progress';
                                            break;
                                        case 'partially_received':
                                            $statusClass = 'badge-info';
                                            $statusDisplay = 'Partially Received';
                                            break;
                                        case 'received':
                                        case 'completed':
                                            $statusClass = 'badge-success';
                                            $statusDisplay = 'Received';
                                            break;
                                        case 'cancelled':
                                        case 'deleted':
                                            $statusClass = 'badge-danger';
                                            $statusDisplay = 'Cancelled';
                                            break;
                                        case 'approved':
                                            $statusClass = 'badge-info';
                                            $statusDisplay = 'Approved';
                                            break;
                                        default:
                                            $statusClass = 'badge-secondary';
                                            $statusDisplay = ucfirst(str_replace('_', ' ', $status));
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo $statusDisplay; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php if (!empty($data['purchase']->cancellation_reason)): ?>
                                <tr>
                                    <td><strong>Cancellation/Deletion Reason:</strong></td>
                                    <td>
                                        <div class="alert alert-warning py-2 mb-0">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            <?php echo htmlspecialchars($data['purchase']->cancellation_reason); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if (!empty($data['receiving_status'])): ?>
                                <tr>
                                    <td><strong>Receiving Status:</strong></td>
                                    <td>
                                        <?php
                                        $receivingStatus = $data['receiving_status']->status ?? 'pending';
                                        $receivingStatusClass = '';
                                        switch ($receivingStatus) {
                                            case 'pending':
                                                $receivingStatusClass = 'badge-warning';
                                                break;
                                            case 'partially_received':
                                                $receivingStatusClass = 'badge-info';
                                                break;
                                            case 'received':
                                                $receivingStatusClass = 'badge-success';
                                                break;
                                            default:
                                                $receivingStatusClass = 'badge-secondary';
                                        }
                                        ?>
                                        <span class="badge <?php echo $receivingStatusClass; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $receivingStatus)); ?>
                                        </span>
                                        <?php if (!empty($data['receiving_status']->received_date)): ?>
                                            <br><small class="text-muted">Received:
                                                <?php echo date('M j, Y', strtotime($data['receiving_status']->received_date)); ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td><strong>Total Amount:</strong></td>
                                <td>
                                    <h5 class="text-success mb-0">
                                        ₹<?php echo number_format($data['purchase']->total_amount ?? 0, 2); ?>
                                    </h5>
                                </td>
                            </tr>
                            <?php if (!empty($data['purchase']->notes)): ?>
                                <tr>
                                    <td><strong>Notes:</strong></td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($data['purchase']->notes); ?>
                                        </small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Order Stats</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-primary"><?php echo count($data['purchase_items']); ?></h4>
                                <small class="text-muted">Items</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-info">
                                    <?php
                                    $totalQty = 0;
                                    foreach ($data['purchase_items'] as $item) {
                                        $totalQty += $item->quantity;
                                    }
                                    echo $totalQty;
                                    ?>
                                </h4>
                                <small class="text-muted">Total Qty</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                <h4 class="text-danger">Purchase Order Not Found</h4>
                <p class="text-muted">The requested purchase order could not be found or you don't have permission to view
                    it.</p>
                <a href="<?php echo URLROOT; ?>/purchases" class="btn btn-primary">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Purchases
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Add Tracking Number Modal -->
<div class="modal fade" id="trackingModal" tabindex="-1" role="dialog" aria-labelledby="trackingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="trackingModalLabel">
                    <i class="fas fa-truck mr-2"></i>Add Tracking Number
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><strong>Purchase Order:</strong></label>
                    <p class="mb-3" id="trackingPoNumber">
                        <?php echo htmlspecialchars($data['purchase']->po_number ?? 'N/A'); ?>
                    </p>

                    <label for="trackingNumberInput"><strong>Tracking Number:</strong></label>
                    <input type="text" class="form-control" id="trackingNumberInput"
                        placeholder="Enter tracking number (e.g., UPS123456789)" maxlength="50">
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i>
                        Adding a tracking number will automatically update the status to "In Transit"
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-info"
                    onclick="updateTracking(<?php echo $data['purchase']->purchase_id ?? 0; ?>)">
                    <i class="fas fa-save mr-1"></i>Add Tracking Number
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="cancelModalLabel">
                    <i class="fas fa-ban mr-2"></i>Cancel Purchase Order
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Warning:</strong> This action will cancel the purchase order and send cancellation
                    notifications to the supplier and internal team.
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-envelope mr-2"></i>
                    <strong>Email Notifications:</strong> Automatic cancellation emails will be sent to notify all
                    parties.
                </div>
                <form id="cancelForm">
                    <div class="form-group">
                        <label for="cancelReason"><strong>Reason for cancellation:</strong></label>
                        <textarea class="form-control" id="cancelReason" name="reason" rows="3"
                            placeholder="Please provide a reason for cancelling this purchase order..."
                            required></textarea>
                        <small class="form-text text-muted">This reason will be included in the cancellation email and
                            logged for audit purposes.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Keep Order
                </button>
                <button type="button" class="btn btn-warning" onclick="confirmCancel()">
                    <i class="fas fa-ban mr-1"></i>Cancel Order & Send Emails
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPurchaseId = null;

    // Tracking Number Functions
    function showTrackingModal(purchaseId, poNumber) {
        $('#trackingModal').modal('show');
        $('#trackingNumberInput').val('');
        $('#trackingPoNumber').text(poNumber);
    }

    function updateTracking(purchaseId) {
        const trackingNumber = $('#trackingNumberInput').val().trim();

        if (!trackingNumber) {
            alert('Please enter a tracking number');
            return;
        }

        // Basic validation for tracking number
        if (trackingNumber.length < 6) {
            alert('Tracking number must be at least 6 characters');
            return;
        }

        // Show loading state
        const saveBtn = $('#trackingModal .btn-info');
        const originalText = saveBtn.html();
        saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Updating...');

        $.ajax({
            url: '<?php echo URLROOT; ?>/purchases/updateTrackingAjax',
            method: 'POST',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                purchase_id: purchaseId,
                tracking_number: trackingNumber
            },
            success: function (response) {
                console.log('AJAX Response:', response); // Debug log
                if (response && response.success) {
                    // Close modal
                    $('#trackingModal').modal('hide');

                    // Show success message
                    showNotification('Success!', 'Tracking number added and status updated to "In Transit"', 'success');

                    // Refresh the page to show updated data
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                } else {
                    alert('Error: ' + (response.message || 'Unknown error occurred'));
                    saveBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function (xhr, status, error) {
                console.log('AJAX Error:', xhr.responseText); // Debug log

                // Handle authentication error
                if (xhr.status === 401) {
                    alert('Session expired. Please login again.');
                    window.location.href = '<?php echo URLROOT; ?>/users/login';
                    return;
                }

                alert('Error updating tracking number. Details: ' + error);
                saveBtn.prop('disabled', false).html(originalText);
            }
        });
    }

    // Cancel Order Functions
    function showCancelModal(purchaseId) {
        currentPurchaseId = purchaseId;
        $('#cancelModal').modal('show');
        $('#cancelReason').val('');
    }

    function confirmCancel() {
        const reason = $('#cancelReason').val().trim();

        if (!reason) {
            alert('Please provide a reason for cancellation');
            return;
        }

        if (reason.length < 10) {
            alert('Cancellation reason must be at least 10 characters');
            return;
        }

        // Show loading state
        const cancelBtn = $('.modal-footer .btn-warning');
        const originalText = cancelBtn.html();
        cancelBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Cancelling...');

        $.ajax({
            url: '<?php echo URLROOT; ?>/purchases/cancelPurchaseAjax',
            method: 'POST',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                purchase_id: currentPurchaseId,
                reason: reason
            },
            success: function (response) {
                console.log('Cancel Response:', response);
                if (response && response.success) {
                    $('#cancelModal').modal('hide');
                    showNotification('Success!', response.message, 'success');

                    // Redirect to purchases list after a short delay
                    setTimeout(function () {
                        window.location.href = '<?php echo URLROOT; ?>/purchases';
                    }, 2000);
                } else {
                    alert('Error: ' + (response.message || 'Unknown error occurred'));
                    cancelBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function (xhr, status, error) {
                console.log('Cancel Error:', xhr.responseText);

                if (xhr.status === 401) {
                    alert('Session expired. Please login again.');
                    window.location.href = '<?php echo URLROOT; ?>/users/login';
                    return;
                }

                alert('Error cancelling purchase order. Details: ' + error);
                cancelBtn.prop('disabled', false).html(originalText);
            }
        });
    }

    function showNotification(title, message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';

        const notification = `
            <div class="alert ${alertClass} alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="${icon} mr-2"></i>
                <strong>${title}</strong> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;

        $('body').append(notification);

        // Auto-remove after 5 seconds
        setTimeout(function () {
            $('.alert').fadeOut();
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Add tooltips to action buttons
        $('[data-toggle="tooltip"]').tooltip();

        // Confirm actions for important buttons
        document.querySelectorAll('a[href*="/approve/"]').forEach(btn => {
            btn.addEventListener('click', function (e) {
                if (!confirm('Are you sure you want to approve this purchase order?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>