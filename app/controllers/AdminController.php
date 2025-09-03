<?php
class AdminController extends BaseController
{
    public $userModel;
    public $roleModel;

    public function __construct()
    {
        // Start session if not already started
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Check if this is an AJAX/API request
        $isAjaxRequest = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        $isApiCall = strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/') !== false &&
            in_array($_SERVER['REQUEST_METHOD'] ?? '', ['POST', 'PUT', 'DELETE', 'PATCH']);

        if (!isLoggedIn()) {
            if ($isAjaxRequest || $isApiCall) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Authentication required']);
                exit();
            }
            redirect('users/login');
        }

        // Check if user has admin permissions
        if (!$this->hasAdminPermissions()) {
            if ($isAjaxRequest || $isApiCall) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Access denied. Admin permissions required.']);
                exit();
            }
            flash('error_message', 'Access denied. Admin permissions required.', 'alert alert-danger');
            redirect('dashboard');
        }

        $this->userModel = $this->model('User');
        $this->roleModel = $this->model('Role');
    }

    /**
     * Pricing Dashboard - view for Price Bot and summary KPIs
     */
    public function pricing_dashboard()
    {
        $data = [
            'title' => 'Pricing Dashboard'
        ];
        $this->view('admin/pricing_dashboard', $data);
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
        // Use the new method that gets users from all three tables
        $users = $this->userModel->getAllUsersWithCategories();
        $roles = $this->roleModel->getAllRoles();

        $data = [
            'title' => 'User Management',
            'users' => $users,
            'roles' => $roles
        ];

        $this->view('admin/users', $data);
    }

    /**
     * View individual user details
     */
    public function viewUser($userId = null)
    {
        if (!$userId) {
            flash('user_message', 'User ID not provided', 'alert alert-danger');
            redirect('admin/users');
        }

        // Get user data from any of the tables (users, customers, contractors)
        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            flash('user_message', 'User not found', 'alert alert-danger');
            redirect('admin/users');
        }

        $data = [
            'title' => 'User Details - ' . ($user->name ?? $user->username),
            'user' => $user
        ];

        $this->renderLayout('admin/viewUser', $data);
    }

    /**
     * Edit user profile - Admin can edit any user's profile
     */
    public function editUserProfile($userId = null)
    {
        if (!$userId) {
            flash('user_message', 'User ID not provided', 'alert alert-danger');
            redirect('admin/users');
        }

        // Get user data from any of the tables (users, customers, contractors)
        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            flash('user_message', 'User not found', 'alert alert-danger');
            redirect('admin/users');
        }

        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            // Handle profile picture upload
            $profilePicturePath = '';
            if (isset($_FILES['profile_picture_file']) && $_FILES['profile_picture_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'storage/uploads/users/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $ext = pathinfo($_FILES['profile_picture_file']['name'], PATHINFO_EXTENSION);
                $username = $user->username ?? $user->user_name ?? 'user_' . $userId;
                $filename = trim($username) . '.' . $ext;
                $targetPath = $uploadDir . $filename;

                // Remove old profile picture if it exists
                if (file_exists($targetPath)) {
                    unlink($targetPath);
                }

                if (move_uploaded_file($_FILES['profile_picture_file']['tmp_name'], $targetPath)) {
                    $profilePicturePath = URLROOT . '/' . $targetPath;
                }
            }

            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email'] ?? ''),
                'job_title' => trim($_POST['job_title'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'birthday' => trim($_POST['birthday'] ?? ''),
                'education' => trim($_POST['education'] ?? ''),
                'profile_picture' => $profilePicturePath
            ];

            // Only update profile picture if a new one was uploaded
            if (empty($profilePicturePath)) {
                unset($data['profile_picture']);
            }

            // Update user in the appropriate table based on source_table
            $sourceTable = $user->source_table ?? 'users';
            if ($this->userModel->updateUserProfile($userId, $data, $sourceTable)) {
                flash('profile_message', 'Profile updated successfully', 'alert alert-success');
                redirect('admin/viewUser/' . $userId);
            } else {
                flash('profile_message', 'Something went wrong', 'alert alert-danger');
            }
        }

        $data = [
            'title' => 'Edit User Profile - ' . ($user->name ?? $user->username),
            'user' => $user
        ];

        $this->renderLayout('admin/editUserProfile', $data);
    }

    /**
     * User categorization interface - shows three separate tables
     */
    public function userCategorization()
    {
        // Get all users with their categories
        $allUsers = $this->userModel->getAllUsersWithRoles();

        // Initialize arrays for each category
        $officials = [];
        $customers = [];
        $contractors = [];
        $counts = ['official' => 0, 'customer' => 0, 'contractor' => 0];

        // Separate users by category
        if ($allUsers && is_array($allUsers)) {
            foreach ($allUsers as $user) {
                $category = isset($user->user_category) ? $user->user_category : 'official'; // Default to official if no category set

                switch ($category) {
                    case 'customer':
                        $customers[] = $user;
                        $counts['customer']++;
                        break;
                    case 'contractor':
                        $contractors[] = $user;
                        $counts['contractor']++;
                        break;
                    default:
                        $officials[] = $user;
                        $counts['official']++;
                        break;
                }
            }
        }

        $data = [
            'title' => 'User Management by Category',
            'officials' => $officials,
            'customers' => $customers,
            'contractors' => $contractors,
            'counts' => $counts,
            'roles' => $this->roleModel->getAllRoles()
        ];

        $this->view('admin/user_categorization', $data);
    }

    /**
     * AJAX endpoint to toggle a user's status (activate/deactivate)
     */
    public function toggleUserStatus()
    {
        // Only accept POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            if (ob_get_length())
                ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        // Parse input
        $userId = intval($_POST['user_id'] ?? 0);
        $status = strtolower(trim($_POST['status'] ?? ''));
        $sourceTable = $_POST['source_table'] ?? 'users';

        // Composite id support: expected format 'table:123'
        $composite = trim((string) ($_POST['composite_id'] ?? ''));
        if ($composite !== '') {
            $parts = explode(':', $composite, 2);
            if (count($parts) === 2) {
                $maybeTable = strtolower(trim($parts[0]));
                $maybeId = intval($parts[1]);
                if ($maybeId > 0) {
                    $userId = $maybeId;
                    $sourceTable = $maybeTable;
                }
            }
        }

        // Normalize source table value to expected names
        $sourceTable = strtolower(trim((string) $sourceTable));
        // Map common singular/plural variants and accidental values
        $tableMap = [
            'user' => 'users',
            'users' => 'users',
            'customer' => 'customers',
            'customers' => 'customers',
            'contractor' => 'contractors',
            'contractors' => 'contractors'
        ];

        if (isset($tableMap[$sourceTable])) {
            $sourceTable = $tableMap[$sourceTable];
        }

        // If sourceTable still not one of the allowed tables, attempt to find which table the user belongs to
        $allowed = ['users', 'customers', 'contractors'];
        if (!in_array($sourceTable, $allowed, true)) {
            // Try a cross-table lookup for this user id
            try {
                $found = $this->userModel->getUserById($userId);
                if ($found && !empty($found->source_table)) {
                    $sourceTable = $found->source_table;
                } else {
                    // Log the bad incoming source_table for debugging
                    error_log("Toggle Status Warning: unexpected source_table received: '" . ($_POST['source_table'] ?? '') . "' for userId={$userId}");
                }
            } catch (Exception $e) {
                error_log('Toggle Status lookup error: ' . $e->getMessage());
            }
        }

        // Detailed request logging to help debug payload issues (write to app.log)
        try {
            $logPath = __DIR__ . '/../../storage/logs/app.log';
            $entry = sprintf("[%s] toggleUserStatus called. POST: %s, RAW_INPUT: %s\n", date('Y-m-d H:i:s'), json_encode($_POST), file_get_contents('php://input'));
            // append safely
            @file_put_contents($logPath, $entry, FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            // swallow logging errors to avoid breaking API
        }

        if ($userId <= 0 || !in_array($status, ['active', 'inactive'])) {
            if (ob_get_length())
                ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            return;
        }

        // Prevent changing the currently logged-in admin's own status
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId && $sourceTable === 'users') {
            if (ob_get_length())
                ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Cannot change your own status']);
            return;
        }

        // Get user from correct table
        $target = $this->userModel->getUserByIdAndTable($userId, $sourceTable);
        if (!$target) {
            // Try cross-table resolution in case the incoming source_table is incorrect
            try {
                $resolved = $this->userModel->getUserById($userId);
                if ($resolved && !empty($resolved->source_table) && $resolved->source_table !== $sourceTable) {
                    // Log the resolution and retry with the correct table
                    $oldTable = $sourceTable;
                    $sourceTable = $resolved->source_table;
                    $logPath = __DIR__ . '/../../storage/logs/app.log';
                    @file_put_contents($logPath, sprintf("[%s] toggleUserStatus: resolved source_table from '%s' to '%s' for userId=%s\n", date('Y-m-d H:i:s'), $oldTable, $sourceTable, var_export($userId, true)), FILE_APPEND | LOCK_EX);
                    $target = $this->userModel->getUserByIdAndTable($userId, $sourceTable);
                }
            } catch (Exception $e) {
                // swallow
            }
        }

        if (!$target) {
            error_log("Toggle Status Error: User not found - userId=$userId, sourceTable=$sourceTable");
            if (ob_get_length())
                ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }

        error_log("Toggle Status Debug: Found user - " . json_encode($target));

        // For users table only: Ensure target is not an admin
        if ($sourceTable === 'users') {
            $targetRole = strtolower($target->role_name ?? '');
            if (in_array($targetRole, ['admin', 'super admin', 'administrator'])) {
                if (ob_get_length())
                    ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Cannot change status of admin users']);
                return;
            }
        }

        // Update status in appropriate table
        $ok = false;
        switch ($sourceTable) {
            case 'users':
                $ok = $this->userModel->setStatus($userId, $status);
                break;
            case 'customers':
                $ok = $this->userModel->setCustomerStatus($userId, $status);
                break;
            case 'contractors':
                $ok = $this->userModel->setContractorStatus($userId, $status);
                break;
        }

        if ($ok) {
            // log activity if available
            if (method_exists($this->userModel, 'logActivity')) {
                // Determine a safe display name for logging (support users/customers/contractors)
                $targetName = $target->name ?? $target->full_name ?? $target->username ?? ($target->customer_name ?? 'unknown');
                $this->userModel->logActivity($_SESSION['user_id'] ?? 0, 'user_status_changed', "Set {$targetName} ({$sourceTable}) to {$status}");
            }
            if (ob_get_length())
                ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            if (ob_get_length())
                ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
    }

    /**
     * Save user categories (AJAX endpoint)
     */
    public function saveUserCategories()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $categories = $_POST['categories'] ?? [];

        if (empty($categories)) {
            echo json_encode(['success' => false, 'message' => 'No categories provided']);
            return;
        }

        $successCount = 0;
        $totalCount = count($categories);

        foreach ($categories as $userId => $category) {
            if ($this->userModel->setUserCategory((int) $userId, $category)) {
                $successCount++;
            }
        }

        if ($successCount === $totalCount) {
            echo json_encode(['success' => true, 'message' => "Successfully updated $successCount user categories"]);
        } else {
            echo json_encode(['success' => false, 'message' => "Updated $successCount of $totalCount categories"]);
        }
    }

    /**
     * Add new user
     */
    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Ensure clean output for AJAX responses
            ob_clean();
            header('Content-Type: application/json');

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
            exit();
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
     * Add Official User (Employee)
     */
    public function addOfficial()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            ob_clean();
            header('Content-Type: application/json');

            try {
                $_POST = sanitizePost($_POST);

                $data = [
                    'name' => trim($_POST['name'] ?? ''),
                    'username' => trim($_POST['username'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'password' => trim($_POST['password'] ?? ''),
                    'role_id' => intval($_POST['role_id'] ?? 0),
                    'status' => $_POST['status'] ?? 'active',
                    'phone' => trim($_POST['phone'] ?? ''),
                    'department' => trim($_POST['department'] ?? ''),
                    'hire_date' => $_POST['hire_date'] ?? date('Y-m-d')
                ];

                $errors = $this->validateOfficialData($data);

                if (empty($errors)) {
                    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                    if ($this->userModel->addUser($data)) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Official user created successfully',
                            'redirect' => 'admin/users'
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to create official user']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Server error occurred']);
            }
            exit();
        }

        // GET request - show form
        $roles = $this->roleModel->getAllRoles();
        // Filter to show only official roles (not Customer/Contractor)
        $officialRoles = array_filter($roles, function ($role) {
            return !in_array($role->role_name, ['Customer', 'Contractor']);
        });

        $data = [
            'title' => 'Add Official User',
            'roles' => $officialRoles
        ];
        $this->view('admin/add_official', $data);
    }

    /**
     * Add Customer
     */
    public function addCustomer()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            ob_clean();
            header('Content-Type: application/json');

            try {
                $_POST = sanitizePost($_POST);

                // Customer data
                $customerData = [
                    'company_name' => trim($_POST['company_name'] ?? ''),
                    'contact_person' => trim($_POST['contact_person'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'address' => trim($_POST['address'] ?? ''),
                    'city' => trim($_POST['city'] ?? ''),
                    'state' => trim($_POST['state'] ?? ''),
                    'zip_code' => trim($_POST['zip_code'] ?? ''),
                    'discount_type' => $_POST['discount_type'] ?? 'percentage',
                    'discount_value' => floatval($_POST['discount_value'] ?? 0),
                    'credit_limit' => floatval($_POST['credit_limit'] ?? 0),
                    'payment_terms' => intval($_POST['payment_terms'] ?? 30),
                    'is_active' => 1
                ];

                $errors = $this->validateCustomerData($customerData);

                if (empty($errors)) {
                    $customerModel = new Customer();
                    if ($customerModel->addCustomer($customerData)) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Customer created successfully',
                            'redirect' => 'admin/users'
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to create customer']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Server error occurred']);
            }
            exit();
        }

        // GET request - show form
        $data = [
            'title' => 'Add Customer'
        ];
        $this->view('admin/add_customer', $data);
    }

    /**
     * Add Contractor
     */
    public function addContractor()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            ob_clean();
            header('Content-Type: application/json');

            try {
                $_POST = sanitizePost($_POST);

                // Contractor data
                $contractorData = [
                    'company_name' => trim($_POST['company_name'] ?? ''),
                    'contact_person' => trim($_POST['contact_person'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'address' => trim($_POST['address'] ?? ''),
                    'city' => trim($_POST['city'] ?? ''),
                    'state' => trim($_POST['state'] ?? ''),
                    'zip_code' => trim($_POST['zip_code'] ?? ''),
                    'specialty' => trim($_POST['specialty'] ?? ''),
                    'license_number' => trim($_POST['license_number'] ?? ''),
                    'commission_type' => $_POST['commission_type'] ?? 'percentage',
                    'commission_value' => floatval($_POST['commission_value'] ?? 0),
                    'payment_terms' => intval($_POST['payment_terms'] ?? 30),
                    'is_active' => 1
                ];

                $errors = $this->validateContractorData($contractorData);

                if (empty($errors)) {
                    $contractorModel = new Contractor();
                    if ($contractorModel->addContractor($contractorData)) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Contractor created successfully',
                            'redirect' => 'admin/users'
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to create contractor']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Server error occurred']);
            }
            exit();
        }

        // GET request - show form
        $data = [
            'title' => 'Add Contractor'
        ];
        $this->view('admin/add_contractor', $data);
    }

    /**
     * Validation helpers
     */
    private function validateOfficialData($data)
    {
        $errors = [];
        if (empty($data['name']))
            $errors[] = 'Name is required';
        if (empty($data['username']))
            $errors[] = 'Username is required';
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }
        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }
        if (empty($data['role_id']))
            $errors[] = 'Role is required';

        // Check duplicates
        if (!empty($data['email']) && $this->userModel->findUserByEmail($data['email'])) {
            $errors[] = 'Email already exists';
        }
        if (!empty($data['username']) && $this->userModel->findUserByUsername($data['username'])) {
            $errors[] = 'Username already exists';
        }

        return $errors;
    }

    private function validateCustomerData($data)
    {
        $errors = [];
        if (empty($data['contact_person']))
            $errors[] = 'Contact person is required';
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email format required';
        }
        if (empty($data['phone']))
            $errors[] = 'Phone number is required';
        if ($data['discount_value'] < 0 || $data['discount_value'] > 100) {
            $errors[] = 'Discount value must be between 0 and 100';
        }

        return $errors;
    }

    private function validateContractorData($data)
    {
        $errors = [];
        if (empty($data['company_name']))
            $errors[] = 'Company name is required';
        if (empty($data['contact_person']))
            $errors[] = 'Contact person is required';
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }
        if (empty($data['phone']))
            $errors[] = 'Phone number is required';
        if (empty($data['specialty']))
            $errors[] = 'Specialty is required';
        if ($data['commission_value'] < 0 || $data['commission_value'] > 100) {
            $errors[] = 'Commission value must be between 0 and 100';
        }

        return $errors;
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
                'status' => $_POST['status'] ?? 'active',
                'source_table' => $_POST['source_table'] ?? $_GET['source'] ?? 'users'
            ];

            // Validation
            $errors = [];
            if (empty($data['name'])) {
                $errors[] = 'Name is required';
            }
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email is required';
            }

            // Check if email already exists for other users (only for officials)
            if ($data['source_table'] === 'users') {
                $existingUser = $this->userModel->findUserByEmail($data['email']);
                if ($existingUser && $existingUser->user_id != $userId) {
                    $errors[] = 'Email already exists';
                }
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
        $sourceTable = $_GET['source'] ?? 'users';
        $user = $this->userModel->getUserByIdAndTable($userId, $sourceTable);
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

    /**
     * Price Management page
     */
    public function priceManagement()
    {
        // Load the Product model
        $productModel = $this->model('Product');

        // Get filter parameters from URL
        $filters = [
            'category' => $_GET['category'] ?? '',
            'price_range' => $_GET['price_range'] ?? '',
            'stock_status' => $_GET['stock_status'] ?? '',
            'stock_filter' => $_GET['stock_filter'] ?? '',
            'margin_filter' => $_GET['margin_filter'] ?? '',
            'sales_filter' => $_GET['sales_filter'] ?? ''
        ];

        // Get products with pricing information
        $products = $productModel->getProductsForPriceManagement($filters);

        // Get price management statistics
        $stats = $this->getPriceManagementStats($productModel);

        $data = [
            'title' => 'Price Management',
            'products' => $products,
            'stats' => $stats,
            'filters' => $filters
        ];

        $this->view('admin/price_management', $data);
    }

    /**
     * Update product price via AJAX
     */
    public function updateProductPrice()
    {
        // Ensure clean output
        ob_clean();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }

        try {
            $productId = intval($_POST['product_id'] ?? 0);
            $newPrice = floatval($_POST['new_price'] ?? 0);
            $autoSave = isset($_POST['auto_save']) && $_POST['auto_save'] === 'true';

            if ($productId <= 0 || $newPrice < 0) {
                throw new Exception('Invalid product ID or price');
            }

            $productModel = $this->model('Product');

            // Update the product price
            if ($productModel->updateProductPrice($productId, $newPrice)) {
                // Log the price change if method exists
                if (method_exists($this->userModel, 'logActivity')) {
                    $this->userModel->logActivity(
                        $_SESSION['user_id'],
                        'price_updated',
                        "Updated price for product ID {$productId} to $" . number_format($newPrice, 2)
                    );
                }

                echo json_encode([
                    'success' => true,
                    'message' => $autoSave ? 'Price auto-saved' : 'Price updated successfully',
                    'new_price' => number_format($newPrice, 2)
                ]);
            } else {
                throw new Exception('Failed to update price in database');
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    /**
     * Get price history for a product
     */
    public function getPriceHistory()
    {
        // Ensure clean output
        ob_clean();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }

        try {
            $productId = intval($_GET['product_id'] ?? 0);

            if ($productId <= 0) {
                throw new Exception('Invalid product ID');
            }

            $productModel = $this->model('Product');
            $priceHistory = $productModel->getPriceHistory($productId);

            echo json_encode([
                'success' => true,
                'data' => $priceHistory
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    /**
     * Bulk update product prices
     */
    public function bulkPriceUpdate()
    {
        // Ensure clean output
        ob_clean();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }

        try {
            $products = $_POST['products'] ?? [];
            $updateType = $_POST['update_type'] ?? '';
            $updateValue = floatval($_POST['update_value'] ?? 0);
            $roundPrices = isset($_POST['round_prices']) && $_POST['round_prices'] === 'true';

            if (empty($products) || empty($updateType) || $updateValue <= 0) {
                throw new Exception('Invalid bulk update parameters');
            }

            $productModel = $this->model('Product');
            $updatedCount = 0;

            foreach ($products as $productId) {
                $productId = intval($productId);
                if ($productId <= 0)
                    continue;

                // Get current price
                $currentPrice = $productModel->getProductPrice($productId);
                if (!$currentPrice)
                    continue;

                // Calculate new price based on update type
                $newPrice = $this->calculateNewPrice($currentPrice, $updateType, $updateValue, $roundPrices);

                if ($newPrice > 0 && $productModel->updateProductPrice($productId, $newPrice)) {
                    $updatedCount++;
                }
            }

            // Log bulk update activity
            if (method_exists($this->userModel, 'logActivity')) {
                $this->userModel->logActivity(
                    $_SESSION['user_id'],
                    'bulk_price_update',
                    "Bulk updated {$updatedCount} product prices using {$updateType}"
                );
            }

            echo json_encode([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} product prices",
                'updated_count' => $updatedCount
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    /**
     * Export product prices
     */
    public function exportPrices()
    {
        try {
            $productModel = $this->model('Product');
            $selectedProducts = !empty($_GET['products']) ? explode(',', $_GET['products']) : [];

            // Get products for export
            if (!empty($selectedProducts)) {
                $products = $productModel->getProductsByIds($selectedProducts);
            } else {
                $products = $productModel->getAllProductsForExport();
            }

            // Set headers for CSV download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="product_prices_' . date('Y-m-d') . '.csv"');

            $output = fopen('php://output', 'w');

            // CSV headers
            fputcsv($output, ['Product ID', 'SKU', 'Name', 'Category', 'Current Price', 'Cost', 'Margin %', 'Stock Quantity']);

            // CSV data
            foreach ($products as $product) {
                $margin = 0;
                if (($product->price ?? 0) > 0 && ($product->cost ?? 0) > 0) {
                    $margin = (($product->price - $product->cost) / $product->price) * 100;
                }

                fputcsv($output, [
                    $product->product_id ?? '',
                    $product->sku ?? '',
                    $product->name ?? '',
                    $product->category ?? '',
                    number_format($product->price ?? 0, 2),
                    number_format($product->cost ?? 0, 2),
                    number_format($margin, 2),
                    $product->stock_quantity ?? 0
                ]);
            }

            fclose($output);

            // Log export activity
            if (method_exists($this->userModel, 'logActivity')) {
                $count = count($products);
                $this->userModel->logActivity(
                    $_SESSION['user_id'],
                    'price_export',
                    "Exported {$count} product prices to CSV"
                );
            }
        } catch (Exception $e) {
            flash('error_message', 'Export failed: ' . $e->getMessage(), 'alert alert-danger');
            redirect('admin/priceManagement');
        }
    }

    /**
     * Import product prices from CSV
     */
    public function importPrices()
    {
        // Ensure clean output
        ob_clean();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }

        try {
            if (!isset($_FILES['price_file']) || $_FILES['price_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('No file uploaded or upload error');
            }

            $file = $_FILES['price_file'];
            $allowedTypes = ['text/csv', 'application/csv', 'application/vnd.ms-excel'];

            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception('Only CSV files are allowed');
            }

            $productModel = $this->model('Product');
            $handle = fopen($file['tmp_name'], 'r');
            $updatedCount = 0;
            $lineNumber = 0;

            // Skip header row
            fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== FALSE) {
                $lineNumber++;

                if (count($data) < 5)
                    continue; // Need at least product ID and price

                $productId = intval($data[0]);
                $newPrice = floatval($data[4]);

                if ($productId > 0 && $newPrice > 0) {
                    if ($productModel->updateProductPrice($productId, $newPrice)) {
                        $updatedCount++;
                    }
                }
            }

            fclose($handle);

            // Log import activity
            if (method_exists($this->userModel, 'logActivity')) {
                $this->userModel->logActivity(
                    $_SESSION['user_id'],
                    'price_import',
                    "Imported {$updatedCount} product prices from CSV"
                );
            }

            echo json_encode([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} product prices",
                'updated_count' => $updatedCount
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    /**
     * Get price management statistics
     */
    private function getPriceManagementStats($productModel)
    {
        try {
            $stats = $productModel->getPriceManagementStats();
            return [
                'total_products' => $stats['total_products'] ?? 0,
                'average_margin' => $stats['average_margin'] ?? 0,
                'low_margin_products' => $stats['low_margin_products'] ?? 0,
                'total_gross_margin' => $stats['total_gross_margin'] ?? 0
            ];
        } catch (Exception $e) {
            // Return default stats if there's an error
            return [
                'total_products' => 0,
                'average_margin' => 0,
                'low_margin_products' => 0,
                'total_gross_margin' => 0
            ];
        }
    }

    /**
     * Calculate new price based on update type
     */
    private function calculateNewPrice($currentPrice, $updateType, $updateValue, $roundPrices = false)
    {
        $newPrice = $currentPrice;

        switch ($updateType) {
            case 'percentage_increase':
                $newPrice = $currentPrice * (1 + $updateValue / 100);
                break;
            case 'percentage_decrease':
                $newPrice = $currentPrice * (1 - $updateValue / 100);
                break;
            case 'fixed_increase':
                $newPrice = $currentPrice + $updateValue;
                break;
            case 'fixed_decrease':
                $newPrice = $currentPrice - $updateValue;
                break;
            case 'set_margin':
                // This would require cost data - simplified for now
                $newPrice = $currentPrice; // Keep current price if cost is unknown
                break;
        }

        // Ensure price doesn't go below 0
        $newPrice = max(0, $newPrice);

        // Round prices if requested
        if ($roundPrices && $newPrice > 0) {
            if ($newPrice < 10) {
                $newPrice = floor($newPrice) + 0.99;
            } else {
                $newPrice = floor($newPrice) + 0.95;
            }
        }

        return round($newPrice, 2);
    }

    /**
     * Execute Bot - Single execution endpoint for pricing bot
     */
    public function executeBot()
    {
        // Clear any existing output buffer to prevent HTML from interfering with JSON
        if (ob_get_level()) {
            ob_clean();
        }

        header('Content-Type: application/json');

        if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $botId = $_POST['bot_id'] ?? '';

        if (empty($botId)) {
            echo json_encode(['success' => false, 'message' => 'Bot ID is required']);
            return;
        }

        try {
            // Handle different bot types
            if ($botId === 'pricing_bot') {
                $result = $this->executePricingBot();
                echo json_encode($result);
            } elseif ($botId === 'sales_bot') {
                $result = $this->executeSalesBot();
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Unknown bot type: ' . $botId]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Bot execution error: ' . $e->getMessage()]);
        }
    }

    /**
     * Execute Pricing Bot Logic
     */
    private function executePricingBot()
    {
        try {
            $productModel = $this->model('Product');

            // Get products that need price optimization
            $products = $productModel->getProductsForPriceManagement();

            if (empty($products)) {
                return [
                    'success' => true,
                    'message' => 'No products need pricing optimization',
                    'action' => 'skipped_pricing',
                    'details' => 'All products have optimal pricing'
                ];
            }

            // Select a random product for bot processing (limit to prevent mass changes)
            $selectedProduct = $products[array_rand($products)];

            // Calculate optimal price using bot algorithm
            $currentPrice = (float) $selectedProduct->price; // Use 'price' not 'selling_price'
            $costPrice = (float) $selectedProduct->cost; // Use 'cost' not 'purchase_price'

            if ($costPrice <= 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot calculate price: invalid cost price',
                    'action' => 'pricing_error',
                    'details' => "Product {$selectedProduct->name} has invalid cost price"
                ];
            }

            // Bot pricing algorithm - target 30% margin with slight variation
            $targetMargin = 0.30; // 30% profit margin
            $variation = (rand(-50, 50) / 1000); // ±5% variation
            $finalMargin = $targetMargin + $variation;

            $newPrice = $costPrice / (1 - $finalMargin);

            // Apply safety limits
            $minPrice = $costPrice * 1.10; // Minimum 10% markup
            $maxPrice = $costPrice * 2.50; // Maximum 150% markup
            $newPrice = max($minPrice, min($maxPrice, $newPrice));

            // Round to nearest cent
            $newPrice = round($newPrice, 2);

            // Only update if price changed significantly (more than 1%)
            $priceChangePercent = abs(($newPrice - $currentPrice) / $currentPrice) * 100;

            if ($priceChangePercent < 1.0) {
                return [
                    'success' => true,
                    'message' => 'Price already optimal',
                    'action' => 'no_price_change',
                    'details' => "Price for {$selectedProduct->name} is within 1% of optimal"
                ];
            }

            // Update the price in database
            $updateResult = $productModel->updateProductPrice(
                $selectedProduct->product_id,
                $newPrice
            );

            if (!$updateResult) {
                return [
                    'success' => false,
                    'message' => 'Failed to update product price',
                    'action' => 'pricing_error'
                ];
            }

            // Calculate profit margin
            $newMargin = (($newPrice - $costPrice) / $newPrice) * 100;
            $priceChange = $newPrice - $currentPrice;
            $changeDirection = $priceChange > 0 ? 'increased' : 'decreased';

            return [
                'success' => true,
                'message' => 'Price optimized successfully',
                'action' => 'price_updated',
                'details' => "Price for {$selectedProduct->name} {$changeDirection} from $" . number_format($currentPrice, 2) . " to $" . number_format($newPrice, 2) . " (Margin: " . round($newMargin, 1) . "%)",
                'product_name' => $selectedProduct->name,
                'old_price' => $currentPrice,
                'new_price' => $newPrice,
                'price_change' => $priceChange,
                'margin_percent' => round($newMargin, 1),
                'change_percent' => round($priceChangePercent, 2)
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Pricing bot error: ' . $e->getMessage(),
                'action' => 'error'
            ];
        }
    }

    /**
     * Execute Sales Bot Logic - Delegate to BotController
     */
    private function executeSalesBot()
    {
        try {
            // Use the BotController to execute sales bot logic
            require_once APPROOT . '/app/controllers/BotController.php';
            $botController = new BotController();

            // Use reflection to access the private method
            $reflection = new ReflectionClass($botController);
            $method = $reflection->getMethod('executeSalesBot');
            $method->setAccessible(true);

            return $method->invoke($botController);

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Sales bot error: ' . $e->getMessage(),
                'action' => 'error'
            ];
        }
    }
}
