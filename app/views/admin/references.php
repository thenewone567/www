<?php
// Extract data passed from controller
extract($data);

$pageTitle = 'References & Commission Management - Admin Panel';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<div class="admin-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-0">
                    <i class="fas fa-handshake"></i> References & Commission Management
                </h1>
                <p class="mb-0 mt-2 opacity-75">Track contractor referrals and manage commissions</p>
            </div>
            <div class="col-md-4 text-md-right">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addReferenceModal">
                    <i class="fas fa-plus"></i> Add Reference
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Admin Navigation -->
    <div class="container-fluid mb-4">
        <div class="row align-items-stretch admin-action-grid">
            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <a class="card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center nav-link"
                    href="<?= URLROOT ?>/admin" role="button">
                    <div class="text-primary mb-2"><i class="fas fa-tachometer-alt fa-2x"></i></div>
                    <div class="font-weight-bold">Dashboard</div>
                    <small class="text-muted">Overview</small>
                </a>
            </div>

            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <a class="card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center nav-link"
                    href="<?= URLROOT ?>/admin/users" role="button">
                    <div class="text-primary mb-2"><i class="fas fa-users fa-2x"></i></div>
                    <div class="font-weight-bold">Users</div>
                    <small class="text-muted">Manage accounts</small>
                </a>
            </div>

            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <a class="card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center nav-link active"
                    href="<?= URLROOT ?>/admin/references" role="button">
                    <div class="text-success mb-2"><i class="fas fa-handshake fa-2x"></i></div>
                    <div class="font-weight-bold">References</div>
                    <small class="text-muted">Commission system</small>
                </a>
            </div>

            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <a class="card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center nav-link"
                    href="<?= URLROOT ?>/admin/activityLogs" role="button">
                    <div class="text-muted mb-2"><i class="fas fa-history fa-2x"></i></div>
                    <div class="font-weight-bold">Activity Logs</div>
                    <small class="text-muted">Audit trail</small>
                </a>
            </div>

            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <a class="card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center nav-link"
                    href="<?= URLROOT ?>/admin/settings" role="button">
                    <div class="text-dark mb-2"><i class="fas fa-cog fa-2x"></i></div>
                    <div class="font-weight-bold">Settings</div>
                    <small class="text-muted">System prefs</small>
                </a>
            </div>
        </div>
    </div>

    <!-- References Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-theme border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total References</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($references) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-theme border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Commissions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($commissions) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-theme border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Contractors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($contractors) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hard-hat fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-theme border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Commission Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format(array_sum(array_map(function ($c) {
                                    return $c->commission_amount ?? 0;
                                }, $commissions)), 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs for References and Commissions -->
    <div class="row">
        <div class="col-12">
            <div class="card-theme">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="references-tab" data-toggle="tab" href="#references"
                                role="tab">
                                <i class="fas fa-handshake"></i> Customer References
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="commissions-tab" data-toggle="tab" href="#commissions" role="tab">
                                <i class="fas fa-dollar-sign"></i> Traditional Commissions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="target-commissions-tab" data-toggle="tab" href="#target-commissions"
                                role="tab">
                                <i class="fas fa-bullseye"></i> Target-Based Commissions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="commission-tiers-tab" data-toggle="tab" href="#commission-tiers"
                                role="tab">
                                <i class="fas fa-layer-group"></i> Commission Tiers
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- References Tab -->
                        <div class="tab-pane fade show active" id="references" role="tabpanel">
                            <!-- Debug Info -->
                            <?php if (isset($_GET['debug'])): ?>
                                <div class="alert alert-info">
                                    <strong>Debug Info:</strong><br>
                                    References count: <?= is_array($references) ? count($references) : 'Not an array' ?><br>
                                    References type: <?= gettype($references) ?><br>
                                    <?php if (!empty($references) && is_array($references)): ?>
                                        First reference: <?= print_r($references[0], true) ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-hover" id="referencesTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Contractor</th>
                                            <th>Customer</th>
                                            <th>Reference Date</th>
                                            <th>Status</th>
                                            <th>Total Commissions</th>
                                            <th>Commission Earned</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($references) && is_array($references)): ?>
                                            <?php foreach ($references as $reference): ?>
                                                <?php if (is_object($reference) && isset($reference->reference_id)): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?= htmlspecialchars($reference->contractor_name ?? 'N/A') ?></strong>
                                                            <br>
                                                            <small
                                                                class="text-muted"><?= htmlspecialchars($reference->contractor_contact ?? '') ?></small>
                                                        </td>
                                                        <td>
                                                            <strong><?= htmlspecialchars($reference->customer_name ?? 'N/A') ?></strong>
                                                            <br>
                                                            <small
                                                                class="text-muted"><?= htmlspecialchars($reference->customer_contact ?? '') ?></small>
                                                        </td>
                                                        <td><?= isset($reference->reference_date) ? date('M j, Y', strtotime($reference->reference_date)) : 'N/A' ?>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge badge-<?= ($reference->status ?? 'inactive') === 'active' ? 'success' : 'secondary' ?>">
                                                                <?= ucfirst($reference->status ?? 'inactive') ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge badge-info"><?= $reference->total_commissions ?? 0 ?></span>
                                                        </td>
                                                        <td>
                                                            <strong>$<?= number_format($reference->total_commission_earned ?? 0, 2) ?></strong>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <button class="btn btn-sm btn-outline-primary"
                                                                    onclick="viewReference(<?= $reference->reference_id ?>)">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-success"
                                                                    onclick="addCommission(<?= $reference->reference_id ?>)">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger"
                                                                    onclick="deleteReference(<?= $reference->reference_id ?>)">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    <i class="fas fa-handshake fa-2x mb-2"></i>
                                                    <p>No customer references found</p>
                                                    <button class="btn btn-success" data-toggle="modal"
                                                        data-target="#addReferenceModal">
                                                        <i class="fas fa-plus"></i> Add First Reference
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Commissions Tab -->
                        <div class="tab-pane fade" id="commissions" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover" id="commissionsTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Contractor</th>
                                            <th>Customer</th>
                                            <th>Sale Amount</th>
                                            <th>Commission Rate</th>
                                            <th>Commission Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($commissions)): ?>
                                            <?php foreach ($commissions as $commission): ?>
                                                <tr>
                                                    <td><?= date('M j, Y', strtotime($commission->commission_date)) ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($commission->contractor_name) ?></strong>
                                                    </td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($commission->customer_name) ?></strong>
                                                    </td>
                                                    <td>$<?= number_format($commission->sale_amount, 2) ?></td>
                                                    <td><?= $commission->commission_rate ?>%</td>
                                                    <td><strong>$<?= number_format($commission->commission_amount, 2) ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-<?php
                                                        switch ($commission->status) {
                                                            case 'pending':
                                                                echo 'warning';
                                                                break;
                                                            case 'approved':
                                                                echo 'info';
                                                                break;
                                                            case 'paid':
                                                                echo 'success';
                                                                break;
                                                            case 'cancelled':
                                                                echo 'danger';
                                                                break;
                                                            default:
                                                                echo 'secondary';
                                                                break;
                                                        }
                                                        ?>">
                                                            <?= ucfirst($commission->status) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <?php if ($commission->status === 'pending'): ?>
                                                                <button class="btn btn-sm btn-outline-success"
                                                                    onclick="updateCommissionStatus(<?= $commission->commission_id ?>, 'approved')">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                            <?php if ($commission->status === 'approved'): ?>
                                                                <button class="btn btn-sm btn-outline-primary"
                                                                    onclick="updateCommissionStatus(<?= $commission->commission_id ?>, 'paid')">
                                                                    <i class="fas fa-money-bill"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                            <button class="btn btn-sm btn-outline-danger"
                                                                onclick="updateCommissionStatus(<?= $commission->commission_id ?>, 'cancelled')">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">
                                                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                                    <p>No commissions recorded yet</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Target-Based Commissions Tab -->
                    <div class="tab-pane fade" id="target-commissions" role="tabpanel">
                        <!-- Monthly Performance Table -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5><i class="fas fa-calendar-alt"></i> Monthly Performance</h5>
                                <button class="btn btn-warning btn-sm" id="finalizeMonthBtn">
                                    <i class="fas fa-check-circle"></i> Finalize Current Month
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="monthlyPerformanceTable">
                                        <thead>
                                            <tr>
                                                <th>Contractor</th>
                                                <th>Month</th>
                                                <th>Total Sales</th>
                                                <th>Tier Achieved</th>
                                                <th>Commission Rate</th>
                                                <th>Commission Amount</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data will be loaded via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Commission Tiers Management Tab -->
                    <div class="tab-pane fade" id="commission-tiers" role="tabpanel">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Tier Configuration -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-layer-group"></i> Commission Tier Configuration</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Tier Name</th>
                                                        <th>Minimum Sales (₹)</th>
                                                        <th>Commission Rate</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tiersTableBody">
                                                    <!-- Tiers will be loaded via AJAX -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Add/Edit Tier -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-plus"></i> Add/Edit Tier</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="tierForm">
                                            <input type="hidden" id="tierId" name="tier_id">
                                            <div class="form-group">
                                                <label for="tierName">Tier Name</label>
                                                <input type="text" class="form-control" id="tierName" name="tier_name"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="minSales">Minimum Sales (₹)</label>
                                                <input type="number" class="form-control" id="minSales" name="min_sales"
                                                    step="0.01" min="0" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="commissionRate">Commission Rate (%)</label>
                                                <input type="number" class="form-control" id="commissionRate"
                                                    name="commission_rate" step="0.01" min="0" max="100" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Save Tier
                                            </button>
                                            <button type="button" class="btn btn-secondary" id="resetTierForm">
                                                <i class="fas fa-undo"></i> Reset
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Tier Legend -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6><i class="fas fa-info-circle"></i> Default Tiers</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="small">
                                            <div class="mb-1"><span class="badge badge-secondary">Bronze</span> ₹0 - 1%
                                            </div>
                                            <div class="mb-1"><span class="badge badge-info">Silver</span> ₹100,000 - 2%
                                            </div>
                                            <div class="mb-1"><span class="badge badge-warning">Gold</span> ₹250,000 -
                                                3%</div>
                                            <div class="mb-1"><span class="badge badge-success">Platinum</span> ₹500,000
                                                - 4%</div>
                                            <div class="mb-1"><span class="badge badge-primary">Diamond</span>
                                                ₹1,000,000 - 5%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Reference Modal -->
