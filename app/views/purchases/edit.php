<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified mt-0 pt-3">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/purchases" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Purchases
            </a>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h2 class="mb-0">
                <i class="fa-solid fa-edit"></i> Edit Purchase Order
            </h2>
        </div>
    </div>

    <!-- Purchase Order Details -->
    <div class="row">
        <div class="col-12">
            <div class="card theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-invoice"></i>
                        Purchase Order:
                        <?php echo htmlspecialchars($data['order']->po_number ?? $data['order']->purchase_id); ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/purchases/edit/<?php echo $data['order']->purchase_id; ?>"
                        method="post">
                        <div class="row">
                            <!-- Order Information -->
                            <div class="col-md-6">
                                <div class="card border-left-info">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Order Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="order_number" class="font-weight-bold">Order Number</label>
                                            <input type="text" class="form-control" id="order_number"
                                                value="<?php echo htmlspecialchars($data['order']->po_number ?? $data['order']->purchase_id); ?>"
                                                readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="supplier_name" class="font-weight-bold">Supplier</label>
                                            <input type="text" class="form-control" id="supplier_name"
                                                value="<?php echo htmlspecialchars($data['order']->supplier_name ?? 'N/A'); ?>"
                                                readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="order_date" class="font-weight-bold">Order Date</label>
                                            <input type="text" class="form-control" id="order_date"
                                                value="<?php echo date('M d, Y', strtotime($data['order']->purchase_date)); ?>"
                                                readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="total_amount" class="font-weight-bold">Total Amount</label>
                                            <input type="text" class="form-control" id="total_amount"
                                                value="<?php echo formatCurrency($data['order']->total_amount ?? 0, 2); ?>"
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Editable Fields -->
                            <div class="col-md-6">
                                <div class="card border-left-warning">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-edit"></i> Editable Fields</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="status" class="font-weight-bold">Status <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" id="status" name="status" required>
                                                <option value="pending" <?php echo ($data['order']->status ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="sent" <?php echo ($data['order']->status ?? '') === 'sent' ? 'selected' : ''; ?>>Sent</option>
                                                <option value="in_transit" <?php echo ($data['order']->status ?? '') === 'in_transit' ? 'selected' : ''; ?>>In Transit</option>
                                                <option value="ready_to_receive" <?php echo ($data['order']->status ?? '') === 'ready_to_receive' ? 'selected' : ''; ?>>Ready to Receive
                                                </option>
                                                <option value="receiving_in_progress" <?php echo ($data['order']->status ?? '') === 'receiving_in_progress' ? 'selected' : ''; ?>>Receiving in
                                                    Progress</option>
                                                <option value="partially_received" <?php echo ($data['order']->status ?? '') === 'partially_received' ? 'selected' : ''; ?>>Partially Received
                                                </option>
                                                <option value="received" <?php echo ($data['order']->status ?? '') === 'received' ? 'selected' : ''; ?>>Received</option>
                                                <option value="cancelled" <?php echo ($data['order']->status ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </div>

                                        <!-- Cancellation Reason (shown when cancelled is selected) -->
                                        <div class="form-group" id="cancellation-reason-group" style="display: none;">
                                            <label for="cancellation_reason" class="font-weight-bold">Cancellation
                                                Reason <span class="text-danger">*</span></label>
                                            <select class="form-control" id="cancellation_reason"
                                                name="cancellation_reason">
                                                <option value="">Select reason...</option>
                                                <option value="supplier_cancelled">Supplier Cancelled Order</option>
                                                <option value="out_of_stock">Items Out of Stock</option>
                                                <option value="pricing_issue">Pricing Issue</option>
                                                <option value="business_decision">Business Decision</option>
                                                <option value="duplicate_order">Duplicate Order</option>
                                                <option value="supplier_issue">Supplier Issue</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>

                                        <!-- Custom reason (shown when 'other' is selected) -->
                                        <div class="form-group" id="custom-reason-group" style="display: none;">
                                            <label for="custom_reason" class="font-weight-bold">Specify Reason</label>
                                            <input type="text" class="form-control" id="custom_reason"
                                                name="custom_reason" placeholder="Enter custom cancellation reason">
                                        </div>

                                        <!-- Action Selection for Cancelled Orders -->
                                        <div class="form-group" id="cancelled-action-group" style="display: none;">
                                            <label for="cancelled_action" class="font-weight-bold">Next Action <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" id="cancelled_action" name="cancelled_action">
                                                <option value="">Select action...</option>
                                                <option value="close_only">Close Order Only (No Further Action)</option>
                                                <option value="create_return">Create Return Order (if items were
                                                    prepaid/charged)</option>
                                                <option value="vendor_return">Process Vendor Return (if items shipped
                                                    but cancelled)</option>
                                                <option value="partial_cancel">Partial Cancellation (some items only)
                                                </option>
                                            </select>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Choose what should happen after cancellation
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <label for="tracking_number" class="font-weight-bold">Tracking
                                                Number</label>
                                            <input type="text" class="form-control" id="tracking_number"
                                                name="tracking_number"
                                                value="<?php echo htmlspecialchars($data['order']->tracking_number ?? ''); ?>"
                                                placeholder="Enter tracking number (e.g., UPS123456789)">
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                Adding a tracking number will automatically update status to "In
                                                Transit"
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <label for="expected_date" class="font-weight-bold">Expected Delivery
                                                Date</label>
                                            <input type="date" class="form-control" id="expected_date"
                                                name="expected_date"
                                                value="<?php echo htmlspecialchars($data['order']->expected_date ?? ''); ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="notes" class="font-weight-bold">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="4"
                                                placeholder="Add any notes or comments about this purchase order..."><?php echo htmlspecialchars($data['order']->notes ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Status Display -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="mb-2"><i class="fas fa-info-circle"></i> Current Status</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Status:</strong>
                                            <?php
                                            $status = strtolower($data['order']->status ?? '');
                                            $statusClass = '';
                                            $iconClass = '';
                                            switch ($status) {
                                                case 'pending':
                                                    $statusClass = 'badge-warning';
                                                    $iconClass = 'fas fa-clock';
                                                    break;
                                                case 'sent':
                                                    $statusClass = 'badge-info';
                                                    $iconClass = 'fas fa-paper-plane';
                                                    break;
                                                case 'in_transit':
                                                    $statusClass = 'badge-primary';
                                                    $iconClass = 'fas fa-truck';
                                                    break;
                                                case 'ready_to_receive':
                                                    $statusClass = 'badge-info';
                                                    $iconClass = 'fas fa-box-open';
                                                    break;
                                                case 'receiving_in_progress':
                                                    $statusClass = 'badge-warning';
                                                    $iconClass = 'fas fa-spinner';
                                                    break;
                                                case 'partially_received':
                                                    $statusClass = 'badge-warning';
                                                    $iconClass = 'fas fa-boxes';
                                                    break;
                                                case 'received':
                                                    $statusClass = 'badge-success';
                                                    $iconClass = 'fas fa-check-circle';
                                                    break;
                                                case 'cancelled':
                                                    $statusClass = 'badge-danger';
                                                    $iconClass = 'fas fa-ban';
                                                    break;
                                                default:
                                                    $statusClass = 'badge-light';
                                                    $iconClass = 'fas fa-question';
                                            }
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?> px-2 py-1">
                                                <i class="<?php echo $iconClass; ?> mr-1"></i>
                                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $status))); ?>
                                            </span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Created:</strong>
                                            <?php echo date('M d, Y', strtotime($data['order']->purchase_date)); ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Last Updated:</strong>
                                            <?php echo isset($data['order']->updated_at) ? date('M d, Y', strtotime($data['order']->updated_at)) : 'Never'; ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Tracking:</strong>
                                            <?php echo htmlspecialchars($data['order']->tracking_number ?? 'Not set'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-success btn-lg mr-3">
                                    <i class="fas fa-save"></i> Update Purchase Order
                                </button>
                                <a href="<?php echo URLROOT; ?>/purchases" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<script>
    $(document).ready(function () {
        // Show/hide cancellation fields based on status
        function toggleCancellationFields() {
            var status = $('#status').val();
            if (status === 'cancelled') {
                $('#cancellation-reason-group').show();
                $('#cancelled-action-group').show();
                $('#cancellation_reason').prop('required', true);
                $('#cancelled_action').prop('required', true);
            } else {
                $('#cancellation-reason-group').hide();
                $('#cancelled-action-group').hide();
                $('#custom-reason-group').hide();
                $('#cancellation_reason').prop('required', false);
                $('#cancelled_action').prop('required', false);
                $('#custom_reason').prop('required', false);
            }
        }

        // Show/hide custom reason field
        function toggleCustomReason() {
            var reason = $('#cancellation_reason').val();
            if (reason === 'other') {
                $('#custom-reason-group').show();
                $('#custom_reason').prop('required', true);
            } else {
                $('#custom-reason-group').hide();
                $('#custom_reason').prop('required', false);
            }
        }

        // Enhanced confirmation for status changes
        $('#status').change(function () {
            var newStatus = $(this).val();
            var currentStatus = '<?php echo $data['order']->status ?? ''; ?>';

            if (newStatus !== currentStatus) {
                if (newStatus === 'cancelled') {
                    showStatusChangeInfo('This will enable cancellation options below. Please specify the reason and action before saving.');
                } else {
                    var statusText = newStatus.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                    showStatusChangeConfirmation(
                        `Change status to "${statusText}"?`,
                        `This will update the purchase order status. Make sure this is the correct status for the current situation.`,
                        function () {
                            // User confirmed - status change is allowed
                            toggleCancellationFields();
                        },
                        function () {
                            // User cancelled - revert status
                            $(this).val(currentStatus);
                        }.bind(this)
                    );
                    return; // Don't call toggleCancellationFields yet
                }
            }

            toggleCancellationFields();
        });

        // Enhanced status change info
        function showStatusChangeInfo(message) {
            // Remove existing info messages
            $('.status-info').remove();

            var infoDiv = $('<div class="alert alert-info status-info mt-2">' +
                '<i class="fas fa-info-circle mr-2"></i>' + message + '</div>');

            $('#status').closest('.form-group').after(infoDiv);

            // Auto-remove after 7 seconds
            setTimeout(function () {
                infoDiv.fadeOut(300, function () { $(this).remove(); });
            }, 7000);
        }

        // Enhanced status change confirmation
        function showStatusChangeConfirmation(title, message, onConfirm, onCancel) {
            var modalHTML = `
                <div class="modal fade" id="statusChangeModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-edit mr-2"></i>${title}
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-question-circle text-info mr-3 mt-1" style="font-size: 1.5rem;"></i>
                                    <p class="mb-0">${message}</p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelStatusChange">
                                    <i class="fas fa-times mr-1"></i>Cancel
                                </button>
                                <button type="button" class="btn btn-info" id="confirmStatusChange">
                                    <i class="fas fa-check mr-1"></i>Update Status
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal
            $('#statusChangeModal').remove();

            // Add modal to page
            $('body').append(modalHTML);

            // Handle confirm button
            $('#confirmStatusChange').on('click', function () {
                $('#statusChangeModal').modal('hide');
                if (onConfirm) onConfirm();
            });

            // Handle cancel button
            $('#cancelStatusChange').on('click', function () {
                $('#statusChangeModal').modal('hide');
                if (onCancel) onCancel();
            });

            // Handle modal close
            $('#statusChangeModal').on('hidden.bs.modal', function () {
                if (onCancel) onCancel();
            });

            // Show modal
            $('#statusChangeModal').modal('show');
        }

        // Handle cancellation reason changes
        $('#cancellation_reason').change(toggleCustomReason);

        // Initialize on page load
        toggleCancellationFields();

        // Enhanced validation with better UX
        $('form').submit(function (e) {
            var status = $('#status').val();
            if (status === 'cancelled') {
                var reason = $('#cancellation_reason').val();
                var action = $('#cancelled_action').val();

                if (!reason) {
                    e.preventDefault();
                    showValidationError('Please select a cancellation reason.', '#cancellation_reason');
                    return false;
                }

                if (reason === 'other' && !$('#custom_reason').val().trim()) {
                    e.preventDefault();
                    showValidationError('Please specify the custom cancellation reason.', '#custom_reason');
                    return false;
                }

                if (!action) {
                    e.preventDefault();
                    showValidationError('Please select what action should be taken after cancellation.', '#cancelled_action');
                    return false;
                }

                // Show enhanced confirmation modal instead of basic confirm
                e.preventDefault();
                var actionText = $('#cancelled_action option:selected').text();
                var reasonText = reason === 'other' ? $('#custom_reason').val() : $('#cancellation_reason option:selected').text();

                showCancellationConfirmation(reasonText, actionText, function () {
                    // User confirmed - submit the form
                    $('form')[0].submit();
                });
                return false;
            }
        });

        // Enhanced validation error display
        function showValidationError(message, fieldSelector) {
            // Remove existing error messages
            $('.validation-error').remove();

            // Create error message
            var errorDiv = $('<div class="alert alert-danger validation-error mt-2 mb-0">' +
                '<i class="fas fa-exclamation-circle mr-2"></i>' + message + '</div>');

            // Insert after the field
            $(fieldSelector).closest('.form-group').append(errorDiv);

            // Focus on the field
            $(fieldSelector).focus();

            // Auto-remove after 5 seconds
            setTimeout(function () {
                errorDiv.fadeOut(300, function () { $(this).remove(); });
            }, 5000);
        }

        // Enhanced cancellation confirmation modal
        function showCancellationConfirmation(reason, action, onConfirm) {
            var modalHTML = `
                <div class="modal fade" id="cancellationConfirmModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-dark">
                                <h5 class="modal-title">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Confirm Purchase Order Cancellation
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-2 text-center">
                                        <i class="fas fa-ban text-danger" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <div class="col-md-10">
                                        <h6>You are about to cancel this purchase order:</h6>
                                        <div class="bg-light p-3 rounded mt-3">
                                            <strong>Reason:</strong> ${reason}<br>
                                            <strong>Action:</strong> ${action}
                                        </div>
                                        <div class="alert alert-warning mt-3 mb-0">
                                            <small><strong>Warning:</strong> This action will update the order status and may trigger additional processes. This cannot be easily undone.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times mr-1"></i>Keep Order Active
                                </button>
                                <button type="button" class="btn btn-danger" id="confirmCancellation">
                                    <i class="fas fa-ban mr-1"></i>Cancel Purchase Order
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal
            $('#cancellationConfirmModal').remove();

            // Add modal to page
            $('body').append(modalHTML);

            // Handle confirm button
            $('#confirmCancellation').on('click', function () {
                $('#cancellationConfirmModal').modal('hide');
                if (onConfirm) onConfirm();
            });

            // Show modal
            $('#cancellationConfirmModal').modal('show');
        }

        // Auto-set expected date based on status
        $('#status').change(function () {
            var status = $(this).val();
            var expectedDateField = $('#expected_date');

            if (status === 'sent' && !expectedDateField.val()) {
                // Set default expected date to 7 days from now
                var date = new Date();
                date.setDate(date.getDate() + 7);
                expectedDateField.val(date.toISOString().split('T')[0]);
            }
        });

        // Auto-update status when tracking number is entered
        // Enhanced auto-update status when tracking number is entered
        $('#tracking_number').on('input', function () {
            var trackingValue = $(this).val().trim();
            var statusField = $('#status');
            var currentStatus = statusField.val();

            if (trackingValue && currentStatus !== 'in_transit' && currentStatus !== 'received') {
                showTrackingStatusUpdate(function () {
                    statusField.val('in_transit');
                    // Trigger the status change event to set expected date if needed
                    statusField.trigger('change');
                    showStatusChangeInfo('Status automatically updated to "In Transit" because tracking number was added.');
                });
            }
        });

        // Enhanced tracking status update confirmation
        function showTrackingStatusUpdate(onConfirm) {
            var modalHTML = `
                <div class="modal fade" id="trackingStatusModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-truck mr-2"></i>Update Status to In Transit?
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-shipping-fast text-primary mr-3 mt-1" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <p class="mb-2">Adding a tracking number typically means the order is in transit.</p>
                                        <p class="mb-0 text-muted">Would you like to automatically update the status to "In Transit"?</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times mr-1"></i>Keep Current Status
                                </button>
                                <button type="button" class="btn btn-primary" id="updateToInTransit">
                                    <i class="fas fa-truck mr-1"></i>Update to In Transit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal
            $('#trackingStatusModal').remove();

            // Add modal to page
            $('body').append(modalHTML);

            // Handle confirm button
            $('#updateToInTransit').on('click', function () {
                $('#trackingStatusModal').modal('hide');
                if (onConfirm) onConfirm();
            });

            // Show modal
            $('#trackingStatusModal').modal('show');
        }

        // Enhanced tracking number validation
        $('#tracking_number').on('blur', function () {
            var trackingValue = $(this).val().trim();
            if (trackingValue) {
                // Enhanced validation - tracking numbers are usually alphanumeric and at least 6 characters
                if (trackingValue.length < 6 || !/^[a-zA-Z0-9\-\_\s]+$/.test(trackingValue)) {
                    $(this).addClass('is-invalid');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">Please enter a valid tracking number (at least 6 alphanumeric characters)</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid is-valid').addClass('is-valid');
                    $(this).next('.invalid-feedback').remove();
                    if (!$(this).next('.valid-feedback').length) {
                        $(this).after('<div class="valid-feedback">Valid tracking number format</div>');
                    }
                }
            } else {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.invalid-feedback, .valid-feedback').remove();
            }
        });
    });
</script>