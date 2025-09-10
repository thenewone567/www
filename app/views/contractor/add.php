<?php
// Extract data passed from controller
extract($data);

$pageTitle = 'Add Contractor - Contractor Management';
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
                <a href="<?php echo URLROOT; ?>/contractor" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Contractors
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
                    <form id="addContractorForm" method="POST" action="<?php echo URLROOT; ?>/contractor/add">

                        <!-- Company Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-building"></i> Company Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Contractor Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact_info">Email Address <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="contact_info"
                                                name="contact_info" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-address-card"></i> Additional Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="specialization">Specialization <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="specialization"
                                                name="specialization"
                                                placeholder="e.g., Plumbing, Electrical, Carpentry" required>
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
                            </div>
                        </div>

                        <!-- Commission Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-percentage"></i> Commission Type</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tier">Commission Type <span class="text-danger">*</span></label>
                                            <select class="form-control" id="tier" name="tier" required>
                                                <option value="">Select Commission Type</option>
                                                <option value="percentage">Percentage Based</option>
                                                <option value="fixed">Fixed Amount</option>
                                                <option value="hourly">Hourly Rate</option>
                                            </select>
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
                                    <a href="<?php echo URLROOT; ?>/contractor" class="btn btn-secondary mr-2">
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
    // Form submission handler
    document.getElementById('addContractorForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

        fetch('<?php echo URLROOT; ?>/contractor/add', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Contractor created successfully!');
                    window.location.href = '<?php echo URLROOT; ?>/contractor';
                } else {
                    alert('Error: ' + (data.message || data.error || 'Failed to create contractor'));

                    // Show specific field errors if available
                    if (data.errors) {
                        let errorMsg = 'Please fix the following errors:\n';
                        for (let field in data.errors) {
                            errorMsg += '- ' + data.errors[field] + '\n';
                        }
                        alert(errorMsg);
                    }

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