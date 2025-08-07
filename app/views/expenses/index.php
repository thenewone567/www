<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Expenses Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/">Home</a></li>
                        <li class="breadcrumb-item active">Expenses</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Summary Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?= isset($data['expenses']) && is_array($data['expenses']) ? count($data['expenses']) : 0 ?>
                            </h3>
                            <p>Total Expenses</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>$<?= number_format(isset($data['summary']->total_amount) ? $data['summary']->total_amount : 0, 2) ?>
                            </h3>
                            <p>Total Amount</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>$<?= number_format(isset($data['summary']->monthly_total) ? $data['summary']->monthly_total : 0, 2) ?>
                            </h3>
                            <p>This Month</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-month"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= isset($data['categories']) && is_array($data['categories']) ? count($data['categories']) : 0 ?>
                            </h3>
                            <p>Categories</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tags"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="btn-group" role="group">
                        <a href="<?php echo URLROOT; ?>/expenses/add" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Expense
                        </a>
                        <a href="<?php echo URLROOT; ?>/expenses/categories" class="btn btn-info">
                            <i class="fas fa-tags"></i> Manage Categories
                        </a>
                        <button class="btn btn-primary" onclick="exportExpenses()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Expenses</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo URLROOT; ?>/expenses">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="<?= $_GET['date_from'] ?? '' ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control"
                                        value="<?= $_GET['date_to'] ?? '' ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select name="category_id" class="form-control">
                                        <option value="">All Categories</option>
                                        <?php if (isset($data['categories']) && is_array($data['categories'])): ?>
                                            <?php foreach ($data['categories'] as $category): ?>
                                                <option value="<?= $category->category_id ?>" <?= ($_GET['category_id'] ?? '') == $category->category_id ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($category->category_name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="<?php echo URLROOT; ?>/expenses" class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Expenses Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Expenses List</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="expensesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Reference</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['expenses']) && !empty($data['expenses'])): ?>
                                    <?php foreach ($data['expenses'] as $expense): ?>
                                        <tr>
                                            <td><?= date('Y-m-d', strtotime($expense->expense_date)) ?></td>
                                            <td><?= htmlspecialchars($expense->description) ?></td>
                                            <td><?= htmlspecialchars($expense->category_name ?? 'N/A') ?></td>
                                            <td>$<?= number_format($expense->amount, 2) ?></td>
                                            <td><?= htmlspecialchars($expense->reference_number ?? '') ?></td>
                                            <td><?= htmlspecialchars($expense->created_by_name ?? 'N/A') ?></td>
                                            <td>
                                                <a href="<?php echo URLROOT; ?>/expenses/edit/<?php echo $expense->expense_id; ?>"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="deleteExpense(<?= $expense->expense_id ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No expenses found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function () {
        $('#expensesTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "order": [[0, "desc"]]
        });
    });

    function deleteExpense(expenseId) {
        if (confirm('Are you sure you want to delete this expense?')) {
            window.location.href = '<?php echo URLROOT; ?>/expenses/delete/' + expenseId;
        }
    }

    function exportExpenses() {
        // Add export functionality
        alert('Export functionality to be implemented');
    }
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>