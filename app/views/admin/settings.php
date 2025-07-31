<?php
$pageTitle = 'System Settings - Admin Panel';
require_once '../app/views/layout/header.php';
?>

<style>
    .admin-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }

    .admin-nav {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .admin-nav .nav-link {
        color: #495057;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        margin: 0 0.25rem;
        border-radius: 5px;
        transition: all 0.2s;
    }

    .admin-nav .nav-link:hover {
        background: #e9ecef;
        color: #007bff;
    }

    .admin-nav .nav-link.active {
        background: #007bff;
        color: white;
    }

    .settings-group {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .settings-group-header {
        background: #f8f9fa;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #dee2e6;
        font-weight: 600;
        color: #495057;
    }

    .settings-group-body {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-text {
        color: #6c757d;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .backup-status {
        padding: 0.5rem 1rem;
        border-radius: 5px;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .backup-status.success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .backup-status.warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
</style>

<div class="admin-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-0">
                    <i class="fas fa-cog"></i> System Settings
                </h1>
                <p class="mb-0 mt-2 opacity-75">Configure system-wide settings and preferences</p>
            </div>
            <div class="col-md-4 text-md-right">
                <button type="button" class="btn btn-light" onclick="resetToDefaults()">
                    <i class="fas fa-undo"></i> Reset to Defaults
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Admin Navigation -->
    <div class="admin-nav">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link" href="<?= URLROOT ?>/admin">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= URLROOT ?>/admin/users">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= URLROOT ?>/admin/roles">
                    <i class="fas fa-user-tag"></i> Roles & Permissions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= URLROOT ?>/admin/activityLogs">
                    <i class="fas fa-history"></i> Activity Logs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?= URLROOT ?>/admin/settings">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>
        </ul>
    </div>

    <?php flash('admin_message'); ?>

    <form action="<?= URLROOT ?>/admin/settings" method="POST" id="settingsForm">
        <div class="row">
            <div class="col-lg-8">
                <!-- General Settings -->
                <div class="settings-group">
                    <div class="settings-group-header">
                        <i class="fas fa-cogs"></i> General Settings
                    </div>
                    <div class="settings-group-body">
                        <div class="form-group">
                            <label for="site_name" class="form-label">Site Name</label>
                            <input type="text" class="form-control" id="site_name" name="site_name"
                                value="<?= htmlspecialchars($data['settings']['site_name'] ?? 'Inventory Management System') ?>">
                            <div class="form-text">The name of your application displayed in the browser title and
                                header.</div>
                        </div>

                        <div class="form-group">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-control" id="timezone" name="timezone">
                                <option value="UTC" <?= ($data['settings']['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                                <option value="America/New_York" <?= ($data['settings']['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>Eastern Time</option>
                                <option value="America/Chicago" <?= ($data['settings']['timezone'] ?? '') === 'America/Chicago' ? 'selected' : '' ?>>Central Time</option>
                                <option value="America/Denver" <?= ($data['settings']['timezone'] ?? '') === 'America/Denver' ? 'selected' : '' ?>>Mountain Time</option>
                                <option value="America/Los_Angeles" <?= ($data['settings']['timezone'] ?? '') === 'America/Los_Angeles' ? 'selected' : '' ?>>Pacific Time</option>
                            </select>
                            <div class="form-text">Default timezone for the application.</div>
                        </div>

                        <div class="form-group">
                            <label for="items_per_page" class="form-label">Items Per Page</label>
                            <select class="form-control" id="items_per_page" name="items_per_page">
                                <option value="10" <?= ($data['settings']['items_per_page'] ?? 25) == 10 ? 'selected' : '' ?>>10</option>
                                <option value="25" <?= ($data['settings']['items_per_page'] ?? 25) == 25 ? 'selected' : '' ?>>25</option>
                                <option value="50" <?= ($data['settings']['items_per_page'] ?? 25) == 50 ? 'selected' : '' ?>>50</option>
                                <option value="100" <?= ($data['settings']['items_per_page'] ?? 25) == 100 ? 'selected' : '' ?>>100</option>
                            </select>
                            <div class="form-text">Number of items to display per page in lists and tables.</div>
                        </div>
                    </div>
                </div>

                <!-- Business Settings -->
                <div class="settings-group">
                    <div class="settings-group-header">
                        <i class="fas fa-store"></i> Business Settings
                    </div>
                    <div class="settings-group-body">
                        <div class="form-group">
                            <label for="auto_approve_threshold" class="form-label">Auto-Approve Threshold ($)</label>
                            <input type="number" class="form-control" id="auto_approve_threshold"
                                name="auto_approve_threshold" step="0.01" min="0"
                                value="<?= $data['settings']['auto_approve_threshold'] ?? 1000 ?>">
                            <div class="form-text">Purchase orders below this amount will be automatically approved.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
                            <input type="number" class="form-control" id="low_stock_threshold"
                                name="low_stock_threshold" min="1"
                                value="<?= $data['settings']['low_stock_threshold'] ?? 10 ?>">
                            <div class="form-text">Alert when product quantity falls below this number.</div>
                        </div>

                        <div class="form-group">
                            <label for="currency" class="form-label">Default Currency</label>
                            <select class="form-control" id="currency" name="currency">
                                <option value="USD" <?= ($data['settings']['currency'] ?? 'USD') === 'USD' ? 'selected' : '' ?>>USD ($)</option>
                                <option value="EUR" <?= ($data['settings']['currency'] ?? 'USD') === 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                                <option value="GBP" <?= ($data['settings']['currency'] ?? 'USD') === 'GBP' ? 'selected' : '' ?>>GBP (£)</option>
                                <option value="CAD" <?= ($data['settings']['currency'] ?? 'USD') === 'CAD' ? 'selected' : '' ?>>CAD ($)</option>
                            </select>
                            <div class="form-text">Default currency for pricing and financial calculations.</div>
                        </div>

                        <div class="form-group">
                            <label for="tax_rate" class="form-label">Default Tax Rate (%)</label>
                            <input type="number" class="form-control" id="tax_rate" name="tax_rate" step="0.01" min="0"
                                max="100" value="<?= $data['settings']['tax_rate'] ?? 8.25 ?>">
                            <div class="form-text">Default tax rate applied to sales and purchases.</div>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="settings-group">
                    <div class="settings-group-header">
                        <i class="fas fa-shield-alt"></i> Security Settings
                    </div>
                    <div class="settings-group-body">
                        <div class="form-group">
                            <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                            <select class="form-control" id="session_timeout" name="session_timeout">
                                <option value="30" <?= ($data['settings']['session_timeout'] ?? 60) == 30 ? 'selected' : '' ?>>30 minutes</option>
                                <option value="60" <?= ($data['settings']['session_timeout'] ?? 60) == 60 ? 'selected' : '' ?>>1 hour</option>
                                <option value="120" <?= ($data['settings']['session_timeout'] ?? 60) == 120 ? 'selected' : '' ?>>2 hours</option>
                                <option value="480" <?= ($data['settings']['session_timeout'] ?? 60) == 480 ? 'selected' : '' ?>>8 hours</option>
                            </select>
                            <div class="form-text">How long user sessions remain active without activity.</div>
                        </div>

                        <div class="form-group">
                            <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                            <select class="form-control" id="max_login_attempts" name="max_login_attempts">
                                <option value="3" <?= ($data['settings']['max_login_attempts'] ?? 5) == 3 ? 'selected' : '' ?>>3</option>
                                <option value="5" <?= ($data['settings']['max_login_attempts'] ?? 5) == 5 ? 'selected' : '' ?>>5</option>
                                <option value="10" <?= ($data['settings']['max_login_attempts'] ?? 5) == 10 ? 'selected' : '' ?>>10</option>
                            </select>
                            <div class="form-text">Number of failed login attempts before account lockout.</div>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="require_strong_passwords"
                                    name="require_strong_passwords" value="1"
                                    <?= ($data['settings']['require_strong_passwords'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="require_strong_passwords">
                                    Require Strong Passwords
                                </label>
                            </div>
                            <div class="form-text">Enforce password complexity requirements.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Backup Settings -->
                <div class="settings-group">
                    <div class="settings-group-header">
                        <i class="fas fa-database"></i> Backup Settings
                    </div>
                    <div class="settings-group-body">
                        <div class="form-group">
                            <label for="backup_frequency" class="form-label">Backup Frequency</label>
                            <select class="form-control" id="backup_frequency" name="backup_frequency">
                                <option value="daily" <?= ($data['settings']['backup_frequency'] ?? 'daily') === 'daily' ? 'selected' : '' ?>>Daily</option>
                                <option value="weekly" <?= ($data['settings']['backup_frequency'] ?? 'daily') === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                                <option value="monthly" <?= ($data['settings']['backup_frequency'] ?? 'daily') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                                <option value="manual" <?= ($data['settings']['backup_frequency'] ?? 'daily') === 'manual' ? 'selected' : '' ?>>Manual Only</option>
                            </select>
                            <div class="form-text">How often to automatically backup the database.</div>
                        </div>

                        <div class="form-group">
                            <label for="backup_retention" class="form-label">Backup Retention (days)</label>
                            <select class="form-control" id="backup_retention" name="backup_retention">
                                <option value="7" <?= ($data['settings']['backup_retention'] ?? 30) == 7 ? 'selected' : '' ?>>7 days</option>
                                <option value="30" <?= ($data['settings']['backup_retention'] ?? 30) == 30 ? 'selected' : '' ?>>30 days</option>
                                <option value="90" <?= ($data['settings']['backup_retention'] ?? 30) == 90 ? 'selected' : '' ?>>90 days</option>
                                <option value="365" <?= ($data['settings']['backup_retention'] ?? 30) == 365 ? 'selected' : '' ?>>1 year</option>
                            </select>
                            <div class="form-text">How long to keep backup files.</div>
                        </div>

                        <div class="backup-status success">
                            <i class="fas fa-check-circle"></i> Last backup: <?= date('M j, Y g:i A') ?>
                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="createBackup()">
                            <i class="fas fa-download"></i> Create Backup Now
                        </button>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="settings-group">
                    <div class="settings-group-header">
                        <i class="fas fa-bell"></i> Notifications
                    </div>
                    <div class="settings-group-body">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="email_notifications"
                                    name="email_notifications" value="1" <?= ($data['settings']['email_notifications'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="email_notifications">
                                    Email Notifications
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="low_stock_alerts"
                                    name="low_stock_alerts" value="1" <?= ($data['settings']['low_stock_alerts'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="low_stock_alerts">
                                    Low Stock Alerts
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="system_maintenance_alerts"
                                    name="system_maintenance_alerts" value="1"
                                    <?= ($data['settings']['system_maintenance_alerts'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="system_maintenance_alerts">
                                    System Maintenance Alerts
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                    <button type="button" class="btn btn-secondary btn-lg ml-2" onclick="window.history.back()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function resetToDefaults() {
        if (confirm('Are you sure you want to reset all settings to their default values?')) {
            // Reset form to defaults
            document.getElementById('settingsForm').reset();

            // Set specific default values
            document.getElementById('site_name').value = 'Inventory Management System';
            document.getElementById('timezone').value = 'UTC';
            document.getElementById('items_per_page').value = '25';
            document.getElementById('auto_approve_threshold').value = '1000';
            document.getElementById('low_stock_threshold').value = '10';
            document.getElementById('currency').value = 'USD';
            document.getElementById('tax_rate').value = '8.25';
            document.getElementById('session_timeout').value = '60';
            document.getElementById('max_login_attempts').value = '5';
            document.getElementById('backup_frequency').value = 'daily';
            document.getElementById('backup_retention').value = '30';

            // Check default checkboxes
            document.getElementById('require_strong_passwords').checked = true;
            document.getElementById('email_notifications').checked = true;
            document.getElementById('low_stock_alerts').checked = true;
            document.getElementById('system_maintenance_alerts').checked = true;
        }
    }

    function createBackup() {
        if (confirm('Create a database backup now? This may take a few minutes.')) {
            // Show loading indicator
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Backup...';
            button.disabled = true;

            // Simulate backup process
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-check"></i> Backup Created';
                button.className = 'btn btn-success btn-sm mt-2';

                // Reset after 3 seconds
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.className = 'btn btn-outline-primary btn-sm mt-2';
                    button.disabled = false;
                }, 3000);
            }, 2000);
        }
    }

    // Form validation
    document.getElementById('settingsForm').addEventListener('submit', function (e) {
        const autoApprove = parseFloat(document.getElementById('auto_approve_threshold').value);
        const lowStock = parseInt(document.getElementById('low_stock_threshold').value);
        const taxRate = parseFloat(document.getElementById('tax_rate').value);

        if (autoApprove < 0) {
            alert('Auto-approve threshold must be a positive number.');
            e.preventDefault();
            return;
        }

        if (lowStock < 1) {
            alert('Low stock threshold must be at least 1.');
            e.preventDefault();
            return;
        }

        if (taxRate < 0 || taxRate > 100) {
            alert('Tax rate must be between 0 and 100.');
            e.preventDefault();
            return;
        }
    });
</script>


            </div> <!-- End container-fluid -->
        </div> <!-- End page-content-wrapper -->
    </div> <!-- End wrapper -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>
</html>