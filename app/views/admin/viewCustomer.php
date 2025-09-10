<?php
$pageTitle = 'Customer Details - ' . $data['customer']->customer_name;
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<style>
    .customer-header {
        background: linear-gradient(135deg, #4a90e2, #67b26f);
        color: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .info-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .info-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 5px;
    }

    .info-value {
        color: var(--text-secondary);
        margin-bottom: 15px;
        padding: 8px;
        background: var(--bg-secondary);
        border-radius: 4px;
    }

    .sales-table {
        background: var(--card-bg);
        border-radius: 8px;
        overflow: hidden;
    }
</style>

<div class="container-fluid">
    <!-- Header with Title and Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="text-theme-primary">Customer Details</h2>
                <div class="d-flex gap-2">
                    <a href="<?php echo URLROOT; ?>/customer/edit/<?php echo $data['customer']->customer_id; ?>"
                        class="btn btn-primary">
                        <i class="fa-solid fa-edit"></i> Edit Customer
                    </a>

                    <a href="<?php echo URLROOT; ?>/sales?customer_id=<?php echo $data['customer']->customer_id; ?>"
                        class="btn btn-info">
                        <i class="fa-solid fa-shopping-cart"></i> View All Sales
                    </a>

                    <a href="<?php echo URLROOT; ?>/customer" class="btn btn-secondary">
                        <i class="fa-solid fa-list"></i> All Customers
                    </a>

                    <a href="<?php echo URLROOT; ?>/customer" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Back to Customers
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Header -->
    <div class="customer-header">
        <h3><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($data['customer']->customer_name); ?></h3>
        <p class="mb-0">Customer ID: <?php echo $data['customer']->customer_id; ?></p>
        <?php if (isset($data['customer']->unique_id)): ?>
            <p class="mb-0">Unique ID: <?php echo $data['customer']->unique_id; ?></p>
        <?php endif; ?>
    </div>

    <div class="row">
        <!-- Customer Information -->
        <div class="col-md-6">
            <div class="info-card">
                <h5 class="text-theme-primary mb-3">
                    <i class="fa-solid fa-info-circle"></i> Customer Information
                </h5>

                <div class="info-label">Customer Name</div>
                <div class="info-value"><?php echo htmlspecialchars($data['customer']->customer_name); ?></div>

                <?php if (isset($data['customer']->unique_id) && !empty($data['customer']->unique_id)): ?>
                    <div class="info-label">Unique ID</div>
                    <div class="info-value">
                        <span class="badge bg-info"><?php echo $data['customer']->unique_id; ?></span>
                    </div>
                <?php endif; ?>

                <div class="info-label">Customer Type</div>
                <div class="info-value">
                    <span class="badge 
                        <?php
                        switch ($data['customer']->customer_type) {
                            case 'business':
                                echo 'bg-primary';
                                break;
                            case 'individual':
                                echo 'bg-success';
                                break;
                            case 'walk-in':
                                echo 'bg-warning';
                                break;
                            default:
                                echo 'bg-secondary';
                        }
                        ?>">
                        <i class="fa-solid 
                            <?php
                            switch ($data['customer']->customer_type) {
                                case 'business':
                                    echo 'fa-building';
                                    break;
                                case 'individual':
                                    echo 'fa-user';
                                    break;
                                case 'walk-in':
                                    echo 'fa-walking';
                                    break;
                                default:
                                    echo 'fa-question';
                            }
                            ?>"></i>
                        <?php echo ucfirst($data['customer']->customer_type); ?>
                    </span>
                </div>

                <?php if (isset($data['customer']->contact_info) && !empty($data['customer']->contact_info)): ?>
                    <div class="info-label">Contact Information</div>
                    <div class="info-value">
                        <i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($data['customer']->contact_info); ?>
                    </div>
                <?php endif; ?>

                <div class="info-label">Status</div>
                <div class="info-value">
                    <span
                        class="badge <?php echo $data['customer']->status === 'active' ? 'bg-success' : 'bg-danger'; ?>">
                        <i
                            class="fa-solid <?php echo $data['customer']->status === 'active' ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                        <?php echo ucfirst($data['customer']->status); ?>
                    </span>
                </div>

                <div class="info-label">Credit Limit</div>
                <div class="info-value">
                    <i class="fa-solid fa-credit-card text-primary"></i>
                    $<?php echo number_format($data['customer']->credit_limit, 2); ?>
                </div>

                <?php if (isset($data['customer']->discount_credit_balance) && $data['customer']->discount_credit_balance > 0): ?>
                    <div class="info-label">Discount Credit Balance</div>
                    <div class="info-value">
                        <i class="fa-solid fa-piggy-bank text-success"></i>
                        $<?php echo number_format($data['customer']->discount_credit_balance, 2); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($data['customer']->total_discount_earned) && $data['customer']->total_discount_earned > 0): ?>
                    <div class="info-label">Total Discount Earned</div>
                    <div class="info-value">
                        <i class="fa-solid fa-gift text-warning"></i>
                        $<?php echo number_format($data['customer']->total_discount_earned, 2); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($data['customer']->total_discount_used) && $data['customer']->total_discount_used > 0): ?>
                    <div class="info-label">Total Discount Used</div>
                    <div class="info-value">
                        <i class="fa-solid fa-shopping-bag text-info"></i>
                        $<?php echo number_format($data['customer']->total_discount_used, 2); ?>
                    </div>
                <?php endif; ?>

                <!-- Customer Statistics -->
                <hr class="my-3">
                <h6 class="text-theme-primary mb-3">
                    <i class="fa-solid fa-chart-bar"></i> Customer Statistics
                </h6>

                <?php if (isset($data['transactions']) && !empty($data['transactions'])): ?>
                    <?php
                    $totalSpent = array_sum(array_column($data['transactions'], 'amount'));
                    $transactionCount = count($data['transactions']);
                    $avgTransaction = $transactionCount > 0 ? $totalSpent / $transactionCount : 0;

                    // Get date range
                    $dates = array_column($data['transactions'], 'transaction_date');
                    $firstPurchase = !empty($dates) ? min($dates) : null;
                    $lastPurchase = !empty($dates) ? max($dates) : null;
                    ?>

                    <div class="row">
                        <div class="col-6">
                            <div class="info-label">Total Transactions</div>
                            <div class="info-value">
                                <span class="badge bg-primary"><?php echo $transactionCount; ?></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">Total Spent</div>
                            <div class="info-value">
                                <span class="badge bg-success">$<?php echo number_format($totalSpent, 2); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="info-label">Average Transaction</div>
                            <div class="info-value">
                                <span class="badge bg-info">$<?php echo number_format($avgTransaction, 2); ?></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">Customer Tier</div>
                            <div class="info-value">
                                <?php if ($transactionCount >= 100): ?>
                                    <span class="badge bg-warning">💎 VIP</span>
                                <?php elseif ($transactionCount >= 50): ?>
                                    <span class="badge bg-info">⭐ Premium</span>
                                <?php elseif ($transactionCount >= 10): ?>
                                    <span class="badge bg-success">🥉 Regular</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">👤 New</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($firstPurchase): ?>
                        <div class="info-label">First Purchase</div>
                        <div class="info-value">
                            <i class="fa-solid fa-calendar-plus text-success"></i>
                            <?php echo date('M d, Y', strtotime($firstPurchase)); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($lastPurchase): ?>
                        <div class="info-label">Last Purchase</div>
                        <div class="info-value">
                            <i class="fa-solid fa-calendar-check text-primary"></i>
                            <?php echo date('M d, Y', strtotime($lastPurchase)); ?>
                            <small class="text-muted">(<?php echo date('H:i', strtotime($lastPurchase)); ?>)</small>
                        </div>
                    <?php endif; ?>

                    <?php
                    $daysSinceLastPurchase = $lastPurchase ? floor((time() - strtotime($lastPurchase)) / (60 * 60 * 24)) : null;
                    if ($daysSinceLastPurchase !== null):
                        ?>
                        <div class="info-label">Customer Activity</div>
                        <div class="info-value">
                            <?php if ($daysSinceLastPurchase <= 7): ?>
                                <span class="badge bg-success">🔥 Very Active</span>
                                <small class="text-muted">(Last purchase: <?php echo $daysSinceLastPurchase; ?> days ago)</small>
                            <?php elseif ($daysSinceLastPurchase <= 30): ?>
                                <span class="badge bg-warning">✨ Active</span>
                                <small class="text-muted">(Last purchase: <?php echo $daysSinceLastPurchase; ?> days ago)</small>
                            <?php elseif ($daysSinceLastPurchase <= 90): ?>
                                <span class="badge bg-info">😴 Inactive</span>
                                <small class="text-muted">(Last purchase: <?php echo $daysSinceLastPurchase; ?> days ago)</small>
                            <?php else: ?>
                                <span class="badge bg-secondary">💤 Dormant</span>
                                <small class="text-muted">(Last purchase: <?php echo $daysSinceLastPurchase; ?> days ago)</small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (isset($data['customer']->created_at)): ?>
                    <div class="info-label">Customer Since</div>
                    <div class="info-value">
                        <i class="fa-solid fa-user-plus text-info"></i>
                        <?php echo date('M d, Y', strtotime($data['customer']->created_at)); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Customer Transactions -->
        <div class="col-md-6">
            <div class="info-card">
                <h5 class="text-theme-primary mb-3">
                    <i class="fa-solid fa-receipt"></i> All Transactions
                </h5>

                <?php if (isset($data['transactions']) && !empty($data['transactions'])): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($data['transactions'], 0, 10) as $transaction): ?>
                                    <tr>
                                        <td><?php echo $transaction->transaction_id; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($transaction->transaction_date)); ?></td>
                                        <td>
                                            <span class="badge bg-success"><?php echo $transaction->transaction_type; ?></span>
                                        </td>
                                        <td>$<?php echo number_format($transaction->amount, 2); ?></td>
                                        <td><?php echo ucfirst($transaction->payment_method ?? 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (count($data['transactions']) > 10): ?>
                        <div class="mt-2 text-center">
                            <small class="text-muted">Showing latest 10 transactions. Total:
                                <?php echo count($data['transactions']); ?></small>
                        </div>
                    <?php endif; ?>

                    <div class="mt-3">
                        <div class="row">
                            <div class="col-6">
                                <strong>Total Transactions:</strong><br>
                                <span class="badge bg-info"><?php echo count($data['transactions']); ?></span>
                            </div>
                            <div class="col-6">
                                <strong>Total Amount:</strong><br>
                                <span class="badge bg-success">
                                    $<?php echo number_format(array_sum(array_column($data['transactions'], 'amount')), 2); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fa-solid fa-receipt fa-3x mb-3"></i>
                        <p>No transactions found for this customer</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>