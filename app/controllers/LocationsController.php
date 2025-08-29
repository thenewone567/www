<?php
/**
 * Multi-Location Controller
 * Handles enterprise multi-location operations
 */
class LocationsController extends Controller
{
    private $businessLocationModel;
    private $userModel;

    public function __construct()
    {
        $this->businessLocationModel = $this->model('BusinessLocation');
        $this->userModel = $this->model('User');
        
        // Ensure user is logged in
        if (!isLoggedIn()) {
            redirect('auth/login');
        }
    }

    /**
     * Location dashboard - shows all accessible locations
     */
    public function index()
    {
        $userId = $_SESSION['user_id'];
        $userRole = getUserRole();
        
        // Get user's accessible locations
        if ($userRole === 'admin' || hasGlobalAccess()) {
            $locations = $this->businessLocationModel->getLocationsByCompany(1); // Assuming company_id = 1
        } else {
            $locations = $this->businessLocationModel->getUserAccessibleLocations($userId);
        }

        // Get stats for each location
        $locationStats = [];
        foreach ($locations as $location) {
            $locationStats[$location->location_id] = $this->businessLocationModel->getLocationStats($location->location_id);
        }

        $data = [
            'title' => 'Business Locations',
            'locations' => $locations,
            'locationStats' => $locationStats,
            'userRole' => $userRole,
            'canManageLocations' => hasPermission('locations', 'create')
        ];

        $this->view('locations/index', $data);
    }

    /**
     * View specific location details
     */
    public function view($locationId = null)
    {
        if (!$locationId) {
            flash('error_message', 'Location not specified');
            redirect('locations');
        }

        $userId = $_SESSION['user_id'];
        
        // Check if user has access to this location
        if (!hasGlobalAccess() && !$this->businessLocationModel->userHasLocationAccess($userId, $locationId)) {
            flash('error_message', 'Access denied to this location');
            redirect('locations');
        }

        $location = $this->businessLocationModel->getLocationById($locationId);
        if (!$location) {
            flash('error_message', 'Location not found');
            redirect('locations');
        }

        $stats = $this->businessLocationModel->getLocationStats($locationId);
        $staff = $this->businessLocationModel->getLocationStaff($locationId);

        $data = [
            'title' => $location->location_name,
            'location' => $location,
            'stats' => $stats,
            'staff' => $staff,
            'canManageStaff' => hasPermission('users', 'update')
        ];

        $this->view('locations/view', $data);
    }

    /**
     * Create new location (Admin only)
     */
    public function create()
    {
        if (!hasPermission('locations', 'create')) {
            flash('error_message', 'Access denied');
            redirect('locations');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'company_id' => 1, // Default company
                'location_name' => trim($_POST['location_name']),
                'location_code' => strtoupper(trim($_POST['location_code'])),
                'location_type' => $_POST['location_type'],
                'city' => trim($_POST['city']),
                'state' => trim($_POST['state']),
                'address' => trim($_POST['address']),
                'phone' => trim($_POST['phone']),
                'email' => trim($_POST['email']),
                'manager_user_id' => !empty($_POST['manager_user_id']) ? $_POST['manager_user_id'] : null,
                'operating_hours' => !empty($_POST['operating_hours']) ? json_encode($_POST['operating_hours']) : null
            ];

            // Validation
            $errors = [];
            if (empty($data['location_name'])) {
                $errors[] = 'Location name is required';
            }
            if (empty($data['location_code'])) {
                $errors[] = 'Location code is required';
            }
            if (empty($data['city'])) {
                $errors[] = 'City is required';
            }

            if (empty($errors)) {
                if ($this->businessLocationModel->createLocation($data)) {
                    flash('success_message', 'Location created successfully');
                    redirect('locations');
                } else {
                    flash('error_message', 'Failed to create location');
                }
            } else {
                flash('error_message', implode('<br>', $errors));
            }
        }

        // Get potential managers (users with manager role)
        $managers = $this->userModel->getUsersByRole('manager');

        $data = [
            'title' => 'Create New Location',
            'managers' => $managers
        ];

