<!-- Unified CSS -->
<link rel="stylesheet" href="<?= URLROOT ?>/css/app-unified.css">

<?php
$pageTitle = 'Price Management';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<!-- Load Streamlined Theme System --><div class="container-fluid page-top-area mb-4">
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
                    <div class="text-primary mb-3">
                        <i class="fas fa-clock fa-3x"></i>
                    </div>
                    <h2 class="mb-2 font-weight-bold text-primary">
                        <?= $data['stats']['recent_updates'] ?? 0 ?>
                    </h2>
                    <p class="text-muted mb-0 font-weight-600">Recent Updates</p>
                    <small class="text-muted">Last 7 days</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Bulk Actions -->
    <div class="filter-section">
        <div class="row">
            <div class="col-md-8">
                <h5 class="mb-3">
                    <i class="fas fa-filter mr-2"></i>Filter Products
                </h5>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="categoryFilter">Category</label>
                        <select class="form-theme" id="categoryFilter">
                            <option value="">All Categories</option>
                            <option value="tools">Tools</option>
                            <option value="hardware">Hardware</option>
                            <option value="electrical">Electrical</option>
                            <option value="plumbing">Plumbing</option>
                            <option value="paint">Paint & Supplies</option>
                            <option value="lumber">Lumber</option>
                            <option value="garden">Garden & Outdoor</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="priceRange">Price Range</label>
                        <select class="form-theme" id="priceRange">
                            <option value="">All Prices</option>
                            <option value="0-10">$0 - $10</option>
                            <option value="10-50">$10 - $50</option>
                            <option value="50-100">$50 - $100</option>
                            <option value="100-500">$100 - $500</option>
                            <option value="500+">$500+</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="stockStatus">Stock Status</label>
                        <select class="form-theme" id="stockStatus">
                            <option value="">All Items</option>
                            <option value="in-stock">In Stock (>10)</option>
                            <option value="low-stock">Low Stock (1-10)</option>
                            <option value="out-of-stock">Out of Stock (0)</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="marginFilter">Margin Range</label>
                        <select class="form-theme" id="marginFilter">
                            <option value="">All Margins</option>
                            <option value="low">Low (< 15%)</option>
                            <option value="medium">Medium (15-30%)</option>
                            <option value="high">High (> 30%)</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn-theme" onclick="filterPrices()">
                    <i class="fas fa-search mr-2"></i>Apply Filters
                </button>
                <button class="btn btn-outline-secondary ml-2" onclick="clearFilters()">
                    <i class="fas fa-times mr-2"></i>Clear Filters
                </button>
            </div>

            <div class="col-md-4">
                <div class="bulk-actions">
                    <h5 class="mb-3">
                        <i class="fas fa-tools mr-2"></i>Bulk Actions
                    </h5>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="selectAll" onchange="toggleSelectAll()">
                        <label class="form-check-label" for="selectAll">
                            Select All Products
                        </label>
                    </div>
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary" onclick="bulkPriceIncrease()">
                            <i class="fas fa-arrow-up mr-1"></i>+%
                        </button>
                        <button class="btn btn-outline-danger" onclick="bulkPriceDecrease()">
                            <i class="fas fa-arrow-down mr-1"></i>-%
                        </button>
                        <button class="btn btn-outline-success" onclick="bulkMarginUpdate()">
                            <i class="fas fa-percentage mr-1"></i>Margin
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Price Management Table -->
    <div class="table-theme">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="m-0 font-weight-bold" style="color: var(--primary);">
                        <i class="fas fa-table mr-2"></i>Product Pricing Table
                    </h5>
                </div>
                <div class="col-auto">
                    <span class="badge badge-info">
                        <span id="selectedCount">0</span> selected
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="priceManagementTable">
                    <thead>
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="masterSelect" onchange="toggleSelectAll()">
                            </th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Current Price</th>
                            <th>Cost</th>
                            <th>Margin</th>
                            <th>Stock</th>
                            <th>Last Updated</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data['products']) && !empty($data['products'])): ?>
                            <?php foreach ($data['products'] as $product): ?>
                                <tr data-product-id="<?= $product->product_id ?? 0 ?>">
                                    <td>
                                        <input type="checkbox" class="product-checkbox" value="<?= $product->product_id ?? 0 ?>"
                                            onchange="updateSelectedCount()">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($product->image_path)): ?>
                                                <img src="<?= URLROOT ?>/<?= $product->image_path ?>" alt="Product Image"
                                                    class="rounded mr-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded mr-2 d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= htmlspecialchars($product->name ?? 'Unknown Product') ?></strong>
                                                <br>
                                                <small
                                                    class="text-muted"><?= htmlspecialchars(substr($product->description ?? '', 0, 50)) ?><?= strlen($product->description ?? '') > 50 ? '...' : '' ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code><?= htmlspecialchars($product->sku ?? 'N/A') ?></code>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            <?= ucfirst($product->category ?? 'Uncategorized') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm price-input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" class="form-control price-input"
                                                value="<?= number_format($product->price ?? 0, 2, '.', '') ?>" step="0.01"
                                                min="0" data-product-id="<?= $product->product_id ?? 0 ?>"
                                                data-original-price="<?= $product->price ?? 0 ?>"
                                                onchange="calculateMargin(this)">
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">$<?= number_format($product->cost ?? 0, 2) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $margin = 0;
                                        if (($product->price ?? 0) > 0 && ($product->cost ?? 0) > 0) {
                                            $margin = (($product->price - $product->cost) / $product->price) * 100;
                                        }
                                        ?>
                                        <span
                                            class="badge margin-badge badge-<?= $margin > 30 ? 'success' : ($margin > 15 ? 'warning' : 'danger') ?>"
                                            data-product-id="<?= $product->product_id ?? 0 ?>">
                                            <?= number_format($margin, 1) ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge stock-badge badge-<?= ($product->stock_quantity ?? 0) > 10 ? 'success' : (($product->stock_quantity ?? 0) > 0 ? 'warning' : 'danger') ?>">
                                            <?= $product->stock_quantity ?? 0 ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= isset($product->price_updated_at) ? date('M j, Y', strtotime($product->price_updated_at)) : 'Never' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-success"
                                                onclick="updatePrice(<?= $product->product_id ?? 0 ?>)" title="Save Price">
                                                <i class="fas fa-save"></i>
                                            </button>
                                            <button class="btn btn-outline-info"
                                                onclick="viewPriceHistory(<?= $product->product_id ?? 0 ?>)"
                                                title="Price History">
                                                <i class="fas fa-history"></i>
                                            </button>
                                            <button class="btn btn-outline-warning"
                                                onclick="duplicatePrice(<?= $product->product_id ?? 0 ?>)" title="Copy Price">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="fas fa-dollar-sign fa-3x mb-3"></i>
                                    <h5>No products found for price management</h5>
                                    <p>Add some products to start managing prices.</p>
                                    <a href="<?= URLROOT ?>/products/add" class="btn-theme btn-primary-theme">
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

