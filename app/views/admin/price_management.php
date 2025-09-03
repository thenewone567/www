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
        <!-- <div class="col-12 col-md-6 text-md-right">
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
        </div> -->
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

    <!-- Bubble Chart Analysis -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-theme">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bubble mr-2"></i>Price & Margin Analysis
                    </h5>
                    <small class="text-muted">Visual analysis of price, margin, revenue and profitability</small>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="bubbleChart"></canvas>
                    </div>
                    <div class="chart-legend mt-3">
                        <div class="row text-center">
                            <div class="col-md-2">
                                <small class="text-muted"><strong>X-Axis:</strong> Price per Unit ($)</small>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted"><strong>Y-Axis:</strong> Gross Margin (%)</small>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted"><strong>Bubble Size:</strong> Revenue/Sales Volume</small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <span style="color: #28a745;">●</span> Profitable &nbsp;
                                    <span style="color: #dc3545;">●</span> Loss-making &nbsp;
                                    <span style="color: #ffc107;">●</span> Needs Pricing
                                </small>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12 text-center">
                                <small class="text-muted"><em>Orange bubbles show products without prices (positioned at
                                        estimated 30% markup for visualization)</em></small>
                            </div>
                        </div>
                    </div>
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
                        <!-- Range-Based Margin Pricing -->
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-layer-group mr-2"></i>Price Range Margin Settings
                            </h6>
                            <p class="text-muted small mb-3">
                                Set different profit margins for different cost ranges. Selling Price = Cost × (1 +
                                Margin%).
                                <br><strong>Positive Example:</strong> Cost $20 with 150% margin = Selling Price $50
                                (Profit $30)
                                <br><strong>Negative Example:</strong> Cost $20 with -30% margin = Selling Price $14
                                (Loss $6 - Dead Stock Clearance)
                                <br><span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Use negative
                                    margins to clear dead stock at a loss</span>
                            </p>

                            <div class="row">
                                <!-- Range 1 -->
                                <div class="col-md-6 mb-3">
                                    <div class="price-range-row p-3 border rounded">
                                        <label class="small font-weight-bold text-info">Range 1</label>
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range1_from" placeholder="From" value="0" min="0"
                                                            step="0.01">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range1_to" placeholder="To" value="50" min="0"
                                                            step="0.01">
                                                    </div>
                                                </div>
                                                <small class="text-muted">Cost Range ($)</small>
                                            </div>
                                            <div class="col-5">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control" id="range1_margin"
                                                        value="150" min="-90" max="1000" step="5">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <small class="text-muted">Margin (-90% to 1000%)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Range 2 -->
                                <div class="col-md-6 mb-3">
                                    <div class="price-range-row p-3 border rounded">
                                        <label class="small font-weight-bold text-info">Range 2</label>
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range2_from" placeholder="From" value="50.01" min="0"
                                                            step="0.01">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range2_to" placeholder="To" value="100" min="0"
                                                            step="0.01">
                                                    </div>
                                                </div>
                                                <small class="text-muted">Cost Range ($)</small>
                                            </div>
                                            <div class="col-5">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control" id="range2_margin"
                                                        value="120" min="-90" max="1000" step="5">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <small class="text-muted">Margin (-90% to 1000%)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Range 3 -->
                                <div class="col-md-6 mb-3">
                                    <div class="price-range-row p-3 border rounded">
                                        <label class="small font-weight-bold text-info">Range 3</label>
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range3_from" placeholder="From" value="100.01" min="0"
                                                            step="0.01">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range3_to" placeholder="To" value="200" min="0"
                                                            step="0.01">
                                                    </div>
                                                </div>
                                                <small class="text-muted">Cost Range ($)</small>
                                            </div>
                                            <div class="col-5">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control" id="range3_margin"
                                                        value="100" min="-90" max="1000" step="5">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <small class="text-muted">Margin (-90% to 1000%)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Range 4 -->
                                <div class="col-md-6 mb-3">
                                    <div class="price-range-row p-3 border rounded">
                                        <label class="small font-weight-bold text-info">Range 4</label>
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range4_from" placeholder="From" value="200.01" min="0"
                                                            step="0.01">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range4_to" placeholder="To" value="500" min="0"
                                                            step="0.01">
                                                    </div>
                                                </div>
                                                <small class="text-muted">Cost Range ($)</small>
                                            </div>
                                            <div class="col-5">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control" id="range4_margin"
                                                        value="80" min="-90" max="1000" step="5">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <small class="text-muted">Margin (-90% to 1000%)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Range 5 -->
                                <div class="col-md-6 mb-3">
                                    <div class="price-range-row p-3 border rounded">
                                        <label class="small font-weight-bold text-info">Range 5</label>
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range5_from" placeholder="From" value="500.01" min="0"
                                                            step="0.01">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range5_to" placeholder="To" value="1000" min="0"
                                                            step="0.01">
                                                    </div>
                                                </div>
                                                <small class="text-muted">Cost Range ($)</small>
                                            </div>
                                            <div class="col-5">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control" id="range5_margin"
                                                        value="60" min="-90" max="1000" step="5">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <small class="text-muted">Margin (-90% to 1000%)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Range 6 -->
                                <div class="col-md-6 mb-3">
                                    <div class="price-range-row p-3 border rounded">
                                        <label class="small font-weight-bold text-info">Range 6</label>
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range6_from" placeholder="From" value="1000.01" min="0"
                                                            step="0.01">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range6_to" placeholder="To" value="2000" min="0"
                                                            step="0.01">
                                                    </div>
                                                </div>
                                                <small class="text-muted">Cost Range ($)</small>
                                            </div>
                                            <div class="col-5">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control" id="range6_margin"
                                                        value="50" min="-90" max="1000" step="5">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <small class="text-muted">Margin (-90% to 1000%)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Range 7 -->
                                <div class="col-md-6 mb-3">
                                    <div class="price-range-row p-3 border rounded">
                                        <label class="small font-weight-bold text-info">Range 7</label>
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range7_from" placeholder="From" value="2000.01" min="0"
                                                            step="0.01">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range7_to" placeholder="To" value="5000" min="0"
                                                            step="0.01">
                                                    </div>
                                                </div>
                                                <small class="text-muted">Cost Range ($)</small>
                                            </div>
                                            <div class="col-5">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control" id="range7_margin"
                                                        value="40" min="-90" max="1000" step="5">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <small class="text-muted">Margin (-90% to 1000%)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Range 8 -->
                                <div class="col-md-6 mb-3">
                                    <div class="price-range-row p-3 border rounded">
                                        <label class="small font-weight-bold text-info">Range 8</label>
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range8_from" placeholder="From" value="5000.01" min="0"
                                                            step="0.01">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range8_to" placeholder="To" value="10000" min="0"
                                                            step="0.01">
                                                    </div>
                                                </div>
                                                <small class="text-muted">Cost Range ($)</small>
                                            </div>
                                            <div class="col-5">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control" id="range8_margin"
                                                        value="30" min="-90" max="1000" step="5">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <small class="text-muted">Margin (-90% to 1000%)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Range 9 -->
                                <div class="col-md-6 mb-3">
                                    <div class="price-range-row p-3 border rounded">
                                        <label class="small font-weight-bold text-info">Range 9</label>
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range9_from" placeholder="From" value="10000.01" min="0"
                                                            step="0.01">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range9_to" placeholder="To" value="20000" min="0"
                                                            step="0.01">
                                                    </div>
                                                </div>
                                                <small class="text-muted">Cost Range ($)</small>
                                            </div>
                                            <div class="col-5">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control" id="range9_margin"
                                                        value="25" min="-90" max="1000" step="5">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <small class="text-muted">Margin (-90% to 1000%)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Range 10 -->
                                <div class="col-md-6 mb-3">
                                    <div class="price-range-row p-3 border rounded">
                                        <label class="small font-weight-bold text-info">Range 10</label>
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range10_from" placeholder="From" value="20000.01"
                                                            min="0" step="0.01">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="range10_to" placeholder="To" value="999999" min="0"
                                                            step="0.01">
                                                    </div>
                                                </div>
                                                <small class="text-muted">Cost Range ($)</small>
                                            </div>
                                            <div class="col-5">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control" id="range10_margin"
                                                        value="20" min="-90" max="1000" step="5">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <small class="text-muted">Margin (-90% to 1000%)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dead Stock Clearance Section -->
                        <div class="mb-4">
                            <hr class="my-4">
                            <h6 class="font-weight-bold text-danger mb-3">
                                <i class="fas fa-fire mr-2"></i>Dead Stock Clearance (Negative Margins)
                            </h6>
                            <div class="alert alert-warning">
                                <small><i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Warning:</strong> These settings will result in selling at a loss to clear
                                    inventory.
                                </small>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <button class="btn btn-outline-danger btn-sm w-100" onclick="applyQuickMargin(-10)">
                                        <i class="fas fa-percentage mr-1"></i>-10% Loss
                                    </button>
                                    <small class="text-muted d-block text-center">Light clearance</small>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button class="btn btn-outline-danger btn-sm w-100" onclick="applyQuickMargin(-25)">
                                        <i class="fas fa-percentage mr-1"></i>-25% Loss
                                    </button>
                                    <small class="text-muted d-block text-center">Moderate clearance</small>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button class="btn btn-danger btn-sm w-100" onclick="applyQuickMargin(-50)">
                                        <i class="fas fa-percentage mr-1"></i>-50% Loss
                                    </button>
                                    <small class="text-muted d-block text-center">Heavy clearance</small>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button class="btn btn-danger btn-sm w-100" onclick="applyQuickMargin(-75)">
                                        <i class="fas fa-percentage mr-1"></i>-75% Loss
                                    </button>
                                    <small class="text-muted d-block text-center">Emergency clearance</small>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="small font-weight-bold">Custom Negative Margin</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" id="customNegativeMargin"
                                            placeholder="Enter negative %" min="-90" max="-1" step="1">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-danger"
                                                onclick="applyCustomNegativeMargin()">
                                                Apply
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="small font-weight-bold">Quick Example</label>
                                    <div class="small text-muted">
                                        Cost $100 with -30% = Sell $70 (Loss $30)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mb-3">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="selectAllForMargin">
                                <label class="form-check-label" for="selectAllForMargin">
                                    Apply to all products (or select individual products in table below)
                                </label>
                            </div>

                            <div class="btn-group w-100" role="group">
                                <button class="btn btn-success" onclick="applyRangeBasedMargins()">
                                    <i class="fas fa-calculator mr-2"></i>Calculate & Apply Margins
                                </button>
                                <button class="btn btn-info" onclick="previewRangeBasedMargins()">
                                    <i class="fas fa-eye mr-2"></i>Preview Changes
                                </button>
                                <button class="btn btn-warning" onclick="resetMarginRanges()">
                                    <i class="fas fa-undo mr-2"></i>Reset to Defaults
                                </button>
                            </div>
                        </div>

                        <!-- Preview Area -->
                        <div id="marginPreview" class="alert alert-info" style="display: none;">
                            <h6><i class="fas fa-info-circle mr-2"></i>Margin Preview</h6>
                            <div id="marginPreviewContent"></div>
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
                            <th>Cost Analysis</th>
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
                                // Dual-cost system for better profitability analysis
                                $basePurchasePrice = $product->base_purchase_price ?? 0;  // Original/supplier cost
                                $currentAverageCost = $product->current_average_cost ?? 0; // Real average cost
                                $effectiveCost = $product->cost ?? 0; // Cost used for calculations (prioritizes current_average_cost)
                                $salePrice = $product->price ?? 0;

                                // Use effective cost for profit calculations
                                $margin = 0;
                                $profitPerUnit = $salePrice - $effectiveCost;

                                if ($salePrice > 0 && $effectiveCost > 0) {
                                    $margin = (($salePrice - $effectiveCost) / $salePrice) * 100;
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
                                                    class="rounded mr-3" style="width: 40px; height: 40px; object-fit: cover;"
                                                    onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="bg-light rounded mr-3 align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px; display: none;">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
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
                                        <div class="cost-analysis">
                                            <div>
                                                <strong class="text-danger">₹<?= number_format($effectiveCost, 2) ?></strong>
                                                <small class="text-muted ml-1">(Current)</small>
                                            </div>
                                            <?php if ($basePurchasePrice > 0 && $basePurchasePrice != $effectiveCost): ?>
                                                <div class="mt-1">
                                                    <small class="text-muted">Base:
                                                        ₹<?= number_format($basePurchasePrice, 2) ?></small>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($currentAverageCost > 0 && $currentAverageCost != $basePurchasePrice): ?>
                                                <div>
                                                    <small class="text-info">Avg:
                                                        ₹<?= number_format($currentAverageCost, 2) ?></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="input-group input-group-sm" style="width: 120px;">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₹</span>
                                            </div>
                                            <input type="number" class="form-control text-right sale-price-input"
                                                value="<?= number_format($salePrice, 2, '.', '') ?>" step="0.01" min="0"
                                                data-product-id="<?= $product->product_id ?? 0 ?>"
                                                data-cost="<?= $effectiveCost ?>" data-base-cost="<?= $basePurchasePrice ?>"
                                                data-avg-cost="<?= $currentAverageCost ?>" onchange="updateSalePrice(this)">
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
                                        $profitPerUnit = $salePrice - $effectiveCost;

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
    // Range-Based Margin Functions (Global scope for onclick handlers)
    function applyRangeBasedMargins() {
        console.log('Initiating range-based margin application...');
        console.log('URLROOT:', '<?= URLROOT ?>');

        const ranges = getRangeSettings();
        const applyToAll = document.getElementById('selectAllForMargin').checked;

        if (!applyToAll) {
            const selectedProducts = $('.product-checkbox:checked');
            if (selectedProducts.length === 0) {
                alert('Please select products or check "Apply to all products"');
                return;
            }
        }

        if (!confirm('Apply range-based margins to selected products?\n\nThis will update selling prices based on cost ranges and SAVE to database.')) {
            return;
        }

        let updateCount = 0;
        let saveCount = 0;
        const savePromises = [];

        // Get the correct table rows based on the actual table structure
        const products = applyToAll ? $('#productPriceTable tbody tr') : $('.product-checkbox:checked').closest('tr');
        console.log(`Found ${products.length} products to update`);
        console.log('First product element:', products.first()[0]);
        console.log('First product data-product-id:', products.first().data('product-id'));

        products.each(function (index) {
            const row = $(this);
            const productId = row.data('product-id');
            console.log(`Product ${index}: ID = ${productId}`);

            // Find the sale price input in this row
            const priceInput = row.find('.sale-price-input');

            if (priceInput.length) {
                // Get the cost from the data attribute of the price input
                const cost = parseFloat(priceInput.data('cost')) || 0;
                console.log(`Processing product ${productId}: cost = ${cost}`);

                if (cost > 0 && productId) {
                    const margin = getMarginForCost(cost, ranges);
                    const newPrice = cost * (1 + margin / 100);
                    console.log(`Product ${productId}: applying ${margin}% margin, new price = ${newPrice.toFixed(2)}`);

                    // Update the price input
                    priceInput.val(newPrice.toFixed(2));

                    // Call the updateSalePrice function to update margin displays
                    updateSalePrice(priceInput[0]);
                    updateCount++;

                    // Save to database
                    console.log(`Preparing to save product ${productId} with price ${newPrice.toFixed(2)}`);
                    console.log(`API URL: <?= URLROOT ?>/api/updateProductPrice.php`);

                    const savePromise = fetch('<?= URLROOT ?>/api/updateProductPrice.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            price: parseFloat(newPrice.toFixed(2))
                        })
                    })
                        .then(response => {
                            console.log(`Response status for product ${productId}:`, response.status);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.text(); // Get text first to see what we're actually receiving
                        })
                        .then(responseText => {
                            console.log(`Raw response for product ${productId}:`, responseText.substring(0, 200) + '...');
                            try {
                                const data = JSON.parse(responseText);
                                console.log(`Response data for product ${productId}:`, data);
                                if (data.success) {
                                    console.log(`Successfully saved price for product ${productId}`);
                                    saveCount++;
                                    // Add visual indicator for successful save
                                    priceInput.addClass('border-success');
                                    setTimeout(() => priceInput.removeClass('border-success'), 2000);
                                } else {
                                    console.error(`Failed to save price for product ${productId}:`, data.error);
                                    throw new Error(data.error || 'Unknown API error');
                                }
                            } catch (parseError) {
                                console.error(`JSON parse error for product ${productId}:`, parseError);
                                console.error(`Full response text:`, responseText);
                                throw new Error(`Invalid JSON response: ${parseError.message}`);
                            }
                        })
                        .catch(error => {
                            console.error(`Error saving price for product ${productId}:`, error);
                            // Add visual indicator for failed save
                            priceInput.addClass('border-danger');
                            setTimeout(() => priceInput.removeClass('border-danger'), 3000);
                        });

                    savePromises.push(savePromise);
                }
            }
        });

        console.log(`Total save promises created: ${savePromises.length}`);

        // Wait for all saves to complete
        if (savePromises.length > 0) {
            Promise.allSettled(savePromises).then(results => {
                const failedSaves = results.filter(result => result.status === 'rejected').length;
                const successSaves = results.length - failedSaves;

                console.log(`Range-based margins applied to ${updateCount} products, ${successSaves} saved to database`);

                if (failedSaves > 0) {
                    showToast(`Margins applied to ${updateCount} products. ${successSaves} saved, ${failedSaves} failed to save.`, 'warning');
                } else {
                    showToast(`Range-based margins applied and saved for ${successSaves} products!`, 'success');
                }
            });
        } else {
            console.log('No products were eligible for saving');
            showToast(`Margins applied to ${updateCount} products but no prices were saved (no valid product IDs or costs).`, 'warning');
        }
    }

    function previewRangeBasedMargins() {
        const ranges = getRangeSettings();
        const applyToAll = document.getElementById('selectAllForMargin').checked;
        const products = applyToAll ? $('#productPriceTable tbody tr') : $('.product-checkbox:checked').closest('tr');

        if (products.length === 0) {
            alert('No products selected for preview');
            return;
        }

        let previewHtml = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Product</th><th>Current Cost</th><th>Current Price</th><th>New Price</th><th>Margin</th><th>Profit/Loss</th></tr></thead><tbody>';

        products.each(function () {
            const row = $(this);
            // Get product name from the product details column (assuming it's in the 2nd column)
            const productName = row.find('td:eq(1) strong').text().trim() || 'Unknown Product';
            const priceInput = row.find('.sale-price-input');

            if (priceInput.length) {
                const cost = parseFloat(priceInput.data('cost')) || 0;
                const currentPrice = parseFloat(priceInput.val()) || 0;

                if (cost > 0) {
                    const margin = getMarginForCost(cost, ranges);
                    const newPrice = cost * (1 + margin / 100);
                    const profitLoss = newPrice - cost;
                    const isLoss = profitLoss < 0;

                    const marginBadge = margin < 0 ?
                        `<span class="badge badge-danger">${margin}%</span>` :
                        `<span class="badge badge-info">${margin}%</span>`;

                    const profitLossDisplay = isLoss ?
                        `<span class="text-danger font-weight-bold">-$${Math.abs(profitLoss).toFixed(2)} LOSS</span>` :
                        `<span class="text-success">+$${profitLoss.toFixed(2)}</span>`;

                    previewHtml += `<tr ${isLoss ? 'class="table-warning"' : ''}>
                        <td>${productName}</td>
                        <td>$${cost.toFixed(2)}</td>
                        <td>$${currentPrice.toFixed(2)}</td>
                        <td><strong>$${newPrice.toFixed(2)}</strong></td>
                        <td>${marginBadge}</td>
                        <td>${profitLossDisplay}</td>
                    </tr>`;
                }
            }
        });

        previewHtml += '</tbody></table></div>';

        document.getElementById('marginPreviewContent').innerHTML = previewHtml;
        document.getElementById('marginPreview').style.display = 'block';
    }

    function resetMarginRanges() {
        if (!confirm('Reset all margin ranges to default values?')) {
            return;
        }

        // Default ranges and margins
        const defaults = [
            { from: 0, to: 50, margin: 150 },
            { from: 50.01, to: 100, margin: 120 },
            { from: 100.01, to: 200, margin: 100 },
            { from: 200.01, to: 500, margin: 80 },
            { from: 500.01, to: 1000, margin: 60 },
            { from: 1000.01, to: 2000, margin: 50 },
            { from: 2000.01, to: 5000, margin: 40 },
            { from: 5000.01, to: 10000, margin: 30 },
            { from: 10000.01, to: 20000, margin: 25 },
            { from: 20000.01, to: 999999, margin: 20 }
        ];

        defaults.forEach((range, index) => {
            const rangeNum = index + 1;
            document.getElementById(`range${rangeNum}_from`).value = range.from;
            document.getElementById(`range${rangeNum}_to`).value = range.to;
            document.getElementById(`range${rangeNum}_margin`).value = range.margin;
        });

        showToast('Margin ranges reset to defaults', 'info');
    }

    function getRangeSettings() {
        const ranges = [];
        for (let i = 1; i <= 10; i++) {
            const from = parseFloat(document.getElementById(`range${i}_from`).value) || 0;
            const to = parseFloat(document.getElementById(`range${i}_to`).value) || 0;
            const margin = parseFloat(document.getElementById(`range${i}_margin`).value) || 0;

            ranges.push({ from, to, margin });
        }
        return ranges;
    }

    function getMarginForCost(cost, ranges) {
        for (const range of ranges) {
            if (cost >= range.from && cost <= range.to) {
                return range.margin;
            }
        }
        // Default margin if no range matches
        return 50;
    }

    // Dead Stock Clearance Functions
    function applyQuickMargin(marginPercent) {
        console.log(`Applying quick margin: ${marginPercent}%`);

        const selectedProducts = $('.product-checkbox:checked');
        if (selectedProducts.length === 0) {
            alert('Please select products to apply negative margin pricing.');
            return;
        }

        const action = marginPercent <= -50 ? 'HEAVY/EMERGENCY clearance' : 'clearance';
        if (!confirm(`Apply ${marginPercent}% margin (${action}) to ${selectedProducts.length} selected products?\n\nThis will result in selling at a LOSS to clear dead stock.`)) {
            return;
        }

        let updateCount = 0;
        selectedProducts.each(function () {
            const checkbox = $(this);
            const row = checkbox.closest('tr');
            const costElement = row.find('.product-cost');
            const priceInput = row.find('.sale-price-input');

            if (costElement.length && priceInput.length) {
                const cost = parseFloat(costElement.text().replace('$', '')) || 0;

                if (cost > 0) {
                    const newPrice = cost * (1 + marginPercent / 100);
                    priceInput.val(Math.max(0.01, newPrice).toFixed(2)); // Minimum price of $0.01
                    priceInput.trigger('change');
                    updateCount++;
                }
            }
        });

        console.log(`Negative margin applied to ${updateCount} products`);
        showToast(`${marginPercent}% margin applied to ${updateCount} products (CLEARANCE PRICING)`, 'warning');
    }

    function applyCustomNegativeMargin() {
        const customMargin = parseFloat(document.getElementById('customNegativeMargin').value);

        if (isNaN(customMargin) || customMargin >= 0) {
            alert('Please enter a negative margin percentage (e.g., -30)');
            return;
        }

        if (customMargin < -90) {
            alert('Margin cannot be less than -90% (would result in prices below 10% of cost)');
            return;
        }

        applyQuickMargin(customMargin);
        document.getElementById('customNegativeMargin').value = ''; // Clear input
    }

    // Handle missing images to prevent 404 errors
    function handleImageError(img) {
        img.src = '<?= URLROOT ?>/public/images/no-image.png';
        img.style.opacity = '0.5';
    }

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
        let profitLevel = 'Profit';

        if (!isProfit) {
            profitLossClass = 'text-danger font-weight-bold';
            profitLevel = 'Loss';
        } else if (margin > 50) {
            profitLossClass = 'text-success font-weight-bold';
            profitLevel = 'High Profit';
        } else if (margin > 25) {
            profitLossClass = 'text-success';
            profitLevel = 'Good Profit';
        } else if (margin > 15) {
            profitLossClass = 'text-info';
            profitLevel = 'Low Profit';
        } else if (margin > 0) {
            profitLossClass = 'text-warning';
            profitLevel = 'Minimal Profit';
        } else {
            profitLossClass = 'text-muted';
            profitLevel = 'Break Even';
        }

        // Update the display
        profitLossDisplay.removeClass('text-success text-warning text-info text-danger text-muted font-weight-bold');
        profitLossDisplay.addClass(profitLossClass);
        profitLossDisplay.html(`${sign}${displayPercent.toFixed(1)}%`);

        // Update the small text elements
        const smallElements = profitLossDisplay.parent().find('small');
        if (smallElements.length >= 2) {
            $(smallElements[0]).text(profitLevel).removeClass('text-success text-danger text-muted').addClass(profitLossClass.replace('font-weight-bold', ''));
            $(smallElements[1]).text(`₹${profitPerUnit.toFixed(2)}/unit`).removeClass('text-success text-danger').addClass(profitPerUnit >= 0 ? 'text-success' : 'text-danger');
        }

        // Mark input as changed
        $(input).addClass('border-warning');

        // Auto-save the price change to database
        if (productId && salePrice >= 0) {
            // Debounce the save to avoid too many requests
            clearTimeout($(input).data('saveTimeout'));
            const saveTimeout = setTimeout(() => {
                savePriceToDatabase(productId, salePrice, input);
            }, 1000); // Wait 1 second after user stops typing
            $(input).data('saveTimeout', saveTimeout);
        }
    }

    function savePriceToDatabase(productId, newPrice, inputElement) {
        console.log(`Auto-saving price for product ${productId}: $${newPrice}`);

        fetch('<?= URLROOT ?>/api/updateProductPrice.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                price: newPrice
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(`Successfully auto-saved price for product ${productId}`);
                    // Visual feedback for successful save
                    $(inputElement).removeClass('border-warning border-danger');
                    $(inputElement).addClass('border-success');
                    setTimeout(() => $(inputElement).removeClass('border-success'), 2000);
                } else {
                    console.error(`Failed to auto-save price for product ${productId}:`, data.error);
                    throw new Error(data.error);
                }
            })
            .catch(error => {
                console.error(`Error auto-saving price for product ${productId}:`, error);
                // Visual feedback for failed save
                $(inputElement).removeClass('border-warning border-success');
                $(inputElement).addClass('border-danger');
                // Keep the danger border longer to indicate the issue
                setTimeout(() => $(inputElement).removeClass('border-danger'), 5000);
            });
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

