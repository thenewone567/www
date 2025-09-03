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
                            <div class="col-2 mb-3">
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
                            <div class="col-2 mb-3">
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
                            <div class="col-2 mb-3">
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
                            <div class="col-2 mb-3">
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
                            <div class="col-2 mb-3">
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
                            <div class="col-2 mb-3">
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
                            <div class="col-2 mb-3">
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
                            <div class="col-2 mb-3">
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
                            <div class="col-2 mb-3">
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
                            <div class="col-2 mb-3">
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
                            <div class="col-2 mb-3">
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
                            <div class="col-2 mb-3">
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
                                                return $p->status == 1; // Check is_active field
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
                                                            $status = $product->status ?? 0;
                                                            // Convert is_active (1/0) to readable status
                                                            if ($status == 1) {
                                                                $statusClass = 'bg-success';
                                                                $statusText = 'Active';
                                                            } else {
                                                                $statusClass = 'bg-secondary';
                                                                $statusText = 'Inactive';
                                                            }
                                                            ?>
                                                            <span class="badge <?php echo $statusClass; ?>">
                                                                <?php echo $statusText; ?>
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
                                    <!-- Enhanced Search and Filter Controls -->
                                    <div class="purchase-history-filters">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="purchase-search-container">
                                                    <div class="input-group">
                                                        <input type="text" id="purchaseSearchInput" class="form-control"
                                                            placeholder="Search purchase orders..."
                                                            onkeyup="filterPurchaseHistory()">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex gap-2">
                                                    <select id="statusFilter" class="form-control"
                                                        onchange="filterPurchaseHistory()">
                                                        <option value="">All Status</option>
                                                        <option value="pending">Pending</option>
                                                        <option value="sent">Sent</option>
                                                        <option value="partially_received">Partially Received</option>
                                                        <option value="received">Received</option>
                                                        <option value="cancelled">Cancelled</option>
                                                    </select>
                                                    <select id="dateFilter" class="form-control"
                                                        onchange="filterPurchaseHistory()">
                                                        <option value="">All Time</option>
                                                        <option value="7">Last 7 days</option>
                                                        <option value="30">Last 30 days</option>
                                                        <option value="90">Last 3 months</option>
                                                        <option value="365">Last year</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="purchase-history-table table table-hover table-layout-fixed mb-0"
                                            id="purchaseHistoryTable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width:80px" title="Serial Number">#</th>
                                                    <th style="width:140px" title="Purchase Order Number">PO Number</th>
                                                    <th style="width:120px" title="Purchase Date">Date</th>
                                                    <th style="width:120px" title="Order Status">Status</th>
                                                    <th style="width:100px" title="Total Items">Items</th>
                                                    <th style="width:120px" title="Total Amount">Amount</th>
                                                    <th style="width:120px" title="Expected/Received Date">Timeline</th>
                                                    <th style="width:140px" title="Available Actions">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="purchaseHistoryTableBody">
                                                <?php foreach ($data['purchase_history'] as $index => $order): ?>
                                                    <tr data-status="<?php echo strtolower($order->status ?? 'unknown'); ?>"
                                                        data-date="<?php echo $order->purchase_date ?? ''; ?>"
                                                        data-po="<?php echo htmlspecialchars($order->po_number ?? 'PO-' . $order->purchase_id); ?>">
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge badge-light"><?php echo $index + 1; ?></span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <strong
                                                                    class="text-primary"><?php echo htmlspecialchars($order->po_number ?? 'PO-' . $order->purchase_id); ?></strong>
                                                                <br><small class="text-muted">ID:
                                                                    <?php echo $order->purchase_id; ?></small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <span
                                                                    class="font-weight-bold"><?php echo !empty($order->purchase_date) ? date('d-M-Y', strtotime($order->purchase_date)) : '-'; ?></span>
                                                                <br><small
                                                                    class="text-muted"><?php echo !empty($order->purchase_date) ? date('H:i', strtotime($order->purchase_date)) : ''; ?></small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $statusClass = '';
                                                            $statusIcon = '';
                                                            $status = $order->status ?? 'unknown';
                                                            switch (strtolower($status)) {
                                                                case 'pending':
                                                                    $statusClass = 'badge-warning';
                                                                    $statusIcon = 'fas fa-clock';
                                                                    break;
                                                                case 'sent':
                                                                    $statusClass = 'badge-info';
                                                                    $statusIcon = 'fas fa-paper-plane';
                                                                    break;
                                                                case 'partially_received':
                                                                    $statusClass = 'badge-secondary';
                                                                    $statusIcon = 'fas fa-boxes';
                                                                    break;
                                                                case 'received':
                                                                    $statusClass = 'badge-success';
                                                                    $statusIcon = 'fas fa-check-circle';
                                                                    break;
                                                                case 'cancelled':
                                                                    $statusClass = 'badge-danger';
                                                                    $statusIcon = 'fas fa-times-circle';
                                                                    break;
                                                                default:
                                                                    $statusClass = 'badge-light';
                                                                    $statusIcon = 'fas fa-question-circle';
                                                            }
                                                            ?>
                                                            <span class="badge <?php echo $statusClass; ?>">
                                                                <i class="<?php echo $statusIcon; ?> mr-1"></i>
                                                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $status))); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="text-center">
                                                                <span
                                                                    class="h6 mb-1 text-info"><?php echo number_format($order->item_count ?? 0); ?></span>
                                                                <br><small class="text-muted">items</small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <strong
                                                                    class="text-success"><?php echo formatCurrency($order->total_amount ?? 0, 2); ?></strong>
                                                                <?php if (($order->total_amount ?? 0) > 0 && ($order->item_count ?? 0) > 0): ?>
                                                                    <br><small class="text-muted">Avg:
                                                                        <?php echo formatCurrency(($order->total_amount ?? 0) / ($order->item_count ?? 1), 2); ?>/item</small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <?php if (!empty($order->expected_date)): ?>
                                                                    <small class="text-muted">Expected:</small>
                                                                    <br><span
                                                                        class="font-weight-bold"><?php echo date('d-M-Y', strtotime($order->expected_date)); ?></span>
                                                                <?php else: ?>
                                                                    <small class="text-muted">No expected date</small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <a href="<?php echo URLROOT; ?>/purchases/details/<?php echo $order->purchase_id; ?>"
                                                                    class="btn btn-outline-primary btn-sm" data-toggle="tooltip"
                                                                    title="View Details">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <?php if (in_array(strtolower($order->status ?? ''), ['pending', 'sent'])): ?>
                                                                    <a href="<?php echo URLROOT; ?>/purchases/edit/<?php echo $order->purchase_id; ?>"
                                                                        class="btn btn-outline-warning btn-sm" data-toggle="tooltip"
                                                                        title="Edit Order">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                                <button type="button" class="btn btn-outline-info btn-sm"
                                                                    onclick="viewPurchaseItems(<?php echo $order->purchase_id; ?>)"
                                                                    data-toggle="tooltip" title="View Items">
                                                                    <i class="fas fa-list"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Enhanced Pagination and Info -->
                                    <div class="row mt-3 purchase-actions">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                Showing <span
                                                    id="visibleOrdersCount"><?php echo count($data['purchase_history']); ?></span>
                                                purchase orders
                                                <?php if (!empty($data['supplier_stats']) && $data['supplier_stats']->total_orders > count($data['purchase_history'])): ?>
                                                    of <?php echo number_format($data['supplier_stats']->total_orders); ?> total
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="exportPurchaseHistory()" data-toggle="tooltip"
                                                    title="Export to CSV">
                                                    <i class="fas fa-download mr-1"></i>Export
                                                </button>
                                                <button type="button" class="btn btn-outline-primary"
                                                    onclick="printPurchaseHistory()" data-toggle="tooltip" title="Print Report">
                                                    <i class="fas fa-print mr-1"></i>Print
                                                </button>
                                                <button type="button" class="btn btn-outline-info"
                                                    onclick="refreshPurchaseHistory()" data-toggle="tooltip"
                                                    title="Refresh Data">
                                                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">No Purchase Orders Found</h6>
                                        <p class="text-muted small">This supplier has no purchase order history.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purchase Items Modal -->
                <div class="modal fade" id="purchaseItemsModal" tabindex="-1" role="dialog"
                    aria-labelledby="purchaseItemsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="purchaseItemsModalLabel">
                                    <i class="fas fa-list mr-2"></i>Purchase Order Items
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div id="purchaseItemsContent">
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <p class="mt-2">Loading purchase items...</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                    <i class="fas fa-times mr-1"></i>Close
                                </button>
                                <button type="button" class="btn btn-primary" id="viewFullPurchaseBtn">
                                    <i class="fas fa-eye mr-1"></i>View Full Details
                                </button>
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

    // Enhanced Purchase History Functions
    function filterPurchaseHistory() {
        const searchTerm = document.getElementById('purchaseSearchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const dateFilter = document.getElementById('dateFilter').value;
        const rows = document.querySelectorAll('#purchaseHistoryTableBody tr');

        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            const rowStatus = row.getAttribute('data-status') || '';
            const rowDate = row.getAttribute('data-date') || '';

            let showRow = true;

            // Text search
            if (searchTerm && !rowText.includes(searchTerm)) {
                showRow = false;
            }

            // Status filter
            if (statusFilter && rowStatus !== statusFilter) {
                showRow = false;
            }

            // Date filter
            if (dateFilter && rowDate) {
                const orderDate = new Date(rowDate);
                const now = new Date();
                const daysAgo = parseInt(dateFilter);
                const filterDate = new Date(now.getTime() - (daysAgo * 24 * 60 * 60 * 1000));

                if (orderDate < filterDate) {
                    showRow = false;
                }
            }

            row.style.display = showRow ? '' : 'none';
        });

        // Update info text
        updatePurchaseHistoryInfo();
    }

    function refreshPurchaseHistory() {
        location.reload();
    }

    function updatePurchaseHistoryInfo() {
        const allRows = document.querySelectorAll('#purchaseHistoryTableBody tr');
        const visibleRows = document.querySelectorAll('#purchaseHistoryTableBody tr:not([style*="display: none"])');

        const countElement = document.getElementById('visibleOrdersCount');
        if (countElement) {
            countElement.textContent = visibleRows.length;
        }
    }

    function viewPurchaseItems(purchaseId) {
        // Show modal
        $('#purchaseItemsModal').modal('show');

        // Set up the view full purchase button
        document.getElementById('viewFullPurchaseBtn').onclick = function () {
            window.open(`${window.URLROOT}/purchases/details/${purchaseId}`, '_blank');
        };

        // Reset modal content
        document.getElementById('purchaseItemsContent').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Loading purchase items...</p>
            </div>
        `;

        // Fetch purchase items
        fetch(`${window.URLROOT}/api/getPurchaseItems.php?purchase_id=${purchaseId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.items) {
                    displayPurchaseItems(data.items, data.purchase_info);
                } else {
                    document.getElementById('purchaseItemsContent').innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            No items found for this purchase order.
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching purchase items:', error);
                document.getElementById('purchaseItemsContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Error loading purchase items. Please try again.
                    </div>
                `;
            });
    }

    function displayPurchaseItems(items, purchaseInfo) {
        let totalValue = 0;
        let itemsHtml = '';

        items.forEach((item, index) => {
            const lineTotal = (item.quantity || 0) * (item.unit_price || 0);
            totalValue += lineTotal;

            itemsHtml += `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            ${item.image_path ?
                    `<img src="${window.URLROOT}/public/uploads/${item.image_path}" 
                                     style="width:40px;height:40px;object-fit:cover;border-radius:4px;" class="mr-3">` :
                    `<div class="bg-light rounded d-flex align-items-center justify-content-center mr-3" 
                                     style="width:40px;height:40px;"><i class="fas fa-box text-muted"></i></div>`
                }
                            <div>
                                <strong>${item.product_name || 'Unknown Product'}</strong>
                                ${item.sku ? `<br><small class="text-muted">SKU: ${item.sku}</small>` : ''}
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-info">${item.quantity || 0}</span>
                    </td>
                    <td class="text-right">
                        ₹${(item.unit_price || 0).toFixed(2)}
                    </td>
                    <td class="text-right">
                        <strong>₹${lineTotal.toFixed(2)}</strong>
                    </td>
                </tr>
            `;
        });

        const content = `
            <!-- Purchase Info -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6><i class="fas fa-file-alt mr-2"></i>PO: ${purchaseInfo?.po_number || 'N/A'}</h6>
                    <small class="text-muted">Date: ${purchaseInfo?.purchase_date ?
                new Date(purchaseInfo.purchase_date).toLocaleDateString() : 'N/A'}</small>
                </div>
                <div class="col-md-6 text-right">
                    <span class="badge badge-${getStatusBadgeClass(purchaseInfo?.status)}">${purchaseInfo?.status || 'Unknown'}</span>
                </div>
            </div>
            
            <!-- Items Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="8%">#</th>
                            <th width="40%">Product</th>
                            <th width="15%" class="text-center">Quantity</th>
                            <th width="17%" class="text-right">Unit Price</th>
                            <th width="20%" class="text-right">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHtml}
                    </tbody>
                    <tfoot class="thead-dark">
                        <tr>
                            <th colspan="4" class="text-right">Total Value:</th>
                            <th class="text-right">₹${totalValue.toFixed(2)}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        `;

        document.getElementById('purchaseItemsContent').innerHTML = content;

        // Update modal title
        document.getElementById('purchaseItemsModalLabel').innerHTML =
            `<i class="fas fa-list mr-2"></i>Items for PO: ${purchaseInfo?.po_number || 'N/A'}`;
    }

    function getStatusBadgeClass(status) {
        switch (status?.toLowerCase()) {
            case 'pending': return 'warning';
            case 'sent': return 'info';
            case 'partially_received': return 'secondary';
            case 'received': return 'success';
            case 'cancelled': return 'danger';
            default: return 'light';
        }
    }

    function exportPurchaseHistory() {
        const supplierId = <?php echo (isset($jsSupplier) && is_object($jsSupplier) && isset($jsSupplier->supplier_id)) ? $jsSupplier->supplier_id : 0; ?>;
        const supplierName = '<?php echo (isset($jsSupplier) && is_object($jsSupplier) && isset($jsSupplier->supplier_name)) ? addslashes($jsSupplier->supplier_name) : ''; ?>';

        if (confirm(`Export purchase history for "${supplierName}"?`)) {
            window.location.href = `${window.URLROOT}/suppliers/exportPurchaseHistory/${supplierId}`;
        }
    }

    function printPurchaseHistory() {
        const printContent = document.getElementById('purchaseHistorySection').cloneNode(true);

        // Remove action buttons from print view
        const actionColumns = printContent.querySelectorAll('.btn-group');
        actionColumns.forEach(col => col.remove());

        // Create print window
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Purchase History - <?php echo (isset($jsSupplier) && is_object($jsSupplier) && isset($jsSupplier->supplier_name)) ? addslashes($jsSupplier->supplier_name) : ''; ?></title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        @media print {
                            .no-print { display: none !important; }
                            body { margin: 0; padding: 20px; }
                            .table { font-size: 12px; }
                        }
                    </style>
                </head>
                <body>
                    <div class="container-fluid">
                        <h3>Purchase History Report</h3>
                        <p><strong>Supplier:</strong> <?php echo (isset($jsSupplier) && is_object($jsSupplier) && isset($jsSupplier->supplier_name)) ? htmlspecialchars($jsSupplier->supplier_name) : ''; ?></p>
                        <p><strong>Generated:</strong> ${new Date().toLocaleDateString()}</p>
                        ${printContent.innerHTML}
                    </div>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }

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