<?php
$pageTitle = 'Activity Logs - Admin Panel';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<!-- Immediate Error Suppression for Browser Extensions -->
<script>
    (function () {
        // Suppress runtime.lastError immediately
        if (typeof chrome !== 'undefined' && chrome.runtime) {
            const originalAddListener = chrome.runtime.onMessage.addListener;
            chrome.runtime.onMessage.addListener = function (callback) {
                return originalAddListener.call(this, function (...args) {
                    try {
                        return callback(...args);
                    } catch (e) {
                        // Suppress extension errors
                        return false;
                    }
                });
            };
        }

        // Global error handler
        window.addEventListener('error', function (e) {
            if (e.message && (
                e.message.includes('message port closed') ||
                e.message.includes('Extension context invalidated') ||
                e.message.includes('runtime.lastError')
            )) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        }, true);

        // Console error suppression for extension messages
        const originalConsoleError = console.error;
        console.error = function (...args) {
            const message = args.join(' ');
            if (message.includes('runtime.lastError') ||
                message.includes('message port closed') ||
                message.includes('Extension context invalidated') ||
                message.includes('Unchecked runtime.lastError')) {
                return; // Suppress these specific errors
            }
            originalConsoleError.apply(console, args);
        };

        // Additional suppression for browser-level errors
        const originalConsoleWarn = console.warn;
        console.warn = function (...args) {
            const message = args.join(' ');
            if (message.includes('runtime.lastError') || message.includes('message port closed')) {
                return;
            }
            originalConsoleWarn.apply(console, args);
        };
    })();
</script>

