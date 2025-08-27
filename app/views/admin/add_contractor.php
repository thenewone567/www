<?php
// Extract data passed from controller
extract($data);

$pageTitle = 'Add Contractor - Admin Panel';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<div class="admin-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-0">
                    <i class="fas fa-hard-hat"></i> Add Contractor
                </h1>
                <p class="mb-0 mt-2 opacity-75">Add a new contractor with commission tracking</p>
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
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-plus"></i> Contractor Information
                    </h4>
                </div>
                <div class="card-body">
                    <form id="addContractorForm" method="POST" action="<?php echo URLROOT; ?>/admin/addContractor">

                        <!-- Company Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-building"></i> Company Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company_name">Company Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="company_name"
                                                name="company_name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact_person">Contact Person <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="contact_person"
                                                name="contact_person" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-address-card"></i> Contact Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" id="phone" name="phone" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <input type="text" class="form-control" id="address" name="address">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="city">City</label>
                                            <input type="text" class="form-control" id="city" name="city">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="state">State</label>
                                            <input type="text" class="form-control" id="state" name="state">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="zip_code">ZIP Code</label>
                                            <input type="text" class="form-control" id="zip_code" name="zip_code">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-tools"></i> Professional Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="specialty">Specialty/Trade <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="specialty" name="specialty"
                                                placeholder="e.g., Plumbing, Electrical, Carpentry" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="license_number">License Number</label>
                                            <input type="text" class="form-control" id="license_number"
                                                name="license_number">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Commission Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-percentage"></i> Commission & Payment Terms</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="commission_type">Commission Type</label>
                                            <select class="form-control" id="commission_type" name="commission_type">
                                                <option value="percentage">Percentage (%)</option>
                                                <option value="fixed">Fixed Amount ($)</option>
                                                <option value="tiered">Tiered (Based on volume)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="commission_value">Commission Value</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="commission_value"
                                                    name="commission_value" min="0" max="100" step="0.01" value="5">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="commission-unit">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="payment_terms">Payment Terms (Days)</label>
                                            <select class="form-control" id="payment_terms" name="payment_terms">
                                                <option value="0">Immediate</option>
                                                <option value="15">Net 15</option>
                                                <option value="30" selected>Net 30</option>
                                                <option value="45">Net 45</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Commission Tiers (shown only when tiered is selected) -->
                                <div id="commissionTiers" style="display: none;">
                                    <hr>
                                    <h6><i class="fas fa-layer-group"></i> Commission Tiers</h6>
                                    <div class="alert alert-info">
                                        <small><i class="fas fa-info-circle"></i> Define different commission rates
                                            based on monthly sales volume</small>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Tier 1: $0 - $1,000</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="tier1_rate" min="0"
                                                    max="100" step="0.1" value="3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Tier 2: $1,001 - $5,000</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="tier2_rate" min="0"
                                                    max="100" step="0.1" value="5">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Tier 3: $5,001+</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="tier3_rate" min="0"
                                                    max="100" step="0.1" value="7">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="text-right">
                                    <a href="<?php echo URLROOT; ?>/admin/users" class="btn btn-secondary mr-2">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Create Contractor
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update commission unit based on type and show/hide tiers
    document.getElementById('commission_type').addEventListener('change', function () {
        const unit = document.getElementById('commission-unit');
        const valueField = document.getElementById('commission_value');
        const tiersDiv = document.getElementById('commissionTiers');

        if (this.value === 'percentage') {
            unit.textContent = '%';
            valueField.max = '100';
            valueField.placeholder = 'e.g., 5 for 5%';
            tiersDiv.style.display = 'none';
        } else if (this.value === 'fixed') {
            unit.textContent = '$';
            valueField.max = '999999';
            valueField.placeholder = 'e.g., 100 for $100 per job';
            tiersDiv.style.display = 'none';
        } else if (this.value === 'tiered') {
            unit.textContent = '%';
            valueField.max = '100';
            valueField.value = '';
            valueField.placeholder = 'Will use tier rates';
            valueField.disabled = true;
            tiersDiv.style.display = 'block';
        }

        // Re-enable value field for percentage and fixed
        if (this.value !== 'tiered') {
            valueField.disabled = false;
        }
    });

    // Form submission handler
    document.getElementById('addContractorForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

        fetch('<?php echo URLROOT; ?>/admin/addContractor', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Contractor created successfully!');
                    if (data.redirect) {
                        window.location.href = '<?php echo URLROOT; ?>/' + data.redirect;
                    } else {
                        window.location.href = '<?php echo URLROOT; ?>/admin/users';
                    }
                } else {
                    alert('Error: ' + (data.message || 'Failed to create contractor'));
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the contractor');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>