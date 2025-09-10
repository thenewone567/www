<?php
// Extract data passed from controller
extract($data);

$pageTitle = 'Contractors Directory - Hardware Store';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<div class="admin-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-0">
                    <i class="fas fa-hard-hat"></i> Contractors Directory
                </h1>
                <p class="mb-0 mt-2 opacity-75">Manage contractor network and performance metrics</p>
            </div>
            <div class="col-md-4 text-md-right">
                <div class="btn-group" role="group">
                    <a href="<?php echo URLROOT; ?>/admin/users" class="btn btn-outline-secondary btn-sm mr-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="<?php echo URLROOT; ?>/contractor/add" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Contractor
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- KPI Cards Section - Compact Single Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card-theme h-100 border-left-primary">
                <div class="card-body py-3 px-4">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Contractors
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= isset($kpis['total_contractors']) ? $kpis['total_contractors'] : 0 ?>
                            </div>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i>
                                <?= isset($kpis['active_contractors']) ? $kpis['active_contractors'] : 0 ?> Active
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card-theme h-100 border-left-success">
                <div class="card-body py-3 px-4">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?= isset($kpis['total_revenue']) ? number_format($kpis['total_revenue'], 2) : '0.00' ?>
                            </div>
                            <small class="text-info">
                                <i class="fas fa-calendar"></i> All Time
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card-theme h-100 border-left-warning">
                <div class="card-body py-3 px-4">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Commissions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?= isset($kpis['total_commissions']) ? number_format($kpis['total_commissions'], 2) : '0.00' ?>
                            </div>
                            <small class="text-warning">
                                <i class="fas fa-handshake"></i> Earned
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card-theme h-100 border-left-info">
                <div class="card-body py-3 px-4">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                This Quarter
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?= isset($kpis['quarterly_revenue']) ? number_format($kpis['quarterly_revenue'], 2) : '0.00' ?>
                            </div>
                            <small class="text-primary">
                                <i class="fas fa-calendar-quarter"></i> Q<?= ceil(date('n') / 3) ?> <?= date('Y') ?>
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tier Distribution Section - Compact Single Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-theme">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy"></i> Tier Distribution
                    </h6>
                </div>
                <div class="card-body py-3">
                    <div class="row justify-content-center">
                        <?php
                        $tierInfo = [
                            1 => ['name' => 'Bronze', 'icon' => '🥉', 'class' => 'tier-bronze', 'color' => 'warning'],
                            2 => ['name' => 'Silver', 'icon' => '🥈', 'class' => 'tier-silver', 'color' => 'secondary'],
                            3 => ['name' => 'Gold', 'icon' => '🥇', 'class' => 'tier-gold', 'color' => 'warning'],
                            4 => ['name' => 'Platinum', 'icon' => '💎', 'class' => 'tier-platinum', 'color' => 'info'],
                            5 => ['name' => 'Diamond', 'icon' => '💠', 'class' => 'tier-diamond', 'color' => 'primary']
                        ];

                        foreach ($tierInfo as $level => $info):
                            $count = isset($kpis['tier_distribution'][$level]) ? $kpis['tier_distribution'][$level] : 0;
                            ?>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-6 mb-2">
                                <div class="text-center">
                                    <div class="tier-badge <?= $info['class'] ?> mb-1 d-inline-flex align-items-center"
                                        style="font-size: 0.8rem; padding: 0.2rem 0.4rem;">
                                        <span class="tier-icon mr-1"></span>
                                        <?= $info['name'] ?>
                                    </div>
                                    <div class="h6 text-<?= $info['color'] ?> mb-0"><?= $count ?></div>
                                    <small class="text-muted" style="font-size: 0.7rem;">contractors</small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contractors Table -->
    <div class="row">
        <div class="col-12">
            <div class="card-theme">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-hard-hat"></i> Contractors List
                        </h6>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary active" data-filter="all"
                                onclick="filterContractors('all')">
                                <i class="fas fa-users"></i> All
                                <span class="badge badge-primary ml-1"
                                    id="count-all"><?= isset($contractors) ? count($contractors) : 0 ?></span>
                            </button>
                            <button type="button" class="btn btn-outline-success" data-filter="active"
                                onclick="filterContractors('active')">
                                <i class="fas fa-check-circle"></i> Active
                                <span class="badge badge-success ml-1" id="count-active">0</span>
                            </button>
                            <button type="button" class="btn btn-outline-warning" data-filter="inactive"
                                onclick="filterContractors('inactive')">
                                <i class="fas fa-pause-circle"></i> Inactive
                                <span class="badge badge-warning ml-1" id="count-inactive">0</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="contractorsTable" width="100%">
                            <thead>
                                <tr>
                                    <th>Contractor</th>
                                    <th>Current Tier</th>
                                    <th>Specialization</th>
                                    <th>Quarterly Revenue</th>
                                    <th>Total Commission</th>
                                    <th>Status</th>
                                    <th>Contact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($contractors) && !empty($contractors)): ?>
                                    <?php foreach ($contractors as $contractor): ?>
                                        <?php
                                        $isActive = ($contractor->is_active == 1);
                                        $tierLevel = (int) ($contractor->current_tier_achievement ?? 1);
                                        $tierNames = [1 => 'Bronze', 2 => 'Silver', 3 => 'Gold', 4 => 'Platinum', 5 => 'Diamond'];
                                        $tierName = $tierNames[$tierLevel] ?? 'Bronze';
                                        $tierClass = 'tier-' . strtolower($tierName);
                                        ?>
                                        <tr class="contractor-row" data-status="<?= $isActive ? 'active' : 'inactive' ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="contractor-avatar mr-3">
                                                        <div
                                                            style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #007bff, #28a745); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                            <?= strtoupper(substr($contractor->contractor_name ?? 'C', 0, 1)) ?>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold">
                                                            <?= htmlspecialchars($contractor->contractor_name) ?>
                                                        </div>
                                                        <small class="text-muted">
                                                            ID: <?= htmlspecialchars($contractor->unique_id ?? 'N/A') ?>
                                                        </small>
                                                        <?php if (!empty($contractor->company_name)): ?>
                                                            <br><small class="text-info">
                                                                <i class="fas fa-building"></i>
                                                                <?= htmlspecialchars($contractor->company_name) ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="tier-badge <?= $tierClass ?>">
                                                    <span class="tier-icon"></span>
                                                    <?= $tierName ?>
                                                </span>
                                                <?php if (!empty($contractor->tier_earned_quarter)): ?>
                                                    <br><small class="text-muted">
                                                        <i class="fas fa-calendar"></i>
                                                        <?= htmlspecialchars($contractor->tier_earned_quarter) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-outline-primary">
                                                    <?= htmlspecialchars($contractor->specialization ?? 'General') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong>$<?= number_format($contractor->quarterly_revenue_generated ?? 0, 2) ?></strong>
                                                <br><small class="text-muted">This Quarter</small>
                                            </td>
                                            <td>
                                                <strong
                                                    class="text-success">$<?= number_format($contractor->total_commission_earned ?? 0, 2) ?></strong>
                                                <br><small class="text-muted">
                                                    <?= htmlspecialchars($contractor->commission_rate ?? 0) ?>% Rate
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $isActive ? 'success' : 'secondary' ?>">
                                                    <?= $isActive ? 'Active' : 'Inactive' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($contractor->email)): ?>
                                                    <div class="mb-1">
                                                        <i class="fas fa-envelope text-muted"></i>
                                                        <small><?= htmlspecialchars($contractor->email) ?></small>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($contractor->phone)): ?>
                                                    <div>
                                                        <i class="fas fa-phone text-muted"></i>
                                                        <small><?= htmlspecialchars($contractor->phone) ?></small>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-success"
                                                        onclick="viewContractorProfile(<?= $contractor->contractor_id ?>, 'contractors')"
                                                        title="View Profile">
                                                        <i class="fas fa-user"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="editContractor(<?= $contractor->contractor_id ?>)"
                                                        title="Edit Contractor">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm <?= $isActive ? 'btn-warning' : 'btn-outline-success' ?> toggle-status-btn"
                                                        data-contractor-id="<?= $contractor->contractor_id ?>"
                                                        data-current-status="<?= $isActive ? 'active' : 'inactive' ?>"
                                                        title="<?= $isActive ? 'Deactivate' : 'Activate' ?>">
                                                        <i class="fas fa-<?= $isActive ? 'pause' : 'play' ?>"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No contractors found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable
        $('#contractorsTable').DataTable({
            "pageLength": 25,
            "order": [[1, "desc"]], // Order by tier level
            "columnDefs": [
                { "orderable": false, "targets": [7] }, // Actions column
                { "defaultContent": "", "targets": "_all" }
            ],
            "language": {
                "emptyTable": "No contractors found",
                "info": "Showing _START_ to _END_ of _TOTAL_ contractors",
                "infoEmpty": "Showing 0 to 0 of 0 contractors",
                "lengthMenu": "Show _MENU_ contractors per page",
                "search": "Search contractors:",
                "zeroRecords": "No matching contractors found"
            }
        });

        // Update counts
        updateStatusCounts();
    });

    function filterContractors(status) {
        const table = $('#contractorsTable').DataTable();

        if (status === 'all') {
            table.search('').draw();
        } else {
            table.column(5).search(status === 'active' ? 'Active' : 'Inactive').draw();
        }

        // Update button states
        $('.btn-group button').removeClass('active');
        $(`button[data-filter="${status}"]`).addClass('active');
    }

    function updateStatusCounts() {
        const table = $('#contractorsTable').DataTable();
        const allRows = table.rows({ search: 'applied' }).data();

        let activeCount = 0;
        let inactiveCount = 0;

        for (let i = 0; i < allRows.length; i++) {
            const statusCell = $(allRows[i][5]).text().trim();
            if (statusCell === 'Active') {
                activeCount++;
            } else {
                inactiveCount++;
            }
        }

        $('#count-all').text(allRows.length);
        $('#count-active').text(activeCount);
        $('#count-inactive').text(inactiveCount);
    }

    function viewContractorProfile(contractorId, sourceTable) {
        if (contractorId) {
            window.location.href = '<?= URLROOT ?>/contractor/viewContractor/' + contractorId;
        }
    }

    function editContractor(contractorId) {
        window.location.href = '<?= URLROOT ?>/contractor/edit/' + contractorId;
    }

    // Toggle contractor status
    $(document).on('click', '.toggle-status-btn', function () {
        const contractorId = $(this).data('contractor-id');
        const currentStatus = $(this).data('current-status');
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';

        if (confirm(`Are you sure you want to ${newStatus === 'active' ? 'activate' : 'deactivate'} this contractor?`)) {
            $.ajax({
                url: '<?= URLROOT ?>/admin/toggleContractorStatus',
                method: 'POST',
                data: {
                    contractor_id: contractorId,
                    status: newStatus
                },
                success: function (response) {
                    location.reload(); // Reload to show updated status
                },
                error: function () {
                    alert('Error updating contractor status. Please try again.');
                }
            });
        }
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>