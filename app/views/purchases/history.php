<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-history mr-2"></i>Purchase Order History</h2>
                <div>
                    <a href="<?php echo URLROOT; ?>/purchases" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>Back to Purchases
                    </a>
                    <a href="<?php echo URLROOT; ?>/purchases/add" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>New Purchase Order
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php flash('purchase_message'); ?>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list mr-2"></i>All Purchase Orders</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($data['purchases'])): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>PO Number</th>
                                <th>Supplier</th>
                                <th>Purchase Date</th>
                                <th>Status</th>
                                <th>Total Amount</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['purchases'] as $purchase): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($purchase->order_no ?? $purchase->po_number ?? 'N/A'); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($purchase->supplier ?? 'Unknown'); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($purchase->date ?? $purchase->purchase_date ?? 'now')); ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status = $purchase->status ?? 'pending';
                                        $statusClass = '';
                                        switch ($status) {
                                            case 'pending':
                                                $statusClass = 'badge-warning';
                                                break;
                                            case 'approved':
                                                $statusClass = 'badge-info';
                                                break;
                                            case 'received':
                                            case 'completed':
                                                $statusClass = 'badge-success';
                                                break;
                                            case 'cancelled':
                                                $statusClass = 'badge-danger';
                                                break;
                                            default:
                                                $statusClass = 'badge-secondary';
                                        }
                                        ?>
                                        <span class="badge <?php echo $statusClass; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong>₹<?php echo number_format($purchase->total_amount ?? 0, 2); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($purchase->created_by ?? 'Unknown'); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?php echo URLROOT; ?>/purchases/details/<?php echo $purchase->id; ?>"
                                                class="btn btn-outline-primary btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (($purchase->status ?? 'pending') === 'pending'): ?>
                                                <a href="<?php echo URLROOT; ?>/purchases/approve/<?php echo $purchase->id; ?>"
                                                    class="btn btn-outline-success btn-sm" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (in_array(($purchase->status ?? 'pending'), ['approved', 'ready_to_receive'])): ?>
                                                <a href="<?php echo URLROOT; ?>/receiving/process/<?php echo $purchase->id; ?>"
                                                    class="btn btn-outline-info btn-sm" title="Receive">
                                                    <i class="fas fa-truck"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?php echo URLROOT; ?>/purchases/viewReceipt/<?php echo $purchase->id; ?>"
                                                class="btn btn-outline-secondary btn-sm" title="View Receipt">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Summary Stats -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Orders</h5>
                                <h3 class="text-primary"><?php echo count($data['purchases']); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="card-title">Pending</h5>
                                <h3 class="text-warning">
                                    <?php
                                    echo count(array_filter($data['purchases'], function ($p) {
                                        return ($p->status ?? 'pending') === 'pending';
                                    }));
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="card-title">Completed</h5>
                                <h3 class="text-success">
                                    <?php
                                    echo count(array_filter($data['purchases'], function ($p) {
                                        return in_array(($p->status ?? 'pending'), ['completed', 'received']);
                                    }));
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Value</h5>
                                <h3 class="text-info">
                                    ₹<?php
                                    echo number_format(array_sum(array_map(function ($p) {
                                        return $p->total_amount ?? 0;
                                    }, $data['purchases'])), 2);
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Purchase Orders Found</h4>
                    <p class="text-muted">Start by creating your first purchase order.</p>
                    <a href="<?php echo URLROOT; ?>/purchases/add" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Create Purchase Order
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Add some interactive features
    document.addEventListener('DOMContentLoaded', function () {
        // Add tooltip for action buttons
        $('[title]').tooltip();

        // Add search functionality
        if (document.querySelector('.table')) {
            const searchHtml = `
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" id="tableSearch" placeholder="Search PO numbers, suppliers...">
                </div>
                <div class="col-md-6">
                    <select class="form-control" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="received">Received</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
        `;
            document.querySelector('.card-body').insertAdjacentHTML('afterbegin', searchHtml);

            // Simple table search
            document.getElementById('tableSearch').addEventListener('input', function () {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            });

            // Status filter
            document.getElementById('statusFilter').addEventListener('change', function () {
                const filter = this.value;
                const rows = document.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    if (!filter) {
                        row.style.display = '';
                    } else {
                        const badge = row.querySelector('.badge');
                        const status = badge ? badge.textContent.toLowerCase() : '';
                        row.style.display = status === filter ? '' : 'none';
                    }
                });
            });
        }
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>