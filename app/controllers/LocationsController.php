<?php
class LocationsController extends Controller
{
    public $InventoryModel;
    public $barcodeModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->InventoryModel = $this->model('Inventory');
        $this->barcodeModel = $this->model('Barcode');
    }

    public function index()
    {
        // Redirect to locations listing since this is the locations controller
        redirect('locations/locations');
    }

    public function add()
    {
        // Redirect to inventory controller for Inventory operations
        redirect('inventory');
    }

    public function move()
    {
        // Redirect to inventory controller for Inventory movement operations
        redirect('inventory');
    }

    public function locations()
    {
        $locations = $this->InventoryModel->getWarehouseLocations();
        if (!$locations) {
            $locations = [];
            flash('Inventory_message', 'No warehouse locations found');
        }
        $data = [
            'locations' => $locations
        ];
        $this->view('locations/locations', $data);
    }

    public function addlocation()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $data = [
                'location_name' => isset($_POST['location_name']) ? trim($_POST['location_name']) : '',
                'rack' => isset($_POST['rack']) ? trim($_POST['rack']) : '',
                'shelf' => isset($_POST['shelf']) ? trim($_POST['shelf']) : '',
                'location_name_err' => ''
            ];

            // Validate location name
            if (empty($data['location_name'])) {
                $data['location_name_err'] = 'Please enter location name';
            }

            if (empty($data['location_name_err'])) {
                if ($this->InventoryModel->addWarehouseLocation($data)) {
                    flash('Inventory_message', 'Location Added');
                    redirect('locations/locations');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('locations/addlocation', $data);
            }
        } else {
            $data = [
                'location_name' => '',
                'rack' => '',
                'shelf' => ''
            ];
            $this->view('locations/addlocation', $data);
        }
    }

    /**
     * Generate barcode for warehouse location
     */
    public function generate_location_barcode($location_id = null)
    {
        if (!$location_id || !is_numeric($location_id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid location ID']);
            exit;
        }

        // Check if location exists
        $locations = $this->InventoryModel->getWarehouseLocations();
        $location = null;
        foreach ($locations as $loc) {
            if ($loc->location_id == $location_id) {
                $location = $loc;
                break;
            }
        }

        if (!$location) {
            echo json_encode(['success' => false, 'message' => 'Location not found']);
            exit;
        }

        // Check if barcode already exists
        $existingBarcode = $this->barcodeModel->getBarcodesForLocation($location_id);
        if ($existingBarcode) {
            echo json_encode([
                'success' => false,
                'message' => 'Location already has a barcode',
                'barcode' => $existingBarcode[0]->barcode_value
            ]);
            exit;
        }

        // Generate new barcode
        $barcodeValue = $this->barcodeModel->generateBarcodeForLocation($location_id);
        if ($barcodeValue) {
            echo json_encode([
                'success' => true,
                'message' => 'Location barcode generated successfully',
                'barcode' => $barcodeValue
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to generate barcode']);
        }
        exit;
    }

    /**
     * View location barcodes
     */
    public function location_barcodes()
    {
        $locations = $this->InventoryModel->getWarehouseLocations();
        $locationBarcodes = $this->barcodeModel->getAllLocationBarcodes();

        $data = [
            'title' => 'Location Barcodes',
            'locations' => $locations,
            'location_barcodes' => $locationBarcodes
        ];

        $this->view('locations/location_barcodes', $data);
    }

    /**
     * Print location barcodes
     */
    public function print_location_barcodes($location_id = null)
    {
        if ($location_id && is_numeric($location_id)) {
            // Print specific location barcode
            $locations = $this->InventoryModel->getWarehouseLocations();
            $location = null;
            foreach ($locations as $loc) {
                if ($loc->location_id == $location_id) {
                    $location = $loc;
                    break;
                }
            }

            if (!$location) {
                flash('Inventory_message', 'Location not found', 'alert alert-danger');
                redirect('locations/location_barcodes');
                return;
            }

            $barcodes = $this->barcodeModel->getBarcodesForLocation($location_id);
            if (!$barcodes) {
                // Generate barcode if doesn't exist
                $barcodeValue = $this->barcodeModel->generateBarcodeForLocation($location_id);
                if ($barcodeValue) {
                    $barcodes = [['barcode_value' => $barcodeValue, 'type' => 'CODE128']];
                }
            }

            $data = [
                'title' => 'Print Location Barcode - ' . $location->location_name,
                'location' => $location,
                'barcodes' => $barcodes
            ];
        } else {
            // Print all location barcodes
            $locationBarcodes = $this->barcodeModel->getAllLocationBarcodes();

            $data = [
                'title' => 'Print All Location Barcodes',
                'location_barcodes' => $locationBarcodes
            ];
        }

        $this->view('locations/print_location_barcodes', $data);
    }

    /**
     * Bulk generate barcodes for locations without barcodes
     */
    public function bulk_generate_location_barcodes()
    {
        $locations = $this->InventoryModel->getWarehouseLocations();
        $generatedCount = 0;
        $errors = [];

        foreach ($locations as $location) {
            $existingBarcode = $this->barcodeModel->getBarcodesForLocation($location->location_id);

            if (!$existingBarcode) {
                $barcodeValue = $this->barcodeModel->generateBarcodeForLocation($location->location_id);
                if ($barcodeValue) {
                    $generatedCount++;
                } else {
                    $errors[] = "Failed to generate barcode for " . $location->location_name;
                }
            }
        }

        echo json_encode([
            'success' => true,
            'generated' => $generatedCount,
            'errors' => $errors,
            'message' => "$generatedCount location barcodes generated successfully"
        ]);
        exit;
    }

    /**
     * Scan location barcode for inventory management
     */
    public function scan_location_barcode()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $barcode = $_POST['barcode'] ?? '';

            if (empty($barcode)) {
                echo json_encode(['success' => false, 'message' => 'Barcode required']);
                exit;
            }

            $location = $this->barcodeModel->getLocationByBarcode($barcode);

            if ($location) {
                echo json_encode([
                    'success' => true,
                    'location' => [
                        'id' => $location->location_id,
                        'name' => $location->location_name,
                        'rack' => $location->rack,
                        'shelf' => $location->shelf,
                        'barcode' => $location->barcode_value
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Location not found']);
            }
            exit;
        }
    }
}
