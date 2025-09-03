<?php
$pageTitle = 'Pricing Bot Dashboard';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<!-- Load Unified CSS -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/app-unified.css">

<div class="container-fluid page-top-area mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-tags mr-2 text-warning"></i>Pricing Bot Dashboard
            </h1>
            <small class="text-muted">Automated price optimization with real-time controls</small>
        </div>
        <div class="col-12 col-md-6 text-right">
            <a href="<?= URLROOT ?>/admin/priceManagement" class="btn btn-secondary mr-2">
                <i class="fas fa-list mr-1"></i> Price Management
            </a>
            <a href="<?= URLROOT ?>/admin/bot" class="btn btn-info">
                <i class="fas fa-robot mr-1"></i> All Bots
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Pricing Bot Control Panel -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-theme">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fas fa-tags mr-2 text-warning"></i>
                                Pricing Bot Control
                            </h6>
                        </div>
                        <div class="col-auto">
                            <span class="badge badge-secondary" id="pricingBotStatus">
                                Inactive
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Automatically optimizes product pricing based on margin targets and market data
                    </p>

                    <!-- Bot Controls -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <button class="btn btn-success btn-sm w-100" id="startPricingBot">
                                <i class="fas fa-play mr-1"></i>Start Bot
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-danger btn-sm w-100" id="stopPricingBot" disabled>
                                <i class="fas fa-pause mr-1"></i>Stop Bot
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-info btn-sm w-100" id="runOnceBtn">
                                <i class="fas fa-step-forward mr-1"></i>Run Once
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-secondary btn-sm w-100" onclick="refreshPricingDashboard()">
                                <i class="fas fa-sync mr-1"></i>Refresh
                            </button>
                        </div>
                    </div>

                    <!-- Bot Settings -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="small text-muted">Interval (seconds)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control" id="botInterval" value="60" min="30"
                                    max="3600">
                                <div class="input-group-append">
                                    <span class="input-group-text">sec</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted">Target Margin</label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control" id="targetMargin" value="30" min="5" max="50"
                                    step="1">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted">Price Change Limit</label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control" id="priceChangeLimit" value="10" min="1"
                                    max="50" step="1">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bot Statistics -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="font-weight-bold text-primary" id="priceUpdates">0</div>
                                <small class="text-muted">Price Updates</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="font-weight-bold text-success" id="totalSavings">$0.00</div>
                                <small class="text-muted">Total Optimizations</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="font-weight-bold text-info" id="botUptime">00:00:00</div>
                                <small class="text-muted">Uptime</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="font-weight-bold text-warning" id="avgMargin">0%</div>
                                <small class="text-muted">Avg Margin</small>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-3">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-warning" id="pricingProgress" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <!-- Bot Activity Log -->
                <div class="card-footer p-2">
                    <div id="pricingBotActivityLog" style="max-height: 200px; overflow-y: auto; font-size: 0.85rem;">
                        <div class="text-muted text-center py-2">
                            <i class="fas fa-tags"></i> Pricing bot ready to start...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Bot Logic Panel (moved from bot_dashboard) -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card-theme">
                <div class="card-header">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-brain mr-2 text-info"></i>Pricing Bot Logic
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold mb-3"><i
                                    class="fas fa-crosshairs mr-2 text-primary"></i>Selection Criteria</h6>
                            <div id="selection-criteria" class="small">
                                <div class="mb-2"><span class="badge badge-info">Margin Deviation</span>
                                    <div class="text-muted">Target: ≥ 30% margin</div>
                                </div>
                                <div class="mb-2"><span class="badge badge-warning">Inventory Level</span>
                                    <div class="text-muted">Stock status impact</div>
                                </div>
                                <div class="mb-2"><span class="badge badge-success">Sales Performance</span>
                                    <div class="text-muted">Recent movement data</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold mb-3"><i class="fas fa-chart-line mr-2 text-success"></i>Active
                                Strategies</h6>
                            <div id="active-strategies" class="small">
                                <div class="strategy-item mb-2" data-strategy="low-margin">
                                    <div class="d-flex justify-content-between align-items-center"><span>Low Margin
                                            Correction</span>
                                        <span class="badge badge-primary">5-15%</span>
                                    </div>
                                </div>
                                <div class="strategy-item mb-2" data-strategy="inventory-movement">
                                    <div class="d-flex justify-content-between align-items-center"><span>Inventory
                                            Movement</span>
                                        <span class="badge badge-warning">3-10%</span>
                                    </div>
                                </div>
                                <div class="strategy-item mb-2" data-strategy="scarcity-premium">
                                    <div class="d-flex justify-content-between align-items-center"><span>Scarcity
                                            Premium</span>
                                        <span class="badge badge-danger">10-20%</span>
                                    </div>
                                </div>
                                <div class="strategy-item mb-2" data-strategy="demand-optimization">
                                    <div class="d-flex justify-content-between align-items-center"><span>Demand
                                            Optimization</span>
                                        <span class="badge badge-info">2-8%</span>
                                    </div>
                                </div>
                                <div class="strategy-item mb-2" data-strategy="clearance">
                                    <div class="d-flex justify-content-between align-items-center"><span>Clearance
                                            Pricing</span>
                                        <span class="badge badge-secondary">-5 to -15%</span>
                                    </div>
                                </div>
                                <div class="strategy-item mb-2" data-strategy="competitive">
                                    <div class="d-flex justify-content-between align-items-center"><span>Market
                                            Competitive</span>
                                        <span class="badge badge-dark">±5%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6 class="font-weight-bold mb-3"><i class="fas fa-history mr-2 text-warning"></i>Recent
                            Decisions</h6>
                        <div id="recent-decisions" style="max-height: 200px; overflow-y: auto;">
                            <div class="text-center py-3 text-muted"><i class="fas fa-robot"></i>
                                <div class="small">No recent pricing decisions</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Overview Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card h-100">
                <div class="text-center">
                    <div class="mb-2" style="color: var(--primary);">
                        <i class="fas fa-boxes fa-2x"></i>
                    </div>
                    <div class="kpi-value" style="color: var(--primary); font-size: 1.8rem;">
                        42
                    </div>
                    <div class="kpi-label" style="font-size: 0.9rem;">Products</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card h-100">
                <div class="text-center">
                    <div class="mb-2" style="color: var(--success);">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                    <div class="kpi-value" style="color: var(--success); font-size: 1.8rem;" id="productsOptimized">
                        0
                    </div>
                    <div class="kpi-label" style="font-size: 0.9rem;">Optimized</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card h-100">
                <div class="text-center">
                    <div class="mb-2" style="color: var(--warning);">
                        <i class="fas fa-percentage fa-2x"></i>
                    </div>
                    <div class="kpi-value" style="color: var(--warning); font-size: 1.8rem;" id="currentMargin">
                        0%
                    </div>
                    <div class="kpi-label" style="font-size: 0.9rem;">Current Avg Margin</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card h-100">
                <div class="text-center">
                    <div class="mb-2" style="color: var(--info);">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div class="kpi-value" style="color: var(--info); font-size: 1.8rem;" id="lastRun">
                        Never
                    </div>
                    <div class="kpi-label" style="font-size: 0.9rem;">Last Run</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Price Changes -->
    <div class="row">
        <div class="col-12">
            <div class="card-theme">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0 font-weight-bold">
                                <i class="fas fa-history mr-2"></i>Recent Price Changes
                            </h5>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-secondary btn-sm" onclick="clearPriceHistory()">
                                <i class="fas fa-trash mr-1"></i>Clear
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="priceChangesTable" style="max-height: 400px; overflow-y: auto;">
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                            <div>No price changes yet...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let pricingBotInterval = null;
    let botStartTime = null;
    let pricingBotStats = {
        priceUpdates: 0,
        totalSavings: 0,
        uptimeSeconds: 0
    };

    // Pricing Bot Controls
    document.getElementById('startPricingBot').addEventListener('click', function () {
        startPricingBot();
    });

    document.getElementById('stopPricingBot').addEventListener('click', function () {
        stopPricingBot();
    });

    document.getElementById('runOnceBtn').addEventListener('click', function () {
        runPricingBotOnce();
    });

    function startPricingBot() {
        const interval = parseInt(document.getElementById('botInterval').value) * 1000;

        if (pricingBotInterval) {
            clearInterval(pricingBotInterval);
        }

        // Update UI
        document.getElementById('pricingBotStatus').textContent = 'Active';
        document.getElementById('pricingBotStatus').className = 'badge badge-success';
        document.getElementById('startPricingBot').disabled = true;
        document.getElementById('stopPricingBot').disabled = false;

        // Log start
        addToPricingLog('🤖 Pricing bot started with ' + (interval / 1000) + 's interval', 'success');

        // Start bot
        botStartTime = Date.now();
        updateBotUptime();
        pricingBotInterval = setInterval(function () {
            runPricingBotOnce();
            updateBotUptime();
        }, interval);

        // Start uptime counter
        setInterval(updateBotUptime, 1000);
    }

    function stopPricingBot() {
        if (pricingBotInterval) {
            clearInterval(pricingBotInterval);
            pricingBotInterval = null;
        }

        // Update UI
        document.getElementById('pricingBotStatus').textContent = 'Inactive';
        document.getElementById('pricingBotStatus').className = 'badge badge-secondary';
        document.getElementById('startPricingBot').disabled = false;
        document.getElementById('stopPricingBot').disabled = true;

        // Log stop
        addToPricingLog('⏹️ Pricing bot stopped', 'warning');
        botStartTime = null;
    }

    function runPricingBotOnce() {
        // Show progress
        document.getElementById('pricingProgress').style.width = '50%';

        // Execute pricing bot
        fetch('<?= URLROOT ?>/admin/executeBot', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'bot_id=pricing_bot',
            credentials: 'same-origin'  // Include session cookies
        })
            .then(response => {
                // Check if response is ok and content type is JSON
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    // Response is not JSON, probably HTML error page
                    return response.text().then(text => {
                        throw new Error(`Expected JSON, got ${contentType}: ${text.substring(0, 200)}...`);
                    });
                }

                return response.json();
            })
            .then(data => {
                document.getElementById('pricingProgress').style.width = '100%';

                if (data.success) {
                    handlePricingBotSuccess(data);
                } else {
                    handlePricingBotError(data);
                }

                // Reset progress after delay
                setTimeout(() => {
                    document.getElementById('pricingProgress').style.width = '0%';
                }, 2000);
            })
            .catch(error => {
                document.getElementById('pricingProgress').style.width = '0%';
                addToPricingLog('❌ Error: ' + error.message, 'error');
                console.error('Bot execution error:', error);
            });
    }

    function handlePricingBotSuccess(data) {
        const timestamp = new Date().toLocaleTimeString();

        if (data.action === 'price_updated') {
            pricingBotStats.priceUpdates++;

            // Add to price changes table
            addPriceChange(data);

            // Update stats
            updatePricingStats();

            // Log success
            addToPricingLog(`💰 ${data.product_name}: $${data.old_price} → $${data.new_price} (${data.margin_percent}% margin)`, 'success');
        } else {
            addToPricingLog(`ℹ️ ${data.message}`, 'info');
        }

        // Update last run
        document.getElementById('lastRun').textContent = timestamp;
    }

    function handlePricingBotError(data) {
        addToPricingLog(`❌ ${data.message}`, 'error');
    }

    function addToPricingLog(message, type = 'info') {
        const log = document.getElementById('pricingBotActivityLog');
        const timestamp = new Date().toLocaleTimeString();

        const colors = {
            success: 'text-success',
            error: 'text-danger',
            warning: 'text-warning',
            info: 'text-info'
        };

        const logEntry = document.createElement('div');
        logEntry.className = `small ${colors[type] || 'text-muted'} mb-1`;
        logEntry.innerHTML = `[${timestamp}] ${message}`;

        log.appendChild(logEntry);
        log.scrollTop = log.scrollHeight;

        // Keep only last 20 entries
        while (log.children.length > 20) {
            log.removeChild(log.firstChild);
        }
    }

    function addPriceChange(data) {
        const table = document.getElementById('priceChangesTable');

        // Create table if it doesn't exist
        if (!table.querySelector('table')) {
            table.innerHTML = `
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Time</th>
                        <th>Product</th>
                        <th>Old Price</th>
                        <th>New Price</th>
                        <th>Change</th>
                        <th>Margin</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        `;
        }

        const tbody = table.querySelector('tbody');
        const timestamp = new Date().toLocaleTimeString();
        const changeClass = data.price_change > 0 ? 'text-success' : 'text-danger';
        const changeSymbol = data.price_change > 0 ? '+' : '';

        const row = document.createElement('tr');
        row.innerHTML = `
        <td class="small">${timestamp}</td>
        <td class="small">${data.product_name}</td>
        <td class="small">$${parseFloat(data.old_price).toFixed(2)}</td>
        <td class="small">$${parseFloat(data.new_price).toFixed(2)}</td>
        <td class="small ${changeClass}">${changeSymbol}$${parseFloat(data.price_change).toFixed(2)}</td>
        <td class="small">${data.margin_percent}%</td>
    `;

        tbody.insertBefore(row, tbody.firstChild);

        // Keep only last 10 rows
        while (tbody.children.length > 10) {
            tbody.removeChild(tbody.lastChild);
        }
    }

    function updatePricingStats() {
        document.getElementById('priceUpdates').textContent = pricingBotStats.priceUpdates;
        document.getElementById('productsOptimized').textContent = pricingBotStats.priceUpdates;
    }

    function updateBotUptime() {
        if (botStartTime) {
            const uptimeMs = Date.now() - botStartTime;
            const uptimeSeconds = Math.floor(uptimeMs / 1000);
            const hours = Math.floor(uptimeSeconds / 3600);
            const minutes = Math.floor((uptimeSeconds % 3600) / 60);
            const seconds = uptimeSeconds % 60;

            const timeStr = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            document.getElementById('botUptime').textContent = timeStr;
        }
    }

    function refreshPricingDashboard() {
        location.reload();
    }

    function clearPriceHistory() {
        document.getElementById('priceChangesTable').innerHTML = `
        <div class="text-center py-4 text-muted">
            <i class="fas fa-chart-line fa-2x mb-2"></i>
            <div>No price changes yet...</div>
        </div>
    `;

        // Reset stats
        pricingBotStats = {
            priceUpdates: 0,
            totalSavings: 0,
            uptimeSeconds: 0
        };
        updatePricingStats();

        addToPricingLog('🗑️ Price history cleared', 'info');
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        addToPricingLog('📊 Pricing dashboard loaded', 'info');

        // Add event listener for the old "Run Price Bot" button if it exists
        const btn = document.getElementById('runPriceBotBtn');
        if (btn) {
            btn.addEventListener('click', async function () {
                // Submission process rule: log before starting
                console.log('Initiating submission...');
                alert('Initiating submission...');
                appendOutput('[UI] Initiating submission...');
                btn.disabled = true;

                try {
                    const resp = await fetch('<?= URLROOT ?>/scripts/price_bot.php');
                    if (!resp.ok) throw new Error('Network response was not ok: ' + resp.status);
                    const text = await resp.text();

                    // Show returned simulated API logs
                    appendOutput(text);
                    console.log('Submission successful!');
                    alert('Submission successful!');
                    appendOutput('[UI] Submission successful!');

                    // Try to parse the script output for new prices and update the table where possible
                    const priceRegex = /Product\s+(\d+) price updated to ([0-9\.]+)/g;
                    let match;
                    while ((match = priceRegex.exec(text)) !== null) {
                        const id = match[1];
                        const price = parseFloat(match[2]).toFixed(2);
                        const cell = document.querySelector('#productsTable td[data-id="' + id + '"]');
                        if (cell) cell.textContent = '$' + price;
                    }
                } catch (err) {
                    console.error(err);
                    alert('Submission failed! Details: ' + err.message);
                    appendOutput('[UI] Submission failed! Details: ' + err.message);
                } finally {
                    btn.disabled = false;
                }
            });
        }
    });

    // Load sample product data for display
    function loadRealProductData() {
        return [
            { id: 143, cost: 55.20, sales_per_month: 10, profit_weighting: 1.0, name: 'Alert Test Product', current_price: 121.44 },
            { id: 139, cost: 94.20, sales_per_month: 10, profit_weighting: 1.1, name: 'Bulb', current_price: 183.04 },
            { id: 108, cost: 1500.00, sales_per_month: 10, profit_weighting: 0.9, name: 'Cement 25 Kg', current_price: 2898.00 },
            { id: 2, cost: 89.99, sales_per_month: 10, profit_weighting: 1.2, name: 'Circular Saw 7-1/4"', current_price: 125.00 },
            { id: 1, cost: 89.99, sales_per_month: 10, profit_weighting: 1.2, name: 'Cordless Drill 18V', current_price: 125.00 },
        ];
    }

    const sampleProducts = loadRealProductData();

    function renderProducts() {
        const tbody = document.querySelector('#productsTable tbody');
        if (!tbody) {
            // No products table in current design, skip rendering
            return;
        }

        tbody.innerHTML = '';
        sampleProducts.forEach(p => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td>${p.id}</td>
            <td>$${p.cost.toFixed(2)}</td>
            <td>${p.sales_per_month}</td>
            <td>${p.profit_weighting.toFixed(2)}</td>
            <td data-id="${p.id}">$${p.current_price ? p.current_price.toFixed(2) : '-'}</td>
        `;
            tbody.appendChild(tr);
        });
    }

    function appendOutput(text) {
        const el = document.getElementById('botOutput');
        if (el) {
            el.textContent += text + '\n';
            el.scrollTop = el.scrollHeight;
        }
    }
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<!-- Pricing Logic Styles and Diagnostics (moved) -->
<style>
    .strategy-item {
        transition: all 0.3s ease;
        border-radius: 4px;
        padding: 4px 8px;
    }

    .strategy-item.active-strategy {
        background-color: #e3f2fd;
        border-left: 3px solid #2196f3;
    }

    .strategy-item.highlight {
        background-color: #fff3e0;
        border-left: 3px solid #ff9800;
        animation: pulse 1s ease-in-out;
    }

    .decision-entry {
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .decision-entry:hover {
        background-color: #e8f5e8 !important;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }

        100% {
            opacity: 1;
        }
    }

    .pricing-logic-badge {
        font-size: 0.75rem;
        padding: 2px 6px;
    }
</style>

<script>
    // Lightweight front-end diagnostic widget (moved)
    (function frontEndPricingDiagnostics() {
        function ensurePanel() {
            var e = document.getElementById('pricing-debug-status');
            if (!e) {
                e = document.createElement('div');
                e.id = 'pricing-debug-status';
                e.style.position = 'fixed'; e.style.right = '12px'; e.style.bottom = '12px'; e.style.zIndex = 99999;
                e.style.background = 'rgba(0,0,0,0.7)'; e.style.color = '#fff'; e.style.padding = '8px 10px';
                e.style.fontSize = '12px'; e.style.borderRadius = '6px'; e.style.boxShadow = '0 2px 8px rgba(0,0,0,0.3)';
                document.body.appendChild(e);
            }
            return e;
        }

        function checkOnce() {
            var panel = ensurePanel();
            var jq = (typeof window.$ !== 'undefined' && window.$.fn) ? window.$.fn.jquery : 'none';
            var startBtns = (typeof window.$ !== 'undefined') ? window.$('#startPricingBot').length : 'no-jq';
            var bd = (typeof window.pricingBot !== 'undefined') ? 'ready' : 'not-ready';
            var txt = 'jQuery: ' + jq + ' | startBtn: ' + startBtns + ' | pricingBot: ' + bd;
            panel.textContent = txt; console.log('[PricingDiag] ' + txt);
        }

        setTimeout(checkOnce, 800); setTimeout(checkOnce, 2000); setTimeout(checkOnce, 5000);
    })();
</script>