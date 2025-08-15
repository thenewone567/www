<?php
class CompanyProfileController extends Controller
{
    public $settingModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->settingModel = $this->model('Setting');
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $existing = $this->settingModel->getSettings();
            $data = [
                'company_name' => isset($_POST['company_name']) ? trim($_POST['company_name']) : ($existing['company_name'] ?? ''),
                'company_logo' => $existing['company_logo'] ?? '', // default to existing; may be replaced by upload
                'company_gst' => isset($_POST['company_gst']) ? trim($_POST['company_gst']) : ($existing['company_gst'] ?? ''),
                'currency' => isset($_POST['currency']) ? trim($_POST['currency']) : ($existing['currency'] ?? ''),
                'company_address' => isset($_POST['company_address']) ? trim($_POST['company_address']) : ($existing['company_address'] ?? ''),
                'company_email' => isset($_POST['company_email']) ? trim($_POST['company_email']) : ($existing['company_email'] ?? ''),
                'company_phone' => isset($_POST['company_phone']) ? trim($_POST['company_phone']) : ($existing['company_phone'] ?? ''),
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
                $toSave = $data;
                unset($toSave['errors'], $toSave['mode']);
                if ($this->settingModel->updateSettings($toSave)) {
                    flash('company_profile_message', 'Company Profile Updated');
                    redirect('company-profile');
                } else {
                    $data['errors']['general'] = 'Save failed';
                }
            }
            $this->view('company-profile/index', $data);
        } else {
            $settings = $this->settingModel->getSettings();
            if (!$settings || !is_array($settings)) {
                $settings = [];
            }
            $mode = isset($_GET['edit']) ? 'edit' : 'view';
            $data = [
                'company_name' => $settings['company_name'] ?? '',
                'company_logo' => $settings['company_logo'] ?? '',
                'company_gst' => $settings['company_gst'] ?? '',
                'currency' => $settings['currency'] ?? '',
                'company_address' => $settings['company_address'] ?? '',
                'company_email' => $settings['company_email'] ?? '',
                'company_phone' => $settings['company_phone'] ?? '',
                'errors' => [],
                'mode' => $mode
            ];
            // If no profile data yet, jump straight to edit mode for convenience
            if ($mode === 'view' && empty($data['company_name']) && empty($data['company_email']) && empty($data['company_phone'])) {
                $data['mode'] = 'edit';
            }
            $this->view('company-profile/index', $data);
        }
    }
}