<!-- Price History Modal -->
<div class="modal fade" id="priceHistoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-history mr-2"></i>Price History
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="priceHistoryContent">
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Loading price history...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i>Bulk Price Update
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkUpdateForm">
                    <div class="form-group">
                        <label>Update Type</label>
                        <select class="form-theme" id="updateType" required>
                            <option value="">Select update type</option>
                            <option value="percentage_increase">Percentage Increase</option>
                            <option value="percentage_decrease">Percentage Decrease</option>
                            <option value="fixed_increase">Fixed Amount Increase</option>
                            <option value="fixed_decrease">Fixed Amount Decrease</option>
                            <option value="set_margin">Set Target Margin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Value</label>
                        <input type="number" class="form-theme" id="updateValue" step="0.01" min="0" required>
                        <small class="form-text text-muted">
                            Enter percentage (e.g., 10 for 10%) or dollar amount (e.g., 2.50 for $2.50)
                        </small>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="roundPrices">
                            <label class="form-check-label" for="roundPrices">
                                Round to nearest $0.99 or $0.95
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-theme btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn-theme btn-primary-theme" onclick="processBulkUpdate()">
                    <i class="fas fa-save mr-2"></i>Update Prices
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Price Management JavaScript
    $(document).ready(function () {
        // Initialize DataTables
        if ($.fn.DataTable) {
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

        // Auto-save price changes after 2 seconds of inactivity
        let priceChangeTimer;
        $('.price-input').on('input', function () {
            clearTimeout(priceChangeTimer);
            const productId = $(this).data('product-id');
            const originalPrice = $(this).data('original-price');
            const newPrice = $(this).val();

            // Highlight changed prices
            if (parseFloat(newPrice) !== parseFloat(originalPrice)) {
                $(this).addClass('border-warning');
            } else {
                $(this).removeClass('border-warning');
            }

            priceChangeTimer = setTimeout(() => {
                if (parseFloat(newPrice) !== parseFloat(originalPrice)) {
                    autoSavePrice(productId, newPrice);
                }
            }, 2000);
        });

        // Update selected count on page load
        updateSelectedCount();
    });

    // Price Management Functions
    function filterPrices() {
        const category = $('#categoryFilter').val();
        const priceRange = $('#priceRange').val();
        const stockStatus = $('#stockStatus').val();
        const marginFilter = $('#marginFilter').val();

        console.log('Filtering prices with:', { category, priceRange, stockStatus, marginFilter });

        const params = new URLSearchParams(window.location.search);
        if (category) params.set('category', category);
        else params.delete('category');

        if (priceRange) params.set('price_range', priceRange);
        else params.delete('price_range');

        if (stockStatus) params.set('stock_status', stockStatus);
        else params.delete('stock_status');

        if (marginFilter) params.set('margin_filter', marginFilter);
        else params.delete('margin_filter');

        window.location.search = params.toString();
    }

    function clearFilters() {
        $('#categoryFilter, #priceRange, #stockStatus, #marginFilter').val('');
        window.location.href = window.location.pathname;
    }

    function toggleSelectAll() {
        const isChecked = $('#masterSelect').is(':checked') || $('#selectAll').is(':checked');
        $('.product-checkbox').prop('checked', isChecked);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selectedCount = $('.product-checkbox:checked').length;
        $('#selectedCount').text(selectedCount);
    }

    function calculateMargin(priceInput) {
        const productId = $(priceInput).data('product-id');
        const newPrice = parseFloat($(priceInput).val()) || 0;
        const row = $(priceInput).closest('tr');
        const costText = row.find('td:nth-child(6) span').text().replace('$', '');
        const cost = parseFloat(costText) || 0;

        let margin = 0;
        if (newPrice > 0 && cost > 0) {
            margin = ((newPrice - cost) / newPrice) * 100;
        }

        const marginBadge = row.find(`.margin-badge[data-product-id="${productId}"]`);
        marginBadge.text(margin.toFixed(1) + '%');

        // Update badge color based on margin
        marginBadge.removeClass('badge-success badge-warning badge-danger');
        if (margin > 30) {
            marginBadge.addClass('badge-success');
        } else if (margin > 15) {
            marginBadge.addClass('badge-warning');
        } else {
            marginBadge.addClass('badge-danger');
        }
    }

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
            error: function () {
                alert('Price update failed! Please try again.');
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
            error: function () {
                $('#priceHistoryContent').html(`
                <div class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>Failed to load price history. Please try again.</p>
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
                if (response.success) {
                    alert('Bulk price update successful!');
                    $('#bulkUpdateModal').modal('hide');
                    location.reload();
                } else {
                    alert('Failed to update prices: ' + (response.message || 'Unknown error'));
                }
            },
            error: function () {
                alert('Bulk price update failed! Please try again.');
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

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>