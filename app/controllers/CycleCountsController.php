<?php
/**
 * Cycle Counts Controller
 * Handles cycle counting operations
 */
class CycleCountsController extends Controller
{
    public $cycleCountModel;
    public $productModel;
    public $InventoryModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->cycleCountModel = $this->model('CycleCount');
        $this->productModel = $this->model('Product');
        $this->InventoryModel = $this->model('Inventory');
    }

    /**
     * Cycle counts overview page
     */
    public function index()
    {
        $cycleCounts = $this->cycleCountModel->getCycleCounts();
        if (!$cycleCounts) {
            $cycleCounts = [];
            flash('cycle_count_message', 'No cycle counts found');
        }

        $data = [
            'title' => 'Cycle Counts',
            'cycle_counts' => $cycleCounts
        ];

        $this->view('cycle_counts/index', $data);
    }

    /**
     * Create new cycle count
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $data = [
                'count_name' => trim($_POST['count_name']),
                'type' => trim($_POST['type']),
                'location_id' => trim($_POST['location_id']),
                'category_id' => trim($_POST['category_id']),
                'planned_date' => trim($_POST['planned_date']),
                'notes' => trim($_POST['notes']),
                'count_name_err' => '',
                'type_err' => '',
                'planned_date_err' => ''
            ];

            // Validate inputs
            if (empty($data['count_name'])) {
                $data['count_name_err'] = 'Please enter count name';
            }

            if (empty($data['type'])) {
                $data['type_err'] = 'Please select count type';
            }

            if (empty($data['planned_date'])) {
                $data['planned_date_err'] = 'Please enter planned date';
            }

            // If no errors, create cycle count
            if (empty($data['count_name_err']) && empty($data['type_err']) && empty($data['planned_date_err'])) {
                $data['created_by'] = $_SESSION['user_id'];

                if ($this->cycleCountModel->createCycleCount($data)) {
                    flash('cycle_count_message', 'Cycle count created successfully');
                    redirect('cycle-counts');
                } else {
                    die('Something went wrong');
                }
            } else {
                $data['locations'] = $this->InventoryModel->getWarehouseLocations();
                $data['categories'] = $this->productModel->getCategories();
                $this->view('cycle_counts/create', $data);
            }
        } else {
            $locations = $this->InventoryModel->getWarehouseLocations();
            $categories = $this->productModel->getCategories();

            $data = [
                'title' => 'Create Cycle Count',
                'locations' => $locations,
                'categories' => $categories,
                'count_name' => '',
                'type' => '',
                'location_id' => '',
                'category_id' => '',
                'planned_date' => date('Y-m-d'),
                'notes' => '',
                'count_name_err' => '',
                'type_err' => '',
                'planned_date_err' => ''
            ];

            $this->view('cycle_counts/create', $data);
        }
    }

    /**
     * View cycle count details
     */
    public function show($id)
    {
        $cycleCount = $this->cycleCountModel->getCycleCountById($id);
        if (!$cycleCount) {
            flash('cycle_count_message', 'Cycle count not found', 'alert alert-danger');
            redirect('cycle-counts');
        }

        $items = $this->cycleCountModel->getCycleCountItems($id);

        $data = [
            'title' => 'Cycle Count Details',
            'cycle_count' => $cycleCount,
            'items' => $items
        ];

        $this->view('cycle_counts/view', $data);
    }

    /**
     * Start cycle count
     */
    public function start($id)
    {
        $cycleCount = $this->cycleCountModel->getCycleCountById($id);
        if (!$cycleCount) {
            flash('cycle_count_message', 'Cycle count not found', 'alert alert-danger');
            redirect('cycle-counts');
        }

        if ($cycleCount->status !== 'planned') {
            flash('cycle_count_message', 'Cycle count cannot be started', 'alert alert-danger');
            redirect('cycle-counts');
        }

        // Generate cycle count items based on type
        $products = [];
        switch ($cycleCount->type) {
            case 'full':
                $products = $this->productModel->getProducts();
                break;
            case 'location':
                $products = $this->InventoryModel->getProductsByLocation($cycleCount->location_id);
                break;
            case 'category':
                $products = $this->productModel->getProductsByCategory($cycleCount->category_id);
                break;
        }

        if ($this->cycleCountModel->startCycleCount($id, $products)) {
            flash('cycle_count_message', 'Cycle count started successfully');
        } else {
            flash('cycle_count_message', 'Error starting cycle count', 'alert alert-danger');
        }

        redirect('cycle-counts/show/' . $id);
    }

    /**
     * Count items
     */
    public function count($id)
    {
        $cycleCount = $this->cycleCountModel->getCycleCountById($id);
        if (!$cycleCount || $cycleCount->status !== 'in_progress') {
            flash('cycle_count_message', 'Invalid cycle count', 'alert alert-danger');
            redirect('cycle-counts');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $counts = $_POST['counts'] ?? [];
            $notes = $_POST['notes'] ?? [];

            foreach ($counts as $itemId => $countedQuantity) {
                if (is_numeric($countedQuantity)) {
                    $this->cycleCountModel->updateItemCount(
                        $itemId,
                        $countedQuantity,
                        $notes[$itemId] ?? '',
                        $_SESSION['user_id']
                    );
                }
            }

            flash('cycle_count_message', 'Counts updated successfully');
            redirect('cycle-counts/count/' . $id);
        } else {
            $items = $this->cycleCountModel->getCycleCountItems($id);

            $data = [
                'title' => 'Count Items',
                'cycle_count' => $cycleCount,
                'items' => $items
            ];

            $this->view('cycle_counts/count', $data);
        }
    }

    /**
     * Complete cycle count
     */
    public function complete($id)
    {
        if ($this->cycleCountModel->completeCycleCount($id)) {
            flash('cycle_count_message', 'Cycle count completed successfully');
        } else {
            flash('cycle_count_message', 'Error completing cycle count', 'alert alert-danger');
        }

        redirect('cycle-counts/show/' . $id);
    }

    /**
     * Cancel cycle count
     */
    public function cancel($id)
    {
        if ($this->cycleCountModel->cancelCycleCount($id)) {
            flash('cycle_count_message', 'Cycle count cancelled');
        } else {
            flash('cycle_count_message', 'Error cancelling cycle count', 'alert alert-danger');
        }

        redirect('cycle-counts');
    }
}