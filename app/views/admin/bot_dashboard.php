<?php
$pageTitle = 'Bot Automation Dashboard';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<!-- Load Unified CSS -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/app-unified.css">

<div class="container-fluid page-top-area mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-8">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-robot mr-2 text-primary"></i>Bot Automation Dashboard
            </h1>
            <small class="text-muted">Real-time workflow automation for purchase, receiving, sales and inventory management</small>
        </div>
        <div class="col-12 col-md-4 text-md-right">
            <div class="btn-group" role="group">
                <button class="btn btn-success" id="startAllBots">
                    <i class="fas fa-play mr-2"></i>Start All Bots
                </button>
                <button class="btn btn-danger" id="stopAllBots">
                    <i class="fas fa-stop mr-2"></i>Stop All Bots
                </button>
                <button class="btn btn-info" onclick="refreshDashboard()">
                    <i class="fas fa-sync mr-2"></i>Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- System Overview Stats -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
            <div class="kpi-card h-100">
                <div class="text-center">
                    <div class="mb-2" style="color: var(--primary);">
                        <i class="fas fa-boxes fa-2x"></i>
                    </div>
                    <div class="kpi-value" style="color: var(--primary); font-size: 1.8rem;">
                        <?= $data['system_stats']['total_products'] ?? 0 ?>
                    </div>
                    <div class="kpi-label" style="font-size: 0.9rem;">Products</div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
            <div class="kpi-card h-100">
                <div class="text-center">
                    <div class="mb-2" style="color: var(--success);">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div class="kpi-value" style="color: var(--success); font-size: 1.8rem;">
                        <?= $data['system_stats']['total_customers'] ?? 0 ?>
                    </div>
                    <div class="kpi-label" style="font-size: 0.9rem;">Customers</div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
            <div class="kpi-card h-100">
                <div class="text-center">
                    <div class="mb-2" style="color: var(--info);">
                        <i class="fas fa-truck fa-2x"></i>
                    </div>
                    <div class="kpi-value" style="color: var(--info); font-size: 1.8rem;">
                        <?= $data['system_stats']['total_suppliers'] ?? 0 ?>
                    </div>
                    <div class="kpi-label" style="font-size: 0.9rem;">Suppliers</div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
            <div class="kpi-card h-100">
                <div class="text-center">
                    <div class="mb-2" style="color: var(--warning);">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div class="kpi-value" style="color: var(--warning); font-size: 1.8rem;">
                        <?= $data['system_stats']['low_stock_items'] ?? 0 ?>
                    </div>
                    <div class="kpi-label" style="font-size: 0.9rem;">Low Stock</div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
            <div class="kpi-card h-100">
                <div class="text-center">
                    <div class="mb-2" style="color: var(--danger);">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                    <div class="kpi-value" style="color: var(--danger); font-size: 1.8rem;">
                        <?= $data['system_stats']['out_of_stock_items'] ?? 0 ?>
                    </div>
                    <div class="kpi-label" style="font-size: 0.9rem;">Out of Stock</div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
            <div class="kpi-card h-100">
                <div class="text-center">
                    <div class="mb-2" style="color: var(--secondary);">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                    <div class="kpi-value" style="color: var(--secondary); font-size: 1.8rem;">
                        <?= $data['system_stats']['pending_orders'] ?? 0 ?>
                    </div>
                    <div class="kpi-label" style="font-size: 0.9rem;">Pending Orders</div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
            <div class="kpi-card h-100">
                <div class="text-center">
                    <div class="mb-2" style="color: var(--success);">
                        <i class="fas fa-cash-register fa-2x"></i>
                    </div>
                    <div class="kpi-value" style="color: var(--success); font-size: 1.8rem;">
                        <?= $data['system_stats']['daily_sales'] ?? 0 ?>
                    </div>
                    <div class="kpi-label" style="font-size: 0.9rem;">Today's Sales</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bot Control Panel -->
    <div class="row">
        <?php if (isset($data['bots']) && !empty($data['bots'])): ?>
            <?php foreach ($data['bots'] as $botId => $bot): ?>
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card-theme h-100 bot-card" data-bot-id="<?= $bot['id'] ?>">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="<?= $bot['icon'] ?> mr-2 text-<?= $bot['color'] ?>"></i>
                                        <?= htmlspecialchars($bot['name']) ?>
                                    </h6>
                                </div>
                                <div class="col-auto">
                                    <span class="badge badge-secondary bot-status-badge" id="status-<?= $bot['id'] ?>">
                                        Inactive
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                <?= htmlspecialchars($bot['description']) ?>
                            </p>

                            <!-- Bot Controls -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <button class="btn btn-success btn-sm w-100 start-bot-btn" 
                                            data-bot-id="<?= $bot['id'] ?>">
                                        <i class="fas fa-play mr-1"></i>Start
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-danger btn-sm w-100 stop-bot-btn" 
                                            data-bot-id="<?= $bot['id'] ?>" disabled>
                                        <i class="fas fa-pause mr-1"></i>Pause
                                    </button>
                                </div>
                            </div>

                            <!-- Bot Settings -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="small text-muted">Interval (seconds)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control bot-interval" 
                                               value="<?= $bot['interval'] ?>" 
                                               min="5" max="300"
                                               data-bot-id="<?= $bot['id'] ?>">
                                        <div class="input-group-append">
                                            <span class="input-group-text">sec</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bot Statistics -->
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <div class="font-weight-bold text-primary" id="actions-<?= $bot['id'] ?>">0</div>
                                        <small class="text-muted">Actions</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <div class="font-weight-bold text-info" id="uptime-<?= $bot['id'] ?>">00:00</div>
                                        <small class="text-muted">Uptime</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mt-3">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-<?= $bot['color'] ?>" 
                                         id="progress-<?= $bot['id'] ?>" 
                                         style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Bot Activity Log -->
                        <div class="card-footer p-2">
                            <div class="bot-activity-log" id="log-<?= $bot['id'] ?>" 
                                 style="max-height: 120px; overflow-y: auto; font-size: 0.75rem;">
                                <div class="text-muted text-center py-2">
                                    <i class="fas fa-robot"></i> Bot ready to start...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Global Activity Feed and Pricing Logic Panel -->
    <div class="row mt-4">
        <!-- Global Activity Feed (Left Side) -->
        <div class="col-lg-8">
            <div class="card-theme">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0 font-weight-bold">
                                <i class="fas fa-stream mr-2"></i>Real-time Activity Feed
                            </h5>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-secondary btn-sm" onclick="clearActivityFeed()">
                                <i class="fas fa-trash mr-1"></i>Clear
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="globalActivityFeed" style="max-height: 400px; overflow-y: auto;">
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <div>Waiting for bot activities...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Pricing Bot Logic panel moved to admin/pricing_dashboard.php -->
    </div>