        $this->view('locations/create', $data);
    }

    /**
     * Switch user's current location context
     */
    public function switch($locationId = null)
    {
        if (!$locationId) {
            echo json_encode(['success' => false, 'message' => 'Location ID required']);
            return;
        }

        $userId = $_SESSION['user_id'];
        
        // Verify user has access to this location
        if (!hasGlobalAccess() && !$this->businessLocationModel->userHasLocationAccess($userId, $locationId)) {
            echo json_encode(['success' => false, 'message' => 'Access denied to this location']);
            return;
        }

        $location = $this->businessLocationModel->getLocationById($locationId);
        if (!$location) {
            echo json_encode(['success' => false, 'message' => 'Location not found']);
            return;
        }

        // Set current location in session
        $_SESSION['current_location_id'] = $locationId;
        $_SESSION['current_location_name'] = $location->location_name;
        $_SESSION['current_location_code'] = $location->location_code;

        echo json_encode([
            'success' => true, 
            'message' => 'Switched to ' . $location->location_name,
            'location' => [
                'id' => $location->location_id,
                'name' => $location->location_name,
                'code' => $location->location_code,
                'city' => $location->city
            ]
        ]);
    }

    /**
     * Staff management for location
     */
    public function manageStaff($locationId = null)
    {
        if (!$locationId) {
            flash('error_message', 'Location not specified');
            redirect('locations');
        }

        if (!hasPermission('users', 'update')) {
            flash('error_message', 'Access denied');
            redirect('locations');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $action = $_POST['action'] ?? '';
            
            if ($action === 'assign_user') {
                $userId = $_POST['user_id'];
                $accessType = $_POST['access_type'] ?? 'full';
                
                if ($this->businessLocationModel->assignUserToLocation($userId, $locationId, $accessType)) {
                    flash('success_message', 'User assigned to location successfully');
                } else {
                    flash('error_message', 'Failed to assign user');
                }
            }
        }

        $location = $this->businessLocationModel->getLocationById($locationId);
        $staff = $this->businessLocationModel->getLocationStaff($locationId);
        $availableUsers = $this->userModel->getUnassignedUsers($locationId);

        $data = [
            'title' => 'Manage Staff - ' . $location->location_name,
            'location' => $location,
            'staff' => $staff,
            'availableUsers' => $availableUsers
        ];

        $this->view('locations/manage_staff', $data);
    }

    /**
     * Inter-location transfer
     */
    public function transfer()
    {
        if (!hasPermission('inventory', 'update')) {
            flash('error_message', 'Access denied');
            redirect('locations');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fromLocationId = $_POST['from_location_id'];
            $toLocationId = $_POST['to_location_id'];
            $productId = $_POST['product_id'];
            $quantity = intval($_POST['quantity']);
            $notes = trim($_POST['notes']);

            try {
                $transferId = $this->businessLocationModel->transferInventory(
                    $fromLocationId, 
                    $toLocationId, 
                    $productId, 
                    $quantity, 
                    $notes
                );
                
                flash('success_message', "Transfer initiated successfully. Transfer ID: $transferId");
                redirect('locations/view/' . $fromLocationId);
                
            } catch (Exception $e) {
                flash('error_message', 'Transfer failed: ' . $e->getMessage());
            }
        }

        $userId = $_SESSION['user_id'];
        $locations = $this->businessLocationModel->getUserAccessibleLocations($userId);
        
        $data = [
            'title' => 'Inter-Location Transfer',
            'locations' => $locations
        ];

        $this->view('locations/transfer', $data);
    }

    /**
     * Get location data for AJAX calls
     */
    public function getLocationData($locationId = null)
    {
        if (!$locationId) {
            echo json_encode(['success' => false, 'message' => 'Location ID required']);
            return;
        }

        $location = $this->businessLocationModel->getLocationById($locationId);
        if (!$location) {
            echo json_encode(['success' => false, 'message' => 'Location not found']);
            return;
        }

        $stats = $this->businessLocationModel->getLocationStats($locationId);

        echo json_encode([
            'success' => true,
            'location' => $location,
            'stats' => $stats
        ]);
    }
}

/**
 * Helper function to check if user has global access
 */
function hasGlobalAccess()
{
    $userRole = getUserRole();
    return in_array($userRole, ['admin', 'super_admin', 'corporate_admin']);
}

/**
 * Get current user's default location
 */
function getCurrentLocation()
{
    if (isset($_SESSION['current_location_id'])) {
        return [
            'id' => $_SESSION['current_location_id'],
            'name' => $_SESSION['current_location_name'],
            'code' => $_SESSION['current_location_code']
        ];
    }
    
    // Default to user's assigned location or first location
    return [
        'id' => 1,
        'name' => 'Kurukshetra Store',
        'code' => 'KRK'
    ];
}
?>
