<?php
class User
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  // Minimal update profile
  public function updateProfile($data)
  {
    $this->db->query("UPDATE users SET 
            profile_picture = :profile_picture,
            full_name = :full_name, 
            username = :username, 
            email = :email,
            address = :address,
            job_title = :job_title,
            birthday = :birthday,
            education = :education
            WHERE user_id = :user_id");

    $this->db->bind(':profile_picture', $data['profile_picture'] ?? null);
    $this->db->bind(':full_name', $data['full_name'] ?? null);
    $this->db->bind(':username', $data['username'] ?? null);
    $this->db->bind(':email', $data['email'] ?? null);
    $this->db->bind(':address', $data['address'] ?? null);
    $this->db->bind(':job_title', $data['job_title'] ?? null);
    $this->db->bind(':birthday', $data['birthday'] ?? null);
    $this->db->bind(':education', $data['education'] ?? null);
    $this->db->bind(':user_id', $data['user_id'] ?? 0);

    return $this->db->execute();
  }

  // Login
  public function login($username, $password)
  {
    if ($this->isAccountLocked($username)) {
      return false;
    }

    $this->db->query('SELECT * FROM users WHERE username = :username');
    $this->db->bind(':username', $username);
    if (!$this->db->execute()) {
      $this->logFailedLogin($username, 'Database query failed');
      return false;
    }

    $row = $this->db->single();
    if (!$row) {
      $this->logFailedLogin($username, 'Username not found');
      return false;
    }

    $hashed_password = $row->password_hash ?? '';
    if (password_verify($password, $hashed_password)) {
      $this->resetFailedLoginAttempts($username);
      if (method_exists($this, 'updateLastLogin')) {
        $this->updateLastLogin($row->user_id);
      }
      return $row;
    }

    $this->logFailedLogin($username, 'Incorrect password');
    $this->incrementFailedLoginAttempts($username);
    return false;
  }

  private function isAccountLocked($username)
  {
    return false;
  }

  private function logFailedLogin($username, $reason)
  {
    if (defined('LOG_FILE')) {
      $timestamp = date("Y-m-d H:i:s");
      $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
      $logMessage = "[$timestamp] FAILED LOGIN: Username '$username' - $reason (IP: $ip)" . PHP_EOL;
      file_put_contents(LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
    }
  }

  private function incrementFailedLoginAttempts($username)
  {
    // placeholder
  }

  private function resetFailedLoginAttempts($username)
  {
    // placeholder
  }

  public function findUserByUsername($username)
  {
    $this->db->query('SELECT * FROM users WHERE username = :username');
    $this->db->bind(':username', $username);
    if (!$this->db->execute())
      return false;
    $row = $this->db->single();
    return $row ? true : false;
  }

  public function getUserWithRole($userId)
  {
    $this->db->query('SELECT u.user_id, u.username, u.full_name, u.profile_picture, u.address, u.job_title, u.birthday, u.education, u.password_hash, u.role_id, u.is_active, r.role_name FROM users u LEFT JOIN roles r ON u.role_id = r.role_id WHERE u.user_id = :user_id OR u.username = :username');
    $param = is_numeric($userId) ? (int) $userId : 0;
    $this->db->bind(':user_id', $param);
    $this->db->bind(':username', $userId);
    $this->db->execute();
    $result = $this->db->single();
    if ($result)
      return $result;

    $this->db->query('SELECT * FROM users WHERE user_id = :user_id OR username = :username');
    $this->db->bind(':user_id', $param);
    $this->db->bind(':username', $userId);
    $this->db->execute();
    $user = $this->db->single();
    if ($user) {
      $user->role_name = 'Associate';
      return $user;
    }
    return null;
  }

  /**
   * Get all users from all three tables (users, customers, contractors)
   * This is the proper way to get the complete user categorization
   * @return array
   */
  public function getAllUsersWithCategories()
  {
    try {
      $allUsers = [];

      // Get internal users (officials) from users table
      $officials = $this->getAllUsersWithRoles();
      foreach ($officials as $user) {
        $user->user_category = 'official';
        $user->source_table = 'users';
        $allUsers[] = $user;
      }

      // Get customers from customers table with discount info
      $this->db->query("SELECT 
        customer_id as user_id,
        customer_name as name,
        customer_name as username,
        contact_info,
        status as customer_status,
        CASE WHEN status = 'active' THEN 1 ELSE 0 END as is_active,
        'Customer' as role_name,
        'customer' as user_category,
        'customers' as source_table,
        NOW() as created_at,
        NULL as last_login,
        0 as role_id,
        credit_limit
        FROM customers 
        ORDER BY customer_id DESC");

      if ($this->db->execute()) {
        $customers = $this->db->resultSet();
        foreach ($customers as $customer) {
          // Parse contact_info JSON to extract email and phone
          $contactInfo = json_decode($customer->contact_info, true);
          if ($contactInfo && is_array($contactInfo)) {
            $customer->email = $contactInfo['email'] ?? '';
            $customer->phone = $contactInfo['phone'] ?? '';
            $customer->contact_person = $contactInfo['contact_person'] ?? '';
            $customer->address = $contactInfo['address'] ?? '';
            $customer->discount_type = $contactInfo['discount_type'] ?? '';
            $customer->discount_value = $contactInfo['discount_value'] ?? 0;
          } else {
            // Fallback for old contact_info format
            $customer->email = '';
            $customer->phone = '';
            $customer->contact_person = '';
            $customer->address = '';
            $customer->discount_type = '';
            $customer->discount_value = 0;
          }
          $customer->last_login = null;
          $allUsers[] = $customer;
        }
      }

      // Get contractors from contractors table (if it exists)
      $this->db->query("SHOW TABLES LIKE 'contractors'");
      $this->db->execute();
      $hasContractorsTable = (bool) $this->db->single();

      if ($hasContractorsTable) {
        $this->db->query("SELECT 
          contractor_id as user_id,
          contractor_name as name,
          contractor_name as username,
          COALESCE(email, '') as email,
          COALESCE(phone, '') as phone,
          is_active,
          CASE WHEN is_active = 1 THEN 'active' ELSE 'inactive' END as status,
          CONCAT(UPPER(SUBSTRING(COALESCE(specialization, 'general'), 1, 1)), SUBSTRING(COALESCE(specialization, 'general'), 2), ' Contractor') as role_name,
          'contractor' as user_category,
          'contractors' as source_table,
          COALESCE(created_at, NOW()) as created_at,
          NULL as last_login,
          0 as role_id,
          COALESCE(commission_type, '') as commission_type,
          COALESCE(commission_rate, 0) as commission_rate,
          COALESCE(total_commission_earned, 0) as total_commission_earned
          FROM contractors 
          ORDER BY contractor_id DESC");

        if ($this->db->execute()) {
          $contractors = $this->db->resultSet();
          foreach ($contractors as $contractor) {
            // Ensure all required properties exist
            $contractor->email = $contractor->email ?? '';
            $contractor->phone = $contractor->phone ?? '';
            $contractor->last_login = null;
            $allUsers[] = $contractor;
          }
        }
      }

      return $allUsers;

    } catch (Exception $e) {
      error_log('getAllUsersWithCategories failed: ' . $e->getMessage());
      return [];
    }
  }

  public function getAllUsersWithRoles()
  {
    try {
      // Detect optional columns/tables and build SELECT dynamically
      $this->db->query("SHOW TABLES LIKE 'user_activity_log'");
      $hasActivityLog = false;
      if ($this->db->execute()) {
        $hasActivityLog = (bool) $this->db->single();
      }

      // Check if users table has last_login and created_at columns
      $this->db->query("SHOW COLUMNS FROM users LIKE 'last_login'");
      $this->db->execute();
      $hasLastLoginColumn = (bool) $this->db->single();

      $this->db->query("SHOW COLUMNS FROM users LIKE 'created_at'");
      $this->db->execute();
      $hasCreatedAtColumn = (bool) $this->db->single();

      // Check if users table has user_category column
      $this->db->query("SHOW COLUMNS FROM users LIKE 'user_category'");
      $this->db->execute();
      $hasUserCategoryColumn = (bool) $this->db->single();

      // Base select
      $select = [
        "u.user_id",
        "u.full_name AS name",
        "u.username",
        "u.email",
        "u.role_id",
        "u.is_active AS status",
        "COALESCE(r.role_name, 'User') AS role_name"
      ];

      // Add user_category if available
      if ($hasUserCategoryColumn) {
        $select[] = "COALESCE(u.user_category, 'official') AS user_category";
      } else {
        $select[] = "'official' AS user_category";
      }

      // last_login: prefer users.last_login, else activity log subquery if available
      if ($hasLastLoginColumn) {
        $select[] = "u.last_login AS last_login";
      } elseif ($hasActivityLog) {
        // Broader matching for login-like actions, fall back to any recent activity if specific login actions aren't present
        // Prefer explicit login-like actions, otherwise fall back to any activity timestamp for that user
        $select[] = "(
      SELECT COALESCE(
        (SELECT MAX(ual.created_at) FROM user_activity_log ual WHERE ual.user_id = u.user_id AND (
          ual.action LIKE '%login%' OR ual.action LIKE '%logged in%' OR ual.action LIKE '%sign in%' OR ual.action LIKE '%signin%' OR ual.event_type = 'login' OR ual.event = 'login'
        )),
        (SELECT MAX(ual2.created_at) FROM user_activity_log ual2 WHERE ual2.user_id = u.user_id)
      )
    ) AS last_login";
      } else {
        $select[] = "NULL AS last_login";
      }

      // created_at: prefer users.created_at, else earliest activity log entry if available
      if ($hasCreatedAtColumn) {
        $select[] = "u.created_at AS created_at";
      } elseif ($hasActivityLog) {
        $select[] = "(SELECT MIN(ual2.created_at) FROM user_activity_log ual2 WHERE ual2.user_id = u.user_id) AS created_at";
      } else {
        $select[] = "NULL AS created_at";
      }

      $sql = "SELECT " . implode(', ', $select) . " FROM users u LEFT JOIN roles r ON u.role_id = r.role_id ORDER BY u.user_id DESC";

      $this->db->query($sql);
      if ($this->db->execute()) {
        $users = $this->db->resultSet();
        if ($users && count($users) > 0)
          return $users;
      }
    } catch (Exception $e) {
      error_log('getAllUsersWithRoles failed: ' . $e->getMessage());
    }
    return [];
  }

  // ... remaining helper methods (updateUserRole, toggleUserStatus, etc.) kept minimal for brevity

  /**
   * Return total number of users
   * @return int
   */
  public function getTotalUsers()
  {
    $this->db->query('SELECT COUNT(*) as total FROM users');
    if ($this->db->execute()) {
      $r = $this->db->single();
      return isset($r->total) ? (int) $r->total : 0;
    }
    return 0;
  }

  /**
   * Return number of active users (is_active = 1)
   * @return int
   */
  public function getActiveUsers()
  {
    $this->db->query('SELECT COUNT(*) as total FROM users WHERE is_active = 1');
    if ($this->db->execute()) {
      $r = $this->db->single();
      return isset($r->total) ? (int) $r->total : 0;
    }
    return 0;
  }

  /**
   * Count recent distinct logins in the last N days.
   * If users.last_login exists, use it; otherwise use user_activity_log when available.
   * @param int $days
   * @return int
   */
  public function getRecentLoginsCount($days = 7)
  {
    // Prefer users.last_login if column exists
    $this->db->query("SHOW COLUMNS FROM users LIKE 'last_login'");
    $this->db->execute();
    $hasLastLogin = (bool) $this->db->single();

    if ($hasLastLogin) {
      $this->db->query('SELECT COUNT(*) as total FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL :days DAY)');
      $this->db->bind(':days', (int) $days);
      if ($this->db->execute()) {
        $r = $this->db->single();
        return isset($r->total) ? (int) $r->total : 0;
      }
      return 0;
    }

    // Fallback to user_activity_log
    $this->db->query("SHOW TABLES LIKE 'user_activity_log'");
    $this->db->execute();
    $hasLog = (bool) $this->db->single();
    if ($hasLog) {
      $sql = "SELECT COUNT(DISTINCT user_id) as total FROM user_activity_log WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY) AND (action LIKE '%login%' OR action IN ('login','user_login'))";
      $this->db->query($sql);
      $this->db->bind(':days', (int) $days);
      if ($this->db->execute()) {
        $r = $this->db->single();
        return isset($r->total) ? (int) $r->total : 0;
      }
    }

    return 0;
  }

  /**
   * Return recent activity entries for the admin dashboard
   * @param int $limit
   * @return array
   */
  public function getRecentActivity($limit = 10)
  {
    try {
      // Prefer user_activity_log table if present
      $this->db->query("SHOW TABLES LIKE 'user_activity_log'");
      $this->db->execute();
      $hasLog = (bool) $this->db->single();

      if ($hasLog) {
        $this->db->query('SELECT ual.*, u.username, u.full_name FROM user_activity_log ual LEFT JOIN users u ON u.user_id = ual.user_id ORDER BY ual.created_at DESC LIMIT :limit');
        $this->db->bind(':limit', (int) $limit);
        if ($this->db->execute()) {
          return $this->db->resultSet();
        }
        return [];
      }

      // If no activity log table, attempt to return recent users (as a weak fallback)
      $this->db->query('SELECT user_id, username, full_name AS action, NULL as details, NULL as created_at FROM users ORDER BY user_id DESC LIMIT :limit');
      $this->db->bind(':limit', (int) $limit);
      if ($this->db->execute())
        return $this->db->resultSet();
    } catch (Exception $e) {
      error_log('getRecentActivity error: ' . $e->getMessage());
    }
    return [];
  }

  /**
   * Update the last_login timestamp for a user to NOW()
   * @param int $userId
   * @return bool
   */
  public function updateLastLogin($userId)
  {
    try {
      $this->db->query('UPDATE users SET last_login = NOW() WHERE user_id = :user_id');
      $this->db->bind(':user_id', (int) $userId);
      return (bool) $this->db->execute();
    } catch (Exception $e) {
      error_log('updateLastLogin error: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Set a user's status (active/inactive).
   * Will update both `status` (enum) and `is_active` (int) columns when present.
   * Prevents changing role data — role checks should be done by caller.
   * @param int $userId
   * @param string $status 'active'|'inactive'
   * @return bool
   */
  public function setStatus($userId, $status)
  {
    $status = strtolower($status) === 'active' ? 'active' : 'inactive';

    // Check columns
    $this->db->query("SHOW COLUMNS FROM users LIKE 'status'");
    $this->db->execute();
    $hasStatusCol = (bool) $this->db->single();

    $this->db->query("SHOW COLUMNS FROM users LIKE 'is_active'");
    $this->db->execute();
    $hasIsActiveCol = (bool) $this->db->single();

    $set = [];
    if ($hasStatusCol)
      $set[] = 'status = :status';
    if ($hasIsActiveCol)
      $set[] = 'is_active = :is_active';

    if (empty($set)) {
      // Nothing to update
      return false;
    }

    $sql = 'UPDATE users SET ' . implode(', ', $set) . ' WHERE user_id = :user_id';
    $this->db->query($sql);
    if ($hasStatusCol)
      $this->db->bind(':status', $status);
    if ($hasIsActiveCol)
      $this->db->bind(':is_active', $status === 'active' ? 1 : 0);
    $this->db->bind(':user_id', (int) $userId);
    try {
      return (bool) $this->db->execute();
    } catch (Exception $e) {
      error_log('setStatus error: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Get suggested category based on role
   * @param string $roleName
   * @return string
   */
  public function getSuggestedCategoryByRole($roleName)
  {
    $role = strtolower($roleName ?? 'user');

    // Internal staff roles -> official
    if (in_array($role, ['admin', 'super admin', 'manager', 'supervisor', 'associate', 'cashier', 'clerk'])) {
      return 'official';
    }
    // Customer roles -> customer
    elseif (in_array($role, ['customer', 'client', 'buyer'])) {
      return 'customer';
    }
    // External worker roles -> contractor
    elseif (in_array($role, ['contractor', 'vendor', 'supplier', 'freelancer'])) {
      return 'contractor';
    }

    // Default to official for unknown roles
    return 'official';
  }

  /**
   * Add new user
   * @param array $data
   * @return bool
   */
  public function addUser($data)
  {
    try {
      $this->db->query("
        INSERT INTO users (
          full_name, username, email, password_hash, role_id, is_active, created_at
        ) VALUES (
          :full_name, :username, :email, :password_hash, :role_id, :is_active, NOW()
        )
      ");

      $this->db->bind(':full_name', $data['name']);
      $this->db->bind(':username', $data['username']);
      $this->db->bind(':email', $data['email']);
      $this->db->bind(':password_hash', $data['password']);
      $this->db->bind(':role_id', $data['role_id']);
      $this->db->bind(':is_active', ($data['status'] === 'active') ? 1 : 0);

      return $this->db->execute();
    } catch (Exception $e) {
      error_log('addUser error: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Find user by email
   * @param string $email
   * @return bool
   */
  public function findUserByEmail($email)
  {
    $this->db->query('SELECT user_id FROM users WHERE email = :email');
    $this->db->bind(':email', $email);
    $this->db->execute();
    return (bool) $this->db->single();
  }

  /**
   * Set user category
   * @param int $userId
   * @param string $category 'official'|'customer'|'contractor'
   * @return bool
   */
  public function setUserCategory($userId, $category)
  {
    // Validate category
    $validCategories = ['official', 'customer', 'contractor'];
    if (!in_array($category, $validCategories)) {
      return false;
    }

    // First check if user_category column exists
    $this->db->query('SHOW COLUMNS FROM users LIKE "user_category"');
    $this->db->execute();
    $hasColumn = (bool) $this->db->single();

    if (!$hasColumn) {
      // Column doesn't exist, add it
      $this->db->query('ALTER TABLE users ADD COLUMN user_category VARCHAR(20) DEFAULT "official"');
      $this->db->execute();
    }

    // Update the user category
    $this->db->query('UPDATE users SET user_category = :category WHERE user_id = :user_id');
    $this->db->bind(':category', $category);
    $this->db->bind(':user_id', (int) $userId);

    try {
      return (bool) $this->db->execute();
    } catch (Exception $e) {
      error_log('setUserCategory error: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Get user by ID from any of the three tables (users, customers, contractors)
   * @param int $userId
   * @return object|false
   */
  public function getUserById($userId)
  {
    try {
      // First try to find in the users table (officials)
      $this->db->query("SELECT u.*, r.role_name as role_name 
        FROM users u 
        LEFT JOIN roles r ON u.role_id = r.role_id 
        WHERE u.user_id = :user_id");
      $this->db->bind(':user_id', $userId);
      $this->db->execute();
      $user = $this->db->single();

      if ($user) {
        $user->user_category = 'official';
        $user->source_table = 'users';
        return $user;
      }

      // Try customers table
      $this->db->query("SELECT 
        customer_id as user_id,
        customer_name as name,
        customer_name as username,
        contact_info,
        status,
        credit_limit,
        'Customer' as role_name,
        6 as role_id,
        'customer' as user_category,
        'customers' as source_table
        FROM customers 
        WHERE customer_id = :user_id");
      $this->db->bind(':user_id', $userId);
      $this->db->execute();
      $customer = $this->db->single();

      if ($customer) {
        // Parse contact_info JSON to extract email and phone
        $contactInfo = json_decode($customer->contact_info, true);
        if ($contactInfo && is_array($contactInfo)) {
          $customer->email = $contactInfo['email'] ?? '';
          $customer->phone = $contactInfo['phone'] ?? '';
          $customer->contact_person = $contactInfo['contact_person'] ?? '';
        } else {
          $customer->email = '';
          $customer->phone = '';
          $customer->contact_person = '';
        }
        return $customer;
      }

      // Try contractors table
      $this->db->query("SELECT 
        contractor_id as user_id,
        contractor_name as name,
        contractor_name as username,
        email,
        phone,
        is_active as status,
        specialization,
        'Contractor' as role_name,
        7 as role_id,
        'contractor' as user_category,
        'contractors' as source_table
        FROM contractors 
        WHERE contractor_id = :user_id");
      $this->db->bind(':user_id', $userId);
      $this->db->execute();
      $contractor = $this->db->single();

      if ($contractor) {
        return $contractor;
      }

      return false;
    } catch (Exception $e) {
      error_log('getUserById error: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Update user in the appropriate table based on their category
   * @param array $data
   * @return bool
   */
  public function updateUser($data)
  {
    try {
      $userId = $data['user_id'];
      $sourceTable = $data['source_table'] ?? 'users';

      switch ($sourceTable) {
        case 'users':
          // Update in users table
          $this->db->query("UPDATE users SET 
            name = :name, 
            email = :email, 
            role_id = :role_id,
            is_active = :status
            WHERE user_id = :user_id");
          $this->db->bind(':name', $data['name']);
          $this->db->bind(':email', $data['email']);
          $this->db->bind(':role_id', $data['role_id']);
          $this->db->bind(':status', $data['status'] === 'active' ? 1 : 0);
          $this->db->bind(':user_id', $userId);
          break;

        case 'customers':
          // Update in customers table
          // Get existing contact_info and update it
          $existingUser = $this->getUserByIdAndTable($userId, 'customers');
          $contactInfo = json_decode($existingUser->contact_info ?? '{}', true) ?: [];
          $contactInfo['contact_person'] = $data['name'];
          $contactInfo['email'] = $data['email'];

          $this->db->query("UPDATE customers SET 
            customer_name = :name, 
            contact_info = :contact_info,
            status = :status
            WHERE customer_id = :user_id");
          $this->db->bind(':name', $data['name']);
          $this->db->bind(':contact_info', json_encode($contactInfo));
          $this->db->bind(':status', $data['status']);
          $this->db->bind(':user_id', $userId);
          break;

        case 'contractors':
          // Update in contractors table
          $this->db->query("UPDATE contractors SET 
            contractor_name = :name, 
            email = :email,
            is_active = :status
            WHERE contractor_id = :user_id");
          $this->db->bind(':name', $data['name']);
          $this->db->bind(':email', $data['email']);
          $this->db->bind(':status', $data['status'] === 'active' ? 1 : 0);
          $this->db->bind(':user_id', $userId);
          break;

        default:
          return false;
      }

      return $this->db->execute();
    } catch (Exception $e) {
      error_log('updateUser error: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Log user activity
   * @param int $userId
   * @param string $action
   * @param string $description
   * @return bool
   */
  public function logActivity($userId, $action, $description)
  {
    try {
      $this->db->query("INSERT INTO activity_logs (user_id, action, description, created_at) 
                        VALUES (:user_id, :action, :description, NOW())");
      $this->db->bind(':user_id', $userId);
      $this->db->bind(':action', $action);
      $this->db->bind(':description', $description);
      return $this->db->execute();
    } catch (Exception $e) {
      // If activity_logs table doesn't exist, just log to error log and return true
      error_log('logActivity: ' . $e->getMessage());
      return true; // Don't fail the main operation if logging fails
    }
  }

  /**
   * Get user by ID from a specific table
   * @param int $userId
   * @param string $sourceTable
   * @return object|false
   */
  public function getUserByIdAndTable($userId, $sourceTable)
  {
    try {
      // Diagnostic logging to help debug lookup failures
      try {
        $logPath = __DIR__ . '/../../storage/logs/app.log';
        $dbg = sprintf("[%s] getUserByIdAndTable called. userId=%s, sourceTable=%s\n", date('Y-m-d H:i:s'), var_export($userId, true), var_export($sourceTable, true));
        @file_put_contents($logPath, $dbg, FILE_APPEND | LOCK_EX);
      } catch (Exception $e) {
        // swallow
      }
      switch ($sourceTable) {
        case 'users':
          $this->db->query("SELECT u.*, r.role_name as role_name
                            FROM users u
                            LEFT JOIN roles r ON u.role_id = r.role_id
                            WHERE u.user_id = :user_id");
          $this->db->bind(':user_id', $userId);
          $this->db->execute();
          $user = $this->db->single();

          if ($user) {
            // Log successful find
            try {
              @file_put_contents($logPath, sprintf("[%s] getUserByIdAndTable: found user in users for userId=%s\n", date('Y-m-d H:i:s'), var_export($userId, true)), FILE_APPEND | LOCK_EX);
            } catch (Exception $e) {
            }
            $user->user_category = 'official';
            $user->source_table = 'users';
            return $user;
          }
          // Log miss
          try {
            @file_put_contents($logPath, sprintf("[%s] getUserByIdAndTable: no user in users for userId=%s\n", date('Y-m-d H:i:s'), var_export($userId, true)), FILE_APPEND | LOCK_EX);
          } catch (Exception $e) {
          }
          break;

        case 'customers':
          $this->db->query("SELECT
            customer_id as user_id,
            customer_name as name,
            customer_name as username,
            contact_info,
            status,
            credit_limit,
            'Customer' as role_name,
            6 as role_id,
            'customer' as user_category,
            'customers' as source_table
            FROM customers
            WHERE customer_id = :user_id");
          $this->db->bind(':user_id', $userId);
          $this->db->execute();
          $customer = $this->db->single();

          if ($customer) {
            try {
              @file_put_contents($logPath, sprintf("[%s] getUserByIdAndTable: found user in customers for userId=%s\n", date('Y-m-d H:i:s'), var_export($userId, true)), FILE_APPEND | LOCK_EX);
            } catch (Exception $e) {
            }
            // Parse contact_info JSON to extract email and phone
            $contactInfo = json_decode($customer->contact_info, true);
            if ($contactInfo && is_array($contactInfo)) {
              $customer->email = $contactInfo['email'] ?? '';
              $customer->phone = $contactInfo['phone'] ?? '';
              $customer->contact_person = $contactInfo['contact_person'] ?? '';
            } else {
              $customer->email = '';
              $customer->phone = '';
              $customer->contact_person = '';
            }
            return $customer;
          }
          try {
            @file_put_contents($logPath, sprintf("[%s] getUserByIdAndTable: no user in customers for userId=%s\n", date('Y-m-d H:i:s'), var_export($userId, true)), FILE_APPEND | LOCK_EX);
          } catch (Exception $e) {
          }
          break;

        case 'contractors':
          $this->db->query("SELECT
            contractor_id as user_id,
            contractor_name as name,
            contractor_name as username,
            email,
            phone,
            is_active as status,
            specialization,
            'Contractor' as role_name,
            7 as role_id,
            'contractor' as user_category,
            'contractors' as source_table
            FROM contractors
            WHERE contractor_id = :user_id");
          $this->db->bind(':user_id', $userId);
          $this->db->execute();
          $contractor = $this->db->single();

          if ($contractor) {
            try {
              @file_put_contents($logPath, sprintf("[%s] getUserByIdAndTable: found user in contractors for userId=%s\n", date('Y-m-d H:i:s'), var_export($userId, true)), FILE_APPEND | LOCK_EX);
            } catch (Exception $e) {
            }
            return $contractor;
          }
          try {
            @file_put_contents($logPath, sprintf("[%s] getUserByIdAndTable: no user in contractors for userId=%s\n", date('Y-m-d H:i:s'), var_export($userId, true)), FILE_APPEND | LOCK_EX);
          } catch (Exception $e) {
          }
          break;
      }

      try {
        @file_put_contents($logPath, sprintf("[%s] getUserByIdAndTable: finished lookup, no record found for userId=%s (sourceTable=%s)\n", date('Y-m-d H:i:s'), var_export($userId, true), var_export($sourceTable, true)), FILE_APPEND | LOCK_EX);
      } catch (Exception $e) {
      }
      return false;
    } catch (Exception $e) {
      error_log('getUserByIdAndTable error: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Set customer status
   * @param int $customerId
   * @param string $status
   * @return bool
   */
  public function setCustomerStatus($customerId, $status)
  {
    try {
      $this->db->query('UPDATE customers SET status = :status WHERE customer_id = :customer_id');
      $this->db->bind(':status', $status);
      $this->db->bind(':customer_id', $customerId);
      return $this->db->execute();
    } catch (Exception $e) {
      error_log('setCustomerStatus error: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Set contractor status
   * @param int $contractorId
   * @param string $status
   * @return bool
   */
  public function setContractorStatus($contractorId, $status)
  {
    try {
      $isActive = ($status === 'active') ? 1 : 0;
      $this->db->query('UPDATE contractors SET is_active = :is_active WHERE contractor_id = :contractor_id');
      $this->db->bind(':is_active', $isActive);
      $this->db->bind(':contractor_id', $contractorId);
      return $this->db->execute();
    } catch (Exception $e) {
      error_log('setContractorStatus error: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Update user profile across different tables (users, customers, contractors)
   * @param int $userId
   * @param array $data
   * @param string $sourceTable
   * @return bool
   */
  public function updateUserProfile($userId, $data, $sourceTable = 'users')
  {
    try {
      switch ($sourceTable) {
        case 'users':
          $this->db->query("UPDATE users SET 
            full_name = :name,
            email = :email,
            job_title = :job_title,
            address = :address,
            birthday = :birthday,
            education = :education
            " . (isset($data['profile_picture']) ? ", profile_picture = :profile_picture" : "") . "
            WHERE user_id = :user_id");

          $this->db->bind(':name', $data['name'] ?? null);
          $this->db->bind(':email', $data['email'] ?? null);
          $this->db->bind(':job_title', $data['job_title'] ?? null);
          $this->db->bind(':address', $data['address'] ?? null);
          $this->db->bind(':birthday', $data['birthday'] ?? null);
          $this->db->bind(':education', $data['education'] ?? null);
          if (isset($data['profile_picture'])) {
            $this->db->bind(':profile_picture', $data['profile_picture']);
          }
          $this->db->bind(':user_id', $userId);
          break;

        case 'customers':
          // For customers, update the contact_info JSON and customer_name
          $contactInfo = [
            'email'          => $data['email'] ?? '',
            'phone'          => $data['phone'] ?? '',
            'contact_person' => $data['name'] ?? ''
          ];

          $this->db->query("UPDATE customers SET 
            customer_name = :name,
            contact_info = :contact_info
            WHERE customer_id = :user_id");

          $this->db->bind(':name', $data['name'] ?? null);
          $this->db->bind(':contact_info', json_encode($contactInfo));
          $this->db->bind(':user_id', $userId);
          break;

        case 'contractors':
          $this->db->query("UPDATE contractors SET 
            contractor_name = :name,
            email = :email,
            phone = :phone
            WHERE contractor_id = :user_id");

          $this->db->bind(':name', $data['name'] ?? null);
          $this->db->bind(':email', $data['email'] ?? null);
          $this->db->bind(':phone', $data['phone'] ?? null);
          $this->db->bind(':user_id', $userId);
          break;

        default:
          return false;
      }

      return $this->db->execute();
    } catch (Exception $e) {
      error_log('updateUserProfile error: ' . $e->getMessage());
      return false;
    }
  }
}
