<?php
class AdminController extends Controller
{
    public $userModel;
    public $roleModel;

    public function __construct()
    {
        // Start session if not already started
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isLoggedIn()) {
            redirect('users/login');
        }

        // Check if user has admin permissions
        if (!$this->hasAdminPermissions()) {
            flash('error_message', 'Access denied. Admin permissions required.', 'alert alert-danger');
            redirect('dashboard');
        }

        $this->userModel = $this->model('User');
        $this->roleModel = $this->model('Role');
    }

    /**
     * Check if current user has admin permissions
     */
    private function hasAdminPermissions()
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $user = $this->model('User')->getUserWithRole($_SESSION['user_id']);
        if (!$user) {
            return false;
        }

        // Case-insensitive check for admin roles
        $role = strtolower($user->role_name ?? '');
        return in_array($role, ['admin', 'super admin', 'administrator']);
    }

    /**
     * Admin panel dashboard
     */
    public function index()
    {
        // Get system statistics
        $stats = $this->getSystemStats();
        $recentActivity = $this->userModel->getRecentActivity(10);
        $systemHealth = $this->getSystemHealth();

        $data = [
            'title' => 'Admin Panel',
            'stats' => $stats,
            'recent_activity' => $recentActivity,
            'system_health' => $systemHealth
        ];

        $this->view('admin/dashboard', $data);
    }

    /**
     * User management
     */
    public function users()
    {
        $users = $this->userModel->getAllUsersWithRoles();
        $roles = $this->roleModel->getAllRoles();

        $data = [
            'title' => 'User Management',
            'users' => $users,
            'roles' => $roles
        ];

        $this->view('admin/users', $data);
    }

    /**
     * Add new user
     */
    public function addUser()
    {
        // Set JSON content type for AJAX responses
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // Check if POST data exists
                if (empty($_POST)) {
                    throw new Exception('No form data received');
                }

                $_POST = sanitizePost($_POST);

                $data = [
                    'name' => trim($_POST['name'] ?? ''),
                    'username' => trim($_POST['username'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'password' => trim($_POST['password'] ?? ''),
                    'role_id' => intval($_POST['role_id'] ?? 0),
                    'status' => $_POST['status'] ?? 'active'
                ];

                // Enhanced validation
                $errors = [];
                if (empty($data['name'])) {
                    $errors[] = 'Name is required';
                }
                if (empty($data['username'])) {
                    $errors[] = 'Username is required';
                } elseif (strlen($data['username']) < 3) {
                    $errors[] = 'Username must be at least 3 characters';
                }
                if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Valid email is required';
                }
                if (empty($data['password']) || strlen($data['password']) < 6) {
                    $errors[] = 'Password must be at least 6 characters';
                }
                if (empty($data['role_id'])) {
                    $errors[] = 'Role is required';
                }

                // Check for duplicates
                if (!empty($data['email']) && $this->userModel->findUserByEmail($data['email'])) {
                    $errors[] = 'Email already exists';
                }
                if (!empty($data['username']) && $this->userModel->findUserByUsername($data['username'])) {
                    $errors[] = 'Username already exists';
                }

                if (empty($errors)) {
                    // Hash password
                    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                    if ($this->userModel->addUser($data)) {
                        // Log activity if method exists
                        if (method_exists($this->userModel, 'logActivity')) {
                            $this->userModel->logActivity(
                                $_SESSION['user_id'],
                                'user_created',
                                "Created user: {$data['name']} ({$data['email']})"
                            );
                        }

                        echo json_encode([
                            'success' => true,
                            'message' => 'User created successfully',
                            'user' => [
                                'name' => $data['name'],
                                'username' => $data['username'],
                                'email' => $data['email']
                            ]
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to create user - database error']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                }
            } catch (Exception $e) {
                error_log("AddUser exception: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Server error occurred']);
            }
            return;
        }

        // GET request - show add user form
        try {
            $roles = $this->roleModel->getAllRoles();
            $data = ['roles' => $roles];
            $this->view('admin/add_user', $data);
        } catch (Exception $e) {
            flash('error_message', 'Error loading roles: ' . $e->getMessage(), 'alert alert-danger');
            redirect('admin/users');
        }
    }

    /**
     * Edit user
     */
    public function editUser($userId = null)
    {
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'User ID required']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $data = [
                'user_id' => $userId,
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'role_id' => intval($_POST['role_id']),
                'status' => $_POST['status'] ?? 'active'
            ];

            // Validation
            $errors = [];
            if (empty($data['name'])) {
                $errors[] = 'Name is required';
            }
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email is required';
            }

            // Check if email already exists for other users
            $existingUser = $this->userModel->findUserByEmail($data['email']);
            if ($existingUser && $existingUser->user_id != $userId) {
                $errors[] = 'Email already exists';
            }

            if (empty($errors)) {
                if ($this->userModel->updateUser($data)) {
                    $this->userModel->logActivity(
                        $_SESSION['user_id'],
                        'user_updated',
                        "Updated user: {$data['name']} ({$data['email']})"
                    );

                    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update user']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
            }
            return;
        }

        // GET request - return user data
        $user = $this->userModel->getUserById($userId);
        if ($user) {
            echo json_encode($user);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    }

    /**
     * Role management
     */
    public function roles()
    {
        $roles = $this->roleModel->getAllRolesWithUserCount();
        $permissions = $this->getAvailablePermissions();

        $data = [
            'title' => 'Role Management',
            'roles' => $roles,
            'permissions' => $permissions
        ];

        $this->view('admin/roles', $data);
    }

    /**
     * Add new role
     */
    public function addRole()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $data = [
                'role_name' => trim($_POST['role_name']),
                'description' => trim($_POST['description']),
                'permissions' => $_POST['permissions'] ?? []
            ];

            // Validation
            $errors = [];
            if (empty($data['role_name'])) {
                $errors[] = 'Role name is required';
            }
            if ($this->roleModel->roleExists($data['role_name'])) {
                $errors[] = 'Role name already exists';
            }

            if (empty($errors)) {
                if ($this->roleModel->createRole($data)) {
                    $this->userModel->logActivity(
                        $_SESSION['user_id'],
                        'role_created',
                        "Created role: {$data['role_name']}"
                    );

                    echo json_encode(['success' => true, 'message' => 'Role created successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to create role']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
            }
            return;
        }

        redirect('admin/roles');
    }

    /**
     * Edit role permissions
     */
    public function editRole($roleId = null)
    {
        if (!$roleId) {
            echo json_encode(['success' => false, 'message' => 'Role ID required']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $data = [
                'role_id' => $roleId,
                'role_name' => trim($_POST['role_name']),
                'description' => trim($_POST['description']),
                'permissions' => $_POST['permissions'] ?? []
            ];

            if ($this->roleModel->updateRole($data)) {
                $this->userModel->logActivity(
                    $_SESSION['user_id'],
                    'role_updated',
                    "Updated role: {$data['role_name']}"
                );

                echo json_encode(['success' => true, 'message' => 'Role updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update role']);
            }
            return;
        }

        // GET request - return role data
        $role = $this->roleModel->getRoleById($roleId);
        if ($role) {
            echo json_encode($role);
        } else {
            echo json_encode(['success' => false, 'message' => 'Role not found']);
        }
    }

    /**
     * Activity Logs
     */
    public function activityLogs()
    {
        // Check admin permission
        if (!$this->hasAdminPermissions()) {
            redirect('/dashboard');
            return;
        }

        $page = (int) ($_GET['page'] ?? 1);
        $filter = $_GET['filter'] ?? null;
        $perPage = 25;
        $offset = ($page - 1) * $perPage;

        $activities = $this->userModel->getActivityLogs($perPage, $offset, $filter);
        $totalActivities = $this->userModel->getActivityLogsCount($filter);
        $totalPages = ceil($totalActivities / $perPage);

        $data = [
            'title' => 'Activity Logs',
            'activities' => $activities,
            'total_activities' => $totalActivities,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'current_user' => $_SESSION['user_name'] ?? 'Unknown'
        ];

        $this->view('admin/activity_logs', $data);
    }

    /**
     * System settings
     */
    public function settings()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            // Handle settings update
            $settings = [
                'auto_approve_threshold' => floatval($_POST['auto_approve_threshold'] ?? 1000),
                'low_Inventory_threshold' => intval($_POST['low_Inventory_threshold'] ?? 10),
                'session_timeout' => intval($_POST['session_timeout'] ?? 3600),
                'backup_frequency' => $_POST['backup_frequency'] ?? 'daily'
            ];

            if ($this->updateSystemSettings($settings)) {
                flash('admin_message', 'Settings updated successfully', 'alert alert-success');
            } else {
                flash('admin_message', 'Failed to update settings', 'alert alert-danger');
            }
        }

        $currentSettings = $this->getSystemSettings();

        $data = [
            'title' => 'System Settings',
            'settings' => $currentSettings
        ];

        $this->view('admin/settings', $data);
    }

    /**
     * Get available permissions
     */
    private function getAvailablePermissions()
    {
        return [
            'users' => [
                'label' => 'User Management',
                'permissions' => ['create', 'read', 'update', 'delete']
            ],
            'sales' => [
                'label' => 'Sales',
                'permissions' => ['create', 'read', 'update', 'delete']
            ],
            'purchases' => [
                'label' => 'Purchases',
                'permissions' => ['create', 'read', 'update', 'delete', 'approve']
            ],
            'inventory' => [
                'label' => 'Inventory',
                'permissions' => ['create', 'read', 'update', 'delete']
            ],
            'customers' => [
                'label' => 'Customers',
                'permissions' => ['create', 'read', 'update', 'delete']
            ],
            'suppliers' => [
                'label' => 'Suppliers',
                'permissions' => ['create', 'read', 'update', 'delete']
            ],
            'reports' => [
                'label' => 'Reports',
                'permissions' => ['read', 'export']
            ],
            'settings' => [
                'label' => 'Settings',
                'permissions' => ['read', 'update']
            ],
            'admin' => [
                'label' => 'Admin Panel',
                'permissions' => ['access']
            ]
        ];
    }

    /**
     * Get system statistics
     */
    private function getSystemStats()
    {
        return [
            'total_users' => $this->userModel->getTotalUsers(),
            'active_users' => $this->userModel->getActiveUsers(),
            'total_roles' => $this->roleModel->getTotalRoles(),
            'recent_logins' => $this->userModel->getRecentLoginsCount(7)
        ];
    }

    /**
     * Get system health metrics
     */
    private function getSystemHealth()
    {
        return [
            'database_status' => $this->checkDatabaseConnection(),
            'disk_space' => $this->getDiskUsage(),
            'php_version' => phpversion(),
            'memory_usage' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB'
        ];
    }

    /**
     * Check database connection
     */
    private function checkDatabaseConnection()
    {
        try {
            $this->userModel->getTotalUsers();
            return 'Connected';
        } catch (Exception $e) {
            return 'Error';
        }
    }

    /**
     * Get disk usage
     */
    private function getDiskUsage()
    {
        $bytes = disk_free_space(".");
        $gb = round($bytes / 1024 / 1024 / 1024, 2);
        return $gb . ' GB free';
    }

    /**
     * Get system settings
     */
    private function getSystemSettings()
    {
        // This would typically be stored in database
        // For now, return defaults
        return [
            'auto_approve_threshold' => 1000,
            'low_Inventory_threshold' => 10,
            'session_timeout' => 3600,
            'backup_frequency' => 'daily'
        ];
    }

    /**
     * Update system settings
     */
    private function updateSystemSettings($settings)
    {
        // This would typically update database settings table
        // For now, return true (placeholder)
        return true;
    }

    /**
     * User Permissions Management
     */
    public function userPermissions()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $userId = intval($_POST['user_id']);
            $permissions = $_POST['permissions'] ?? [];

            if ($this->updateUserPermissions($userId, $permissions)) {
                echo json_encode(['success' => true, 'message' => 'Permissions updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update permissions']);
            }
            return;
        }

        // Get all users and their current permissions
        $users = $this->userModel->getAllUsersWithPermissions();
        $availablePages = $this->getAvailablePages();

        $data = [
            'title' => 'User Permissions Management',
            'users' => $users,
            'available_pages' => $availablePages
        ];

        $this->view('admin/user_permissions', $data);
    }

    /**
     * Get available pages/modules for permission assignment
     */
    private function getAvailablePages()
    {
        return [
            'dashboard' => [
                'label' => 'Dashboard',
                'description' => 'Main dashboard access'
            ],
            'sales' => [
                'label' => 'Sales Management',
                'description' => 'Manage sales, invoices, and transactions'
            ],
            'purchases' => [
                'label' => 'Purchase Management',
                'description' => 'Manage purchases and suppliers'
            ],
            'inventory' => [
                'label' => 'Inventory Management',
                'description' => 'Manage products, and inventory'
            ],
            'customers' => [
                'label' => 'Customer Management',
                'description' => 'Manage customer information'
            ],
            'suppliers' => [
                'label' => 'Supplier Management',
                'description' => 'Manage supplier information'
            ],
            'products' => [
                'label' => 'Product Management',
                'description' => 'Manage product catalog'
            ],
            'reports' => [
                'label' => 'Reports',
                'description' => 'Access to various reports'
            ],
            'settings' => [
                'label' => 'System Settings',
                'description' => 'System configuration and settings'
            ],
            'users' => [
                'label' => 'User Management',
                'description' => 'Manage user accounts (Admin only)'
            ],
            'cycle_counts' => [
                'label' => 'Cycle Counts',
                'description' => 'Inventory cycle counting'
            ],
            'returns' => [
                'label' => 'Returns Management',
                'description' => 'Handle product returns'
            ],
            'expenses' => [
                'label' => 'Expense Management',
                'description' => 'Track business expenses'
            ],
            'notifications' => [
                'label' => 'Notifications',
                'description' => 'System notifications'
            ]
        ];
    }

    /**
     * Update user permissions
     */
    private function updateUserPermissions($userId, $permissions)
    {
        return $this->userModel->updateUserPermissions($userId, $permissions);
    }
}