<div class="modal fade" id="addReferenceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-handshake"></i> Add Customer Reference
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addReferenceForm" action="<?= URLROOT ?>/admin/createReference" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="contractorSelect">Contractor</label>
                        <select class="form-control" id="contractorSelect" name="contractor_id" required>
                            <option value="">Select Contractor</option>
                            <?php foreach ($contractors as $contractor): ?>
                                <option value="<?= $contractor->contractor_id ?>">
                                    <?= htmlspecialchars($contractor->contractor_name) ?>
                                    (Rate: <?= $contractor->commission_rate ?>%)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="customerSelect">Customer</label>
                        <select class="form-control" id="customerSelect" name="customer_id" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer->customer_id ?>">
                                    <?= htmlspecialchars($customer->customer_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="referenceNotes">Notes</label>
                        <textarea class="form-control" id="referenceNotes" name="notes" rows="3"
                            placeholder="Additional notes about this reference..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Create Reference
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Prevent multiple initializations with a more robust approach
    if (window.referencesPageInitialized) {
        console.log('References page already initialized, stopping execution');
        return;
    }

    $(document).ready(function () {
        console.log('Document ready fired for references page');

        // Double-check initialization flag
        if (window.dataTablesInitialized) {
            console.log('DataTables already initialized, skipping...');
            return;
        }

        // Initialize DataTables with comprehensive error handling
        if ($.fn.DataTable) {
            try {
                console.log('Starting DataTables initialization...');

                // Check if tables exist
                if ($('#referencesTable').length === 0) {
                    console.warn('referencesTable not found in DOM');
                    return;
                }

                // Validate table structure
                var $table = $('#referencesTable');
                var headerCount = $table.find('thead th').length;
                var $firstRow = $table.find('tbody tr:first');
                var bodyCount = $firstRow.find('td').length;
                var hasColspan = $firstRow.find('td[colspan]').length > 0;

                console.log('Table validation:', {
                    headerCount: headerCount,
                    bodyCount: bodyCount,
                    hasColspan: hasColspan,
                    rowsTotal: $table.find('tbody tr').length,
                    tableHTML: $table.find('tbody').html().substring(0, 200) + '...'
                });

                // Only proceed if structure is valid
                if (headerCount !== bodyCount && !hasColspan) {
                    console.error('Table structure invalid: header=' + headerCount + ' vs body=' + bodyCount);
                    console.error('First row HTML:', $firstRow.html());
                    return;
                }

                // Destroy any existing instances
                if ($.fn.DataTable.isDataTable('#referencesTable')) {
                    console.log('Destroying existing referencesTable instance');
                    $('#referencesTable').DataTable().destroy();
                }

                // Initialize referencesTable
                $('#referencesTable').DataTable({
                    pageLength: 25,
                    responsive: false,
                    order: [[2, 'desc']], // Sort by reference date
                    columnDefs: [
                        { orderable: false, targets: [6] } // Disable sorting on Actions column
                    ],
                    language: {
                        emptyTable: "No customer references found"
                    },
                    initComplete: function () {
                        console.log('referencesTable initialized successfully');
                    },
                    error: function (settings, helpLink, message) {
                        console.error('DataTables error:', message);
                        console.error('Help link:', helpLink);
                    }
                });

                // Initialize commissionsTable if it exists
                if ($('#commissionsTable').length > 0) {
                    if ($.fn.DataTable.isDataTable('#commissionsTable')) {
                        console.log('Destroying existing commissionsTable instance');
                        $('#commissionsTable').DataTable().destroy();
                    }

                    $('#commissionsTable').DataTable({
                        pageLength: 25,
                        responsive: false,
                        order: [[0, 'desc']], // Sort by commission date
                        initComplete: function () {
                            console.log('commissionsTable initialized successfully');
                        }
                    });
                }

                // Set initialization flags
                window.dataTablesInitialized = true;
                window.referencesPageInitialized = true;
                console.log('All DataTables initialized successfully');

            } catch (error) {
                console.error('DataTables initialization error:', error);
                console.error('Error details:', error.message, error.stack);
            }
        } else {
            console.error('DataTables library not loaded');
        }

        // Handle form submission
        $('#addReferenceForm').on('submit', function (e) {
            e.preventDefault();

            console.log('Initiating reference creation...');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Reference created successfully!');
                        location.reload();
                    } else {
                        alert('Failed to create reference: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function () {
                    alert('Creation failed! Please try again.');
                }
            });
        });
    });

    // JavaScript functions for actions
    function viewReference(referenceId) {
        console.log('Viewing reference:', referenceId);
        // Implementation for viewing reference details
    }

    function addCommission(referenceId) {
        console.log('Adding commission for reference:', referenceId);
        // Implementation for adding commission
    }

    function deleteReference(referenceId) {
        if (confirm('Are you sure you want to delete this reference? This will also delete all related commissions.')) {
            console.log('Initiating reference deletion...');

            $.ajax({
                url: '<?= URLROOT ?>/admin/deleteReference',
                method: 'POST',
                data: { reference_id: referenceId },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Reference deleted successfully!');
                        location.reload();
                    } else {
                        alert('Failed to delete reference: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function () {
                    alert('Deletion failed! Please try again.');
                }
            });
        }
    }

    function updateCommissionStatus(commissionId, status) {
        console.log('Updating commission status:', commissionId, status);

        $.ajax({
            url: '<?= URLROOT ?>/admin/updateCommissionStatus',
            method: 'POST',
            data: {
                commission_id: commissionId,
                status: status,
                payment_date: status === 'paid' ? new Date().toISOString().split('T')[0] : null
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert('Commission status updated successfully!');
                    location.reload();
                } else {
                    alert('Failed to update commission: ' + (response.message || 'Unknown error'));
                }
            },
            error: function () {
                alert('Update failed! Please try again.');
            }
        });
    }

    // Target-Based Commission Functions

    function getTierBadgeClass(tier) {
        const classes = {
            'Bronze': 'secondary',
            'Silver': 'info',
            'Gold': 'warning',
            'Platinum': 'success',
            'Diamond': 'primary'
        };
        return classes[tier] || 'secondary';
    }

    // Handle finalize month button
    $(document).on('click', '#finalizeMonthBtn', function () {
        if (confirm('Are you sure you want to finalize commissions for the current month? This action cannot be undone.')) {
            $.ajax({
                url: '<?= URLROOT ?>/admin/finalizeMonthlyCommissions',
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Monthly commissions finalized successfully!');
                        loadMonthlyPerformance();
                    } else {
                        alert('Failed to finalize commissions: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function () {
                    alert('Failed to finalize commissions! Please try again.');
                }
            });
        }
    });

    // Load monthly performance data
    function loadMonthlyPerformance() {
        $('#monthlyPerformanceTable tbody').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');

        $.ajax({
            url: '<?= URLROOT ?>/admin/getMonthlyPerformance',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    let html = '';
                    response.data.forEach(function (item) {
                        const statusBadge = item.status === 'paid' ? 'success' : 'warning';
                        const actionBtn = item.status === 'finalized' ?
                            `<button class="btn btn-sm btn-success" onclick="markCommissionPaid(${item.summary_id})">Mark Paid</button>` :
                            '<span class="text-muted">Pending</span>';

                        html += `
                            <tr>
                                <td>${item.contractor_name}</td>
                                <td>${item.month_year}</td>
                                <td>₹${item.total_sales.toLocaleString()}</td>
                                <td><span class="badge badge-${getTierBadgeClass(item.tier_achieved)}">${item.tier_achieved}</span></td>
                                <td>${item.commission_rate}%</td>
                                <td>₹${item.commission_amount.toLocaleString()}</td>
                                <td><span class="badge badge-${statusBadge}">${item.status}</span></td>
                                <td>${actionBtn}</td>
                            </tr>
                        `;
                    });
                    $('#monthlyPerformanceTable tbody').html(html || '<tr><td colspan="8" class="text-center">No data available</td></tr>');
                }
            },
            error: function () {
                $('#monthlyPerformanceTable tbody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load data</td></tr>');
            }
        });
    }

    // Mark commission as paid
    function markCommissionPaid(summaryId) {
        if (confirm('Mark this commission as paid?')) {
            $.ajax({
                url: '<?= URLROOT ?>/admin/markCommissionPaid',
                type: 'POST',
                data: { summary_id: summaryId },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Commission marked as paid!');
                        loadMonthlyPerformance();
                    } else {
                        alert('Failed to update status: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function () {
                    alert('Failed to update status! Please try again.');
                }
            });
        }
    }

    // Load commission tiers
    function loadCommissionTiers() {
        $.ajax({
            url: '<?= URLROOT ?>/admin/getCommissionTiers',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    let html = '';
                    response.data.forEach(function (tier) {
                        html += `
                            <tr>
                                <td><span class="badge badge-${getTierBadgeClass(tier.tier_name)}">${tier.tier_name}</span></td>
                                <td>₹${tier.min_sales.toLocaleString()}</td>
                                <td>${tier.commission_rate}%</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editTier(${tier.tier_id}, '${tier.tier_name}', ${tier.min_sales}, ${tier.commission_rate})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger ml-1" onclick="deleteTier(${tier.tier_id})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#tiersTableBody').html(html || '<tr><td colspan="4" class="text-center">No tiers configured</td></tr>');
                }
            }
        });
    }

    // Edit tier
    function editTier(tierId, tierName, minSales, commissionRate) {
        $('#tierId').val(tierId);
        $('#tierName').val(tierName);
        $('#minSales').val(minSales);
        $('#commissionRate').val(commissionRate);
    }

    // Delete tier
    function deleteTier(tierId) {
        if (confirm('Are you sure you want to delete this tier?')) {
            $.ajax({
                url: '<?= URLROOT ?>/admin/deleteCommissionTier',
                type: 'POST',
                data: { tier_id: tierId },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Tier deleted successfully!');
                        loadCommissionTiers();
                    } else {
                        alert('Failed to delete tier: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function () {
                    alert('Failed to delete tier! Please try again.');
                }
            });
        }
    }

    // Handle tier form submission
    $(document).on('submit', '#tierForm', function (e) {
        e.preventDefault();

        const isEdit = $('#tierId').val() !== '';
        const url = isEdit ? '<?= URLROOT ?>/admin/updateCommissionTier' : '<?= URLROOT ?>/admin/createCommissionTier';

        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert((isEdit ? 'Tier updated' : 'Tier created') + ' successfully!');
                    $('#tierForm')[0].reset();
                    $('#tierId').val('');
                    loadCommissionTiers();
                } else {
                    alert('Failed to save tier: ' + (response.message || 'Unknown error'));
                }
            },
            error: function () {
                alert('Failed to save tier! Please try again.');
            }
        });
    });

    // Reset tier form
    $(document).on('click', '#resetTierForm', function () {
        $('#tierForm')[0].reset();
        $('#tierId').val('');
    });

    // Tab change handlers
    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        const target = $(e.target).attr("href");

        if (target === '#target-commissions') {
            loadMonthlyPerformance();
        } else if (target === '#commission-tiers') {
            loadCommissionTiers();
        }
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>