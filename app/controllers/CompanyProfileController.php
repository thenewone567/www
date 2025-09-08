<?php
class CompanyProfileController extends Controller
{
    public $settingModel;
    public $companyModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->settingModel = $this->model('Setting');
        $this->companyModel = $this->model('Company');
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            // load existing company record and settings
            $existingCompany = $this->companyModel->getCompany(1);
            $existing = $this->settingModel->getSettings();
            $data = [
                // Use company table fields when available, fallback to settings or empty
                'company_name' => isset($_POST['company_name']) ? trim($_POST['company_name']) : ($existingCompany->company_name ?? $existing['company_name'] ?? ''),
                'company_logo' => $existingCompany->logo_path ?? $existing['company_logo'] ?? '', // may be replaced by upload
                'company_gst' => isset($_POST['company_gst']) ? trim($_POST['company_gst']) : ($existingCompany->tax_number ?? $existing['company_gst'] ?? ''),
                'currency' => isset($_POST['currency']) ? trim($_POST['currency']) : ($existing['currency'] ?? ''),
                'company_address' => isset($_POST['company_address']) ? trim($_POST['company_address']) : ($existingCompany->address ?? $existing['company_address'] ?? ''),
                'company_email' => isset($_POST['company_email']) ? trim($_POST['company_email']) : ($existingCompany->email ?? $existing['company_email'] ?? ''),
                'company_phone' => isset($_POST['company_phone']) ? trim($_POST['company_phone']) : ($existingCompany->phone ?? $existing['company_phone'] ?? ''),
                'errors' => [],
                'mode' => 'edit'
            ];
            // Handle logo file upload
            if (!empty($_FILES['company_logo_file']['name'])) {
                // Use root /uploads/logos so URL /uploads/... works (public/ not in existing asset paths)
                $uploadDir = APPROOT . DS . 'uploads' . DS . 'logos';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0775, true);
                }
                $fileInfo = $_FILES['company_logo_file'];
                $errorCode = $fileInfo['error'];
                $tmp = $fileInfo['tmp_name'];
                $name = basename($fileInfo['name']);
                $size = isset($fileInfo['size']) ? (int) $fileInfo['size'] : 0;
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowed = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
                $maxBytes = 5 * 1024 * 1024; // 5MB limit
                if ($errorCode !== UPLOAD_ERR_OK) {
                    $map = [
                        UPLOAD_ERR_INI_SIZE => 'File exceeds server limit (upload_max_filesize)',
                        UPLOAD_ERR_FORM_SIZE => 'File exceeds form limit (MAX_FILE_SIZE)',
                        UPLOAD_ERR_PARTIAL => 'File partially uploaded',
                        UPLOAD_ERR_NO_FILE => 'No file uploaded',
                        UPLOAD_ERR_NO_TMP_DIR => 'Missing temp directory on server',
                        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload'
                    ];
                    $data['errors']['company_logo'] = $map[$errorCode] ?? ('Upload error code ' . $errorCode);
                } elseif (!in_array($ext, $allowed)) {
                    $data['errors']['company_logo'] = 'Invalid logo format';
                } elseif ($size > $maxBytes) {
                    $data['errors']['company_logo'] = 'Logo too large (max 5MB)';
                } elseif (!is_uploaded_file($tmp)) {
                    $data['errors']['company_logo'] = 'Possible file upload attack detected';
                } else {
                    $safe = preg_replace('/[^a-z0-9_\.]/i', '_', $name);
                    $newName = 'logo_' . time() . '_' . $safe;
                    $target = $uploadDir . DS . $newName;
                    if (@move_uploaded_file($tmp, $target)) {
                        $data['company_logo'] = 'uploads/logos/' . $newName; // relative path
                    } else {
                        $data['errors']['company_logo'] = 'Logo upload failed (move error)';
                    }
                }
            } elseif (!empty($_POST['company_logo_url'])) {
                $data['company_logo'] = trim($_POST['company_logo_url']);
            }

            // Validation
            if ($data['company_name'] === '') {
                $data['errors']['company_name'] = 'Company name required';
            }
            if ($data['company_email'] && !filter_var($data['company_email'], FILTER_VALIDATE_EMAIL)) {
                $data['errors']['company_email'] = 'Invalid email';
            }
            if ($data['currency'] && strlen($data['currency']) > 5) {
                $data['errors']['currency'] = 'Currency code too long';
            }

            if (empty($data['errors'])) {
                // Prepare arrays for company table and settings table
                $companySave = [
                    'company_name' => $data['company_name'],
                    'address' => $data['company_address'],
                    'phone' => $data['company_phone'],
                    'email' => $data['company_email'],
                    'tax_number' => $data['company_gst'],
                    'logo_path' => $data['company_logo']
                ];
                // Save to companies table (company_id 1)
                try {
                    $okCompany = $this->companyModel->saveCompany(1, $companySave);
                } catch (Exception $e) {
                    $okCompany = false;
                    $data['errors']['general'] = 'Save failed: ' . $e->getMessage();
                }

                // Also persist currency into settings (no company column for currency)
                $okSettings = true;
                if (!empty($data['currency'])) {
                    $okSettings = $this->settingModel->updateSettings(['currency' => $data['currency']]);
                }

                if ($okCompany && $okSettings) {
                    // Use flash for user-visible message and redirect back to view
                    flash('company_profile_message', 'Company Profile Updated');
                    redirect('company-profile');
                } else {
                    if (empty($data['errors']['general'])) {
                        $data['errors']['general'] = 'Save failed';
                    }
                }
            }
            $this->view('company-profile/index', $data);
        } else {
            $settings = $this->settingModel->getSettings();
            $company = $this->companyModel->getCompany(1);
            if (!$settings || !is_array($settings)) {
                $settings = [];
            }
            $data = [
                // Prefer company table values, fallback to settings
                'company_name' => $company->company_name ?? $settings['company_name'] ?? '',
                'company_logo' => $company->logo_path ?? $settings['company_logo'] ?? '',
                'company_gst' => $company->tax_number ?? $settings['company_gst'] ?? '',
                'currency' => $settings['currency'] ?? '',
                'company_address' => $company->address ?? $settings['company_address'] ?? '',
                'company_email' => $company->email ?? $settings['company_email'] ?? '',
                'company_phone' => $company->phone ?? $settings['company_phone'] ?? '',
                'errors' => []
            ];
            $this->view('company-profile/index', $data);
        }
    }
}