</div>

<!-- Bot Activity Modal -->
<div class="modal fade" id="botActivityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-robot mr-2"></i>Bot Activity Details
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="botActivityModalBody">
                <!-- Dynamic content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Pass data to JavaScript -->
<script>
    // Global configuration for bot dashboard
    window.URLROOT = '<?= URLROOT ?>';
    window.botConfigs = {
        <?php foreach ($data['bots'] as $botId => $bot): ?>
        '<?= $bot['id'] ?>': {
            id: '<?= $bot['id'] ?>',
            name: '<?= htmlspecialchars($bot['name']) ?>',
            interval: <?= $bot['interval'] ?>
        }<?= array_key_last($data['bots']) !== $botId ? ',' : '' ?>
        <?php endforeach; ?>
    };
</script>

<!-- Pricing Logic Panel Styling -->
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
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

.pricing-logic-badge {
    font-size: 0.75rem;
    padding: 2px 6px;
}
</style>

<!-- Load Bot Dashboard JavaScript -->
<script>
// Load bot-dashboard.js only after jQuery has been loaded to avoid "$ is not defined" errors
(function loadBotDashboard() {
    function inject() {
        var s = document.createElement('script');
        s.src = '<?= URLROOT ?>/public/js/bot-dashboard.js';
        s.defer = true;
        document.body.appendChild(s);
    }

    if (typeof window.jQuery !== 'undefined' || typeof window.$ !== 'undefined') {
        inject();
        return;
    }

    var checkInterval = setInterval(function () {
        if (typeof window.jQuery !== 'undefined' || typeof window.$ !== 'undefined') {
            clearInterval(checkInterval);
            inject();
        }
    }, 100);
})();
</script>

<!-- Lightweight front-end diagnostic widget (temporary) -->
<script>
(function frontEndBotDiagnostics(){
    function ensurePanel(){
        var e = document.getElementById('bot-debug-status');
        if (!e) {
            e = document.createElement('div');
            e.id = 'bot-debug-status';
            e.style.position = 'fixed';
            e.style.right = '12px';
            e.style.bottom = '12px';
            e.style.zIndex = 99999;
            e.style.background = 'rgba(0,0,0,0.7)';
            e.style.color = '#fff';
            e.style.padding = '8px 10px';
            e.style.fontSize = '12px';
            e.style.borderRadius = '6px';
            e.style.boxShadow = '0 2px 8px rgba(0,0,0,0.3)';
            document.body.appendChild(e);
        }
        return e;
    }

    function checkOnce(){
        var panel = ensurePanel();
        var jq = (typeof window.$ !== 'undefined' && window.$.fn) ? window.$.fn.jquery : 'none';
        var startBtns = (typeof window.$ !== 'undefined') ? window.$('.start-bot-btn').length : 'no-jq';
        var handlers = 'n/a';
        if (typeof window.$ !== 'undefined' && startBtns > 0) {
            try {
                var ev = window.$._data(window.$('.start-bot-btn')[0], 'events');
                handlers = ev ? Object.keys(ev).join(',') : 'none';
            } catch (err) { handlers = 'err'; }
        }
        var bd = (typeof window.botDashboard !== 'undefined') ? 'ready' : 'not-ready';
        var txt = 'jQuery: ' + jq + ' | startBtns: ' + startBtns + ' | handlers: ' + handlers + ' | botDashboard: ' + bd;
        panel.textContent = txt;
        console.log('[BotDiag] ' + txt);
    }

    // Run checks a few times to catch later initialization
    setTimeout(checkOnce, 800);
    setTimeout(checkOnce, 2000);
    setTimeout(checkOnce, 5000);
})();
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
