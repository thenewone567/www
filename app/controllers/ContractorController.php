<?php
/**
 * Contractor Management Controller
 * Handles contractor directory and management functionality
 */
class ContractorController extends Controller
{
    private $contractorModel;
    private $userModel;

    public function __construct()
    {
        $this->contractorModel = $this->model('Contractor');
        $this->userModel = $this->model('User');
    }

    /**
     * Contractors Directory - Public listing with KPIs
     */
    public function index()
    {
        // Get all contractors with tier information
        $contractors = $this->getAllContractorsWithTiers();

        // Calculate KPIs
        $kpis = $this->calculateContractorKPIs($contractors);

        $data = [
            'title' => 'Contractors Directory',
            'contractors' => $contractors,
            'kpis' => $kpis
        ];

        $this->view('contractor/index', $data);
    }

    /**
     * Get all contractors with tier information
     */
    private function getAllContractorsWithTiers()
    {
        try {
            $db = new Database();
            $db->query("SELECT 
                contractor_id,
                contractor_name,
                unique_id,
                email,
                phone,
                specialization,
                company_name,
                is_active,
                COALESCE(current_tier_achievement, 1) as current_tier_achievement,
                COALESCE(quarterly_revenue_generated, 0) as quarterly_revenue_generated,
                COALESCE(tier_earned_quarter, '') as tier_earned_quarter,
                COALESCE(commission_rate, 0) as commission_rate,
                COALESCE(total_commission_earned, 0) as total_commission_earned,
                COALESCE(created_at, NOW()) as created_at
                FROM contractors 
                ORDER BY current_tier_achievement DESC, quarterly_revenue_generated DESC");

            $db->execute();
            return $db->resultSet();
        } catch (Exception $e) {
            error_log('Error fetching contractors: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate contractor KPIs
     */
    private function calculateContractorKPIs($contractors)
    {
        $kpis = [
            'total_contractors' => 0,
            'active_contractors' => 0,
            'total_revenue' => 0,
            'quarterly_revenue' => 0,
            'total_commissions' => 0,
            'tier_distribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]
        ];

        foreach ($contractors as $contractor) {
            $kpis['total_contractors']++;

            if ($contractor->is_active == 1) {
                $kpis['active_contractors']++;
            }

            $kpis['quarterly_revenue'] += $contractor->quarterly_revenue_generated ?? 0;
            $kpis['total_commissions'] += $contractor->total_commission_earned ?? 0;

            // Tier distribution
            $tier = (int) ($contractor->current_tier_achievement ?? 1);
            if (isset($kpis['tier_distribution'][$tier])) {
                $kpis['tier_distribution'][$tier]++;
            }
        }

        // Get total revenue from database (all-time)
        try {
            $db = new Database();
            $db->query("SELECT SUM(COALESCE(total_revenue_generated, 0)) as total_revenue FROM contractors");
            $db->execute();
            $result = $db->single();
            $kpis['total_revenue'] = $result->total_revenue ?? 0;
        } catch (Exception $e) {
            error_log('Error calculating total revenue: ' . $e->getMessage());
        }

        return $kpis;
    }

    /**
     * Toggle contractor status (AJAX)
     */
    public function toggleContractorStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contractorId = $_POST['contractor_id'] ?? null;
            $status = $_POST['status'] ?? null;

            if ($contractorId && in_array($status, ['active', 'inactive'])) {
                try {
                    $db = new Database();
                    $db->query("UPDATE contractors SET is_active = ? WHERE contractor_id = ?");
                    $db->bind(1, $status === 'active' ? 1 : 0);
                    $db->bind(2, $contractorId);
                    $db->execute();

                    echo json_encode(['success' => true]);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
        }
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'contractor_name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['contact_info'] ?? ''),
                'specialization' => trim($_POST['specialization'] ?? ''),
                'commission_type' => $_POST['tier'] ?? '',
                'phone' => trim($_POST['phone'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'name_err' => '',
                'contact_info_err' => '',
                'specialization_err' => '',
                'tier_err' => ''
            ];

            // Validate data
            $validation = $this->validateContractorData($data);

            if ($validation['valid']) {
                // Load contractor model
                $contractorModel = $this->model('Contractor');

                // Add contractor
                if ($contractorModel->addContractor($data)) {
                    echo json_encode(['success' => true, 'message' => 'Contractor added successfully']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to add contractor']);
                }
            } else {
                // Return validation errors
                echo json_encode(['success' => false, 'errors' => $validation['errors']]);
            }
        } else {
            // Show add contractor form
            $this->view('contractor/add');
        }
    }

    private function validateContractorData($data)
    {
        $errors = [];
        $valid = true;

        // Validate name
        if (empty($data['contractor_name'])) {
            $errors['name'] = 'Please enter contractor name';
            $valid = false;
        }

        // Validate contact info (email)
        if (empty($data['email'])) {
            $errors['contact_info'] = 'Please enter email address';
            $valid = false;
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['contact_info'] = 'Please enter a valid email address';
            $valid = false;
        }

        // Validate specialization
        if (empty($data['specialization'])) {
            $errors['specialization'] = 'Please enter specialization';
            $valid = false;
        }

        // Validate commission type (was tier)
        if (empty($data['commission_type'])) {
            $errors['tier'] = 'Please select a commission type';
            $valid = false;
        } elseif (!in_array($data['commission_type'], ['percentage', 'fixed', 'hourly'])) {
            $errors['tier'] = 'Please select a valid commission type';
            $valid = false;
        }

        return ['valid' => $valid, 'errors' => $errors];
    }

    public function getPerformance()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $contractorId = $_POST['contractor_id'] ?? null;
            $month = $_POST['month'] ?? date('m');
            $year = $_POST['year'] ?? date('Y');

            if ($contractorId) {
                // Load contractor model
                $contractorModel = $this->model('Contractor');

                // Get performance data
                $performance = $contractorModel->getMonthlyPerformance($contractorId, $month, $year);

                if ($performance) {
                    echo json_encode(['success' => true, 'data' => $performance]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'No performance data found']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Contractor ID required']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
        }
    }

    /**
     * View contractor details
     */
    public function viewContractor($contractorId = null)
    {
        if (!$contractorId) {
            flash('contractor_message', 'Contractor ID not provided', 'alert alert-danger');
            redirect('contractor');
        }

        // Get contractor data
        $contractor = $this->contractorModel->getContractorById($contractorId);

        if (!$contractor) {
            flash('contractor_message', 'Contractor not found', 'alert alert-danger');
            redirect('contractor');
        }

        $data = [
            'title' => 'Contractor Details - ' . $contractor->contractor_name,
            'contractor' => $contractor
        ];

        $this->view('contractor/view', $data);
    }

    /**
     * Edit contractor
     */
    public function edit($contractorId = null)
    {
        if (!$contractorId) {
            flash('contractor_message', 'Contractor ID not provided', 'alert alert-danger');
            redirect('contractor');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Handle form submission
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'contractor_name' => trim($_POST['contractor_name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'state' => trim($_POST['state'] ?? ''),
                'zip_code' => trim($_POST['zip_code'] ?? ''),
                'specialization' => trim($_POST['specialization'] ?? ''),
                'commission_type' => $_POST['commission_type'] ?? '',
                'hourly_rate' => floatval($_POST['hourly_rate'] ?? 0),
                'notes' => trim($_POST['notes'] ?? '')
            ];

            // Validate data (simple validation)
            $errors = [];
            if (empty($data['contractor_name'])) {
                $errors[] = 'Contractor name is required';
            }
            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email is required';
            }

            if (empty($errors)) {
                if ($this->contractorModel->updateContractor($contractorId, $data)) {
                    flash('contractor_message', 'Contractor updated successfully', 'alert alert-success');
                    redirect('contractor/view/' . $contractorId);
                } else {
                    flash('contractor_message', 'Failed to update contractor', 'alert alert-danger');
                }
            } else {
                flash('contractor_message', implode(', ', $errors), 'alert alert-danger');
            }
        }

        // Get contractor data for form
        $contractor = $this->contractorModel->getContractorById($contractorId);

        if (!$contractor) {
            flash('contractor_message', 'Contractor not found', 'alert alert-danger');
            redirect('contractor');
        }

        $data = [
            'title' => 'Edit Contractor - ' . $contractor->contractor_name,
            'contractor' => $contractor
        ];

        $this->view('contractor/edit', $data);
    }
}
?>