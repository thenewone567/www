<?php
$pageTitle = 'Admin Panel';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<!-- Styles removed in favor of unified stylesheet: `public/css/app-unified.css` -->
<!-- Use existing utility classes (card-theme, btn-theme, text-*, nav-*, etc.) from the unified CSS file -->

<div class="container-fluid page-top-area mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-shield-alt mr-2"></i>Admin Panel
            </h1>
            <small class="text-muted">System Administration & Management</small>
        </div>
    </div>
</div>

<!-- Navigation: Icon-card grid (uses unified CSS utilities) -->
<div class="container-fluid mb-4">
    <div class="row align-items-stretch admin-action-grid">
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a class="nav-link card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center active"
                id="dashboard-tab" data-toggle="tab" href="#dashboard" role="tab" aria-controls="dashboard"
                aria-selected="true">
                <div class="text-primary mb-2"><i class="fas fa-tachometer-alt fa-2x"></i></div>
                <div class="font-weight-bold">Dashboard</div>
                <small class="text-muted">Overview</small>
            </a>
        </div>

        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a class="card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center nav-link"
                href="<?= URLROOT ?>/admin/priceManagement" role="button">
                <div class="text-success mb-2"><i class="fas fa-dollar-sign fa-2x"></i></div>
                <div class="font-weight-bold">Price Management</div>
                <small class="text-muted">Pricing rules & audits</small>
            </a>
        </div>

        <!-- Price Bot card (scheduled pricing adjustments) -->
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a class="card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center nav-link"
                href="<?= URLROOT ?>/admin/pricing_dashboard" role="button">
                <div class="text-info mb-2"><i class="fas fa-robot fa-2x"></i></div>
                <div class="font-weight-bold">Price Bot</div>
                <small class="text-muted">Automated pricing</small>
            </a>
        </div>

        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a class="card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center nav-link"
                href="<?= URLROOT ?>/admin/users" role="link">
                <div class="text-primary mb-2"><i class="fas fa-users fa-2x"></i></div>
                <div class="font-weight-bold">Users</div>
                <small class="text-muted">Manage accounts</small>
            </a>
        </div>

        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a class="nav-link card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center"
                id="roles-tab" data-toggle="tab" href="#roles" role="tab" aria-controls="roles" aria-selected="false">
                <div class="text-info mb-2"><i class="fas fa-user-tag fa-2x"></i></div>
                <div class="font-weight-bold">Roles</div>
                <small class="text-muted">Role definitions</small>
            </a>
        </div>

        <!-- Permissions nav button removed: permissions management moved to dedicated page -->

        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a class="card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center nav-link"
                href="<?= URLROOT ?>/admin/activityLogs" role="button">
                <div class="text-muted mb-2"><i class="fas fa-history fa-2x"></i></div>
                <div class="font-weight-bold">Audit trails</div>
                <small class="text-muted">Activity Logs</small>
            </a>
        </div>

        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a class="nav-link card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center"
                id="settings-tab" data-toggle="tab" href="#settings" role="tab" aria-controls="settings"
                aria-selected="false">
                <div class="text-dark mb-2"><i class="fas fa-cog fa-2x"></i></div>
                <div class="font-weight-bold">Settings</div>
                <small class="text-muted">System prefs</small>
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="tab-content" id="adminTabContent">

        <!-- DASHBOARD TAB -->
        <div class="tab-pane fade show active admin-section" id="dashboard" role="tabpanel">
            <!-- System Statistics -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card-theme h-100">
                        <div class="card-body text-center">
                            <div class="text-primary mb-3">
                                <i class="fas fa-users fa-3x"></i>
                            </div>
                            <h2 class="mb-2 font-weight-bold text-primary">
                                <?= $data['stats']['total_users'] ?? 0 ?>
                            </h2>
                            <p class="text-muted mb-0 font-weight-600">Total Users</p>
                            <small class="text-muted">Registered</small>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card-theme h-100">
                        <div class="card-body text-center">
                            <div class="text-success mb-3">
                                <i class="fas fa-user-check fa-3x"></i>
                            </div>
                            <h2 class="mb-2 font-weight-bold text-success">
                                <?= $data['stats']['active_users'] ?? 0 ?>
                            </h2>
                            <p class="text-muted mb-0 font-weight-600">Active Users</p>
                            <small class="text-muted">Online</small>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card-theme h-100">
                        <div class="card-body text-center">
                            <div class="text-info mb-3">
                                <i class="fas fa-user-tag fa-3x"></i>
                            </div>
                            <h2 class="mb-2 font-weight-bold text-info">
                                <?= $data['stats']['total_roles'] ?? 0 ?>
                            </h2>
                            <p class="text-muted mb-0 font-weight-600">System Roles</p>
                            <small class="text-muted">Configured</small>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card-theme h-100">
                        <div class="card-body text-center">
                            <div class="text-warning mb-3">
                                <i class="fas fa-sign-in-alt fa-3x"></i>
                            </div>
                            <h2 class="mb-2 font-weight-bold text-warning">
                                <?= $data['stats']['recent_logins'] ?? 0 ?>
                            </h2>
                            <p class="text-muted mb-0 font-weight-600">Recent Logins</p>
                            <small class="text-muted">Last 7 days</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity removed: moved to a dedicated activity panel or API-driven widget -->
        </div>

        <!-- PRICE MANAGEMENT TAB - Moved to separate page -->
        <!-- Price Management is now available at <?= URLROOT ?>/admin/priceManagement -->

        <!-- USERS TAB removed: moved to a separate page or view -->
        <!-- User management UI has been relocated to <?= URLROOT ?>/admin/users or a dedicated view -->

        <!-- ROLES TAB -->
        <div class="tab-pane fade admin-section" id="roles" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card-theme">
                        <div
                            class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-user-tag mr-2"></i>System Roles
                            </h5>
                            <button class="btn-theme btn-primary-theme" data-toggle="modal" data-target="#addRoleModal">
                                <i class="fas fa-plus mr-2"></i>Add New Role
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Role Name</th>
                                            <th>Description</th>
                                            <th>Users</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($data['roles']) && !empty($data['roles'])): ?>
                                            <?php foreach ($data['roles'] as $role): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= ucfirst($role->role_name ?? 'Unknown') ?></strong>
                                                    </td>
                                                    <td><?= htmlspecialchars($role->description ?? 'No description') ?></td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            <?= $role->user_count ?? 0 ?> users
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-outline-primary"
                                                                onclick="editRole(<?= $role->role_id ?? 0 ?>)">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger"
                                                                onclick="deleteRole(<?= $role->role_id ?? 0 ?>)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    <i class="fas fa-user-tag fa-2x mb-2"></i>
                                                    <p>No roles found</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card-theme">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-shield-alt mr-2"></i>Role Permissions
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Select a role to view and manage its permissions.</p>
                            <div id="rolePermissions">
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-hand-pointer fa-2x mb-2"></i>
                                    <p>Click on a role to view permissions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PERMISSIONS TAB removed: User permissions management moved to dedicated page -->
        <!-- User Permissions Management is now available at <?= URLROOT ?>/admin/permissions or a dedicated view -->

        <!-- ACTIVITY LOGS TAB removed: Activity logs moved to dedicated page -->
        <!-- Activity Logs are now available at <?= URLROOT ?>/admin/activity_logs -->

        <!-- SETTINGS TAB -->
        <div class="tab-pane fade admin-section" id="settings" role="tabpanel">
            <form action="<?= URLROOT ?>/admin/settings" method="POST" id="settingsForm">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- General Settings -->
                        <div class="card-theme mb-4 border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-cog mr-2"></i>General Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="site_name">Site Name</label>
                                        <input type="text" class="form-theme" id="site_name" name="site_name"
                                            value="<?= $data['settings']['site_name'] ?? 'Hardware Store' ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="site_description">Site Description</label>
                                        <input type="text" class="form-theme" id="site_description"
                                            name="site_description"
                                            value="<?= $data['settings']['site_description'] ?? 'Professional Hardware Management' ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="admin_email">Admin Email</label>
                                        <input type="email" class="form-theme" id="admin_email" name="admin_email"
                                            value="<?= $data['settings']['admin_email'] ?? 'admin@example.com' ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="timezone">Timezone</label>
                                        <select class="form-theme" id="timezone" name="timezone">
                                            <option value="UTC">UTC</option>
                                            <option value="America/New_York">Eastern Time</option>
                                            <option value="America/Chicago">Central Time</option>
                                            <option value="America/Denver">Mountain Time</option>
                                            <option value="America/Los_Angeles">Pacific Time</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Settings -->
                        <div class="card-theme mb-4 border-0 shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-server mr-2"></i>System Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="max_upload_size">Max Upload Size (MB)</label>
                                        <input type="number" class="form-theme" id="max_upload_size"
                                            name="max_upload_size"
                                            value="<?= $data['settings']['max_upload_size'] ?? '10' ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="session_timeout">Session Timeout (minutes)</label>
                                        <input type="number" class="form-theme" id="session_timeout"
                                            name="session_timeout"
                                            value="<?= $data['settings']['session_timeout'] ?? '30' ?>">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enable_logging"
                                                name="enable_logging" <?= ($data['settings']['enable_logging'] ?? true) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="enable_logging">
                                                Enable Activity Logging
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="maintenance_mode"
                                                name="maintenance_mode" <?= ($data['settings']['maintenance_mode'] ?? false) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="maintenance_mode">
                                                Maintenance Mode
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Actions -->
                        <div class="card-theme mb-4 border-0 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-save mr-2"></i>Actions</h5>
                            </div>
                            <div class="card-body">
                                <button type="submit" class="btn btn-success btn-block mb-2">
                                    <i class="fas fa-save mr-2"></i>Save Settings
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-block mb-2"
                                    onclick="resetToDefaults()">
                                    <i class="fas fa-undo mr-2"></i>Reset to Defaults
                                </button>
                                <button type="button" class="btn btn-outline-info btn-block mb-2"
                                    onclick="exportSettings()">
                                    <i class="fas fa-download mr-2"></i>Export Settings
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-block"
                                    onclick="importSettings()">
                                    <i class="fas fa-upload mr-2"></i>Import Settings
                                </button>
                            </div>
                        </div>

                        <!-- System Info -->
                        <div class="card-theme">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>System Information</h5>
                            </div>
                            <div class="card-body">
                                <small class="text-muted">
                                    <strong>Version:</strong> 1.0.0<br>
                                    <strong>PHP Version:</strong> <?= phpversion() ?><br>
                                    <strong>Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?><br>
                                    <strong>Database:</strong> MySQL<br>
                                    <strong>Last Backup:</strong> <?= $data['settings']['last_backup'] ?? 'Never' ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Admin Dashboard JavaScript
    $(document).ready(function () {
        console.log('Document ready - jQuery version:', $.fn.jquery);
        console.log('Bootstrap tab function available:', typeof $().tab);

        // Test Bootstrap tab functionality
        if (typeof $().tab === 'undefined') {
            console.error('Bootstrap tabs not loaded!');
            alert('Bootstrap tabs not loaded! The tab functionality will not work.');
        }

        // Initialize tab switching
        initializeTabs();

        // Initialize DataTables for better table functionality with a slight delay
        setTimeout(function () {
            initializeDataTables();
        }, 100);

        // Load initial data
        loadDashboardData();

        // Auto-refresh dashboard data every 30 seconds
        setInterval(loadDashboardData, 30000);

        // Add debug click handler to the logs tab specifically
        $('#logs-tab').on('click', function (e) {
            console.log('Direct logs tab click detected!');
            console.log('Element:', this);
            console.log('Href:', $(this).attr('href'));
            console.log('Data-toggle:', $(this).attr('data-toggle'));
        });

        // Add debug click handler to force Bootstrap tab show for logs (non-invasive)
        $('#logs-tab').on('click.forceShow', function (e) {
            try {
                console.log('Force-show handler: attempting $(this).tab("show")');
                $(this).tab('show');
                console.log('Force-show handler: tab("show") called successfully');

                // Ensure the target pane is visible and active (fallback)
                const target = $(this).attr('href');
                const pane = $(target);
                if (pane.length) {
                    if (!pane.hasClass('show') || !pane.hasClass('active')) {
                        pane.addClass('show active');
                        console.log('Force-show handler: added show/active classes to pane:', target);
                    }
                    // Brief visual highlight
                    pane.css('outline', '3px solid rgba(0,123,255,0.25)');
                    setTimeout(() => pane.css('outline', ''), 1500);

                    // Scroll into view
                    $('html, body').animate({ scrollTop: pane.offset().top - 80 }, 250);
                }
            } catch (err) {
                console.error('Force-show handler error:', err);
            }
        });
    });

    function initializeDataTables() {
        if ($.fn.DataTable) {
            try {
                // Initialize users table only if it exists and has proper structure
                const usersTable = $('#usersTable');
                if (usersTable.length > 0 && !$.fn.DataTable.isDataTable('#usersTable')) {
                    // Check if table has proper header structure
                    const headerCells = usersTable.find('thead tr:first th').length;
                    const bodyRows = usersTable.find('tbody tr').length;
                    const firstRowCells = usersTable.find('tbody tr:first td').length;

                    console.log('Users table - Header cells:', headerCells, 'Body rows:', bodyRows, 'First row cells:', firstRowCells);

                    if (headerCells === 6 && (bodyRows === 0 || firstRowCells === 6 || firstRowCells === 1)) {
                        try {
                            usersTable.DataTable({
                                pageLength: 10,
                                responsive: true,
                                order: [[0, 'asc']],
                                columnDefs: [
                                    { orderable: false, targets: [5] } // Disable sorting on Actions column
                                ],
                                language: {
                                    emptyTable: "No users found",
                                    zeroRecords: "No matching users found"
                                },
                                // Add additional safety options
                                autoWidth: false,
                                processing: true,
                                deferRender: true,
                                destroy: true // Allow re-initialization
                            });
                            console.log('Users DataTable initialized successfully');
                        } catch (userTableError) {
                            console.error('Users DataTable initialization error:', userTableError);
                        }
                    } else {
                        console.warn('Users table structure mismatch - Header:', headerCells, 'Body cells:', firstRowCells);
                    }
                }

                // Activity logs table initialization removed: Activity logs moved to dedicated page
            } catch (error) {
                console.error('DataTable initialization error:', error);
            }
        }
    }

    function initializeTabs() {
        console.log('Initializing tabs...');

        // Let Bootstrap handle the tab switching, just listen for the events
        $('.nav-link[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('href');
            console.log('Tab shown event fired:', target);

            // Load tab-specific data when tab is shown
            switch (target) {
                case '#users':
                    loadUsers();
                    break;
                case '#roles':
                    loadRoles();
                    break;
                // Activity logs removed: now available on dedicated page
            }
        });

        // Also add click handler as backup but don't prevent default
        $('.nav-link[data-toggle="tab"]').on('click', function (e) {
            const target = $(this).attr('href');
            console.log('Tab clicked:', target);

            // Let Bootstrap handle the tab switching naturally
            // Just trigger our custom logic after a short delay
            setTimeout(() => {
                switch (target) {
                    case '#users':
                        loadUsers();
                        break;
                    case '#roles':
                        loadRoles();
                        break;
                    // Activity logs removed: now available on dedicated page
                }
            }, 100);
        });
    }

    function loadDashboardData() {
        // Update system statistics
        $.ajax({
            url: '<?= URLROOT ?>/admin/getSystemStatsAjax',
            method: 'GET',
            success: function (response) {
                if (response.success) {
                    updateSystemStats(response.data);
                }
            },
            error: function () {
                console.error('Failed to load dashboard data');
            }
        });
    }

    function updateSystemStats(stats) {
        // Update dashboard statistics
        if (stats.total_products !== undefined) {
            $('#totalProducts').text(stats.total_products);
        }
        if (stats.total_users !== undefined) {
            $('#totalUsers').text(stats.total_users);
        }
        if (stats.low_stock_count !== undefined) {
            $('#lowStockCount').text(stats.low_stock_count);
        }
        if (stats.total_orders !== undefined) {
            $('#totalOrders').text(stats.total_orders);
        }
    }

    // Tab switching function for quick actions
    function switchTab(tabName) {
        console.log('Switching to tab:', tabName);
        $(`#${tabName}-tab`).tab('show');
    }

    // User Management Functions
    function loadUsers() {
        console.log('Loading users...');
        // Additional user loading logic can be added here
    }

    function editUser(userId) {
        console.log('Initiating user edit for ID:', userId);
        // Implementation for editing user
        $('#addUserModal').modal('show');
        // Load user data and populate form
    }

    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            console.log('Initiating user deletion for ID:', userId);

            $.ajax({
                url: '<?= URLROOT ?>/admin/deleteUser',
                method: 'POST',
                data: { user_id: userId },
                success: function (response) {
                    if (response.success) {
                        alert('User deleted successfully!');
                        location.reload();
                    } else {
                        alert('Failed to delete user: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function () {
                    alert('Deletion failed! Please try again.');
                }
            });
        }
    }

    // Role Management Functions
    function loadRoles() {
        console.log('Loading roles...');
    }

    function editRole(roleId) {
        console.log('Initiating role edit for ID:', roleId);
        // Implementation for editing role
    }

    function deleteRole(roleId) {
        if (confirm('Are you sure you want to delete this role?')) {
            console.log('Initiating role deletion for ID:', roleId);

            $.ajax({
                url: '<?= URLROOT ?>/admin/deleteRole',
                method: 'POST',
                data: { role_id: roleId },
                success: function (response) {
                    if (response.success) {
                        alert('Role deleted successfully!');
                        location.reload();
                    } else {
                        alert('Failed to delete role: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function () {
                    alert('Deletion failed! Please try again.');
                }
            });
        }
    }

    // Permission Management Functions removed: permissions moved to dedicated page

    // Activity Log Functions removed: Activity logs moved to dedicated page
    // Activity Logs functionality is now available at <?= URLROOT ?>/admin/activity_logs

    // Settings Functions
    function resetToDefaults() {
        if (confirm('Are you sure you want to reset all settings to defaults?')) {
            console.log('Initiating settings reset...');

            $.ajax({
                url: '<?= URLROOT ?>/admin/resetSettings',
                method: 'POST',
                success: function (response) {
                    if (response.success) {
                        alert('Settings reset to defaults successfully!');
                        location.reload();
                    } else {
                        alert('Failed to reset settings: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function () {
                    alert('Settings reset failed! Please try again.');
                }
            });
        }
    }

    function exportSettings() {
        console.log('Initiating settings export...');
        window.location.href = '<?= URLROOT ?>/admin/exportSettings';
    }

    function importSettings() {
        console.log('Initiating settings import...');
        // Create file input for import
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.json';
        input.onchange = function (e) {
            const file = e.target.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('settings_file', file);

                $.ajax({
                    url: '<?= URLROOT ?>/admin/importSettings',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            alert('Settings imported successfully!');
                            location.reload();
                        } else {
                            alert('Failed to import settings: ' + (response.message || 'Unknown error'));
                        }
                    },
                    error: function () {
                        alert('Settings import failed! Please try again.');
                    }
                });
            }
        };
        input.click();
    }

    // Form submission with alerts
    $('#settingsForm').on('submit', function (e) {
        e.preventDefault();
        console.log('Initiating settings save...');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                if (response.success) {
                    alert('Settings saved successfully!');
                } else {
                    alert('Failed to save settings: ' + (response.message || 'Unknown error'));
                }
            },
            error: function () {
                alert('Settings save failed! Please try again.');
            }
        });
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>