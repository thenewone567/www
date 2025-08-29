<!-- Unified CSS -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/app-unified.css">

<?php
$pageTitle = 'Price Management';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<!-- Load Streamlined Theme System -->
<div class="container-fluid page-top-area mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-dollar-sign mr-2"></i>Price Management
            </h1>
            <small class="text-muted">Manage product pricing, margins, and bulk updates</small>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <div class="btn-group" role="group">
                <button class="btn-theme btn-success-theme" onclick="bulkPriceUpdate()">
                    <i class="fas fa-edit mr-2"></i>Bulk Update
                </button>
                <button class="btn-theme btn-info-theme" onclick="exportPrices()">
                    <i class="fas fa-download mr-2"></i>Export Prices
                </button>
                <button class="btn-theme btn-warning-theme" onclick="importPrices()">
                    <i class="fas fa-upload mr-2"></i>Import Prices
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Price Management Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="kpi-card h-100">
                <div class="text-center">
                    <div class="mb-3" style="color: var(--success);">
                        <i class="fas fa-chart-line fa-3x"></i>
                    </div>
                    <div class="kpi-value" style="color: var(--success);">
                        <?= $data['stats']['total_products'] ?? 0 ?>
                    </div>
                    <div class="kpi-label">Total Products</div>
                    <small style="color: var(--text-muted);">Being managed</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card-theme h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-3">
                        <i class="fas fa-percentage fa-3x"></i>
                    </div>
                    <h2 class="mb-2 font-weight-bold text-info">
                        <?= number_format($data['stats']['average_margin'] ?? 0, 1) ?>%
                    </h2>
                    <p class="text-muted mb-0 font-weight-600">Average Margin</p>
                    <small class="text-muted">Across all products</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card-theme h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-3">
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                    </div>
                    <h2 class="mb-2 font-weight-bold text-warning">
                        <?= $data['stats']['low_margin_products'] ?? 0 ?>
                    </h2>
                    <p class="text-muted mb-0 font-weight-600">Low Margin</p>
                    <small class="text-muted">
                        < 15% margin</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card-theme h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-3">
                        <i class="fas fa-chart-pie fa-3x"></i>
                    </div>
                    <h2 class="mb-2 font-weight-bold text-success">
                        <?= number_format($data['stats']['total_gross_margin'] ?? 0, 1) ?>%
                    </h2>
                    <p class="text-muted mb-0 font-weight-600">Total Gross Margin</p>
                    <small class="text-muted">Overall profitability</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Margin Management Controls -->
    <div class="margin-management-section mb-4">
        <div class="row">
            <!-- Smart Margin Management Tools -->
            <div class="col-md-12">
                <div class="card-theme">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-brain mr-2"></i>Smart Margin Optimization
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Target Gross Margin -->
                        <div class="mb-3">
                            <label>Target Gross Margin</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="targetGrossMargin" value="25" step="0.1"
                                    min="10" max="50">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <small class="text-muted">Overall target for all products combined</small>
                        </div>

                        <!-- Volume-Based Strategy -->
                        <div class="mb-3">
                            <label class="font-weight-bold">Volume-Based Pricing Strategy</label>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <label class="small">High Volume (>100/month)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" id="highVolumeMargin" value="15"
                                            step="1" min="5" max="200">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="small">Low Volume (<10 /month)</label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control" id="lowVolumeMargin"
                                                    value="45" step="1" min="10" max="500">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                </div>
                            </div>
                            <small class="text-muted">High volume = competitive pricing, Low volume = higher
                                margins</small>
                        </div>

                        <!-- Price Limit Controls -->
                        <div class="mb-3">
                            <label class="font-weight-bold text-primary">
                                <i class="fas fa-shield-alt mr-1"></i>Price Safety Limits
                            </label>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <label class="small text-danger">Maximum Loss Limit</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" id="maxLossLimit" value="0" step="0.5"
                                            min="0" max="100">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">Min margin: 0% (no loss)</small>
                                </div>
                                <div class="col-6">
                                    <label class="small text-success">Maximum Profit Limit</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" id="maxProfitLimit" value="1000"
                                            step="10" min="50" max="2000">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">Max margin: 1000% cap</small>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" id="enablePriceLimits" checked>
                                    <label class="form-check-label" for="enablePriceLimits">
                                        <small>Enable automatic price validation</small>
                                    </label>
                                </div>
                            </div>
                            <small class="text-muted">Prevents prices from exceeding safety thresholds during
                                updates</small>
                        </div>

                        <!-- Selection -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="selectAllVisible">
                                <label class="form-check-label" for="selectAllVisible">
                                    Select all visible products (<span id="visibleCount">0</span>)
                                </label>
                            </div>
                        </div>

                        <!-- Smart Actions -->
                        <div class="btn-group-vertical w-100">
                            <button class="btn btn-primary mb-2" onclick="calculateSmartMargins()">
                                <i class="fas fa-calculator mr-2"></i>Calculate Smart Margins
                            </button>
                            <button class="btn btn-success mb-2" onclick="applySmartMargins()">
                                <i class="fas fa-magic mr-2"></i>Apply Smart Pricing to Selected
                            </button>
                            <button class="btn btn-info mb-2" onclick="previewGrossMarginImpact()">
                                <i class="fas fa-eye mr-2"></i>Preview Gross Margin Impact
                            </button>
                            <button class="btn btn-warning mb-2" onclick="applyPriceLimitsToAll()">
                                <i class="fas fa-shield-alt mr-2"></i>Apply Limits to All Prices
                            </button>
                        </div>

                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                Selected: <span id="selectedCountDisplay" class="font-weight-bold">0</span> products
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Purchase & Sale Price Table -->
    <div class="card-theme mt-4">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="m-0 font-weight-bold" style="color: var(--primary);">
                        <i class="fas fa-table mr-2"></i>Product Purchase & Sale Price Overview
                    </h5>
                    <small class="text-muted">Compare purchase costs with expected sale prices</small>
                </div>
                <div class="col-auto">
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary" onclick="exportPriceData()">
                            <i class="fas fa-download mr-1"></i>Export
                        </button>
                        <button class="btn btn-outline-success" onclick="bulkUpdatePrices()">
                            <i class="fas fa-edit mr-1"></i>Bulk Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="productPriceTable">
                    <thead class="thead-dark">
                        <tr>
                            <th width="50">#</th>
                            <th>Product Details</th>
                            <th>Category</th>
                            <th>Purchase Price</th>
                            <th>Expected Sale Price</th>
                            <th>Margin</th>
                            <th>Stock</th>
                            <th>Monthly Sales</th>
                            <th>Profit/Loss%</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data['products']) && !empty($data['products'])): ?>
                            <?php $index = 1; ?>
                            <?php foreach ($data['products'] as $product): ?>
                                <?php
                                // Calculate values
                                $purchasePrice = $product->cost ?? 0;
                                $salePrice = $product->price ?? 0;
                                $margin = 0;
                                $profitPerUnit = $salePrice - $purchasePrice;

                                if ($salePrice > 0 && $purchasePrice > 0) {
                                    $margin = (($salePrice - $purchasePrice) / $salePrice) * 100;
                                }

                                $monthlySales = round(($product->total_sold ?? 0) / 3);

                                // Color coding for margins
                                $marginClass = 'text-danger';
                                if ($margin > 25)
                                    $marginClass = 'text-success font-weight-bold';
                                elseif ($margin > 15)
                                    $marginClass = 'text-warning';
                                elseif ($margin > 0)
                                    $marginClass = 'text-info';

                                // Stock status
                                $stockClass = 'badge-danger';
                                if (($product->stock_quantity ?? 0) > 20)
                                    $stockClass = 'badge-success';
                                elseif (($product->stock_quantity ?? 0) > 5)
                                    $stockClass = 'badge-warning';
                                ?>
                                <tr data-product-id="<?= $product->product_id ?? 0 ?>">
                                    <td class="text-center font-weight-bold text-muted"><?= $index++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($product->image_path)): ?>
                                                <img src="<?= URLROOT ?>/<?= $product->image_path ?>" alt="Product Image"
                                                    class="rounded mr-3" style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded mr-3 d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <strong
                                                    class="text-primary"><?= htmlspecialchars($product->name ?? 'Unknown Product') ?></strong>
                                                <br>
                                                <small class="text-muted">SKU:
                                                    <?= htmlspecialchars($product->sku ?? 'N/A') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-light"><?= htmlspecialchars($product->category_name ?? 'Uncategorized') ?></span>
                                    </td>
                                    <td class="text-right">
                                        <strong class="text-danger">₹<?= number_format($purchasePrice, 2) ?></strong>
                                        <br>
                                        <small class="text-muted">Cost</small>
                                    </td>
                                    <td class="text-right">
                                        <div class="input-group input-group-sm" style="width: 120px;">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₹</span>
                                            </div>
                                            <input type="number" class="form-control text-right sale-price-input"
                                                value="<?= number_format($salePrice, 2, '.', '') ?>" step="0.01" min="0"
                                                data-product-id="<?= $product->product_id ?? 0 ?>"
                                                data-cost="<?= $purchasePrice ?>" onchange="updateSalePrice(this)">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="<?= $marginClass ?> margin-display"
                                            data-product-id="<?= $product->product_id ?? 0 ?>">
                                            <?= number_format($margin, 1) ?>%
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            <?php if ($margin > 25): ?>
                                                Excellent
                                            <?php elseif ($margin > 15): ?>
                                                Good
                                            <?php elseif ($margin > 0): ?>
                                                Low
                                            <?php else: ?>
                                                Loss
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $stockClass ?> stock-display">
                                            <?= $product->stock_quantity ?? 0 ?>
                                        </span>
                                        <br>
                                        <small class="text-muted">units</small>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $volumeBadge = 'secondary';
                                        if ($monthlySales > 100)
                                            $volumeBadge = 'success';
                                        elseif ($monthlySales > 50)
                                            $volumeBadge = 'primary';
                                        elseif ($monthlySales > 10)
                                            $volumeBadge = 'info';
                                        elseif ($monthlySales > 0)
                                            $volumeBadge = 'warning';
                                        ?>
                                        <span class="badge badge-<?= $volumeBadge ?>"><?= number_format($monthlySales) ?></span>
                                        <br>
                                        <small class="text-muted">units/month</small>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        // Calculate profit/loss percentage based on margin
                                        $profitLossPercent = $margin;
                                        $profitPerUnit = $salePrice - $purchasePrice;

                                        // Determine if it's profit or loss
                                        $isProfit = $profitLossPercent >= 0;
                                        $displayPercent = abs($profitLossPercent);

                                        // Color coding for profit/loss percentage
                                        $profitLossClass = 'text-success';
                                        $riskLevel = 'Profit';

                                        if (!$isProfit) {
                                            $profitLossClass = 'text-danger font-weight-bold';
                                            $riskLevel = 'Loss';
                                        } elseif ($profitLossPercent > 50) {
                                            $profitLossClass = 'text-success font-weight-bold';
                                            $riskLevel = 'High Profit';
                                        } elseif ($profitLossPercent > 25) {
                                            $profitLossClass = 'text-success';
                                            $riskLevel = 'Good Profit';
                                        } elseif ($profitLossPercent > 15) {
                                            $profitLossClass = 'text-info';
                                            $riskLevel = 'Low Profit';
                                        } elseif ($profitLossPercent > 0) {
                                            $profitLossClass = 'text-warning';
                                            $riskLevel = 'Minimal Profit';
                                        } else {
                                            $profitLossClass = 'text-muted';
                                            $riskLevel = 'Break Even';
                                        }
                                        ?>
                                        <span class="<?= $profitLossClass ?> profit-loss-display"
                                            data-product-id="<?= $product->product_id ?? 0 ?>">
                                            <?= $isProfit ? '+' : '-' ?>         <?= number_format($displayPercent, 1) ?>%
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            <?= $riskLevel ?>
                                        </small>
                                        <br>
                                        <small class="<?= $profitPerUnit >= 0 ? 'text-success' : 'text-danger' ?>">
                                            ₹<?= number_format($profitPerUnit, 2) ?>/unit
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-success"
                                                onclick="savePriceChange(<?= $product->product_id ?? 0 ?>)"
                                                title="Save Changes">
                                                <i class="fas fa-save"></i>
                                            </button>
                                            <button class="btn btn-outline-info"
                                                onclick="viewProductDetails(<?= $product->product_id ?? 0 ?>)"
                                                title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3"></i>
                                    <h5>No products found</h5>
                                    <p>Add some products to start managing prices.</p>
                                    <a href="<?= URLROOT ?>/products/add" class="btn btn-primary">
                                        <i class="fas fa-plus mr-2"></i>Add Product
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<script>
    // Price Management JavaScript
    $(document).ready(function () {
        // Initialize DataTables for the old table (if it exists)
        if ($.fn.DataTable && $('#priceManagementTable').length) {
            $('#priceManagementTable').DataTable({
                pageLength: 25,
                responsive: true,
                order: [[1, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [0, 9] }, // Disable sorting for checkbox and actions columns
                    { searchable: false, targets: [0, 9] }
                ]
            });
        }

        // Initialize DataTables for the new product price table
        if ($.fn.DataTable && $('#productPriceTable').length) {
            $('#productPriceTable').DataTable({
                pageLength: 25,
                responsive: true,
                order: [[1, 'asc']], // Sort by product name
                columnDefs: [
                    { orderable: false, targets: [9] }, // Disable sorting for actions column
                    { searchable: true, targets: [1, 2] }, // Enable search for product and category
                    { type: 'num', targets: [3, 4, 5, 6, 7, 8] } // Numeric sorting for price columns
                ],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        }

        // Auto-save price changes after 2 seconds of inactivity
        let priceChangeTimer;
        $('.price-input').on('input', function () {
            clearTimeout(priceChangeTimer);
            const productId = $(this).data('product-id');
            const originalPrice = $(this).data('original-price');
            const newPrice = $(this).val();

            // Update margin and target price display immediately
            calculateMarginAndTarget(this);

            priceChangeTimer = setTimeout(() => {
                if (parseFloat(newPrice) !== parseFloat(originalPrice)) {
                    autoSavePrice(productId, newPrice);
                }
            }, 2000);
        });

        // Update selected count on page load
        updateSelectedCount();
    });

    // Enhanced Price Management Functions for Margin Management
    function applyFilters() {
        const salesFilter = $('#salesFilter').val();
        const marginFilter = $('#marginFilter').val();
        const categoryFilter = $('#categoryFilter').val();
        const stockFilter = $('#stockFilter').val();

        console.log('Applying margin-focused filters:', { salesFilter, marginFilter, categoryFilter, stockFilter });

        const params = new URLSearchParams(window.location.search);
        if (salesFilter) params.set('sales_filter', salesFilter);
        else params.delete('sales_filter');

        if (marginFilter) params.set('margin_filter', marginFilter);
        else params.delete('margin_filter');

        if (categoryFilter) params.set('category', categoryFilter);
        else params.delete('category');

        if (stockFilter) params.set('stock_filter', stockFilter);
        else params.delete('stock_filter');

        window.location.search = params.toString();
    }

    function clearAllFilters() {
        $('#salesFilter, #marginFilter, #categoryFilter, #stockFilter').val('');
        window.location.href = window.location.pathname;
    }

    function calculateMarginAndTarget(priceInput) {
        const productId = $(priceInput).data('product-id');
        const newPrice = parseFloat($(priceInput).val()) || 0;
        const cost = parseFloat($(priceInput).data('cost')) || 0;

        let margin = 0;
        if (newPrice > 0 && cost > 0) {
            margin = ((newPrice - cost) / newPrice) * 100;
        }

        // Update margin badge
        const marginBadge = $(`.margin-badge[data-product-id="${productId}"]`);
        marginBadge.text(margin.toFixed(1) + '%');

        // Update badge color based on margin
        marginBadge.removeClass('badge-success badge-warning badge-danger badge-dark');
        if (margin > 25) {
            marginBadge.addClass('badge-success');
        } else if (margin > 15) {
            marginBadge.addClass('badge-warning');
        } else if (margin >= 0) {
            marginBadge.addClass('badge-danger');
        } else {
            marginBadge.addClass('badge-dark');
        }

        // Update target price display if target margin is set
        const targetMargin = parseFloat($('#targetMargin').val());
        if (targetMargin > 0 && cost > 0) {
            updateTargetPriceDisplay(productId, cost, targetMargin);
        }

        // Mark as changed for unsaved tracking
        const originalPrice = $(priceInput).data('original-price');
        if (parseFloat(newPrice) !== parseFloat(originalPrice)) {
            unsavedChanges.set(productId, { newPrice: newPrice, originalPrice: parseFloat(originalPrice) });
            $(priceInput).addClass('border-warning');
        } else {
            unsavedChanges.delete(productId);
            $(priceInput).removeClass('border-warning');
        }
        updateSaveAllToolbar();
    }

    function updateTargetPriceDisplay(productId, cost, targetMargin) {
        if (cost <= 0 || targetMargin <= 0) return;

        // Calculate target price: cost / (1 - margin/100)
        const targetPrice = cost / (1 - targetMargin / 100);

        const targetDisplay = $(`.target-price-display[data-product-id="${productId}"]`);
        const marginDisplay = $(`.target-margin-display[data-product-id="${productId}"]`);

        targetDisplay.html(`<strong class="text-info">$${targetPrice.toFixed(2)}</strong>`);
        marginDisplay.text(`(${targetMargin}% margin)`);
    }

    function applyTargetMargin() {
        const targetMargin = parseFloat($('#targetMargin').val());
        if (!targetMargin || targetMargin <= 0) {
            alert('Please enter a valid target margin percentage.');
            return;
        }

        const selectedProducts = $('.product-checkbox:checked');
        if (selectedProducts.length === 0) {
            alert('Please select at least one product.');
            return;
        }

        if (!confirm(`Apply ${targetMargin}% margin to ${selectedProducts.length} selected products?`)) {
            return;
        }

        console.log('Applying target margin of', targetMargin, '% to', selectedProducts.length, 'products');

        selectedProducts.each(function () {
            const productId = $(this).val();
            const priceInput = $(`.price-input[data-product-id="${productId}"]`);
            const cost = parseFloat(priceInput.data('cost')) || 0;

            if (cost > 0) {
                const targetPrice = cost / (1 - targetMargin / 100);
                priceInput.val(targetPrice.toFixed(2));
                calculateMarginAndTarget(priceInput[0]);
            }
        });

        showToast(`Applied ${targetMargin}% margin to ${selectedProducts.length} products`, 'success');
    }

    function quickMarginAdjust(margin) {
        $('#targetMargin').val(margin);
        applyTargetMargin();
    }

    function applyTargetPrice(productId) {
        const targetMargin = parseFloat($('#targetMargin').val());
        if (!targetMargin || targetMargin <= 0) {
            alert('Please set a target margin first.');
            return;
        }

        const priceInput = $(`.price-input[data-product-id="${productId}"]`);
        const cost = parseFloat(priceInput.data('cost')) || 0;

        if (cost <= 0) {
            alert('Product cost is required to calculate target price.');
            return;
        }

        const targetPrice = cost / (1 - targetMargin / 100);
        priceInput.val(targetPrice.toFixed(2));
        calculateMarginAndTarget(priceInput[0]);

        showToast('Target price applied', 'success');
    }

    function optimizeMargins() {
        const selectedProducts = $('.product-checkbox:checked');
        if (selectedProducts.length === 0) {
            alert('Please select products to optimize.');
            return;
        }

        if (!confirm('Apply smart margin optimization to selected products?\n\n• Top sellers: 20% margin\n• Medium sellers: 25% margin\n• Slow movers: 30% margin')) {
            return;
        }

        console.log('Optimizing margins for', selectedProducts.length, 'products');

        selectedProducts.each(function () {
            const productId = $(this).val();
            const row = $(this).closest('tr');
            const salesRankText = row.find('.badge:first').text();
            const priceInput = $(`.price-input[data-product-id="${productId}"]`);
            const cost = parseFloat(priceInput.data('cost')) || 0;

            if (cost > 0) {
                let optimalMargin = 25; // default

                if (salesRankText.includes('Top') || salesRankText.includes('#')) {
                    const rank = parseInt(salesRankText.replace(/\D/g, ''));
                    if (rank <= 10) optimalMargin = 20; // Top sellers, lower margin for competitiveness
                    else if (rank <= 50) optimalMargin = 22;
                    else optimalMargin = 30; // Slow movers, higher margin
                } else if (salesRankText.includes('No Sales')) {
                    optimalMargin = 35; // No sales, try higher margin
                }

                const targetPrice = cost / (1 - optimalMargin / 100);
                priceInput.val(targetPrice.toFixed(2));
                calculateMarginAndTarget(priceInput[0]);
            }
        });

        showToast('Smart margin optimization applied', 'success');
    }

    // ===== SMART MARGIN CALCULATION SYSTEM =====

    function calculateSmartMargins() {
        console.log('Calculating smart margins for all visible products...');

        const targetGrossMargin = parseFloat($('#targetGrossMargin').val()) || 25;
        const highVolumeMargin = parseFloat($('#highVolumeMargin').val()) || 15;
        const lowVolumeMargin = parseFloat($('#lowVolumeMargin').val()) || 45;

        $('.product-checkbox:visible').each(function () {
            const productId = $(this).val();
            const row = $(this).closest('tr');
            const cost = parseFloat($(`.price-input[data-product-id="${productId}"]`).data('cost')) || 0;

            if (cost > 0) {
                // Get monthly sales from the Monthly Sales column
                const monthlySalesText = row.find('td:nth-child(3) strong').text().replace(/,/g, '');
                const monthlySales = parseInt(monthlySalesText) || 0;

                // Calculate smart margin based on volume
                let smartMargin;
                if (monthlySales > 100) {
                    smartMargin = highVolumeMargin; // High volume, competitive pricing
                } else if (monthlySales > 50) {
                    smartMargin = (highVolumeMargin + lowVolumeMargin) / 2; // Medium volume
                } else if (monthlySales > 10) {
                    smartMargin = lowVolumeMargin * 0.8; // Low volume
                } else if (monthlySales > 0) {
                    smartMargin = lowVolumeMargin; // Very low volume
                } else {
                    smartMargin = lowVolumeMargin * 1.2; // No sales, try higher margin
                }

                // Cap extreme margins
                smartMargin = Math.min(Math.max(smartMargin, 5), 500);

                // Apply price limits if enabled
                const enableLimits = $('#enablePriceLimits').is(':checked');
                const maxLossLimit = parseFloat($('#maxLossLimit').val()) || 0;
                const maxProfitLimit = parseFloat($('#maxProfitLimit').val()) || 1000;

                if (enableLimits) {
                    // Ensure smart margin doesn't exceed limits
                    const minMargin = -maxLossLimit; // Negative margin means loss
                    const maxMargin = maxProfitLimit;

                    smartMargin = Math.min(Math.max(smartMargin, minMargin), maxMargin);
                }

                // Calculate smart price: cost / (1 - margin/100)
                const smartPrice = cost / (1 - smartMargin / 100);

                // Update displays
                updateSmartPriceDisplay(productId, smartPrice, smartMargin, monthlySales, cost);
            }
        });

        showToast('Smart margins calculated for all visible products', 'success');
    }

    function updateSmartPriceDisplay(productId, smartPrice, smartMargin, monthlySales, cost) {
        // Update smart price display
        const smartPriceDisplay = $(`.smart-price-display[data-product-id="${productId}"]`);
        smartPriceDisplay.html(`<strong class="text-primary">₹${smartPrice.toFixed(2)}</strong>`);

        // Update smart margin display
        const smartMarginDisplay = $(`.smart-margin-display[data-product-id="${productId}"]`);
        const marginBadgeClass = smartMargin > 30 ? 'success' : (smartMargin > 15 ? 'warning' : 'info');
        smartMarginDisplay.html(`<span class="badge badge-${marginBadgeClass}">${smartMargin.toFixed(1)}%</span>`);

        // Calculate and update profit impact
        const smartProfit = monthlySales * (smartPrice - cost);
        const profitDisplay = $(`.profit-impact-display[data-product-id="${productId}"] .smart-profit-value`);
        const profitClass = smartProfit > 500 ? 'text-success' : (smartProfit > 100 ? 'text-info' : 'text-warning');
        profitDisplay.html(`<span class="${profitClass}">₹${smartProfit.toFixed(0)}</span>`);

        // Store smart data for later use
        smartPriceDisplay.data('smart-price', smartPrice);
        smartMarginDisplay.data('smart-margin', smartMargin);
    }

    function applySmartMargins() {
        const selectedProducts = $('.product-checkbox:checked');
        if (selectedProducts.length === 0) {
            alert('Please select products to apply smart pricing.');
            return;
        }

        if (!confirm(`Apply smart pricing to ${selectedProducts.length} selected products?`)) {
            return;
        }

        console.log('Applying smart pricing to', selectedProducts.length, 'products');

        selectedProducts.each(function () {
            const productId = $(this).val();
            const smartPriceDisplay = $(`.smart-price-display[data-product-id="${productId}"]`);
            const smartPrice = smartPriceDisplay.data('smart-price');

            if (smartPrice) {
                const priceInput = $(`.sale-price-input[data-product-id="${productId}"]`);
                priceInput.val(smartPrice.toFixed(2));

                // Trigger price validation and updates
                updateSalePrice(priceInput[0]);
            }
        });

        showToast(`Smart pricing applied to ${selectedProducts.length} products`, 'success');
    }

    function applyPriceLimitsToAll() {
        if (!$('#enablePriceLimits').is(':checked')) {
            alert('Please enable price limits first before applying them.');
            return;
        }

        const maxLossLimit = parseFloat($('#maxLossLimit').val()) || 0;
        const maxProfitLimit = parseFloat($('#maxProfitLimit').val()) || 1000;

        if (!confirm(`Apply price limits to all products?\n\nMax Loss: ${maxLossLimit}%\nMax Profit: ${maxProfitLimit}%\n\nThis will adjust any prices that exceed these limits.`)) {
            return;
        }

        console.log('Applying price limits to all products...');

        let adjustedCount = 0;
        const allPriceInputs = $('.sale-price-input');

        allPriceInputs.each(function () {
            const originalValue = parseFloat($(this).val()) || 0;
            updateSalePrice(this);
            const newValue = parseFloat($(this).val()) || 0;

            if (Math.abs(originalValue - newValue) > 0.01) {
                adjustedCount++;
            }
        });

        showToast(`Price limits applied. ${adjustedCount} prices were adjusted.`, 'success');
    }

    function applySmartPrice(productId) {
        const smartPriceDisplay = $(`.smart-price-display[data-product-id="${productId}"]`);
        const smartPrice = smartPriceDisplay.data('smart-price');

        if (!smartPrice) {
            alert('Please calculate smart margins first.');
            return;
        }

        const priceInput = $(`.price-input[data-product-id="${productId}"]`);
        priceInput.val(smartPrice.toFixed(2));
        calculateMarginAndTarget(priceInput[0]);

        showToast('Smart price applied', 'success');
    }

    function previewGrossMarginImpact() {
        console.log('Calculating gross margin impact...');

        let totalCurrentRevenue = 0;
        let totalCurrentCost = 0;
        let totalSmartRevenue = 0;
        let totalSmartCost = 0;

        $('.product-checkbox:visible').each(function () {
            const productId = $(this).val();
            const row = $(this).closest('tr');
            const cost = parseFloat($(`.price-input[data-product-id="${productId}"]`).data('cost')) || 0;
            const currentPrice = parseFloat($(`.price-input[data-product-id="${productId}"]`).val()) || 0;
            const monthlySalesText = row.find('td:nth-child(3) strong').text().replace(/,/g, '');
            const monthlySales = parseInt(monthlySalesText) || 0;

            const smartPriceDisplay = $(`.smart-price-display[data-product-id="${productId}"]`);
            const smartPrice = smartPriceDisplay.data('smart-price') || currentPrice;

            // Calculate monthly totals
            totalCurrentRevenue += monthlySales * currentPrice;
            totalCurrentCost += monthlySales * cost;
            totalSmartRevenue += monthlySales * smartPrice;
            totalSmartCost += monthlySales * cost;
        });

        const currentGrossMargin = totalCurrentRevenue > 0 ? ((totalCurrentRevenue - totalCurrentCost) / totalCurrentRevenue * 100) : 0;
        const smartGrossMargin = totalSmartRevenue > 0 ? ((totalSmartRevenue - totalSmartCost) / totalSmartRevenue * 100) : 0;
        const profitIncrease = (totalSmartRevenue - totalSmartCost) - (totalCurrentRevenue - totalCurrentCost);

        const previewMessage = `
Gross Margin Impact Analysis:

Current Gross Margin: ${currentGrossMargin.toFixed(1)}%
Smart Pricing Gross Margin: ${smartGrossMargin.toFixed(1)}%

Monthly Profit Change: ₹${profitIncrease.toFixed(0)}
Monthly Revenue Change: ₹${(totalSmartRevenue - totalCurrentRevenue).toFixed(0)}

Would you like to proceed with the smart pricing strategy?`;

        alert(previewMessage);
    }

    // Update selected count and visible count
    function updateSelectedCount() {
        const selectedCount = $('.product-checkbox:checked').length;
        const visibleCount = $('.product-checkbox:visible').length;

        $('#selectedCount').text(selectedCount);
        $('#selectedCountDisplay').text(selectedCount);
        $('#visibleCount').text(visibleCount);
    }

    // Enhanced toggle select all for visible products
    function toggleSelectAll() {
        const isChecked = $('#masterSelect').is(':checked') || $('#selectAllVisible').is(':checked');
        $('.product-checkbox:visible').prop('checked', isChecked);
        updateSelectedCount();
    }

    // Update target margin when input changes
    $('#targetMargin').on('input', function () {
        const targetMargin = parseFloat($(this).val());
        if (targetMargin > 0) {
            $('.price-input').each(function () {
                const productId = $(this).data('product-id');
                const cost = parseFloat($(this).data('cost')) || 0;
                if (cost > 0) {
                    updateTargetPriceDisplay(productId, cost, targetMargin);
                }
            });
        } else {
            $('.target-price-display').html('<span class="text-muted">-</span>');
            $('.target-margin-display').text('');
        }
    });

    // Initialize on page load
    $(document).ready(function () {
        updateSelectedCount();

        // Update counts when checkboxes change
        $(document).on('change', '.product-checkbox', updateSelectedCount);
        $(document).on('change', '#selectAllVisible', toggleSelectAll);
    });

    function autoSavePrice(productId, newPrice) {
        console.log('Auto-saving price for product ID:', productId);

        $.ajax({
            url: '<?= URLROOT ?>/admin/updateProductPrice',
            method: 'POST',
            data: {
                product_id: productId,
                new_price: newPrice,
                auto_save: true
            },
            success: function (response) {
                if (response.success) {
                    // Update original price data attribute
                    $(`.price-input[data-product-id="${productId}"]`).data('original-price', newPrice);
                    $(`.price-input[data-product-id="${productId}"]`).removeClass('border-warning');

                    // Show subtle success indicator
                    showToast('Price auto-saved successfully', 'success');
                }
            },
            error: function () {
                console.error('Auto-save failed for product ID:', productId);
                showToast('Auto-save failed', 'error');
            }
        });
    }

    function updatePrice(productId) {
        const newPrice = $(`.price-input[data-product-id="${productId}"]`).val();

        if (!newPrice || parseFloat(newPrice) < 0) {
            alert('Please enter a valid price.');
            return;
        }

        console.log('Initiating price update for product ID:', productId);

        $.ajax({
            url: '<?= URLROOT ?>/admin/updateProductPrice',
            method: 'POST',
            data: {
                product_id: productId,
                new_price: newPrice
            },
            success: function (response) {
                console.log('Update price response:', response);

                if (response.success) {
                    alert('Price updated successfully!');
                    // Update original price data attribute
                    $(`.price-input[data-product-id="${productId}"]`).data('original-price', newPrice);
                    $(`.price-input[data-product-id="${productId}"]`).removeClass('border-warning');

                    // Update last updated date
                    const row = $(`.price-input[data-product-id="${productId}"]`).closest('tr');
                    row.find('td:nth-child(9) small').text(new Date().toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                    }));
                } else {
                    alert('Failed to update price: ' + (response.message || 'Unknown error'));
                }
            },
            error: function (xhr, status, error) {
                console.error('Price update error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    readyState: xhr.readyState,
                    statusCode: xhr.status
                });

                let errorMsg = 'Price update failed! Please try again.';
                if (xhr.responseText) {
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        errorMsg = 'Error: ' + (errorResponse.message || 'Invalid response format. Check console for details.');
                    } catch (e) {
                        errorMsg = 'Error: Invalid response format. Check console for details.';
                    }
                }
                alert(errorMsg);
            }
        });
    }

    function viewPriceHistory(productId) {
        console.log('Loading price history for product ID:', productId);

        $('#priceHistoryModal').modal('show');

        $.ajax({
            url: '<?= URLROOT ?>/admin/getPriceHistory',
            method: 'GET',
            data: { product_id: productId },
            success: function (response) {
                console.log('Price history response:', response);

                if (response.success && response.data.length > 0) {
                    let historyHtml = '<div class="table-responsive"><table class="table table-sm">';
                    historyHtml += '<thead><tr><th>Date</th><th>Price</th><th>Changed By</th><th>Reason</th></tr></thead><tbody>';

                    response.data.forEach(function (entry) {
                        historyHtml += `<tr>
                        <td>${new Date(entry.date).toLocaleDateString()}</td>
                        <td>$${parseFloat(entry.price).toFixed(2)}</td>
                        <td>${entry.user || 'System'}</td>
                        <td>${entry.reason || 'Manual update'}</td>
                    </tr>`;
                    });

                    historyHtml += '</tbody></table></div>';
                    $('#priceHistoryContent').html(historyHtml);
                } else {
                    $('#priceHistoryContent').html(`
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-history fa-2x mb-2"></i>
                        <p>No price history found for this product.</p>
                    </div>
                `);
                }
            },
            error: function (xhr, status, error) {
                console.error('Price history error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    readyState: xhr.readyState,
                    statusCode: xhr.status
                });

                let errorMsg = 'Failed to load price history. Please try again.';
                if (xhr.responseText) {
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        errorMsg = 'Error: ' + (errorResponse.message || 'Invalid response format. Check console for details.');
                    } catch (e) {
                        errorMsg = 'Error: Invalid response format. Check console for details.';
                    }
                }

                $('#priceHistoryContent').html(`
                    <div class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>${errorMsg}</p>
                    </div>
                `);
            }
        });
    }

    function duplicatePrice(productId) {
        const price = $(`.price-input[data-product-id="${productId}"]`).val();
        const selectedProducts = $('.product-checkbox:checked');

        if (selectedProducts.length === 0) {
            alert('Please select products to copy the price to.');
            return;
        }

        if (confirm(`Copy price $${price} to ${selectedProducts.length} selected products?`)) {
            console.log('Initiating price duplication for product ID:', productId);

            selectedProducts.each(function () {
                const targetProductId = $(this).val();
                if (targetProductId != productId) {
                    $(`.price-input[data-product-id="${targetProductId}"]`).val(price);
                    calculateMargin($(`.price-input[data-product-id="${targetProductId}"]`)[0]);
                }
            });

            alert('Prices copied! Remember to save the changes.');
        }
    }

    function bulkPriceUpdate() {
        const selectedProducts = [];
        $('.product-checkbox:checked').each(function () {
            selectedProducts.push($(this).val());
        });

        if (selectedProducts.length === 0) {
            alert('Please select at least one product to update.');
            return;
        }

        $('#bulkUpdateModal').modal('show');
    }

    function processBulkUpdate() {
        const selectedProducts = [];
        $('.product-checkbox:checked').each(function () {
            selectedProducts.push($(this).val());
        });

        const updateType = $('#updateType').val();
        const updateValue = $('#updateValue').val();
        const roundPrices = $('#roundPrices').is(':checked');

        if (!updateType || !updateValue) {
            alert('Please fill in all required fields.');
            return;
        }

        console.log('Initiating bulk price update...');

        $.ajax({
            url: '<?= URLROOT ?>/admin/bulkPriceUpdate',
            method: 'POST',
            data: {
                products: selectedProducts,
                update_type: updateType,
                update_value: updateValue,
                round_prices: roundPrices
            },
            success: function (response) {
                console.log('Bulk update response:', response);

                if (response.success) {
                    alert('Bulk price update successful!');
                    $('#bulkUpdateModal').modal('hide');
                    location.reload();
                } else {
                    alert('Failed to update prices: ' + (response.message || 'Unknown error'));
                }
            },
            error: function (xhr, status, error) {
                console.error('Bulk update error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    readyState: xhr.readyState,
                    statusCode: xhr.status
                });

                let errorMsg = 'Bulk price update failed! Please try again.';
                if (xhr.responseText) {
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        errorMsg = 'Error: ' + (errorResponse.message || 'Invalid response format. Check console for details.');
                    } catch (e) {
                        errorMsg = 'Error: Invalid response format. Check console for details.';
                    }
                }
                alert(errorMsg);
            }
        });
    }

    function bulkPriceIncrease() {
        $('#updateType').val('percentage_increase');
        $('#updateValue').val('10');
        bulkPriceUpdate();
    }

    function bulkPriceDecrease() {
        $('#updateType').val('percentage_decrease');
        $('#updateValue').val('5');
        bulkPriceUpdate();
    }

    function bulkMarginUpdate() {
        $('#updateType').val('set_margin');
        $('#updateValue').val('25');
        bulkPriceUpdate();
    }

    function exportPrices() {
        console.log('Initiating price export...');

        const selectedProducts = [];
        $('.product-checkbox:checked').each(function () {
            selectedProducts.push($(this).val());
        });

        let url = '<?= URLROOT ?>/admin/exportPrices';
        if (selectedProducts.length > 0) {
            url += '?products=' + selectedProducts.join(',');
        }

        window.location.href = url;
    }

    function importPrices() {
        console.log('Initiating price import...');

        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.csv,.xlsx';
        input.onchange = function (e) {
            const file = e.target.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('price_file', file);

                $.ajax({
                    url: '<?= URLROOT ?>/admin/importPrices',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            alert('Price import successful!');
                            location.reload();
                        } else {
                            alert('Import failed: ' + (response.message || 'Unknown error'));
                        }
                    },
                    error: function () {
                        alert('Price import failed! Please try again.');
                    }
                });
            }
        };
        input.click();
    }

    // New functions for Product Price Table
    function updateSalePrice(input) {
        const productId = $(input).data('product-id');
        const cost = parseFloat($(input).data('cost')) || 0;
        let salePrice = parseFloat($(input).val()) || 0;

        // Get price limit settings
        const enableLimits = $('#enablePriceLimits').is(':checked');
        const maxLossLimit = parseFloat($('#maxLossLimit').val()) || 0;
        const maxProfitLimit = parseFloat($('#maxProfitLimit').val()) || 1000;

        // Apply price limits if enabled
        if (enableLimits && cost > 0) {
            // Calculate minimum price (to prevent exceeding max loss)
            const minAllowedPrice = cost * (1 - (maxLossLimit / 100));

            // Calculate maximum price (to prevent exceeding max profit)
            // For margin calculation: margin = (salePrice - cost) / salePrice * 100
            // Rearranging: salePrice = cost / (1 - (margin/100))
            const maxAllowedPrice = cost / (1 - (maxProfitLimit / 100));

            let priceAdjusted = false;
            let adjustmentMessage = '';

            if (salePrice < minAllowedPrice) {
                salePrice = minAllowedPrice;
                priceAdjusted = true;
                adjustmentMessage = `Price adjusted to minimum allowed (${maxLossLimit}% max loss limit)`;
            } else if (salePrice > maxAllowedPrice) {
                salePrice = maxAllowedPrice;
                priceAdjusted = true;
                adjustmentMessage = `Price adjusted to maximum allowed (${maxProfitLimit}% max profit limit)`;
            }

            if (priceAdjusted) {
                $(input).val(salePrice.toFixed(2));
                $(input).addClass('border-info');

                // Show adjustment notification
                showPriceAdjustmentNotification(adjustmentMessage);

                setTimeout(() => {
                    $(input).removeClass('border-info');
                }, 3000);
            }
        }

        // Calculate new margin
        let margin = 0;
        if (salePrice > 0 && cost > 0) {
            margin = ((salePrice - cost) / salePrice) * 100;
        }

        // Calculate profit per unit
        const profitPerUnit = salePrice - cost;

        // Calculate maximum loss percentage
        let maxLoss = 0;
        let riskLevel = 'No Risk';
        let riskColor = 'text-success';

        if (cost > 0 && salePrice > 0) {
            if (salePrice < cost) {
                maxLoss = ((cost - salePrice) / cost) * 100;
                if (maxLoss > 20) {
                    riskLevel = 'High Risk';
                    riskColor = 'text-danger font-weight-bold';
                } else if (maxLoss > 10) {
                    riskLevel = 'Medium Risk';
                    riskColor = 'text-warning';
                } else {
                    riskLevel = 'Low Risk';
                    riskColor = 'text-info';
                }
            }
        }

        // Update margin display
        const marginDisplay = $(`.margin-display[data-product-id="${productId}"]`);
        marginDisplay.text(margin.toFixed(1) + '%');

        // Update margin color with limit validation
        marginDisplay.removeClass('text-success text-warning text-info text-danger font-weight-bold border border-primary');
        if (enableLimits && (margin < -maxLossLimit || margin > maxProfitLimit)) {
            marginDisplay.addClass('text-danger font-weight-bold border border-danger');
        } else if (margin > 25) {
            marginDisplay.addClass('text-success font-weight-bold');
        } else if (margin > 15) {
            marginDisplay.addClass('text-warning');
        } else if (margin > 0) {
            marginDisplay.addClass('text-info');
        } else {
            marginDisplay.addClass('text-danger');
        }

        // Update profit/loss display (replaces both profit and max loss displays)
        const profitLossDisplay = $(`.profit-loss-display[data-product-id="${productId}"]`);

        // Determine profit/loss values
        const isProfit = margin >= 0;
        const displayPercent = Math.abs(margin);
        const sign = isProfit ? '+' : '-';

        // Color coding for profit/loss
        let profitLossClass = 'text-success';
        let riskLevel = 'Profit';

        if (!isProfit) {
            profitLossClass = 'text-danger font-weight-bold';
            riskLevel = 'Loss';
        } else if (margin > 50) {
            profitLossClass = 'text-success font-weight-bold';
            riskLevel = 'High Profit';
        } else if (margin > 25) {
            profitLossClass = 'text-success';
            riskLevel = 'Good Profit';
        } else if (margin > 15) {
            profitLossClass = 'text-info';
            riskLevel = 'Low Profit';
        } else if (margin > 0) {
            profitLossClass = 'text-warning';
            riskLevel = 'Minimal Profit';
        } else {
            profitLossClass = 'text-muted';
            riskLevel = 'Break Even';
        }

        // Update the display
        profitLossDisplay.removeClass('text-success text-warning text-info text-danger text-muted font-weight-bold');
        profitLossDisplay.addClass(profitLossClass);
        profitLossDisplay.html(`${sign}${displayPercent.toFixed(1)}%`);

        // Update the small text elements
        const smallElements = profitLossDisplay.parent().find('small');
        if (smallElements.length >= 2) {
            $(smallElements[0]).text(riskLevel).removeClass('text-success text-danger text-muted').addClass(profitLossClass.replace('font-weight-bold', ''));
            $(smallElements[1]).text(`₹${profitPerUnit.toFixed(2)}/unit`).removeClass('text-success text-danger').addClass(profitPerUnit >= 0 ? 'text-success' : 'text-danger');
        }

        // Mark input as changed
        $(input).addClass('border-warning');
    }

    function savePriceChange(productId) {
        const priceInput = $(`.sale-price-input[data-product-id="${productId}"]`);
        const newPrice = parseFloat(priceInput.val()) || 0;

        if (newPrice <= 0) {
            alert('Please enter a valid sale price');
            return;
        }

        console.log('Initiating price update for product ID:', productId);

        $.ajax({
            url: '<?= URLROOT ?>/admin/updateProductPrice',
            method: 'POST',
            data: {
                product_id: productId,
                price: newPrice
            },
            success: function (response) {
                if (response.success) {
                    alert('Price updated successfully!');
                    priceInput.removeClass('border-warning');
                    priceInput.addClass('border-success');

                    setTimeout(() => {
                        priceInput.removeClass('border-success');
                    }, 2000);
                } else {
                    alert('Failed to update price: ' + (response.message || 'Unknown error'));
                }
            },
            error: function () {
                alert('Error updating price. Please try again.');
            }
        });
    }

    function showPriceAdjustmentNotification(message) {
        // Remove any existing notifications
        $('.price-adjustment-notification').remove();

        // Create notification element
        const notification = $(`
            <div class="price-adjustment-notification alert alert-info alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 1050; max-width: 350px;">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Price Adjusted:</strong><br>
                <small>${message}</small>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `);

        // Add to body
        $('body').append(notification);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.alert('close');
        }, 5000);
    }

    function viewProductDetails(productId) {
        window.open('<?= URLROOT ?>/products/view/' + productId, '_blank');
    }

    function exportPriceData() {
        console.log('Initiating price data export...');
        window.location.href = '<?= URLROOT ?>/admin/exportProductPrices';
    }

    function bulkUpdatePrices() {
        const marginPercentage = prompt('Enter margin percentage for all products (e.g., 25 for 25%):');

        if (marginPercentage === null) return; // User cancelled

        const margin = parseFloat(marginPercentage);
        if (isNaN(margin) || margin < 0) {
            alert('Please enter a valid margin percentage');
            return;
        }

        if (!confirm(`Are you sure you want to set ${margin}% margin for all products?`)) {
            return;
        }

        console.log('Initiating bulk price update with margin:', margin);

        $.ajax({
            url: '<?= URLROOT ?>/admin/bulkUpdateMargins',
            method: 'POST',
            data: {
                margin_percentage: margin
            },
            success: function (response) {
                if (response.success) {
                    alert('Bulk price update successful!');
                    location.reload();
                } else {
                    alert('Bulk update failed: ' + (response.message || 'Unknown error'));
                }
            },
            error: function () {
                alert('Bulk update failed! Please try again.');
            }
        });
    }

    function showToast(message, type = 'info') {
        // Simple toast notification (you can replace with a proper toast library)
        const toast = $(`
        <div class="alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `);

        $('body').append(toast);

        setTimeout(() => {
            toast.alert('close');
        }, 3000);
    }
</script>

<style>
    /* Price Limit Enhancement Styles */
    .price-adjustment-notification {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-left: 4px solid #17a2b8;
    }

    .sale-price-input.border-info {
        border-color: #17a2b8 !important;
        box-shadow: 0 0 5px rgba(23, 162, 184, 0.3);
    }

    .margin-display.border-danger {
        padding: 2px 4px;
        border-radius: 3px;
        animation: pulse-danger 2s infinite;
    }

    @keyframes pulse-danger {

        0%,
        100% {
            background-color: transparent;
        }

        50% {
            background-color: rgba(220, 53, 69, 0.1);
        }
    }

    #maxLossLimit,
    #maxProfitLimit {
        font-weight: 600;
    }

    .btn-warning:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    /* Price limit controls styling */
    .input-group-sm .form-control {
        border-radius: 0.2rem;
    }

    .text-primary.font-weight-bold {
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }
</style>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>