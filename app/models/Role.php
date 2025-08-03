<?php
/**
 * Role Model
 * Handles role management and permissions
 */
class Role
{
    private $db;

    // Default role permissions
    const PERMISSIONS = [
        'view_dashboard',
        'manage_sales',
        'manage_purchases',
        'manage_inventory',
        'manage_customers',
        'manage_suppliers',
        'manage_products',
        'view_reports',
        'manage_users',
        'manage_settings',
        'all' // Super admin permission
    ];

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all roles
     * @return array
     */
    public function getAllRoles()
    {
        $this->db->query('SELECT * FROM roles ORDER BY role_name');
        return $this->db->resultSet();
    }

    /**
     * Get role by ID
     * @param int $roleId
     * @return object|null
     */
    public function getRoleById($roleId)
    {
        $this->db->query('SELECT * FROM roles WHERE role_id = :role_id');
        $this->db->bind(':role_id', $roleId);
        return $this->db->single();
    }

    /**
     * Create new role
     * @param array $data
     * @return bool
     */
    public function createRole($data)
    {
        $this->db->query('
            INSERT INTO roles (role_name, description, permissions)
            VALUES (:role_name, :description, :permissions)
        ');
        $this->db->bind(':role_name', $data['role_name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':permissions', json_encode($data['permissions']));
        return $this->db->execute();
    }

    /**
     * Update role
     * @param int $roleId
     * @param array $data
     * @return bool
     */
    public function updateRole($roleId, $data)
    {
        $this->db->query('
            UPDATE roles 
            SET role_name = :role_name, description = :description, permissions = :permissions
            WHERE role_id = :role_id
        ');
        $this->db->bind(':role_id', $roleId);
        $this->db->bind(':role_name', $data['role_name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':permissions', json_encode($data['permissions']));
        return $this->db->execute();
    }

    /**
     * Delete role
     * @param int $roleId
     * @return bool
     */
    public function deleteRole($roleId)
    {
        // Check if role is in use
        if ($this->isRoleInUse($roleId)) {
            return false;
        }

        $this->db->query('DELETE FROM roles WHERE role_id = :role_id');
        $this->db->bind(':role_id', $roleId);
        return $this->db->execute();
    }

    /**
     * Check if role is being used by any user
     * @param int $roleId
     * @return bool
     */
    public function isRoleInUse($roleId)
    {
        $this->db->query('SELECT COUNT(*) as count FROM users WHERE role_id = :role_id');
        $this->db->bind(':role_id', $roleId);
        $result = $this->db->single();
        return $result->count > 0;
    }

    /**
     * Get total number of roles
     * @return int
     */
    public function getTotalRoles()
    {
        $this->db->query('SELECT COUNT(*) as count FROM roles');
        $result = $this->db->single();
        return $result ? (int) $result->count : 0;
    }

    /**
     * Get default roles for system initialization
     * @return array
     */
    public function getDefaultRoles()
    {
        return [
            [
                'role_name' => 'Super Admin',
                'description' => 'Full system access',
                'permissions' => ['all']
            ],
            [
                'role_name' => 'Manager',
                'description' => 'Store manager with most permissions',
                'permissions' => [
                    'view_dashboard',
                    'manage_sales',
                    'manage_purchases',
                    'manage_inventory',
                    'manage_customers',
                    'manage_suppliers',
                    'manage_products',
                    'view_reports'
                ]
            ],
            [
                'role_name' => 'Cashier',
                'description' => 'Sales and basic customer management',
                'permissions' => [
                    'view_dashboard',
                    'manage_sales',
                    'manage_customers'
                ]
            ],
            [
                'role_name' => 'Stock Clerk',
                'description' => 'Inventory and product management',
                'permissions' => [
                    'view_dashboard',
                    'manage_inventory',
                    'manage_products'
                ]
            ]
        ];
    }

    /**
     * Initialize default roles
     * @return bool
     */
    public function initializeDefaultRoles()
    {
        $defaultRoles = $this->getDefaultRoles();
        $success = true;

        foreach ($defaultRoles as $role) {
            if (!$this->createRole($role)) {
                $success = false;
            }
        }

        return $success;
    }
}
?>