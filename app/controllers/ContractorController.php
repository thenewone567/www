<?php
/**
 * Contractor Dashboard Controller
 * Handles contractor portal functionality
 */
class ContractorController extends Controller
{
    private $contractorModel;
    private $jobModel;

    public function __construct()
    {
        // Check if user is logged in as contractor
        $this->checkContractorAuth();

        $this->contractorModel = $this->model('Contractor');
        // Load job model if exists, otherwise skip
        if (class_exists('Job')) {
            $this->jobModel = $this->model('Job');
        }
    }

    private function checkContractorAuth()
    {
        if (!isset($_SESSION['contractor_id'])) {
            redirect('contractor/login');
        }
    }

    /**
     * Contractor Dashboard Home
     */
    public function index()
    {
        $contractorId = $_SESSION['contractor_id'];

        // Get contractor info
        $contractor = $this->contractorModel->getContractorById($contractorId);

        // Get recent jobs (if job system exists)
        $recentJobs = [];
        $totalEarnings = 0;
        if ($this->jobModel) {
            $recentJobs = $this->jobModel->getContractorRecentJobs($contractorId, 5);
            $totalEarnings = $this->jobModel->getContractorTotalEarnings($contractorId);
        }

        $data = [
            'title'          => 'Contractor Dashboard',
            'contractor'     => $contractor,
            'recent_jobs'    => $recentJobs,
            'total_earnings' => $totalEarnings
        ];

        $this->view('contractor/dashboard', $data);
    }

    /**
     * Contractor Profile Management
     */
    public function profile()
    {
        $contractorId = $_SESSION['contractor_id'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $updateData = [
                'contractor_id'   => $contractorId,
                'contractor_name' => trim($_POST['contractor_name']),
                'company_name'    => trim($_POST['company_name']),
                'email'           => trim($_POST['email']),
                'phone'           => trim($_POST['phone']),
                'address'         => trim($_POST['address']),
                'city'            => trim($_POST['city']),
                'state'           => trim($_POST['state']),
                'postal_code'     => trim($_POST['postal_code']),
                'specialization'  => trim($_POST['specialization']),
                'license_number'  => trim($_POST['license_number'])
            ];

            if ($this->contractorModel->updateContractorProfile($updateData)) {
                flash('profile_success', 'Profile updated successfully', 'alert alert-success');
            } else {
                flash('profile_error', 'Failed to update profile', 'alert alert-danger');
            }
            redirect('contractor/profile');
        }

        // GET request
        $contractor = $this->contractorModel->getContractorById($contractorId);

        $data = [
            'title'      => 'My Profile',
            'contractor' => $contractor
        ];

        $this->view('contractor/profile', $data);
    }

    /**
     * Contractor Jobs
     */
    public function jobs()
    {
        $contractorId = $_SESSION['contractor_id'];

        $jobs = [];
        if ($this->jobModel) {
            $jobs = $this->jobModel->getContractorJobs($contractorId);
        }

        $data = [
            'title' => 'My Jobs',
            'jobs'  => $jobs
        ];

        $this->view('contractor/jobs', $data);
    }

    /**
     * Contractor Earnings
     */
    public function earnings()
    {
        $contractorId = $_SESSION['contractor_id'];

        $earnings = [];
        $totalEarnings = 0;
        if ($this->jobModel) {
            $earnings = $this->jobModel->getContractorEarnings($contractorId);
            $totalEarnings = $this->jobModel->getContractorTotalEarnings($contractorId);
        }

        $data = [
            'title'          => 'My Earnings',
            'earnings'       => $earnings,
            'total_earnings' => $totalEarnings
        ];

        $this->view('contractor/earnings', $data);
    }

    /**
     * Contractor Login
     */
    public function login()
    {
        if (isset($_SESSION['contractor_id'])) {
            redirect('contractor');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $email = trim($_POST['email']);
            $password = trim($_POST['password']); // For now, we'll implement a simple login

            // Simple authentication - in real app, use proper password hashing
            $contractor = $this->contractorModel->getContractorByEmail($email);

            if ($contractor && $contractor->is_active == 1) {
                $_SESSION['contractor_id'] = $contractor->contractor_id;
                $_SESSION['contractor_name'] = $contractor->contractor_name;
                $_SESSION['contractor_email'] = $contractor->email ?? '';

                flash('login_success', 'Welcome back, ' . $contractor->contractor_name, 'alert alert-success');
                redirect('contractor');
            } else {
                flash('login_error', 'Invalid credentials or account deactivated', 'alert alert-danger');
            }
        }

        $data = [
            'title' => 'Contractor Login'
        ];

        $this->view('contractor/login', $data);
    }

    /**
     * Contractor Logout
     */
    public function logout()
    {
        unset($_SESSION['contractor_id']);
        unset($_SESSION['contractor_name']);
        unset($_SESSION['contractor_email']);

        flash('logout_info', 'You have been logged out', 'alert alert-info');
        redirect('contractor/login');
    }
}
?>