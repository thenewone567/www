<?php
require_once APPROOT . DS . 'vendor' . DS . 'autoload.php';

class BarcodesController extends Controller {
public $barcodeModel;

    public function __construct(){
        if(!isLoggedIn()){
            redirect('users/login');
        }
        $this->barcodeModel = $this->model('Barcode');
    }

    public function index(){
        $barcodes = $this->barcodeModel->getBarcodes();
        $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
        foreach($barcodes as $barcode){
            $barcode->image = base64_encode($generator->getBarcode($barcode->barcode_value, $barcode->type));
        }
        $data = [
            'barcodes' => $barcodes
        ];
        $this->view('barcodes/index', $data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'product_id' => trim($_POST['product_id']),
                'barcode_value' => trim($_POST['barcode_value']),
                'type' => trim($_POST['type']),
                'product_id_err' => '',
                'barcode_value_err' => ''
            ];

            // Validate product id
            if(empty($data['product_id'])){
                $data['product_id_err'] = 'Please enter product id';
            }
            // Validate barcode value
            if(empty($data['barcode_value'])){
                $data['barcode_value_err'] = 'Please enter barcode value';
            }

            if(empty($data['product_id_err']) && empty($data['barcode_value_err'])){
                if($this->barcodeModel->addBarcode($data)){
                    flash('barcode_message', 'Barcode Added');
                    redirect('barcodes');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('barcodes/add', $data);
            }
        } else {
            $data = [
                'product_id' => '',
                'barcode_value' => '',
                'type' => 'C128'
            ];
            $this->view('barcodes/add', $data);
        }
    }

    public function generate($barcode_value, $type = 'C128'){
        $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
        echo $generator->getBarcode($barcode_value, $type);
    }
}
