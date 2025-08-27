<?php
// Extract data passed from controller
extract($data);

$pageTitle = 'Add Customer - Admin Panel';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<div class="admin-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-0">
                    <i class="fas fa-shopping-cart"></i> Add Customer
                </h1>
                <p class="mb-0 mt-2 opacity-75">Add a new customer with discount privileges</p>
            </div>
            <div class="col-md-4 text-md-right">
                <a href="<?php echo URLROOT; ?>/admin/users" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users
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
                        <i class="fas fa-plus"></i> Customer Information
                    </h5>
                </div>
                <div class="card-body py-3">
                    <form id="addCustomerForm" method="POST" action="<?php echo URLROOT; ?>/admin/addCustomer">

                        <!-- Basic Information -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2"><i class="fas fa-user"></i> Basic Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label for="contact_person" class="form-label">Contact Person <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" id="contact_person" name="contact_person" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control form-control-sm" id="phone" name="phone" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control form-control-sm" id="email" name="email">
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2"><i class="fas fa-map-marker-alt"></i> Address</h6>
                            <div class="form-group mb-2">
                                <label for="address" class="form-label">Street Address</label>
                                <input type="text" class="form-control form-control-sm" id="address" name="address">
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group mb-2">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control form-control-sm" id="city" name="city">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label for="state" class="form-label">State</label>
                                        <input type="text" class="form-control form-control-sm" id="state" name="state">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label for="zip_code" class="form-label">ZIP</label>
                                        <input type="text" class="form-control form-control-sm" id="zip_code" name="zip_code">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Discount & Payment -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2"><i class="fas fa-percent"></i> Discount & Payment</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label for="discount_type" class="form-label">Discount Type</label>
                                        <select class="form-control form-control-sm" id="discount_type" name="discount_type">
                                            <option value="percentage">Percentage (%)</option>
                                            <option value="fixed">Fixed Amount ($)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label for="discount_value" class="form-label">Discount Value</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control" id="discount_value" name="discount_value" min="0" max="100" step="0.01" value="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="discount-unit">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label for="credit_limit" class="form-label">Credit Limit ($)</label>
                                        <input type="number" class="form-control form-control-sm" id="credit_limit" name="credit_limit" min="0" step="0.01" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label for="payment_terms" class="form-label">Payment Terms</label>
                                        <select class="form-control form-control-sm" id="payment_terms" name="payment_terms">
                                            <option value="0">Cash on Delivery</option>
                                            <option value="15">Net 15</option>
                                            <option value="30" selected>Net 30</option>
                                            <option value="45">Net 45</option>
                                            <option value="60">Net 60</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <div class="text-right mt-3 pt-2 border-top">
                            <a href="<?php echo URLROOT; ?>/admin/users" class="btn btn-secondary btn-sm mr-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-save"></i> Create Customer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update discount unit based on type
    document.getElementById('discount_type').addEventListener('change', function () {
        const unit = document.getElementById('discount-unit');
        const valueField = document.getElementById('discount_value');

        if (this.value === 'percentage') {
            unit.textContent = '%';
            valueField.max = '100';
            valueField.placeholder = 'e.g., 10 for 10%';
        } else {
            unit.textContent = '$';
            valueField.max = '999999';
            valueField.placeholder = 'e.g., 50 for $50 off';
        }
    });

    // Form submission handler
    document.getElementById('addCustomerForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

        fetch('<?php echo URLROOT; ?>/admin/addCustomer', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Customer created successfully!');
                    if (data.redirect) {
                        window.location.href = '<?php echo URLROOT; ?>/' + data.redirect;
                    } else {
                        window.location.href = '<?php echo URLROOT; ?>/admin/users';
                    }
                } else {
                    alert('Error: ' + (data.message || 'Failed to create customer'));
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the customer');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>