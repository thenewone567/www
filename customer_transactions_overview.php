<?php
require_once 'bootstrap.php';

// Get database connection
$db = new Database();

// Get all customers with their transaction counts
$customersWithTransactions = [];
try {
    $db->query("SELECT 
                   c.customer_id,
                   c.customer_name,
                   c.email,
                   c.phone,
                   COUNT(s.sale_id) as transaction_count,
                   COALESCE(SUM(s.total_amount), 0) as total_spent,
                   MAX(s.sale_date) as last_transaction_date
               FROM customers c
               LEFT JOIN sales s ON c.customer_id = s.customer_id
               GROUP BY c.customer_id, c.customer_name, c.email, c.phone
               ORDER BY transaction_count DESC");
    $customersWithTransactions = $db->resultSet();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// Function to get customer transactions
function getCustomerTransactions($customerId, $db)
{
    try {
        $db->query("SELECT 
                       s.sale_id as transaction_id,
                       s.sale_date as transaction_date,
                       s.total_amount as amount,
                       s.payment_mode as payment_method,
                       'Sale' as transaction_type
                   FROM sales s 
                   WHERE s.customer_id = :customer_id 
                   ORDER BY s.sale_date DESC 
                   LIMIT 10");
        $db->bind(':customer_id', $customerId);
        $db->execute();
        return $db->resultSet();
    } catch (Exception $e) {
        return [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Transactions Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/app-unified.css">
    <style>
        .customer-card {
            transition: transform 0.2s;
        }

        .customer-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .filter-section {
            background: var(--card-bg);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .stats-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
        }

        .transaction-trend {
            font-size: 0.9em;
        }

        .customer-tier {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fa-solid fa-users"></i> Customer Transactions Overview</h2>
                    <div class="btn-group">
                        <button class="btn btn-success" onclick="exportToCSV()">
                            <i class="fa-solid fa-download"></i> Export CSV
                        </button>
                        <button class="btn btn-info" onclick="printReport()">
                            <i class="fa-solid fa-print"></i> Print Report
                        </button>
                        <a href="<?php echo URLROOT; ?>/admin" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i> Back to Admin
                        </a>
                    </div>
                </div>

                <!-- Advanced Filters -->
                <div class="filter-section">
                    <h5><i class="fa-solid fa-filter"></i> Advanced Filters & Search</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Search Customer</label>
                            <input type="text" id="customerSearch" class="form-control"
                                placeholder="Search by name, email, or phone...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Transaction Count</label>
                            <select id="transactionFilter" class="form-select">
                                <option value="">All Customers</option>
                                <option value="with">With Transactions</option>
                                <option value="without">Without Transactions</option>
                                <option value="high">High Volume (50+)</option>
                                <option value="medium">Medium Volume (10-49)</option>
                                <option value="low">Low Volume (1-9)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Spent Range</label>
                            <select id="spentFilter" class="form-select">
                                <option value="">All Amounts</option>
                                <option value="high">$1000+</option>
                                <option value="medium">$100-$999</option>
                                <option value="low">$1-$99</option>
                                <option value="zero">$0</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sort By</label>
                            <select id="sortFilter" class="form-select">
                                <option value="transactions">Transaction Count</option>
                                <option value="spent">Total Spent</option>
                                <option value="name">Customer Name</option>
                                <option value="recent">Most Recent</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Actions</label>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary" onclick="applyFilters()">
                                    <i class="fa-solid fa-search"></i> Apply
                                </button>
                                <button class="btn btn-outline-secondary" onclick="clearFilters()">
                                    <i class="fa-solid fa-refresh"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Customers</h5>
                                <h3 id="totalCustomers"><?php echo count($customersWithTransactions); ?></h3>
                                <small>Registered</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Active Customers</h5>
                                <h3 id="activeCustomers">
                                    <?php echo count(array_filter($customersWithTransactions, function ($c) {
                                        return $c->transaction_count > 0;
                                    })); ?>
                                </h3>
                                <small>With purchases</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Transactions</h5>
                                <h3 id="totalTransactions">
                                    <?php echo number_format(array_sum(array_column($customersWithTransactions, 'transaction_count'))); ?>
                                </h3>
                                <small>All time</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Revenue</h5>
                                <h3 id="totalRevenue">
                                    $<?php echo number_format(array_sum(array_column($customersWithTransactions, 'total_spent')), 2); ?>
                                </h3>
                                <small>All time</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-dark text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Avg per Customer</h5>
                                <h3 id="avgPerCustomer">$<?php
                                $activeCustomers = array_filter($customersWithTransactions, function ($c) {
                                    return $c->transaction_count > 0;
                                });
                                echo number_format(count($activeCustomers) > 0 ? array_sum(array_column($customersWithTransactions, 'total_spent')) / count($activeCustomers) : 0, 2);
                                ?></h3>
                                <small>Average spent</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Inactive</h5>
                                <h3 id="inactiveCustomers">
                                    <?php echo count(array_filter($customersWithTransactions, function ($c) {
                                        return $c->transaction_count == 0;
                                    })); ?>
                                </h3>
                                <small>No purchases</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Bar -->
                <div class="stats-bar mb-4">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <i class="fa-solid fa-chart-line fa-2x mb-2"></i>
                            <h6>Top Customer</h6>
                            <strong><?php
                            $topCustomer = array_reduce($customersWithTransactions, function ($max, $customer) {
                                return ($customer->total_spent > ($max->total_spent ?? 0)) ? $customer : $max;
                            });
                            echo $topCustomer ? htmlspecialchars($topCustomer->customer_name) . ' ($' . number_format($topCustomer->total_spent, 2) . ')' : 'N/A';
                            ?></strong>
                        </div>
                        <div class="col-md-3">
                            <i class="fa-solid fa-trophy fa-2x mb-2"></i>
                            <h6>Most Transactions</h6>
                            <strong><?php
                            $mostActive = array_reduce($customersWithTransactions, function ($max, $customer) {
                                return ($customer->transaction_count > ($max->transaction_count ?? 0)) ? $customer : $max;
                            });
                            echo $mostActive ? htmlspecialchars($mostActive->customer_name) . ' (' . $mostActive->transaction_count . ')' : 'N/A';
                            ?></strong>
                        </div>
                        <div class="col-md-3">
                            <i class="fa-solid fa-calendar fa-2x mb-2"></i>
                            <h6>Latest Transaction</h6>
                            <strong><?php
                            $latestTransaction = array_reduce($customersWithTransactions, function ($latest, $customer) {
                                if (!$customer->last_transaction_date)
                                    return $latest;
                                return (!$latest || $customer->last_transaction_date > $latest) ? $customer->last_transaction_date : $latest;
                            });
                            echo $latestTransaction ? date('M d, Y', strtotime($latestTransaction)) : 'N/A';
                            ?></strong>
                        </div>
                        <div class="col-md-3">
                            <i class="fa-solid fa-percentage fa-2x mb-2"></i>
                            <h6>Customer Activity Rate</h6>
                            <strong><?php
                            $activityRate = count($customersWithTransactions) > 0 ?
                                (count(array_filter($customersWithTransactions, function ($c) {
                                    return $c->transaction_count > 0; })) / count($customersWithTransactions)) * 100 : 0;
                            echo number_format($activityRate, 1) . '%';
                            ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Customer Transaction Details -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Customer Details <span id="customerCount"
                            class="badge bg-primary"><?php echo count($customersWithTransactions); ?></span></h4>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="toggleView('grid')" id="gridViewBtn">
                            <i class="fa-solid fa-th"></i> Grid
                        </button>
                        <button class="btn btn-outline-primary" onclick="toggleView('list')" id="listViewBtn">
                            <i class="fa-solid fa-list"></i> List
                        </button>
                    </div>
                </div>

                <div class="row" id="customerContainer">
                    <?php foreach ($customersWithTransactions as $customer): ?>
                        <div class="col-md-6 mb-4 customer-item customer-card"
                            data-name="<?php echo strtolower($customer->customer_name); ?>"
                            data-email="<?php echo strtolower($customer->email ?? ''); ?>"
                            data-phone="<?php echo strtolower($customer->phone ?? ''); ?>"
                            data-transactions="<?php echo $customer->transaction_count; ?>"
                            data-spent="<?php echo $customer->total_spent; ?>"
                            data-last-transaction="<?php echo $customer->last_transaction_date ?: '1970-01-01'; ?>">

                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fa-solid fa-user"></i>
                                        <?php echo htmlspecialchars($customer->customer_name); ?>
                                        <small class="text-muted">(ID: <?php echo $customer->customer_id; ?>)</small>
                                        <?php if ($customer->transaction_count >= 100): ?>
                                            <span class="customer-tier text-warning">💎 VIP</span>
                                        <?php elseif ($customer->transaction_count >= 50): ?>
                                            <span class="customer-tier text-info">⭐ Premium</span>
                                        <?php elseif ($customer->transaction_count >= 10): ?>
                                            <span class="customer-tier text-success">🥉 Regular</span>
                                        <?php endif; ?>
                                    </h5>
                                    <div>
                                        <?php if ($customer->transaction_count > 0): ?>
                                            <span class="badge bg-success"><?php echo $customer->transaction_count; ?>
                                                transactions</span>
                                            <?php if ($customer->last_transaction_date && strtotime($customer->last_transaction_date) > strtotime('-30 days')): ?>
                                                <span class="badge bg-info">Recent</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No transactions</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Customer Info -->
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">Email:</small><br>
                                            <strong><?php echo $customer->email ?: 'N/A'; ?></strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Phone:</small><br>
                                            <strong><?php echo $customer->phone ?: 'N/A'; ?></strong>
                                        </div>
                                    </div>

                                    <?php if ($customer->transaction_count > 0): ?>
                                        <!-- Transaction Summary -->
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">Total Spent:</small><br>
                                                <strong
                                                    class="text-success">$<?php echo number_format($customer->total_spent, 2); ?></strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Last Transaction:</small><br>
                                                <strong><?php echo date('M d, Y', strtotime($customer->last_transaction_date)); ?></strong>
                                            </div>
                                        </div>

                                        <!-- Recent Transactions -->
                                        <div class="transaction-section">
                                            <h6 class="text-primary mb-2">
                                                <i class="fa-solid fa-receipt"></i> Recent Transactions (Latest 10)
                                            </h6>
                                            <?php
                                            $transactions = getCustomerTransactions($customer->customer_id, $db);
                                            if (!empty($transactions)): ?>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-striped">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Date</th>
                                                                <th>Amount</th>
                                                                <th>Payment</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($transactions as $transaction): ?>
                                                                <tr>
                                                                    <td><?php echo $transaction->transaction_id; ?></td>
                                                                    <td><?php echo date('M d', strtotime($transaction->transaction_date)); ?>
                                                                    </td>
                                                                    <td>$<?php echo number_format($transaction->amount, 2); ?></td>
                                                                    <td>
                                                                        <span class="badge bg-info">
                                                                            <?php echo ucfirst($transaction->payment_method ?? 'N/A'); ?>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-info text-center">
                                                    <i class="fa-solid fa-info-circle"></i>
                                                    <strong>Transaction data mismatch detected!</strong><br>
                                                    <small>Main query shows <?php echo $customer->transaction_count; ?>
                                                        transactions, but detail query returned 0 results for customer ID
                                                        <?php echo $customer->customer_id; ?>.</small>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- View Details Button -->
                                        <div class="text-center mt-3">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo URLROOT; ?>/admin/viewCustomer/<?php echo $customer->customer_id; ?>"
                                                    class="btn btn-primary">
                                                    <i class="fa-solid fa-eye"></i> View Details
                                                </a>
                                                <a href="<?php echo URLROOT; ?>/customer/edit/<?php echo $customer->customer_id; ?>"
                                                    class="btn btn-outline-primary">
                                                    <i class="fa-solid fa-edit"></i> Edit
                                                </a>
                                                <a href="<?php echo URLROOT; ?>/sales?customer_id=<?php echo $customer->customer_id; ?>"
                                                    class="btn btn-outline-info">
                                                    <i class="fa-solid fa-shopping-cart"></i> Sales
                                                </a>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <!-- No Transactions -->
                                        <div class="text-center text-muted py-3">
                                            <i class="fa-solid fa-receipt fa-2x mb-2"></i>
                                            <p class="mb-0">No transactions found</p>
                                            <small>This customer hasn't made any purchases yet</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let originalCustomers = [];

        // Store original customer data
        document.addEventListener('DOMContentLoaded', function () {
            originalCustomers = Array.from(document.querySelectorAll('.customer-item'));
        });

        // Search functionality
        function applyFilters() {
            const searchTerm = document.getElementById('customerSearch').value.toLowerCase();
            const transactionFilter = document.getElementById('transactionFilter').value;
            const spentFilter = document.getElementById('spentFilter').value;
            const sortFilter = document.getElementById('sortFilter').value;

            let customers = Array.from(document.querySelectorAll('.customer-item'));

            // Filter customers
            customers = customers.filter(customer => {
                // Search filter
                const name = customer.dataset.name;
                const email = customer.dataset.email;
                const phone = customer.dataset.phone;
                const searchMatch = searchTerm === '' ||
                    name.includes(searchTerm) ||
                    email.includes(searchTerm) ||
                    phone.includes(searchTerm);

                if (!searchMatch) return false;

                // Transaction count filter
                const transactions = parseInt(customer.dataset.transactions);
                let transactionMatch = true;
                switch (transactionFilter) {
                    case 'with': transactionMatch = transactions > 0; break;
                    case 'without': transactionMatch = transactions === 0; break;
                    case 'high': transactionMatch = transactions >= 50; break;
                    case 'medium': transactionMatch = transactions >= 10 && transactions < 50; break;
                    case 'low': transactionMatch = transactions > 0 && transactions < 10; break;
                }

                if (!transactionMatch) return false;

                // Spent amount filter
                const spent = parseFloat(customer.dataset.spent);
                let spentMatch = true;
                switch (spentFilter) {
                    case 'high': spentMatch = spent >= 1000; break;
                    case 'medium': spentMatch = spent >= 100 && spent < 1000; break;
                    case 'low': spentMatch = spent > 0 && spent < 100; break;
                    case 'zero': spentMatch = spent === 0; break;
                }

                return spentMatch;
            });

            // Sort customers
            customers.sort((a, b) => {
                switch (sortFilter) {
                    case 'transactions':
                        return parseInt(b.dataset.transactions) - parseInt(a.dataset.transactions);
                    case 'spent':
                        return parseFloat(b.dataset.spent) - parseFloat(a.dataset.spent);
                    case 'name':
                        return a.dataset.name.localeCompare(b.dataset.name);
                    case 'recent':
                        return new Date(b.dataset.lastTransaction) - new Date(a.dataset.lastTransaction);
                    default:
                        return 0;
                }
            });

            // Update display
            const container = document.getElementById('customerContainer');
            container.innerHTML = '';
            customers.forEach(customer => {
                container.appendChild(customer);
            });

            // Update count
            document.getElementById('customerCount').textContent = customers.length;

            // Update summary stats
            updateSummaryStats(customers);
        }

        function clearFilters() {
            document.getElementById('customerSearch').value = '';
            document.getElementById('transactionFilter').value = '';
            document.getElementById('spentFilter').value = '';
            document.getElementById('sortFilter').value = 'transactions';

            const container = document.getElementById('customerContainer');
            container.innerHTML = '';
            originalCustomers.forEach(customer => {
                container.appendChild(customer);
            });

            document.getElementById('customerCount').textContent = originalCustomers.length;
            updateSummaryStats(originalCustomers);
        }

        function updateSummaryStats(customers) {
            const totalCustomers = customers.length;
            const activeCustomers = customers.filter(c => parseInt(c.dataset.transactions) > 0).length;
            const totalTransactions = customers.reduce((sum, c) => sum + parseInt(c.dataset.transactions), 0);
            const totalRevenue = customers.reduce((sum, c) => sum + parseFloat(c.dataset.spent), 0);
            const inactiveCustomers = totalCustomers - activeCustomers;
            const avgPerCustomer = activeCustomers > 0 ? totalRevenue / activeCustomers : 0;

            document.getElementById('totalCustomers').textContent = totalCustomers;
            document.getElementById('activeCustomers').textContent = activeCustomers;
            document.getElementById('totalTransactions').textContent = totalTransactions.toLocaleString();
            document.getElementById('totalRevenue').textContent = '$' + totalRevenue.toLocaleString('en-US', { minimumFractionDigits: 2 });
            document.getElementById('inactiveCustomers').textContent = inactiveCustomers;
            document.getElementById('avgPerCustomer').textContent = '$' + avgPerCustomer.toLocaleString('en-US', { minimumFractionDigits: 2 });
        }

        function toggleView(viewType) {
            const container = document.getElementById('customerContainer');
            const customers = container.querySelectorAll('.customer-item');

            if (viewType === 'list') {
                customers.forEach(customer => {
                    customer.className = 'col-12 mb-2 customer-item';
                    const card = customer.querySelector('.card');
                    card.className = 'card card-horizontal';
                });
                document.getElementById('listViewBtn').classList.add('active');
                document.getElementById('gridViewBtn').classList.remove('active');
            } else {
                customers.forEach(customer => {
                    customer.className = 'col-md-6 mb-4 customer-item customer-card';
                    const card = customer.querySelector('.card');
                    card.className = 'card h-100';
                });
                document.getElementById('gridViewBtn').classList.add('active');
                document.getElementById('listViewBtn').classList.remove('active');
            }
        }

        function exportToCSV() {
            const customers = Array.from(document.querySelectorAll('.customer-item:not([style*="display: none"])'));
            let csv = 'Customer ID,Customer Name,Email,Phone,Transaction Count,Total Spent,Last Transaction\n';

            customers.forEach(customer => {
                const card = customer.querySelector('.card');
                const name = customer.dataset.name;
                const email = customer.dataset.email || 'N/A';
                const phone = customer.dataset.phone || 'N/A';
                const transactions = customer.dataset.transactions;
                const spent = customer.dataset.spent;
                const lastTransaction = customer.dataset.lastTransaction !== '1970-01-01' ? customer.dataset.lastTransaction : 'N/A';
                const id = card.querySelector('small').textContent.match(/\d+/)[0];

                csv += `${id},"${name}","${email}","${phone}",${transactions},${spent},"${lastTransaction}"\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'customer_transactions_' + new Date().toISOString().split('T')[0] + '.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }

        function printReport() {
            window.print();
        }

        // Real-time search
        document.getElementById('customerSearch').addEventListener('input', function () {
            if (this.value.length >= 2 || this.value.length === 0) {
                applyFilters();
            }
        });

        // Initialize grid view as active
        document.getElementById('gridViewBtn').classList.add('active');
    </script>

    <style media="print">
        .filter-section,
        .btn-group,
        button {
            display: none !important;
        }

        .card {
            break-inside: avoid;
        }

        .stats-bar {
            -webkit-print-color-adjust: exact;
        }
    </style>
</body>

</html>