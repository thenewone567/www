<?php
class Settings extends Controller {
    public function __construct(){
        if(!isLoggedIn()){
            redirect('users/login');
        }
        $this->settingModel = $this->model('Setting');
    }

    public function index(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'company_name' => trim($_POST['company_name']),
                'company_logo' => trim($_POST['company_logo']),
                'company_gst' => trim($_POST['company_gst']),
                'currency' => trim($_POST['currency'])
            ];

            if($this->settingModel->updateSettings($data)){
                flash('setting_message', 'Settings Updated');
                redirect('settings');
            } else {
                die('Something went wrong');
            }
        } else {
            $settings = $this->settingModel->getSettings();
            $data = [
                'company_name' => $settings['company_name'] ?? '',
                'company_logo' => $settings['company_logo'] ?? '',
                'company_gst' => $settings['company_gst'] ?? '',
                'currency' => $settings['currency'] ?? ''
            ];
            $this->view('settings/index', $data);
        }
    }
}
