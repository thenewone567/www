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

  // Login User
  public function login($username, $password)
  {
    $this->db->query('SELECT * FROM users WHERE username = :username');
    $this->db->bind(':username', $username);

    $row = $this->db->single();
    if (!$row) {
      return false;
    }

    $hashed_password = $row->password_hash;
    if (password_verify($password, $hashed_password)) {
      return $row;
    } else {
      return false;
    }
  }

  // Find user by username
  public function findUserByUsername($username)
  {
    $this->db->query('SELECT * FROM users WHERE username = :username');
    $this->db->bind(':username', $username);

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
    // Try both possible schemas for roles table
    try {
      // First try admin_panel schema (id, name)
      $this->db->query('
        SELECT u.*, r.name as role_name, r.permissions 
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
        WHERE u.user_id = :user_id
      ');
      $this->db->bind(':user_id', $userId);
      $result = $this->db->single();

      if ($result && !empty($result->role_name)) {
        return $result;
      }
    } catch (Exception $e) {
      // If first query fails, try enhancement schema
    }

    try {
      // Try enhancement schema (role_id, role_name)
      $this->db->query('
        SELECT u.*, r.role_name, r.permissions 
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.role_id
        WHERE u.user_id = :user_id
      ');
      $this->db->bind(':user_id', $userId);
      return $this->db->single();
    } catch (Exception $e) {
      // If both fail, return user without role info
      $this->db->query('SELECT * FROM users WHERE user_id = :user_id');
      $this->db->bind(':user_id', $userId);
      $user = $this->db->single();
      if ($user) {
        $user->role_name = 'employee'; // default fallback
      }
      return $user;
    }
  }

  /**
   * Get all users with roles
   * @return array
   */
  public function getAllUsersWithRoles()
  {
    // Try both possible schemas for roles table
    try {
      // First try admin_panel schema (id, name)
      $this->db->query('
        SELECT u.user_id, u.username, u.created_at, u.is_active,
               r.name as role_name, r.permissions
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
        ORDER BY u.created_at DESC
      ');
      $result = $this->db->resultSet();

      // Check if we got valid role data
      if (!empty($result) && !empty($result[0]->role_name)) {
        return $result;
      }
    } catch (Exception $e) {
      // If first query fails, try enhancement schema
    }

    try {
      // Try enhancement schema (role_id, role_name)
      $this->db->query('
        SELECT u.user_id, u.username, u.created_at, u.is_active,
               r.role_name, r.permissions
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.role_id
        ORDER BY u.created_at DESC
      ');
      return $this->db->resultSet();
    } catch (Exception $e) {
      // If both fail, return users without role info
      $this->db->query('SELECT user_id, username, created_at, role_id FROM users ORDER BY created_at DESC');
      return $this->db->resultSet();
    }
  }

  /**
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
    $this->db->query("
      INSERT INTO users (name, email, password_hash, role_id, status) 
      VALUES (:name, :email, :password, :role_id, :status)
    ");

    $this->db->bind(':name', $data['name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':password', $data['password']);
    $this->db->bind(':role_id', $data['role_id']);
    $this->db->bind(':status', $data['status']);

    return $this->db->execute();
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
}