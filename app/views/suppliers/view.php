<?php
require_once APPROOT . DS . 'app' . DS . 'helpers' . DS . 'view_helpers.php';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<!-- Debug output removed -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/app-unified.css">

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card theme-card-light shadow mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="mb-0"><i class="fas fa-eye"></i> Supplier Details</h3>
                    <div class="btn-group">
                        <a href="<?php echo URLROOT; ?>/suppliers/edit/<?php echo isset($data['supplier']) && is_object($data['supplier']) ? $data['supplier']->supplier_id : ''; ?>"
                            class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit Supplier
                        </a>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-plus"></i> Create Order
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item"
                                    href="<?php echo URLROOT; ?>/purchases/add?supplier_id=<?php echo isset($data['supplier']) && is_object($data['supplier']) ? $data['supplier']->supplier_id : ''; ?>">
                                    <i class="fas fa-shopping-cart mr-2"></i>Purchase Order
                                </a>
                                <a class="dropdown-item"
                                    href="<?php echo URLROOT; ?>/inventory/receiving?supplier_id=<?php echo isset($data['supplier']) && is_object($data['supplier']) ? $data['supplier']->supplier_id : ''; ?>">
                                    <i class="fas fa-truck mr-2"></i>Receiving Order
                                </a>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-cog"></i> Actions
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item"
                                    href="<?php echo URLROOT; ?>/suppliers/link?supplier_id=<?php echo isset($data['supplier']) && is_object($data['supplier']) ? $data['supplier']->supplier_id : ''; ?>">
                                    <i class="fas fa-link mr-2"></i>Link Products
                                </a>
                                <a class="dropdown-item" href="#" onclick="viewPurchaseHistory()">
                                    <i class="fas fa-history mr-2"></i>Purchase History
                                </a>
                                <a class="dropdown-item" href="#" onclick="generateReport()">
                                    <i class="fas fa-chart-bar mr-2"></i>Performance Report
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" onclick="evaluateSupplier()">
                                    <i class="fas fa-star mr-2"></i>Evaluate Supplier
                                </a>
                                <a class="dropdown-item" href="#" onclick="updatePaymentTerms()">
                                    <i class="fas fa-credit-card mr-2"></i>Payment Terms
                                </a>
                                <div class="dropdown-divider"></div>
                                <?php if ((isset($data['supplier']) && is_object($data['supplier']) && $data['supplier']->status === 'active')): ?>
                                    <a class="dropdown-item text-warning" href="#" onclick="deactivateSupplier()">
                                        <i class="fas fa-pause mr-2"></i>Deactivate
                                    </a>
                                <?php else: ?>
                                    <a class="dropdown-item text-success" href="#" onclick="activateSupplier()">
                                        <i class="fas fa-play mr-2"></i>Activate
                                    </a>
                                <?php endif; ?>
                                <a class="dropdown-item text-danger" href="#" onclick="deleteSupplier()">
                                    <i class="fas fa-archive mr-2"></i>Archive
                                </a>
                            </div>
                        </div>
                        <a href="<?php echo URLROOT; ?>/suppliers" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    // Debug output for troubleshooting
                    if (isset($_GET['debug']) && $_GET['debug'] == '1') {
                        echo '<div class="alert alert-warning"><b>DEBUG:</b> Supplier ID: ' . htmlspecialchars($_GET['id'] ?? '') . '<br>Supplier object:<pre>';
                        print_r(isset($data['supplier']) && is_object($data['supplier']) ? $data['supplier'] : null);
                        echo '</pre></div>';
                    }
                    ?>
                    <?php if (isset($data['supplier']) && is_object($data['supplier'])):
                        $s = $data['supplier'];
                        $supplier = new Supplier(); // Create supplier instance for audit queries
                        ?>

                        <!-- KPI Cards Section - First Row (6 cards) -->
                        <div class="row mb-4">
                            <!-- Status Card -->
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div
                                    class="card border-<?php echo (safeProperty($s, 'status') === 'active') ? 'success' : ((safeProperty($s, 'status') === 'pending') ? 'warning' : 'danger'); ?> h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i
                                                class="fas fa-<?php echo (safeProperty($s, 'status') === 'active') ? 'check-circle' : ((safeProperty($s, 'status') === 'pending') ? 'clock' : 'times-circle'); ?> 
                                               fa-2x text-<?php echo (safeProperty($s, 'status') === 'active') ? 'success' : ((safeProperty($s, 'status') === 'pending') ? 'warning' : 'danger'); ?>"></i>
                                        </div>
                                        <h6
                                            class="text-<?php echo (safeProperty($s, 'status') === 'active') ? 'success' : ((safeProperty($s, 'status') === 'pending') ? 'warning' : 'danger'); ?> mb-1">
                                            <?php echo safeProperty($s, 'status') ? htmlspecialchars(ucfirst(safeProperty($s, 'status'))) : '-'; ?>
                                        </h6>
                                        <small class="text-muted">Status</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Added By Card -->
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card border-info h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="fas fa-user-plus fa-2x text-info"></i>
                                        </div>
                                        <h6 class="text-info mb-1 text-truncate"
                                            title="<?php echo safeProperty($s, 'added_by_full_name') ? htmlspecialchars(safeProperty($s, 'added_by_full_name')) : '-'; ?>">
                                            <?php echo safeProperty($s, 'added_by_full_name') ? htmlspecialchars(safeProperty($s, 'added_by_full_name')) : '-'; ?>
                                        </h6>
                                        <small class="text-muted">Added By</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Supplier Tier Card -->
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card border-<?php
                                $tier = $s->supplier_tier ?? 'Standard';
                                echo (strtolower($tier) === 'gold') ? 'warning' :
                                    ((strtolower($tier) === 'silver') ? 'secondary' :
                                        ((strtolower($tier) === 'bronze') ? 'warning' : 'light'));
                                ?> h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="fas fa-crown fa-2x text-<?php
                                            echo (strtolower($tier) === 'gold') ? 'warning' :
                                                ((strtolower($tier) === 'silver') ? 'secondary' :
                                                    ((strtolower($tier) === 'bronze') ? 'warning' : 'muted'));
                                            ?>"></i>
                                        </div>
                                        <h6 class="text-<?php
                                        echo (strtolower($tier) === 'gold') ? 'warning' :
                                            ((strtolower($tier) === 'silver') ? 'secondary' :
                                                ((strtolower($tier) === 'bronze') ? 'warning' : 'muted'));
                                        ?> mb-1">
                                            <?php echo htmlspecialchars(ucfirst($tier)); ?>
                                        </h6>
                                        <small class="text-muted">Tier</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Performance Score Card -->
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card border-<?php
                                $score = $s->reliability_score ?? 0;
                                echo ($score >= 8) ? 'success' : (($score >= 6) ? 'warning' : 'danger');
                                ?> h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="fas fa-chart-line fa-2x text-<?php
                                            echo ($score >= 8) ? 'success' : (($score >= 6) ? 'warning' : 'danger');
                                            ?>"></i>
                                        </div>
                                        <h6 class="text-<?php
                                        echo ($score >= 8) ? 'success' : (($score >= 6) ? 'warning' : 'danger');
                                        ?> mb-1">
                                            <?php echo number_format($score, 1); ?>/10
                                        </h6>
                                        <small class="text-muted">Performance</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Quality Rating Card -->
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card border-warning h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="fas fa-star fa-2x text-warning"></i>
                                        </div>
                                        <h6 class="text-warning mb-1">
                                            <?php if (!empty($s->quality_rating)): ?>
                                                <?php echo $s->quality_rating; ?>/5
                                            <?php else: ?>
                                                Not Rated
                                            <?php endif; ?>
                                        </h6>
                                        <small class="text-muted">Quality</small>
                                    </div>
                                </div>
                            </div>

                            <!-- On-Time Delivery Rate Card -->
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card border-<?php
                                $onTimeRate = $s->on_time_delivery_rate ?? 0;
                                echo ($onTimeRate >= 90) ? 'success' : (($onTimeRate >= 75) ? 'warning' : 'danger');
                                ?> h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="fas fa-truck fa-2x text-<?php
                                            echo ($onTimeRate >= 90) ? 'success' : (($onTimeRate >= 75) ? 'warning' : 'danger');
                                            ?>"></i>
                                        </div>
                                        <h6 class="text-<?php
                                        echo ($onTimeRate >= 90) ? 'success' : (($onTimeRate >= 75) ? 'warning' : 'danger');
                                        ?> mb-1">
                                            <?php echo number_format($onTimeRate, 1); ?>%
                                        </h6>
                                        <small class="text-muted">On-Time</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KPI Cards Section - Second Row (6 cards) -->
                        <div class="row mb-4">
                            <!-- Payment Terms Card -->
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="fas fa-credit-card fa-2x text-primary"></i>
                                        </div>
                                        <h6 class="text-primary mb-1">
                                            <?php echo htmlspecialchars($s->preferred_payment_terms ?? 'Net 30'); ?>
                                        </h6>
                                        <small class="text-muted">Payment Terms</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Credit Limit Card -->
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card border-info h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="fas fa-wallet fa-2x text-info"></i>
                                        </div>
                                        <h6 class="text-info mb-1">
                                            <?php echo formatCurrency($s->credit_limit ?? 0, 0); ?>
                                        </h6>
                                        <small class="text-muted">Credit Limit</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Outstanding Card -->
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card border-<?php
                                $outstanding = $s->current_outstanding ?? 0;
                                $creditLimit = $s->credit_limit ?? 0;
                                $utilizationRate = $creditLimit > 0 ? ($outstanding / $creditLimit) * 100 : 0;
                                echo ($utilizationRate >= 80) ? 'danger' : (($utilizationRate >= 60) ? 'warning' : 'success');
                                ?> h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="fas fa-exclamation-triangle fa-2x text-<?php
                                            echo ($utilizationRate >= 80) ? 'danger' : (($utilizationRate >= 60) ? 'warning' : 'success');
                                            ?>"></i>
                                        </div>
                                        <h6 class="text-<?php
                                        echo ($utilizationRate >= 80) ? 'danger' : (($utilizationRate >= 60) ? 'warning' : 'success');
                                        ?> mb-1">
                                            <?php echo formatCurrency($outstanding, 0); ?>
                                        </h6>
                                        <small class="text-muted">Outstanding</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Average Delivery Time Card -->
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card border-info h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="fas fa-clock fa-2x text-info"></i>
                                        </div>
                                        <h6 class="text-info mb-1">
                                            <?php if (!empty($s->average_delivery_days)): ?>
                                                <?php echo number_format($s->average_delivery_days, 1); ?> days
                                            <?php else: ?>
                                                No data
                                            <?php endif; ?>
                                        </h6>
                                        <small class="text-muted">Avg Delivery</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Time Card -->
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card border-info h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="fas fa-truck fa-2x text-info"></i>
                                        </div>
                                        <h6 class="text-info mb-1">
                                            <?php if (!empty($s->default_delivery_days)): ?>
                                                <?php echo $s->default_delivery_days; ?> days
                                            <?php else: ?>
                                                Not Set
                                            <?php endif; ?>
                                        </h6>
                                        <small class="text-muted">Delivery Time</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Verification Status Card -->
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div
                                    class="card border-<?php echo (!empty($s->is_verified) && $s->is_verified == 1) ? 'success' : 'warning'; ?> h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i
                                                class="fas fa-<?php echo (!empty($s->is_verified) && $s->is_verified == 1) ? 'check-circle' : 'exclamation-triangle'; ?> 
                                               fa-2x text-<?php echo (!empty($s->is_verified) && $s->is_verified == 1) ? 'success' : 'warning'; ?>"></i>
                                        </div>
                                        <h6
                                            class="text-<?php echo (!empty($s->is_verified) && $s->is_verified == 1) ? 'success' : 'warning'; ?> mb-1">
                                            <?php echo (!empty($s->is_verified) && $s->is_verified == 1) ? 'Verified' : 'Unverified'; ?>
                                        </h6>
                                        <small class="text-muted">Verification</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Remaining Details Table -->
                        <h5 class="mb-3"><i class="fas fa-info-circle mr-2"></i>Additional Details</h5>
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th>Value</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Updated By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>Name</th>
                                    <td><?php echo htmlspecialchars($s->supplier_name); ?></td>
                                    <td><?php echo !empty($s->created_at) ? htmlspecialchars($s->created_at) : '-'; ?></td>
                                    <?php
                                    $nameAudit = $supplier->getFieldLastUpdate($s->supplier_id, 'supplier_name');
                                    ?>
                                    <td><?php echo $nameAudit ? htmlspecialchars($nameAudit->updated_at) : '-'; ?></td>
                                    <td><?php echo $nameAudit ? htmlspecialchars($nameAudit->updated_by_name) : '-'; ?></td>
                                </tr>
                                <tr>
                                    <th>Contact Person</th>
                                    <td><?php echo htmlspecialchars($s->contact_person ?? '-'); ?></td>
                                    <td><?php echo !empty($s->created_at) ? htmlspecialchars($s->created_at) : '-'; ?></td>
                                    <?php
                                    $contactAudit = $supplier->getFieldLastUpdate($s->supplier_id, 'contact_person');
                                    ?>
                                    <td><?php echo $contactAudit ? htmlspecialchars($contactAudit->updated_at) : '-'; ?>
                                    </td>
                                    <td><?php echo $contactAudit ? htmlspecialchars($contactAudit->updated_by_name) : '-'; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td><?php echo htmlspecialchars($s->phone ?? '-'); ?></td>
                                    <td><?php echo !empty($s->created_at) ? htmlspecialchars($s->created_at) : '-'; ?></td>
                                    <?php
                                    $phoneAudit = $supplier->getFieldLastUpdate($s->supplier_id, 'phone');
                                    ?>
                                    <td><?php echo $phoneAudit ? htmlspecialchars($phoneAudit->updated_at) : '-'; ?></td>
                                    <td><?php echo $phoneAudit ? htmlspecialchars($phoneAudit->updated_by_name) : '-'; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?php echo htmlspecialchars($s->email ?? '-'); ?></td>
                                    <td><?php echo !empty($s->created_at) ? htmlspecialchars($s->created_at) : '-'; ?></td>
                                    <?php
                                    $emailAudit = $supplier->getFieldLastUpdate($s->supplier_id, 'email');
                                    ?>
                                    <td><?php echo $emailAudit ? htmlspecialchars($emailAudit->updated_at) : '-'; ?></td>
                                    <td><?php echo $emailAudit ? htmlspecialchars($emailAudit->updated_by_name) : '-'; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td><?php echo htmlspecialchars($s->address ?? '-'); ?></td>
                                    <td><?php echo !empty($s->created_at) ? htmlspecialchars($s->created_at) : '-'; ?></td>
                                    <?php
                                    $addressAudit = $supplier->getFieldLastUpdate($s->supplier_id, 'address');
                                    ?>
                                    <td><?php echo $addressAudit ? htmlspecialchars($addressAudit->updated_at) : '-'; ?>
                                    </td>
                                    <td><?php echo $addressAudit ? htmlspecialchars($addressAudit->updated_by_name) : '-'; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>GST Number</th>
                                    <td><?php echo htmlspecialchars($s->gst_number ?? '-'); ?></td>
                                    <td><?php echo !empty($s->created_at) ? htmlspecialchars($s->created_at) : '-'; ?></td>
                                    <?php
                                    $gstAudit = $supplier->getFieldLastUpdate($s->supplier_id, 'gst_number');
                                    ?>
                                    <td><?php echo $gstAudit ? htmlspecialchars($gstAudit->updated_at) : '-'; ?></td>
                                    <td><?php echo $gstAudit ? htmlspecialchars($gstAudit->updated_by_name) : '-'; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-danger">Supplier not found.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Purchase Order History and Supplier Products Section -->
            <?php if (!empty($data['supplier'])): ?>
                <div class="row">
                    <!-- Supplier Products Section - Left Side -->
                    <div class="col-lg-6">
                        <div class="card theme-card-light shadow mb-4">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="mb-0"><i class="fas fa-boxes"></i> Supplier Products</h5>
                                <?php if (!empty($data['supplier_products'])): ?>
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-primary">Total Products:
                                            <?php echo count($data['supplier_products']); ?></span>
                                        <span class="badge bg-info">Active Links:
                                            <?php echo count(array_filter($data['supplier_products'], function ($p) {
                                                return $p->status === 'active';
                                            })); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($data['supplier_products'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover table-sm" id="supplierProductsTable">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="35%">Product Name</th>
                                                    <th width="15%">SKU</th>
                                                    <th width="15%">Price</th>
                                                    <th width="15%">Status</th>
                                                    <th width="15%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data['supplier_products'] as $index => $product): ?>
                                                    <tr>
                                                        <td><?php echo $index + 1; ?></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($product->product_name); ?></strong>
                                                            <?php if (!empty($product->brand_name)): ?>
                                                                <br><small
                                                                    class="text-muted"><?php echo htmlspecialchars($product->brand_name); ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($product->sku ?? '-'); ?></td>
                                                        <td>
                                                            <?php if (!empty($product->supplier_price)): ?>
                                                                <strong><?php echo formatCurrency($product->supplier_price, 2); ?></strong>
                                                            <?php else: ?>
                                                                <span class="text-muted">Not set</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $statusClass = '';
                                                            $status = $product->status ?? 'inactive';
                                                            switch (strtolower($status)) {
                                                                case 'active':
                                                                    $statusClass = 'bg-success';
                                                                    break;
                                                                case 'inactive':
                                                                    $statusClass = 'bg-secondary';
                                                                    break;
                                                                case 'discontinued':
                                                                    $statusClass = 'bg-danger';
                                                                    break;
                                                                default:
                                                                    $statusClass = 'bg-light text-dark';
                                                            }
                                                            ?>
                                                            <span class="badge <?php echo $statusClass; ?>">
                                                                <?php echo htmlspecialchars(ucfirst($status)); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <a href="<?php echo URLROOT; ?>/products/view/<?php echo $product->product_id; ?>"
                                                                    class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip"
                                                                    title="View Product">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="<?php echo URLROOT; ?>/products/edit/<?php echo $product->product_id; ?>"
                                                                    class="btn btn-outline-warning btn-sm" data-bs-toggle="tooltip"
                                                                    title="Edit Product">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">No Products Linked</h6>
                                        <p class="text-muted small">This supplier has no products linked.</p>
                                        <a href="<?php echo URLROOT; ?>/suppliers/link/<?php echo $data['supplier']->supplier_id; ?>"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-link"></i> Link Products
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Order History Section - Right Side -->
                    <div class="col-lg-6">
                        <div class="card theme-card-light shadow mb-4" id="purchaseHistorySection">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Purchase Order History</h5>
                                <?php if (!empty($data['supplier_stats'])): ?>
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-primary">Total Orders:
                                            <?php echo number_format($data['supplier_stats']->total_orders ?? 0); ?></span>
                                        <span class="badge bg-success">Total Value:
                                            <?php echo formatCurrency($data['supplier_stats']->total_purchased ?? 0, 2); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($data['purchase_history'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover table-sm" id="purchaseHistoryTable">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th width="8%">#</th>
                                                    <th width="25%">PO Number</th>
                                                    <th width="18%">Date</th>
                                                    <th width="18%">Status</th>
                                                    <th width="18%">Amount</th>
                                                    <th width="13%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data['purchase_history'] as $index => $order): ?>
                                                    <tr>
                                                        <td><?php echo $index + 1; ?></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($order->po_number ?? 'PO-' . $order->purchase_id); ?></strong>
                                                            <br><small
                                                                class="text-muted"><?php echo number_format($order->item_count ?? 0); ?>
                                                                items</small>
                                                        </td>
                                                        <td>
                                                            <span><?php echo !empty($order->purchase_date) ? date('d-m-Y', strtotime($order->purchase_date)) : '-'; ?></span>
                                                            <?php if (!empty($order->expected_date)): ?>
                                                                <br><small class="text-muted">Exp:
                                                                    <?php echo date('d-m-Y', strtotime($order->expected_date)); ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $statusClass = '';
                                                            $status = $order->status ?? 'unknown';
                                                            switch (strtolower($status)) {
                                                                case 'pending':
                                                                    $statusClass = 'bg-warning text-dark';
                                                                    break;
                                                                case 'sent':
                                                                    $statusClass = 'bg-info';
                                                                    break;
                                                                case 'partially_received':
                                                                    $statusClass = 'bg-secondary';
                                                                    break;
                                                                case 'received':
                                                                    $statusClass = 'bg-success';
                                                                    break;
                                                                case 'cancelled':
                                                                    $statusClass = 'bg-danger';
                                                                    break;
                                                                default:
                                                                    $statusClass = 'bg-light text-dark';
                                                            }
                                                            ?>
                                                            <span class="badge <?php echo $statusClass; ?>">
                                                                <?php echo htmlspecialchars(ucfirst($status)); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong><?php echo formatCurrency($order->total_amount ?? 0, 2); ?></strong>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <a href="<?php echo URLROOT; ?>/purchases/details/<?php echo $order->purchase_id; ?>"
                                                                    class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip"
                                                                    title="View Details">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <?php if (in_array(strtolower($order->status ?? ''), ['pending', 'sent'])): ?>
                                                                    <a href="<?php echo URLROOT; ?>/purchases/edit/<?php echo $order->purchase_id; ?>"
                                                                        class="btn btn-outline-warning btn-sm" data-bs-toggle="tooltip"
                                                                        title="Edit Order">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">No Purchase Orders Found</h6>
                                        <p class="text-muted small">This supplier has no purchase order history.</p>
                                        <a href="<?php echo URLROOT; ?>/purchases/add?supplier_id=<?php echo $data['supplier']->supplier_id; ?>"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Create First Order
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Statistics Row -->
                <?php if (!empty($data['supplier_stats'])): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-3"><i class="fas fa-chart-bar mr-2"></i>Order Summary
                                    </h6>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="h4 mb-0 text-primary">
                                                <?php echo number_format($data['supplier_stats']->total_orders ?? 0); ?>
                                            </div>
                                            <small class="text-muted">Total Orders</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="h4 mb-0 text-success">
                                                <?php echo formatCurrency($data['supplier_stats']->total_purchased ?? 0, 2); ?>
                                            </div>
                                            <small class="text-muted">Total Value</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-3"><i class="fas fa-calendar mr-2"></i>Order Timeline
                                    </h6>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="h6 mb-0 text-info">
                                                <?php echo !empty($data['supplier_stats']->first_order_date) ? date('d-m-Y', strtotime($data['supplier_stats']->first_order_date)) : '-'; ?>
                                            </div>
                                            <small class="text-muted">First Order</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="h6 mb-0 text-warning">
                                                <?php echo !empty($data['supplier_stats']->last_order_date) ? date('d-m-Y', strtotime($data['supplier_stats']->last_order_date)) : '-'; ?>
                                            </div>
                                            <small class="text-muted">Last Order</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-danger">
                    <h4><i class="fas fa-exclamation-triangle"></i> Supplier Not Found</h4>
                    <p>The requested supplier could not be loaded. This might be because:</p>
                    <ul>
                        <li>The supplier ID is invalid</li>
                        <li>The supplier has been deleted</li>
                        <li>You don't have permission to view this supplier</li>
                        <li>There's a database connection issue</li>
                    </ul>
                    <a href="<?php echo URLROOT; ?>/suppliers" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Suppliers List
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Include DataTables CSS and JS for interactive table functionality -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    // Wait for jQuery to be available (loaded in footer)
    function waitForJQuery() {
        if (typeof $ !== 'undefined') {
            initializeTables();
        } else {
            setTimeout(waitForJQuery, 100);
        }
    }

    function initializeTables() {
        $(document).ready(function () {
            // Initialize DataTable for supplier products
            $('#supplierProductsTable').DataTable({
                "order": [[1, "asc"]], // Sort by product name ascending
                "pageLength": 5,
                "responsive": true,
                "language": {
                    "search": "Search products:",
                    "lengthMenu": "Show _MENU_ products per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ products",
                    "emptyTable": "No products found for this supplier"
                },
                "columnDefs": [
                    { "orderable": false, "targets": [5] }, // Disable sorting on Actions column
                    { "searchable": false, "targets": [0, 5] } // Disable search on # and Actions columns
                ],
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                "initComplete": function () {
                    // Add quick status filter buttons for products
                    var productStatusFilters = '<div class="btn-group btn-group-sm mb-3" role="group" aria-label="Product Status Filters">' +
                        '<button type="button" class="btn btn-outline-secondary product-status-filter" data-status="all">All</button>' +
                        '<button type="button" class="btn btn-outline-success product-status-filter" data-status="active">Active</button>' +
                        '<button type="button" class="btn btn-outline-secondary product-status-filter" data-status="inactive">Inactive</button>' +
                        '<button type="button" class="btn btn-outline-danger product-status-filter" data-status="discontinued">Discontinued</button>' +
                        '</div>';

                    $('#supplierProductsTable_wrapper .row:first').before(productStatusFilters);

                    // Product status filter functionality
                    $('.product-status-filter').click(function () {
                        var status = $(this).data('status');
                        $('.product-status-filter').removeClass('active');
                        $(this).addClass('active');

                        if (status === 'all') {
                            $('#supplierProductsTable').DataTable().column(4).search('').draw();
                        } else {
                            $('#supplierProductsTable').DataTable().column(4).search(status, true, false).draw();
                        }
                    });

                    // Set 'All' as active by default for products
                    $('.product-status-filter[data-status="all"]').addClass('active');
                }
            });

            // Initialize DataTable for purchase history
            $('#purchaseHistoryTable').DataTable({
                "order": [[2, "desc"]], // Sort by date column descending
                "pageLength": 5,
                "responsive": true,
                "language": {
                    "search": "Search orders:",
                    "lengthMenu": "Show _MENU_ orders per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ orders",
                    "emptyTable": "No purchase orders found for this supplier"
                },
                "columnDefs": [
                    { "orderable": false, "targets": [5] }, // Disable sorting on Actions column
                    { "searchable": false, "targets": [0, 5] } // Disable search on # and Actions columns
                ],
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                "initComplete": function () {
                    // Add quick status filter buttons for orders
                    var orderStatusFilters = '<div class="btn-group btn-group-sm mb-3" role="group" aria-label="Order Status Filters">' +
                        '<button type="button" class="btn btn-outline-secondary order-status-filter" data-status="all">All</button>' +
                        '<button type="button" class="btn btn-outline-warning order-status-filter" data-status="pending">Pending</button>' +
                        '<button type="button" class="btn btn-outline-info order-status-filter" data-status="sent">Sent</button>' +
                        '<button type="button" class="btn btn-outline-secondary order-status-filter" data-status="partially_received">Partial</button>' +
                        '<button type="button" class="btn btn-outline-success order-status-filter" data-status="received">Received</button>' +
                        '<button type="button" class="btn btn-outline-danger order-status-filter" data-status="cancelled">Cancelled</button>' +
                        '</div>';

                    $('#purchaseHistoryTable_wrapper .row:first').before(orderStatusFilters);

                    // Order status filter functionality
                    $('.order-status-filter').click(function () {
                        var status = $(this).data('status');
                        $('.order-status-filter').removeClass('active');
                        $(this).addClass('active');

                        if (status === 'all') {
                            $('#purchaseHistoryTable').DataTable().column(3).search('').draw();
                        } else {
                            $('#purchaseHistoryTable').DataTable().column(3).search(status, true, false).draw();
                        }
                    });

                    // Set 'All' as active by default for orders
                    $('.order-status-filter[data-status="all"]').addClass('active');
                }
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    }

    // Start checking for jQuery availability
    waitForJQuery();

    // Action button functions
    function viewPurchaseHistory() {
        // Scroll to purchase history section
        document.querySelector('#purchaseHistorySection').scrollIntoView({
            behavior: 'smooth'
        });
    }

    function generateReport() {
        <?php $jsSupplier = (isset($data['supplier']) && is_object($data['supplier']) && !empty($data['supplier'])) ? $data['supplier'] : null; ?>
        const supplierId = <?php echo (isset($jsSupplier) && is_object($jsSupplier) && isset($jsSupplier->supplier_id)) ? $jsSupplier->supplier_id : 0; ?>;
        const supplierName = '<?php echo (isset($jsSupplier) && is_object($jsSupplier) && isset($jsSupplier->supplier_name)) ? addslashes($jsSupplier->supplier_name) : ''; ?>';

        if (confirm(`Generate performance report for "${supplierName}"?`)) {
            window.open(`${window.URLROOT}/suppliers/report/${supplierId}`, '_blank');
        }
    }

    function evaluateSupplier() {
        const supplierId = <?php echo (isset($jsSupplier) && is_object($jsSupplier) && isset($jsSupplier->supplier_id)) ? $jsSupplier->supplier_id : 0; ?>;
        const supplierName = '<?php echo (isset($jsSupplier) && is_object($jsSupplier) && isset($jsSupplier->supplier_name)) ? addslashes($jsSupplier->supplier_name) : ''; ?>';

        if (confirm(`Open evaluation form for "${supplierName}"?`)) {
            window.location.href = `${window.URLROOT}/suppliers/evaluate/${supplierId}`;
        }
    }

    function updatePaymentTerms() {
        const supplierId = <?php echo (isset($jsSupplier) && is_object($jsSupplier) && isset($jsSupplier->supplier_id)) ? $jsSupplier->supplier_id : 0; ?>;
        const supplierName = '<?php echo (isset($jsSupplier) && is_object($jsSupplier) && isset($jsSupplier->supplier_name)) ? addslashes($jsSupplier->supplier_name) : ''; ?>';
        const currentTerms = '<?php echo (isset($jsSupplier) && is_object($jsSupplier) && isset($jsSupplier->preferred_payment_terms)) ? addslashes($jsSupplier->preferred_payment_terms) : 'Net 30'; ?>';

        const newTerms = prompt(`Update payment terms for "${supplierName}"\nCurrent: ${currentTerms}\n\nEnter new payment terms:`, currentTerms);

        if (newTerms !== null && newTerms.trim() !== '') {
            // Send AJAX request to update payment terms
            fetch(`${window.URLROOT}/suppliers/updatePaymentTerms`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    supplier_id: supplierId,
                    payment_terms: newTerms.trim()
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Payment terms updated successfully!');
                        location.reload();
                    } else {
                        alert('Error updating payment terms: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating payment terms');
                });
        }
    }

    function deactivateSupplier() {
        const supplierId = <?php echo ($jsSupplier && isset($jsSupplier->supplier_id)) ? $jsSupplier->supplier_id : 0; ?>;
        const supplierName = '<?php echo ($jsSupplier && isset($jsSupplier->supplier_name)) ? addslashes($jsSupplier->supplier_name) : ''; ?>';

        if (confirm(`Are you sure you want to deactivate "${supplierName}"?\n\nThis will temporarily disable the supplier but preserve all data.`)) {
            updateSupplierStatus(supplierId, 'inactive');
        }
    }

    function activateSupplier() {
        const supplierId = <?php echo ($jsSupplier && isset($jsSupplier->supplier_id)) ? $jsSupplier->supplier_id : 0; ?>;
        const supplierName = '<?php echo ($jsSupplier && isset($jsSupplier->supplier_name)) ? addslashes($jsSupplier->supplier_name) : ''; ?>';

        if (confirm(`Activate supplier "${supplierName}"?`)) {
            updateSupplierStatus(supplierId, 'active');
        }
    }

    function updateSupplierStatus(supplierId, status) {
        fetch(`${window.URLROOT}/suppliers/updateStatus`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                supplier_id: supplierId,
                status: status
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Supplier ${status === 'active' ? 'activated' : 'deactivated'} successfully!`);
                    location.reload();
                } else {
                    alert('Error updating supplier status: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating supplier status');
            });
    }


    function deleteSupplier() {
        const supplierId = <?php echo ($jsSupplier && isset($jsSupplier->supplier_id)) ? $jsSupplier->supplier_id : 0; ?>;
        const supplierName = '<?php echo ($jsSupplier && isset($jsSupplier->supplier_name)) ? addslashes($jsSupplier->supplier_name) : ''; ?>';

        if (confirm(`Archive supplier "${supplierName}"?\n\nThis will hide the supplier from active lists but keep all records and history.\n\nYou can restore this supplier later if needed.\n\nProceed to archive?`)) {
            fetch(`${window.URLROOT}/suppliers/delete/${supplierId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Supplier archived successfully!');
                        window.location.href = `${window.URLROOT}/suppliers`;
                    } else {
                        alert('Error archiving supplier: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error archiving supplier');
                });
        }
    }

    // Set the global URLROOT for JavaScript
    window.URLROOT = '<?php echo URLROOT; ?>';
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>