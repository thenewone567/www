<?php
$pageTitle = $data['title'] ?? 'Contractor Details';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<div class="admin-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-0">
                    <i class="fas fa-hard-hat"></i>
                    <?php echo htmlspecialchars($data['contractor']->contractor_name); ?>
                </h1>
                <p class="mb-0 mt-2 opacity-75">Contractor Details & Performance</p>
            </div>
            <div class="col-md-4 text-md-right">
                <a href="<?php echo URLROOT; ?>/contractor/edit/<?php echo $data['contractor']->contractor_id; ?>"
                    class="btn btn-warning mr-2">
                    <i class="fas fa-edit"></i> Edit Contractor
                </a>
                <a href="<?php echo URLROOT; ?>/contractor" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Directory
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Contractor Name:</strong><br>
                                <?php echo htmlspecialchars($data['contractor']->contractor_name); ?>
                            </p>
                            <p><strong>Email:</strong><br>
                                <?php if (!empty($data['contractor']->email)): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($data['contractor']->email); ?>">
                                        <?php echo htmlspecialchars($data['contractor']->email); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Not provided</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Phone:</strong><br>
                                <?php if (!empty($data['contractor']->phone)): ?>
                                    <a href="tel:<?php echo htmlspecialchars($data['contractor']->phone); ?>">
                                        <?php echo htmlspecialchars($data['contractor']->phone); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Not provided</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Specialization:</strong><br>
                                <?php echo htmlspecialchars($data['contractor']->specialization ?? 'Not specified'); ?>
                            </p>
                            <p><strong>Company:</strong><br>
                                <?php echo htmlspecialchars($data['contractor']->company_name ?? 'Not provided'); ?>
                            </p>
                            <p><strong>Status:</strong><br>
                                <?php if ($data['contractor']->is_active == 1): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactive</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <?php if (!empty($data['contractor']->address) || !empty($data['contractor']->city) || !empty($data['contractor']->state)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Address Information</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($data['contractor']->address)): ?>
                            <p><strong>Address:</strong><br>
                                <?php echo htmlspecialchars($data['contractor']->address); ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($data['contractor']->city) || !empty($data['contractor']->state) || !empty($data['contractor']->postal_code)): ?>
                            <p><strong>City, State ZIP:</strong><br>
                                <?php
                                $location = [];
                                if (!empty($data['contractor']->city))
                                    $location[] = $data['contractor']->city;
                                if (!empty($data['contractor']->state))
                                    $location[] = $data['contractor']->state;
                                if (!empty($data['contractor']->postal_code))
                                    $location[] = $data['contractor']->postal_code;
                                echo htmlspecialchars(implode(', ', $location));
                                ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Professional Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tools"></i> Professional Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>License Number:</strong><br>
                                <?php echo htmlspecialchars($data['contractor']->license_number ?? 'Not provided'); ?>
                            </p>
                            <p><strong>Commission Type:</strong><br>
                                <?php echo htmlspecialchars($data['contractor']->commission_type ?? 'Not specified'); ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Commission Rate:</strong><br>
                                <?php if (!empty($data['contractor']->commission_rate)): ?>
                                    <?php echo number_format($data['contractor']->commission_rate, 2); ?>%
                                <?php else: ?>
                                    <span class="text-muted">Not set</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Hourly Rate:</strong><br>
                                <?php if (!empty($data['contractor']->hourly_rate)): ?>
                                    $<?php echo number_format($data['contractor']->hourly_rate, 2); ?>
                                <?php else: ?>
                                    <span class="text-muted">Not set</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <?php if (!empty($data['contractor']->notes)): ?>
                        <hr>
                        <p><strong>Notes:</strong><br>
                            <?php echo nl2br(htmlspecialchars($data['contractor']->notes)); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <h3 class="text-primary mb-0">0</h3>
                            <small class="text-muted">Active Projects</small>
                        </div>
                        <div class="col-12 mb-3">
                            <h3 class="text-success mb-0">$0.00</h3>
                            <small class="text-muted">Total Earnings</small>
                        </div>
                        <div class="col-12">
                            <h3 class="text-info mb-0">0</h3>
                            <small class="text-muted">Completed Jobs</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar"></i> Account Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Member Since:</strong><br>
                        <?php if (!empty($data['contractor']->created_at)): ?>
                            <?php echo date('M d, Y', strtotime($data['contractor']->created_at)); ?>
                        <?php else: ?>
                            <span class="text-muted">Unknown</span>
                        <?php endif; ?>
                    </p>
                    <p><strong>Last Updated:</strong><br>
                        <?php if (!empty($data['contractor']->updated_at)): ?>
                            <?php echo date('M d, Y g:i A', strtotime($data['contractor']->updated_at)); ?>
                        <?php else: ?>
                            <span class="text-muted">Never updated</span>
                        <?php endif; ?>
                    </p>
                    <p><strong>Unique ID:</strong><br>
                        <code><?php echo htmlspecialchars($data['contractor']->unique_id ?? 'Not assigned'); ?></code>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>