<script>
    // Bubble Chart for Price & Margin Analysis
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('bubbleChart').getContext('2d');

        // Prepare data from PHP products array
        const products = <?php echo json_encode($data['products'] ?? []); ?>;
        console.log('Raw products data (first 5):', products.slice(0, 5));
        console.log('Sample product structure:', products[0]);

        console.log('Total products received:', products.length);
        console.log('Sample product data:', products.slice(0, 3));

        // Debug: Check what fields are available in the product data
        if (products.length > 0) {
            console.log('Available product fields:', Object.keys(products[0]));

            // Look for price-related fields
            const priceFields = Object.keys(products[0]).filter(key =>
                key.toLowerCase().includes('price') || key.toLowerCase().includes('cost')
            );
            console.log('Price-related fields found:', priceFields);
        }

        // Enhanced bubble size calculation function
        function calculateBubbleSize(revenue, cost, totalSold, price) {
            // Calculate different size metrics
            const revenueSize = Math.max(revenue, 0);
            const volumeSize = Math.max(totalSold * price, totalSold * cost, 0);
            const stockValue = Math.max(cost * totalSold, price * totalSold, 0);

            // Use the highest value for size calculation
            const sizeMetric = Math.max(revenueSize, volumeSize, stockValue);

            // Debug logging for size calculation
            console.log('Bubble size calculation:', {
                revenue: revenueSize,
                volume: volumeSize,
                stock: stockValue,
                sizeMetric: sizeMetric,
                totalSold: totalSold,
                price: price,
                cost: cost
            });

            // Calculate bubble size with better scaling
            let bubbleSize;
            if (sizeMetric === 0) {
                bubbleSize = 5; // Minimum size for products with no data
            } else if (sizeMetric < 100) {
                bubbleSize = 8; // Small products
            } else if (sizeMetric < 500) {
                bubbleSize = 12; // Medium products
            } else if (sizeMetric < 1000) {
                bubbleSize = 16; // Medium-large products
            } else if (sizeMetric < 5000) {
                bubbleSize = 20; // Large products
            } else {
                bubbleSize = 25; // Very large products
            }

            return bubbleSize;
        }

        // Process data for bubble chart
        const bubbleData = products.map(product => {
            // Use correct field names from database query aliases
            const price = parseFloat(product.price || 0);  // This is selling_price aliased as 'price'
            const cost = parseFloat(product.cost || 0);    // This is purchase_price aliased as 'cost'
            const revenue = parseFloat(product.total_revenue || 0);
            const totalSold = parseFloat(product.total_sold || product.stock_quantity || 0);

            // Debug logging for bulb product - more detailed with cost sources
            if (product.name && product.name.toLowerCase().includes('bulb')) {
                console.log('BULB COST ANALYSIS DEBUG:', {
                    name: product.name,
                    sku: product.sku,
                    final_cost_used: cost,
                    actual_avg_cost: product.actual_avg_cost,
                    product_base_cost: product.product_base_cost,
                    supplier_cost: product.supplier_cost,
                    selling_price: price,
                    full_product_data: product
                });
            }

            // Calculate gross margin percentage
            let grossMargin = 0;
            if (price > 0 && cost > 0) {
                grossMargin = ((price - cost) / price) * 100;
            } else if (price === 0 && cost > 0) {
                // For products without price, show as -100% margin (needs pricing)
                grossMargin = -100;
            }

            // Calculate profit/loss for color coding
            const profit = (price - cost) * totalSold;
            const isProfit = profit >= 0;

            // Determine bubble color based on status
            let backgroundColor, borderColor;
            if (price === 0) {
                // Products without price - show in orange (needs attention)
                backgroundColor = 'rgba(255, 193, 7, 0.6)';
                borderColor = 'rgba(255, 193, 7, 1)';
            } else if (isProfit) {
                // Profitable products - green
                backgroundColor = 'rgba(40, 167, 69, 0.6)';
                borderColor = 'rgba(40, 167, 69, 1)';
            } else {
                // Loss-making products - red
                backgroundColor = 'rgba(220, 53, 69, 0.6)';
                borderColor = 'rgba(220, 53, 69, 1)';
            }

            return {
                x: price > 0 ? price : cost * 1.3, // If no price, estimate at 30% markup for visualization
                y: grossMargin,              // Y-axis: Gross Margin %
                r: calculateBubbleSize(revenue, cost, totalSold, price), // Dynamic bubble size
                label: product.name || 'Unknown Product',
                sku: product.sku || '',
                actualPrice: price,
                cost: cost,
                revenue: revenue,
                totalSold: totalSold,
                profit: profit,
                isProfit: isProfit,
                needsPricing: price === 0,
                backgroundColor: backgroundColor,
                borderColor: borderColor
            };
        }).filter(item => {
            // Show products with either price or cost data
            const hasValidData = (item.actualPrice > 0) || (item.cost > 0);

            if (!hasValidData) {
                console.log('Filtered out product (no price or cost):', item.label);
            }
            return hasValidData;
        });

        console.log('Products after filtering (price > 0):', bubbleData.length);
        console.log('First few bubble data points:', bubbleData.slice(0, 10));

        // Debug bubble sizes
        const sizeDebug = bubbleData.slice(0, 10).map(item => ({
            label: item.label,
            revenue: item.revenue,
            cost: item.cost,
            totalSold: item.totalSold,
            bubbleSize: item.r,
            actualPrice: item.actualPrice
        }));
        console.log('Bubble size debug info:', sizeDebug);

        // Check for size variety
        const allSizes = bubbleData.map(item => item.r);
        const uniqueSizes = [...new Set(allSizes)];
        console.log('All bubble sizes:', allSizes.slice(0, 20));
        console.log('Unique sizes found:', uniqueSizes);
        console.log('Size range:', Math.min(...allSizes), 'to', Math.max(...allSizes));

        // Calculate dynamic axis ranges for better accuracy
        const xValues = bubbleData.map(item => item.x).filter(x => x > 0);
        const yValues = bubbleData.map(item => item.y);

        // X-axis (Price) range calculation
        const minPrice = Math.min(...xValues);
        const maxPrice = Math.max(...xValues);
        const priceRange = maxPrice - minPrice;
        const pricePadding = priceRange * 0.1; // 10% padding

        const xAxisMin = Math.max(0, minPrice - pricePadding);
        const xAxisMax = maxPrice + pricePadding;

        // Y-axis (Margin) range calculation  
        const minMargin = Math.min(...yValues);
        const maxMargin = Math.max(...yValues);
        const marginRange = maxMargin - minMargin;
        const marginPadding = Math.max(5, marginRange * 0.1); // At least 5% padding

        const yAxisMin = minMargin - marginPadding;
        const yAxisMax = maxMargin + marginPadding;

        console.log('Dynamic axis ranges calculated:');
        console.log(`X-axis (Price): ${xAxisMin.toFixed(2)} to ${xAxisMax.toFixed(2)}`);
        console.log(`Y-axis (Margin): ${yAxisMin.toFixed(1)}% to ${yAxisMax.toFixed(1)}%`);

        const chartData = {
            datasets: [{
                label: 'Products',
                data: bubbleData,
                backgroundColor: bubbleData.map(item => item.backgroundColor),
                borderColor: bubbleData.map(item => item.borderColor),
                borderWidth: 2
                // Removed pointRadius and radius properties to let the r value in data control size
            }]
        };

        const config = {
            type: 'bubble',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Price vs Margin Analysis with Revenue Impact',
                        font: { size: 16, weight: 'bold' }
                    },
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            title: function (context) {
                                const point = context[0];
                                return point.raw.label + (point.raw.sku ? ' (' + point.raw.sku + ')' : '');
                            },
                            label: function (context) {
                                const point = context.raw;
                                const labels = [];

                                if (point.needsPricing) {
                                    labels.push('⚠️ NEEDS PRICING');
                                    labels.push(`Estimated Price: $${point.x.toFixed(2)} (cost + 30%)`);
                                    labels.push(`Purchase Cost: $${point.cost.toFixed(2)}`);
                                } else {
                                    labels.push(`Selling Price: $${point.actualPrice.toFixed(2)}`);
                                    labels.push(`Purchase Cost: $${point.cost.toFixed(2)}`);
                                }

                                labels.push(`Gross Margin: ${point.y.toFixed(1)}%`);
                                labels.push(`Stock Quantity: ${point.totalSold}`);
                                labels.push(`Total Revenue: $${point.revenue.toFixed(2)}`);

                                if (!point.needsPricing) {
                                    const profitPerUnit = point.actualPrice - point.cost;
                                    labels.push(`Profit/Unit: $${profitPerUnit.toFixed(2)}`);
                                    labels.push(`Total Profit: ${point.profit >= 0 ? '+' : ''}$${point.profit.toFixed(2)}`);
                                }

                                return labels;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Price per Unit ($)',
                            font: { size: 14, weight: 'bold' }
                        },
                        min: xAxisMin,
                        max: xAxisMax,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: function (value) {
                                return '$' + value.toFixed(2);
                            }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Gross Margin (%)',
                            font: { size: 14, weight: 'bold' }
                        },
                        min: yAxisMin,
                        max: yAxisMax,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: function (value) {
                                if (value <= -100) return 'No Price';
                                return value.toFixed(1) + '%';
                            }
                        }
                    }
                },
                elements: {
                    point: {
                        hoverRadius: 8
                    }
                }
            }
        };

        // Create the chart
        const bubbleChart = new Chart(ctx, config);

        // Add click handler for drill-down functionality
        ctx.canvas.addEventListener('click', function (event) {
            const points = bubbleChart.getElementsAtEventForMode(event, 'nearest', { intersect: true }, true);

            if (points.length) {
                const firstPoint = points[0];
                const datasetIndex = firstPoint.datasetIndex;
                const index = firstPoint.index;
                const data = bubbleChart.data.datasets[datasetIndex].data[index];

                // Show detailed information in a modal or alert
                const message = `Product Details:\n` +
                    `Name: ${data.label}\n` +
                    `SKU: ${data.sku}\n` +
                    `Price: $${data.x.toFixed(2)}\n` +
                    `Gross Margin: ${data.y.toFixed(1)}%\n` +
                    `Total Revenue: $${data.revenue.toFixed(2)}\n` +
                    `Net Impact: ${data.profit >= 0 ? '+' : ''}$${data.profit.toFixed(2)}`;

                alert(message);
            }
        });
    });
