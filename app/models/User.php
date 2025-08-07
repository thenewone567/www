<?php
class User
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  // Register user
  public function register($data)
  {
    $this->db->query('INSERT INTO users (username, password_hash, role_id) VALUES(:username, :password, :role_id)');
    // Bind values
    $this->db->bind(':username', $data['username']);
    $this->db->bind(':password', $data['password']);
    $this->db->bind(':role_id', $data['role_id']);

    // Execute
    return $this->db->execute();
  }

  // Validate username with comprehensive rules
  public function validateUsername($username)
  {
    $errors = [];

    // Check if username is empty
    if (empty($username)) {
      $errors[] = 'Username is required';
      return $errors;
    }

    // Length validation (3-20 characters)
    if (strlen($username) < 3) {
      $errors[] = 'Username must be at least 3 characters long';
    }
    if (strlen($username) > 20) {
      $errors[] = 'Username must not exceed 20 characters';
    }

    // No spaces/gaps allowed
    if (strpos($username, ' ') !== false) {
      $errors[] = 'Username cannot contain spaces';
    }

    // Only alphanumeric characters and underscores allowed
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
      $errors[] = 'Username can only contain letters, numbers, and underscores';
    }

    // Must start with a letter
    if (!preg_match('/^[a-zA-Z]/', $username)) {
      $errors[] = 'Username must start with a letter';
    }

    // Cannot end with underscore
    if (substr($username, -1) === '_') {
      $errors[] = 'Username cannot end with an underscore';
    }

    // No consecutive underscores
    if (strpos($username, '__') !== false) {
      $errors[] = 'Username cannot contain consecutive underscores';
    }

    // Reserved usernames
    $reserved = ['admin', 'root', 'user', 'test', 'guest', 'null', 'undefined', 'system', 'administrator'];
    if (in_array(strtolower($username), $reserved)) {
      $errors[] = 'This username is reserved and cannot be used';
    }

    // Check if username already exists
    if (empty($errors) && $this->findUserByUsername($username)) {
      $errors[] = 'Username is already taken';
    }

    return $errors;
  }

  // Enhanced Login User with security features
  public function login($username, $password)
  {
    // Check for account lockout first
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
      // Log failed login attempt
      $this->logFailedLogin($username, 'Username not found');
      return false;
    }

    $hashed_password = $row->password_hash;
    if (password_verify($password, $hashed_password)) {
      // Reset failed login attempts on successful login
      $this->resetFailedLoginAttempts($username);

      // Update last login time
      $this->updateLastLogin($row->user_id);

      return $row;
    } else {
      // Log failed login attempt
      $this->logFailedLogin($username, 'Incorrect password');
      $this->incrementFailedLoginAttempts($username);
      return false;
    }
  }

  /**
   * Check if account is locked due to failed login attempts
   */
  private function isAccountLocked($username)
  {
    // Simple implementation - can be enhanced
    return false; // For now, no account locking
  }

  /**
   * Log failed login attempts
   */
  private function logFailedLogin($username, $reason)
  {
    if (defined('LOG_FILE')) {
      $timestamp = date("Y-m-d H:i:s");
      $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
      $logMessage = "[$timestamp] FAILED LOGIN: Username '$username' - $reason (IP: $ip)" . PHP_EOL;
      file_put_contents(LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
    }
  }

  /**
   * Increment failed login attempts (placeholder for future implementation)
   */
  private function incrementFailedLoginAttempts($username)
  {
    // Future: implement failed login tracking
  }

  /**
   * Reset failed login attempts (placeholder for future implementation)
   */
  private function resetFailedLoginAttempts($username)
  {
    // Future: reset failed login counter
  }

  // Find user by username
  public function findUserByUsername($username)
  {
    $this->db->query('SELECT * FROM users WHERE username = :username');
    $this->db->bind(':username', $username);

    if (!$this->db->execute()) {
      return false;
    }

    $row = $this->db->single();

    // Check row
    return $row ? true : false;
  }

  /**
   * Get user with role information
   * @param int $userId
   * @return object|null
   */
  public function getUserWithRole($userId)
  {
    // Fixed query without the non-existent permissions column
    $success = $this->db->query('
      SELECT u.user_id, u.username, u.password_hash, u.role_id, u.is_active,
             r.role_name
      FROM users u
      LEFT JOIN roles r ON u.role_id = r.role_id
      WHERE u.user_id = :user_id
    ');

    if ($success) {
      $this->db->bind(':user_id', $userId);
      $this->db->execute(); // Added missing execute() call
      $result = $this->db->single();

      // Check if we got a valid result and it has the expected properties
      if ($result && isset($result->user_id) && isset($result->username)) {
        return $result;
      }
    }

    // Fallback: get user without role using single() method
    $this->db->query('SELECT * FROM users WHERE user_id = :user_id');
    $this->db->bind(':user_id', $userId);
    $this->db->execute(); // Added missing execute() call

    $user = $this->db->single();
    if ($user) {
      // Add default role info for display
      $user->role_name = 'Associate'; // Default fallback
      return $user;
    }

    return null;
  }

  /**
   * Get all users with roles
   * @return array
   */
  public function getAllUsersWithRoles()
  {
    try {
      // Try the comprehensive query first
      $this->db->query('
        SELECT u.user_id, u.name, u.username, u.email, u.role_id, u.status,
               u.last_login, u.created_at, u.updated_at,
               COALESCE(r.role_name, r.name, "User") as role_name
        FROM users u
        LEFT JOIN roles r ON (u.role_id = r.role_id OR u.role_id = r.id)
        ORDER BY u.created_at DESC
      ');

      if ($this->db->execute()) {
        $users = $this->db->resultSet();
        if ($users && count($users) > 0) {
          return $users;
        }
      }

    } catch (Exception $e) {
      error_log("getAllUsersWithRoles JOIN failed: " . $e->getMessage());
    }

    // Fallback 1: Try simpler query with different column names
    try {
      $this->db->query('
        SELECT u.*, COALESCE(r.role_name, r.name, "User") as role_name
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.role_id
        ORDER BY u.created_at DESC
      ');

      if ($this->db->execute()) {
        $users = $this->db->resultSet();
        if ($users && count($users) > 0) {
          return $users;
        }
      }

    } catch (Exception $e) {
      error_log("getAllUsersWithRoles fallback 1 failed: " . $e->getMessage());
    }

    // Fallback 2: Get users without roles
    try {
      $this->db->query('SELECT * FROM users ORDER BY created_at DESC');

      if ($this->db->execute()) {
        $users = $this->db->resultSet();

        if ($users && count($users) > 0) {
          // Add default role name to each user
          foreach ($users as &$user) {
            $user->role_name = 'User';
          }
          return $users;
        }
      }

    } catch (Exception $e) {
      error_log("getAllUsersWithRoles fallback 2 failed: " . $e->getMessage());
    }

    // Last resort: return empty array
    return [];
  }  /**
     * Update user role
     * @param int $userId
     * @param int $roleId
     * @return bool
     */
  public function updateUserRole($userId, $roleId)
  {
    $this->db->query('UPDATE users SET role_id = :role_id WHERE user_id = :user_id');
    $this->db->bind(':role_id', $roleId);
    $this->db->bind(':user_id', $userId);
    return $this->db->execute();
  }

  /**
   * Toggle user active status
   * @param int $userId
   * @param string $status
   * @return bool
   */
  public function toggleUserStatus($userId, $status = null)
  {
    if ($status) {
      $this->db->query('UPDATE users SET status = :status WHERE user_id = :user_id');
      $this->db->bind(':status', $status);
    } else {
      $this->db->query('UPDATE users SET is_active = NOT is_active WHERE user_id = :user_id');
    }
    $this->db->bind(':user_id', $userId);
    return $this->db->execute();
  }

  /**
   * Get user activity log
   * @param int $userId
   * @param int $limit
   * @return array
   */
  public function getUserActivity($userId, $limit = 50)
  {
    $this->db->query('
      SELECT * FROM user_activity_log 
      WHERE user_id = :user_id 
      ORDER BY created_at DESC 
      LIMIT :limit
    ');
    $this->db->bind(':user_id', $userId);
    $this->db->bind(':limit', $limit);
    return $this->db->resultSet();
  }

  /**
   * Log user activity
   * @param int $userId
   * @param string $action
   * @param string $details
   * @return bool
   */
  public function logActivity($userId, $action, $details = '')
  {
    $this->db->query('
      INSERT INTO user_activity_log (user_id, action, details, ip_address, user_agent)
      VALUES (:user_id, :action, :details, :ip_address, :user_agent)
    ');
    $this->db->bind(':user_id', $userId);
    $this->db->bind(':action', $action);
    $this->db->bind(':details', $details);
    $this->db->bind(':ip_address', $_SERVER['REMOTE_ADDR'] ?? '');
    $this->db->bind(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '');
    return $this->db->execute();
  }

  /**
   * Check if user has permission
   * @param int $userId
   * @param string $permission
   * @return bool
   */
  public function hasPermission($userId, $permission)
  {
    $user = $this->getUserWithRole($userId);
    if (!$user || !$user->permissions) {
      return false;
    }

    $permissions = json_decode($user->permissions, true);
    return in_array($permission, $permissions) || in_array('all', $permissions);
  }

  /**
   * Add new user
   * @param array $data
   * @return bool
   */
  public function addUser($data)
  {
    try {
      // First, check what columns exist in the users table
      $columns = ['name', 'email', 'password_hash', 'role_id', 'status'];
      $values = [':name', ':email', ':password', ':role_id', ':status'];

      // Add username if provided
      if (isset($data['username']) && !empty($data['username'])) {
        $columns[] = 'username';
        $values[] = ':username';
      }

      // Build the query dynamically
      $columnList = implode(', ', $columns);
      $valueList = implode(', ', $values);

      $this->db->query("
        INSERT INTO users ({$columnList}) 
        VALUES ({$valueList})
      ");

      // Bind all values
      $this->db->bind(':name', $data['name']);
      $this->db->bind(':email', $data['email']);
      $this->db->bind(':password', $data['password']);
      $this->db->bind(':role_id', $data['role_id']);
      $this->db->bind(':status', $data['status'] ?? 'active');

      if (isset($data['username']) && !empty($data['username'])) {
        $this->db->bind(':username', $data['username']);
      }

      $result = $this->db->execute();

      if ($result) {
        error_log("User added successfully: " . $data['email']);
      } else {
        error_log("Failed to add user: " . $data['email']);
      }

      return $result;

    } catch (Exception $e) {
      error_log("addUser exception: " . $e->getMessage());
      return false;
    }
  }

  /**
   * Update user
   * @param array $data
   * @return bool
   */
  public function updateUser($data)
  {
    $this->db->query("
      UPDATE users 
      SET name = :name, email = :email, role_id = :role_id, status = :status 
      WHERE user_id = :user_id
    ");

    $this->db->bind(':user_id', $data['user_id']);
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':role_id', $data['role_id']);
    $this->db->bind(':status', $data['status']);

    return $this->db->execute();
  }

  /**
   * Get user by ID
   * @param int $userId
   * @return object|false
   */
  public function getUserById($userId)
  {
    $this->db->query("SELECT * FROM users WHERE user_id = :user_id");
    $this->db->bind(':user_id', $userId);
    return $this->db->single();
  }

  /**
   * Find user by email
   * @param string $email
   * @return object|false
   */
  public function findUserByEmail($email)
  {
    $this->db->query("SELECT * FROM users WHERE email = :email");
    $this->db->bind(':email', $email);
    return $this->db->single();
  }

  /**
   * Get total users count
   * @return int
   */
  public function getTotalUsers()
  {
    $this->db->query("SELECT COUNT(*) as total FROM users");
    $result = $this->db->single();
    return $result->total ?? 0;
  }

  /**
   * Get active users count
   * @return int
   */
  public function getActiveUsers()
  {
    $this->db->query("SELECT COUNT(*) as total FROM users WHERE status = 'active'");
    $result = $this->db->single();
    return $result->total ?? 0;
  }

  /**
   * Get recent logins count for specified days
   * @param int $days
   * @return int
   */
  public function getRecentLoginsCount($days = 7)
  {
    $this->db->query("
      SELECT COUNT(DISTINCT user_id) as total 
      FROM user_activity_log 
      WHERE action = 'login' AND created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
    ");
    $this->db->bind(':days', $days);
    $result = $this->db->single();
    return $result->total ?? 0;
  }

  /**
   * Get recent activity
   * @param int $limit
   * @return array
   */
  public function getRecentActivity($limit = 20)
  {
    $this->db->query("
      SELECT ual.*, u.name as user_name
      FROM user_activity_log ual
      LEFT JOIN users u ON ual.user_id = u.user_id
      ORDER BY ual.created_at DESC
      LIMIT :limit
    ");
    $this->db->bind(':limit', $limit);
    return $this->db->resultSet();
  }

  /**
   * Get activity logs with pagination and filtering
   * @param int $limit
   * @param int $offset
   * @param string|null $filter
   * @return array
   */
  public function getActivityLogs($limit = 50, $offset = 0, $filter = null)
  {
    $whereClause = '';
    $params = [];

    if ($filter) {
      $whereClause = 'WHERE ual.action = :filter';
      $params[':filter'] = $filter;
    }

    $sql = "SELECT ual.*, u.name as user_name
            FROM user_activity_log ual
            LEFT JOIN users u ON ual.user_id = u.id
            $whereClause
            ORDER BY ual.created_at DESC
            LIMIT :limit OFFSET :offset";

    $this->db->query($sql);

    // Bind parameters
    foreach ($params as $key => $value) {
      $this->db->bind($key, $value);
    }
    $this->db->bind(':limit', $limit, PDO::PARAM_INT);
    $this->db->bind(':offset', $offset, PDO::PARAM_INT);

    return $this->db->resultSet();
  }

  /**
   * Get total activity count with optional filtering
   * @param string|null $filter
   * @return int
   */
  public function getTotalActivityCount($filter = null)
  {
    $whereClause = '';
    $params = [];

    if ($filter) {
      $whereClause = 'WHERE action = :filter';
      $params[':filter'] = $filter;
    }

    $sql = "SELECT COUNT(*) as total FROM user_activity_log $whereClause";

    $this->db->query($sql);

    foreach ($params as $key => $value) {
      $this->db->bind($key, $value);
    }

    $result = $this->db->single();
    return $result->total ?? 0;
  }

  /**
   * Get activity logs count (alias for compatibility)
   */
  public function getActivityLogsCount($filter = null)
  {
    return $this->getTotalActivityCount($filter);
  }

  /**
   * Update last login timestamp
   * @param int $userId
   * @return bool
   */
  public function updateLastLogin($userId)
  {
    $this->db->query("UPDATE users SET last_login = NOW() WHERE user_id = :user_id");
    $this->db->bind(':user_id', $userId);
    return $this->db->execute();
  }

  /**
   * Find user by ID
   * @param int $id
   * @return object|null
   */
  public function findUserById($id)
  {
    $this->db->query('SELECT * FROM users WHERE user_id = :user_id');
    $this->db->bind(':user_id', $id);
    return $this->db->single();
  }

  /**
   * Update user password
   * @param int $userId
   * @param string $hashedPassword
   * @return bool
   */
  public function updatePassword($userId, $hashedPassword)
  {
    $this->db->query('UPDATE users SET password_hash = :password WHERE user_id = :user_id');
    $this->db->bind(':password', $hashedPassword);
    $this->db->bind(':user_id', $userId);
    return $this->db->execute();
  }

  /**
   * Get all users with their permissions
   * @return array
   */
  public function getAllUsersWithPermissions()
  {
    try {
      $this->db->query("SELECT u.user_id, u.username, u.name, u.email, u.status, 
                               r.role_name, up.permissions
                        FROM users u
                        LEFT JOIN roles r ON u.role_id = r.role_id
                        LEFT JOIN user_permissions up ON u.user_id = up.user_id
                        WHERE u.status = 'active'
                        ORDER BY u.username");

      $users = $this->db->resultSet();

      // Parse permissions JSON for each user
      foreach ($users as $user) {
        $user->permissions = $user->permissions ? json_decode($user->permissions, true) : [];
      }

      return $users;
    } catch (Exception $e) {
      error_log("Error in getAllUsersWithPermissions: " . $e->getMessage());
      return [];
    }
  }

  /**
   * Update user permissions
   * @param int $userId
   * @param array $permissions
   * @return bool
   */
  public function updateUserPermissions($userId, $permissions)
  {
    try {
      $this->db->beginTransaction();

      // Convert permissions array to JSON
      $permissionsJson = json_encode($permissions);

      // Check if user permissions record exists
      $this->db->query("SELECT user_id FROM user_permissions WHERE user_id = :user_id");
      $this->db->bind(':user_id', $userId);
      $existing = $this->db->single();

      if ($existing) {
        // Update existing permissions
        $this->db->query("UPDATE user_permissions 
                         SET permissions = :permissions, updated_at = NOW()
                         WHERE user_id = :user_id");
      } else {
        // Insert new permissions record
        $this->db->query("INSERT INTO user_permissions (user_id, permissions, created_at, updated_at)
                         VALUES (:user_id, :permissions, NOW(), NOW())");
      }

      $this->db->bind(':user_id', $userId);
      $this->db->bind(':permissions', $permissionsJson);

      if (!$this->db->execute()) {
        throw new Exception("Failed to update user permissions");
      }

      // Log the activity
      $this->logActivity(
        $_SESSION['user_id'] ?? 0,
        'permissions_updated',
        "Updated permissions for user ID: $userId"
      );

      $this->db->commit();
      return true;
    } catch (Exception $e) {
      $this->db->rollback();
      error_log("Error in updateUserPermissions: " . $e->getMessage());
      return false;
    }
  }

  /**
   * Get user permissions
   * @param int $userId
   * @return array
   */
  public function getUserPermissions($userId)
  {
    try {
      $this->db->query("SELECT permissions FROM user_permissions WHERE user_id = :user_id");
      $this->db->bind(':user_id', $userId);
      $result = $this->db->single();

      if ($result && $result->permissions) {
        return json_decode($result->permissions, true);
      }

      return [];
    } catch (Exception $e) {
      error_log("Error in getUserPermissions: " . $e->getMessage());
      return [];
    }
  }

  /**
   * Check if user has permission for a specific page/module
   * @param int $userId
   * @param string $page
   * @return bool
   */
  public function hasPagePermission($userId, $page)
  {
    $permissions = $this->getUserPermissions($userId);
    return isset($permissions[$page]) && $permissions[$page] === true;
  }

  /**
   * Update user profile picture
   * @param int $userId
   * @param string $filename
   * @return bool
   */
  public function updateProfilePicture($userId, $filename)
  {
    $this->db->query("UPDATE users SET profile_picture = :profile_picture WHERE user_id = :user_id");
    $this->db->bind(':user_id', $userId);
    $this->db->bind(':profile_picture', $filename);
    return $this->db->execute();
  }

  /**
   * Get user profile picture
   * @param int $userId
   * @return string|null
   */
  public function getProfilePicture($userId)
  {
    $this->db->query("SELECT profile_picture FROM users WHERE user_id = :user_id");
    $this->db->bind(':user_id', $userId);
    $result = $this->db->single();
    return $result ? $result->profile_picture : null;
  }

  /**
   * Remove user profile picture
   * @param int $userId
   * @return bool
   */
  public function removeProfilePicture($userId)
  {
    $this->db->query("UPDATE users SET profile_picture = NULL WHERE user_id = :user_id");
    $this->db->bind(':user_id', $userId);
    return $this->db->execute();
  }
}