<div class="container-fluid page-top-area mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-8">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-history mr-2"></i>Activity Logs
            </h1>
            <small class="text-muted">System Activity & Audit Trails - Compliance Ready</small>
        </div>
        <div class="col-12 col-md-12 d-flex justify-content-end align-items-center">
            <div class="btn-group mr-3" role="group">
                <button class="btn btn-outline-primary" onclick="filterByPeriod('today')">
                    <i class="fas fa-calendar-day mr-1"></i>Today
                </button>
                <button class="btn btn-outline-primary" onclick="filterByPeriod('week')">
                    <i class="fas fa-calendar-week mr-1"></i>Week
                </button>
                <button class="btn btn-outline-primary" onclick="filterByPeriod('month')">
                    <i class="fas fa-calendar-alt mr-1"></i>Month
                </button>
            </div>
            <a href="<?= URLROOT ?>/admin" class="btn btn-outline-secondary ml-auto">
                <i class="fas fa-arrow-left mr-1"></i>Back
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Enhanced Filters Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card-theme">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <label class="mb-0 mr-2 font-weight-bold">Filter by Action:</label>
                        <select id="actionFilter" class="form-control form-control-sm" style="width: auto;">
                            <option value="">All Actions</option>
                            <option value="CREATE">Create</option>
                            <option value="UPDATE">Update</option>
                            <option value="DELETE">Delete</option>
                            <option value="LOGIN">Login</option>
                            <option value="LOGOUT">Logout</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-theme">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <label class="mb-0 mr-2 font-weight-bold">Filter by Entity:</label>
                        <select id="entityFilter" class="form-control form-control-sm" style="width: auto;">
                            <option value="">All Entities</option>
                            <option value="Product">Products</option>
                            <option value="Sale">Sales</option>
                            <option value="Purchase">Purchases</option>
                            <option value="Supplier">Suppliers</option>
                            <option value="Inventory">Inventory</option>
                            <option value="Expense">Expenses</option>
                            <option value="System">System</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Activity Logs Table -->
    <div class="card-theme">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history mr-2"></i>Activity Logs - Audit Trail
            </h5>
            <div class="d-flex align-items-center">
                <span class="badge badge-info mr-3">
                    <i class="fas fa-database mr-1"></i>
                    Total: <?= $data['total_activities'] ?? 0 ?> entries
                </span>
                <span class="badge badge-success">
                    <i class="fas fa-shield-alt mr-1"></i>Government Compliance
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="activityLogsTable">
                    <thead class="thead-light">
                        <tr>
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Action</th>
                            <th>Entity</th>
                            <th>Entity ID</th>
                            <th>Details</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data['activity_logs']) && !empty($data['activity_logs'])): ?>
                            <?php foreach ($data['activity_logs'] as $log): ?>
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">
                                            <?= !empty($log->created_at) ? date('Y-m-d H:i', strtotime($log->created_at)) : 'N/A' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($log->full_name ?? $log->username ?? 'System') ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?= htmlspecialchars($log->role ?? 'N/A') ?></span>
                                    </td>
                                    <td>
                                        <span class="activity-badge badge badge-primary">
                                            <?= htmlspecialchars(strtoupper($log->action ?? 'Unknown')) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="text-muted"><?= htmlspecialchars($log->entity ?? $log->target_type ?? 'System') ?></span>
                                    </td>
                                    <td>
                                        <code
                                            class="text-primary"><?= htmlspecialchars($log->entity_id ?? $log->target_id ?? '-') ?></code>
                                    </td>
                                    <td>
                                        <small
                                            class="text-secondary"><?= htmlspecialchars($log->details ?? 'No details') ?></small>
                                    </td>
                                    <td>
                                        <code class="text-muted"><?= htmlspecialchars($log->ip_address ?? 'N/A') ?></code>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-history fa-2x mb-2"></i>
                                    <p>No activity logs found</p>
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
    // Enhanced error suppression for browser extensions
    (function () {
        // Override chrome.runtime.lastError property
        if (typeof chrome !== 'undefined' && chrome.runtime) {
            try {
                Object.defineProperty(chrome.runtime, 'lastError', {
                    get: function () { return undefined; },
                    set: function () { /* do nothing */ },
                    configurable: false
                });
            } catch (e) {
                // If we can't override the property, catch errors another way
            }
        }

        // Comprehensive error handling
        window.addEventListener('error', function (e) {
            if (e.message && (
                e.message.includes('message port closed') ||
                e.message.includes('runtime.lastError') ||
                e.message.includes('Extension context')
            )) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        }, true);

        // Handle unhandled promise rejections from extensions
        window.addEventListener('unhandledrejection', function (e) {
            if (e.reason && e.reason.message &&
                e.reason.message.includes('message port closed')) {
                e.preventDefault();
                return false;
            }
        });
    })();

    $(document).ready(function () {
        // Initialize DataTables for activity logs if table has data
        if ($('#activityLogsTable tbody tr').length > 0 && !$('#activityLogsTable tbody tr').first().find('td[colspan="8"]').length) {
            $('#activityLogsTable').DataTable({
                responsive: true,
                order: [[0, 'desc']], // Sort by Date & Time descending
                pageLength: 25,
                columnDefs: [
                    { width: "15%", targets: 0 }, // Date & Time
                    { width: "12%", targets: 1 }, // User
                    { width: "8%", targets: 2 },  // Role
                    { width: "10%", targets: 3 }, // Action
                    { width: "10%", targets: 4 }, // Entity
                    { width: "8%", targets: 5 },  // Entity ID
                    { width: "25%", targets: 6 }, // Details
                    { width: "12%", targets: 7 }  // IP Address
                ],
                language: {
                    emptyTable: "No activity logs found",
                    zeroRecords: "No matching activity logs found"
                }
            });
        }

        // Load activity logs on page load
        loadActivityLogs();
    });

    // Activity Log Functions
    function loadActivityLogs() {
        // Show loading indicator
        $('#activityLogsTable tbody').html(`
        <tr>
            <td colspan="8" class="text-center py-4">
                <i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i>
                <p>Loading activity logs...</p>
            </td>
        </tr>
    `);        // Activity logs are already loaded on page load via PHP
        // Just make sure the table is visible and scroll to it
        $('#activityLogsTable').show();

        // Make an AJAX call to refresh the data
        $.ajax({
            url: '<?= URLROOT ?>/admin/getActivityLogsAjax',
            method: 'GET',
            success: function (response) {
                if (response.success && response.data) {
                    updateActivityLogsTable(response.data);
                } else {
                    $('#activityLogsTable tbody').html(`
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <p>No activity logs available</p>
                        </td>
                    </tr>
                `);
                }
            },
            error: function (xhr, status, error) {
                $('#activityLogsTable tbody').html(`
                <tr>
                    <td colspan="8" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>Error loading activity logs: ${error}</p>
                        <small>Using existing data if available</small>
                    </td>
                </tr>
            `);
            }
        });
    }

    function updateActivityLogsTable(logs) {
        const tbody = $('#activityLogsTable tbody');
        tbody.empty();

        if (logs && logs.length > 0) {
            logs.forEach(function (log) {
                const formattedDate = log.created_at ?
                    new Date(log.created_at).toLocaleString('en-US', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    }).replace(/(\d+)\/(\d+)\/(\d+),/, '$3-$1-$2') : 'N/A';

                const fullName = log.full_name || log.username || 'System';
                const role = log.role || 'N/A';
                const action = (log.action || 'Unknown').toUpperCase();
                const entity = log.entity || log.target_type || 'System';
                const entityId = log.entity_id || log.target_id || '-';
                const details = log.details || 'No details';
                const ipAddress = log.ip_address || 'N/A';

                const row = `
                <tr>
                    <td><div class="font-weight-bold">${escapeHtml(formattedDate)}</div></td>
                    <td><strong>${escapeHtml(fullName)}</strong></td>
                    <td><span class="badge badge-info">${escapeHtml(role)}</span></td>
                    <td><span class="activity-badge badge badge-primary">${escapeHtml(action)}</span></td>
                    <td><span class="text-muted">${escapeHtml(entity)}</span></td>
                    <td><code class="text-primary">${escapeHtml(entityId)}</code></td>
                    <td><small class="text-secondary">${escapeHtml(details)}</small></td>
                    <td><code class="text-muted">${escapeHtml(ipAddress)}</code></td>
                </tr>
            `;
                tbody.append(row);
            });
            // Activity logs table updated successfully
        } else {
            tbody.append(`
            <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                    <i class="fas fa-history fa-2x mb-2"></i>
                    <p>No activity logs found</p>
                </td>
            </tr>
        `);
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function filterByPeriod(period) {

        $.ajax({
            url: '<?= URLROOT ?>/admin/filterLogs',
            method: 'POST',
            data: { period: period },
            success: function (response) {
                if (response.success) {
                    // Reload the logs table
                    location.reload();
                }
            }
        });
    }

    // Enhanced filtering with dropdown filters
    $('#actionFilter, #entityFilter').on('change', function () {
        const table = $('#activityLogsTable').DataTable();

        // Get filter values
        const actionFilter = $('#actionFilter').val();
        const entityFilter = $('#entityFilter').val();

        // Clear existing search
        table.search('').columns().search('').draw();

        // Apply filters
        if (actionFilter) {
            table.column(3).search(actionFilter, false, true);
        }
        if (entityFilter) {
            table.column(4).search(entityFilter, false, true);
        }

        // Apply all filters
        table.draw();
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>