</script>

<style>
    /* Price Range Styles */
    .price-range-row {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-color, #e9ecef) !important;
        transition: all 0.2s ease;
    }

    .price-range-row:hover {
        border-color: var(--primary, #007bff) !important;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
        transform: translateY(-1px);
    }

    .price-range-row label {
        color: var(--primary, #007bff);
        margin-bottom: 0.5rem;
    }

    .price-range-row .form-control {
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }

    .price-range-row .input-group-text {
        background: var(--primary, #007bff);
        color: white;
        border-color: var(--primary, #007bff);
        font-weight: 600;
    }

    /* Negative Margin Styles */
    .price-range-row input[type="number"][min="-90"] {
        color: var(--danger, #dc3545);
        font-weight: 500;
    }

    .price-range-row input[type="number"][min="-90"]:focus {
        border-color: var(--danger, #dc3545);
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    /* Dead Stock Clearance Section */
    .btn-outline-danger:hover,
    .btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
    }

    /* Table warning for loss items */
    .table-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }

    /* Bubble Chart Styles */
    .chart-container {
        background: var(--card-bg, #fff);
        border-radius: var(--border-radius, 0.375rem);
        padding: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .chart-legend {
        background: rgba(var(--primary-rgb, 0, 123, 255), 0.05);
        border-radius: var(--border-radius, 0.375rem);
        padding: 0.75rem;
        border: 1px solid rgba(var(--primary-rgb, 0, 123, 255), 0.1);
    }

    .chart-legend small {
        font-size: 0.85rem;
        font-weight: 500;
    }

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

<!-- Load Chart.js before jQuery-dependent scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>