<?php
$pageTitle = $data['title'] ?? 'Edit Contractor';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<div class="admin-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-0">
                    <i class="fas fa-edit"></i> Edit Contractor
                </h1>
                <p class="mb-0 mt-2 opacity-75"><?php echo htmlspecialchars($data['contractor']->contractor_name); ?>
                </p>
            </div>
            <div class="col-md-4 text-md-right">
                <a href="<?php echo URLROOT; ?>/contractor/viewContractor/<?php echo $data['contractor']->contractor_id; ?>"
                    class="btn btn-info mr-2">
                    <i class="fas fa-eye"></i> View Details
                </a>
                <a href="<?php echo URLROOT; ?>/contractor" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Directory
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
                        <i class="fas fa-edit"></i> Edit Contractor Information
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST"
                        action="<?php echo URLROOT; ?>/contractor/edit/<?php echo $data['contractor']->contractor_id; ?>">

                        <!-- Basic Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Basic Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contractor_name">Contractor Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="contractor_name"
                                                name="contractor_name"
                                                value="<?php echo htmlspecialchars($data['contractor']->contractor_name); ?>"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="<?php echo htmlspecialchars($data['contractor']->email ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone"
                                                value="<?php echo htmlspecialchars($data['contractor']->phone ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="specialization">Specialization</label>
                                            <input type="text" class="form-control" id="specialization"
                                                name="specialization"
                                                value="<?php echo htmlspecialchars($data['contractor']->specialization ?? ''); ?>"
                                                placeholder="e.g., Plumbing, Electrical, Carpentry">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Address Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                value="<?php echo htmlspecialchars($data['contractor']->address ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="city">City</label>
                                            <input type="text" class="form-control" id="city" name="city"
                                                value="<?php echo htmlspecialchars($data['contractor']->city ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="state">State</label>
                                            <input type="text" class="form-control" id="state" name="state"
                                                value="<?php echo htmlspecialchars($data['contractor']->state ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="zip_code">ZIP Code</label>
                                            <input type="text" class="form-control" id="zip_code" name="zip_code"
                                                value="<?php echo htmlspecialchars($data['contractor']->zip_code ?? ''); ?>">
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
                                            <label for="commission_type">Commission Type</label>
                                            <select class="form-control" id="commission_type" name="commission_type">
                                                <option value="percentage" <?php echo ($data['contractor']->commission_type ?? '') === 'percentage' ? 'selected' : ''; ?>>Percentage</option>
                                                <option value="fixed" <?php echo ($data['contractor']->commission_type ?? '') === 'fixed' ? 'selected' : ''; ?>>Fixed Amount</option>
                                                <option value="hourly" <?php echo ($data['contractor']->commission_type ?? '') === 'hourly' ? 'selected' : ''; ?>>Hourly Rate</option>
                                                <option value="contractor" <?php echo ($data['contractor']->commission_type ?? '') === 'contractor' ? 'selected' : ''; ?>>Contractor</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="hourly_rate">Hourly Rate ($)</label>
                                            <input type="number" class="form-control" id="hourly_rate"
                                                name="hourly_rate"
                                                value="<?php echo htmlspecialchars($data['contractor']->hourly_rate ?? ''); ?>"
                                                min="0" step="0.01" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="notes">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="4"
                                                placeholder="Additional notes about this contractor"><?php echo htmlspecialchars($data['contractor']->notes ?? ''); ?></textarea>
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
                                    <a href="<?php echo URLROOT; ?>/contractor/viewContractor/<?php echo $data['contractor']->contractor_id; ?>"
                                        class="btn btn-secondary mr-2">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Update Contractor
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

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>