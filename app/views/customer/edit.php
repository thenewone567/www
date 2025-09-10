<?php
// Extract data passed from controller
extract($data);

$pageTitle = 'Edit Customer - Admin Panel';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<div class="admin-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-0">
                    <i class="fas fa-edit"></i> Edit Customer
                </h1>
                <p class="mb-0 mt-2 opacity-75">Update customer information and settings</p>
            </div>
            <div class="col-md-4 text-md-right">
                <a href="<?php echo URLROOT; ?>/admin/viewCustomer/<?php echo $customer->customer_id; ?>"
                    class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Customer Details
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header py-2">
                    <h5 class="mb-0">
                        <i class="fas fa-edit"></i> Customer Information
                    </h5>
                </div>
                <div class="card-body py-3">
                    <form id="editCustomerForm" method="POST"
                        action="<?php echo URLROOT; ?>/customer/edit/<?php echo $customer->customer_id; ?>">

                        <!-- Basic Information -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2"><i class="fas fa-user"></i> Basic Information</h6>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-2">
                                        <label for="customer_name" class="form-label">Customer Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" id="customer_name"
                                            name="customer_name"
                                            value="<?php echo htmlspecialchars($customer->customer_name); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label for="customer_type" class="form-label">Customer Type</label>
                                        <select class="form-control form-control-sm" id="customer_type"
                                            name="customer_type">
                                            <option value="business" <?php echo ($customer->customer_type === 'business') ? 'selected' : ''; ?>>Business</option>
                                            <option value="individual" <?php echo ($customer->customer_type === 'individual') ? 'selected' : ''; ?>>
                                                Individual</option>
                                            <option value="walk-in" <?php echo ($customer->customer_type === 'walk-in') ? 'selected' : ''; ?>>Walk-in</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-control form-control-sm" id="status" name="status">
                                            <option value="active" <?php echo ($customer->status === 'active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="inactive" <?php echo ($customer->status === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2"><i class="fas fa-address-book"></i> Contact Information</h6>
                            <div class="form-group mb-2">
                                <label for="contact_info" class="form-label">Contact Information</label>
                                <input type="text" class="form-control form-control-sm" id="contact_info"
                                    name="contact_info"
                                    value="<?php echo htmlspecialchars($customer->contact_info ?? ''); ?>"
                                    placeholder="Phone, Email, etc.">
                                <small class="form-text text-muted">Enter phone number, email, or other contact
                                    details</small>
                            </div>
                        </div>

                        <!-- Financial Settings -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2"><i class="fas fa-credit-card"></i> Financial Settings</h6>
                            <div class="form-group mb-2">
                                <label for="credit_limit" class="form-label">Credit Limit</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control form-control-sm" id="credit_limit"
                                        name="credit_limit"
                                        value="<?php echo number_format($customer->credit_limit, 2, '.', ''); ?>"
                                        min="0" step="0.01">
                                </div>
                                <small class="form-text text-muted">Maximum credit amount allowed for this
                                    customer</small>
                            </div>
                        </div>

                        <!-- Customer Stats (Read-only) -->
                        <?php if (isset($customer->unique_id)): ?>
                            <div class="mb-3">
                                <h6 class="text-muted mb-2"><i class="fas fa-chart-bar"></i> Customer Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="form-label">Customer ID</label>
                                            <input type="text" class="form-control form-control-sm"
                                                value="<?php echo $customer->customer_id; ?>" readonly>
                                        </div>
                                    </div>
                                    <?php if (!empty($customer->unique_id)): ?>
                                        <div class="col-md-6">
                                            <div class="form-group mb-2">
                                                <label class="form-label">Unique ID</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    value="<?php echo $customer->unique_id; ?>" readonly>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo URLROOT; ?>/admin/viewCustomer/<?php echo $customer->customer_id; ?>"
                                class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Customer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('editCustomerForm');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show';
                        alertDiv.innerHTML = `
                    <i class="fas fa-check"></i> ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                        form.parentNode.insertBefore(alertDiv, form);

                        // Redirect after a short delay
                        setTimeout(() => {
                            if (data.redirect) {
                                window.location.href = '<?php echo URLROOT; ?>/' + data.redirect;
                            }
                        }, 1500);
                    } else {
                        // Show error message
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                        alertDiv.innerHTML = `
                    <i class="fas fa-exclamation-triangle"></i> ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                        form.parentNode.insertBefore(alertDiv, form);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                    alertDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle"></i> An error occurred. Please try again.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
                    form.parentNode.insertBefore(alertDiv, form);
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        });
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>