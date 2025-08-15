<?php
class UsersController extends BaseController
{

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $profilePicturePath = '';
            if (isset($_FILES['profile_picture_file']) && $_FILES['profile_picture_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/profile_pictures/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $ext = pathinfo($_FILES['profile_picture_file']['name'], PATHINFO_EXTENSION);
                $filename = 'user_' . uniqid() . '_' . time() . '.' . $ext;
                $targetPath = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['profile_picture_file']['tmp_name'], $targetPath)) {
                    $profilePicturePath = $targetPath;
                }
            }
            $data = [
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email'] ?? ''),
                'password' => trim($_POST['password']),
                'role_id' => trim($_POST['role_id']),
                'profile_picture' => $profilePicturePath,
                'address' => trim($_POST['address'] ?? ''),
                'job_title' => trim($_POST['job_title'] ?? ''),
                'birthday' => trim($_POST['birthday'] ?? ''),
                'education' => trim($_POST['education'] ?? ''),
                'username_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'email_err' => ''
            ];
            // Validation (add more as needed)
            if (empty($data['username'])) {
                $data['username_err'] = 'Username is required';
            }
            if (empty($data['password'])) {
                $data['password_err'] = 'Password is required';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }
            if ($data['password'] !== trim($_POST['confirm_password'])) {
                $data['confirm_password_err'] = 'Passwords do not match';
            }
            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Invalid email address';
            }
            if (empty($data['username_err']) && empty($data['password_err']) && empty($data['confirm_password_err']) && empty($data['email_err'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                if ($this->userModel->register($data)) {
                    flash('register_success', 'Registration successful! You can now log in.', 'alert alert-success');
                    redirect('users/login');
                } else {
                    flash('register_error', 'Something went wrong. Please try again.', 'alert alert-danger');
                }
            }
            $this->renderLayout('users/register', $data);
        } else {
            $data = [
                'username' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'role_id' => '',
                'profile_picture' => '',
                'address' => '',
                'job_title' => '',
                'birthday' => '',
                'education' => '',
                'username_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'email_err' => ''
            ];
            $this->renderLayout('users/register', $data);
        }
    }
    public $userModel;

    public function __construct()
    {
        parent::__construct(); // Add if parent has a constructor
        $this->userModel = $this->model('User');
    }

    public function editProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            echo '<pre>DEBUG: Not logged in. $_SESSION["user_id"] is not set.</pre>';
            exit;
            //redirect('users/login');
        }
        $user = $this->userModel->getUserWithRole($_SESSION['user_id']);
        if (!$user) {
            echo '<pre>DEBUG: User not found for user_id: ' . htmlspecialchars($_SESSION['user_id']) . '</pre>';
            exit;
            //flash('edit_profile_message', 'User not found', 'alert alert-danger');
            //redirect('users/profile');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $profilePicturePath = $user->profile_picture;
            if (isset($_FILES['profile_picture_file']) && $_FILES['profile_picture_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/profile_pictures/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $ext = pathinfo($_FILES['profile_picture_file']['name'], PATHINFO_EXTENSION);
                $filename = 'user_' . $user->user_id . '_' . time() . '.' . $ext;
                $targetPath = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['profile_picture_file']['tmp_name'], $targetPath)) {
                    $profilePicturePath = $targetPath;
                }
            }
            $data = [
                'user_id' => $user->user_id,
                'profile_picture' => $profilePicturePath,
                'full_name' => trim($_POST['full_name']),
                // Use the username from the user object, not from POST (readonly field)
                'username' => $user->username,
                'email' => trim($_POST['email']),
                'address' => trim($_POST['address'] ?? ''),
                'job_title' => trim($_POST['job_title'] ?? ''),
                'birthday' => trim($_POST['birthday'] ?? ''),
                'education' => trim($_POST['education'] ?? ''),
                'full_name_err' => '',
                'username_err' => '',
                'email_err' => ''
            ];
            if (empty($data['full_name'])) {
                $data['full_name_err'] = 'Full name is required';
            }
            if (empty($data['username'])) {
                $data['username_err'] = 'Username is required';
            }
            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Invalid email address';
            }
            // Optionally, add username validation here if needed
            if (empty($data['full_name_err']) && empty($data['username_err']) && empty($data['email_err'])) {
                if ($this->userModel->updateProfile($data)) {
                    flash('edit_profile_message', 'Profile updated successfully', 'alert alert-success');
                    redirect('users/profile');
                } else {
                    flash('edit_profile_message', 'Something went wrong', 'alert alert-danger');
                }
            }
            $data['user'] = (object) $data;
            $this->renderLayout('users/editProfile', $data);
        } else {
            $data = ['user' => $user];
            $this->renderLayout('users/editProfile', $data);
        }
    }

    public function login()
    {
        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            // Sanitize POST data
            $_POST = sanitizePost($_POST);

            $data = [
                'username' => isset($_POST['username']) ? trim($_POST['username']) : '',
                'password' => isset($_POST['password']) ? trim($_POST['password']) : '',
                'remember_me' => isset($_POST['remember_me']) ? true : false,
                'username_err' => '',
                'password_err' => '',
            ];

            // Validate username
            if (empty($data['username'])) {
                $data['username_err'] = 'Please enter your username';
            } elseif (strlen($data['username']) < 3) {
                $data['username_err'] = 'Username must be at least 3 characters';
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter your password';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }

            // Check for user/username only if no validation errors
            if (empty($data['username_err'])) {
                if (!$this->userModel->findUserByUsername($data['username'])) {
                    $data['username_err'] = 'Username not found. Please check your username or register for an account.';
                }
            }

            // Make sure errors are empty
            if (empty($data['username_err']) && empty($data['password_err'])) {
                // Validated - attempt login
                $loggedInUser = $this->userModel->login($data['username'], $data['password']);

                if ($loggedInUser) {
                    // Handle remember me functionality
                    if ($data['remember_me']) {
                        $this->setRememberMeCookie($loggedInUser->user_id);
                    }

                    // Create Session
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Password is incorrect. Please try again.';
                    // Add company branding data
                    $settings = $this->model('Setting')->getSettings();
                    $data['company_name'] = $settings['company_name'] ?? SITENAME;
                    $data['company_logo'] = $settings['company_logo'] ?? '';
                    $this->renderLayout('users/login', $data, false); // no sidebar layout on login
                }
            } else {
                // Load view with errors
                $settings = $this->model('Setting')->getSettings();
                $data['company_name'] = $settings['company_name'] ?? SITENAME;
                $data['company_logo'] = $settings['company_logo'] ?? '';
                $this->renderLayout('users/login', $data, false);
            }

        } else {
            // Check for remember me cookie
            if (isset($_COOKIE['remember_user']) && !isLoggedIn()) {
                $userId = $_COOKIE['remember_user'];
                $user = $this->userModel->findUserById($userId);
                if ($user) {
                    $this->createUserSession($user);
                    return;
                }
            }

            // Init data for GET request
            $data = [
                'username' => '',
                'password' => '',
                'username_err' => '',
                'password_err' => '',
            ];

            // Load view
            $settings = $this->model('Setting')->getSettings();
            $data['company_name'] = $settings['company_name'] ?? SITENAME;
            $data['company_logo'] = $settings['company_logo'] ?? '';
            $this->renderLayout('users/login', $data, false); // prevent sidebar rendering
        }
    }

    public function createUserSession($user)
    {
        if (isset($user->user_id) && isset($user->username)) {
            // Get user with role information
            $userWithRole = $this->userModel->getUserWithRole($user->user_id);

            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['user_username'] = $user->username;
            $_SESSION['user_name'] = $user->username;
            $_SESSION['display_name'] = $userWithRole->display_name ?? ucfirst($user->username);
            $_SESSION['user_role'] = $userWithRole->role_name ?? 'Associate';
            $_SESSION['role_id'] = $userWithRole->role_id ?? 4;
            $_SESSION['login_time'] = time();

            // Log successful login
            if (defined('LOG_FILE')) {
                $timestamp = date("Y-m-d H:i:s");
                $logMessage = "[$timestamp] LOGIN: User '{$user->username}' (ID: {$user->user_id}) logged in successfully" . PHP_EOL;
                file_put_contents(LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
            }

            // Flash success message
            flash('login_success', 'Welcome back, ' . $_SESSION['display_name'] . '!', 'alert alert-success');

            // Redirect to dashboard/home
            redirect('pages/index');
        } else {
            flash('login_error', 'Invalid user session data', 'alert alert-danger');
            redirect('users/login');
        }
    }

    /**
     * Set remember me cookie
     */
    private function setRememberMeCookie($userId)
    {
        // Set cookie for 30 days
        $cookieValue = $userId;
        $expiration = time() + (30 * 24 * 60 * 60); // 30 days

        setcookie('remember_user', $cookieValue, $expiration, '/', '', false, true); // httpOnly for security
    }

    /**
     * Clear remember me cookie
     */
    private function clearRememberMeCookie()
    {
        setcookie('remember_user', '', time() - 3600, '/', '', false, true);
        unset($_COOKIE['remember_user']);
    }

    public function logout()
    {
        // Log logout
        if (isset($_SESSION['user_username']) && defined('LOG_FILE')) {
            $timestamp = date("Y-m-d H:i:s");
            $logMessage = "[$timestamp] LOGOUT: User '{$_SESSION['user_username']}' logged out" . PHP_EOL;
            file_put_contents(LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
        }

        // Clear all session variables
        unset($_SESSION['user_id']);
        unset($_SESSION['user_username']);
        unset($_SESSION['user_name']);
        unset($_SESSION['display_name']);
        unset($_SESSION['user_role']);
        unset($_SESSION['role_id']);
        unset($_SESSION['login_time']);

        // Clear remember me cookie
        $this->clearRememberMeCookie();

        // Destroy session
        session_destroy();

        // Flash logout message
        session_start(); // Restart session to show flash message
        flash('logout_success', 'You have been logged out successfully', 'alert alert-info');

        redirect('users/login');
    }

    public function profile()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            redirect('users/login');
        }

        // Get user data
        $user = $this->userModel->getUserWithRole($_SESSION['user_id']);

        if (!$user) {
            flash('profile_message', 'User not found', 'alert alert-danger');
            redirect('pages/index');
        }

        $data = [
            'user' => $user
        ];

        $this->renderLayout('users/profile', $data);
    }

    public function changePassword()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            redirect('users/login');
        }

        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // Init data
            $data = [
                'current_password' => trim($_POST['current_password']),
                'new_password' => trim($_POST['new_password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'current_password_err' => '',
                'new_password_err' => '',
                'confirm_password_err' => ''
            ];

            // Validate current password
            if (empty($data['current_password'])) {
                $data['current_password_err'] = 'Please enter your current password';
            } else {
                // Check if current password is correct
                $user = $this->userModel->findUserById($_SESSION['user_id']);
                if (!password_verify($data['current_password'], $user->password_hash)) {
                    $data['current_password_err'] = 'Current password is incorrect';
                }
            }

            // Validate new password
            if (empty($data['new_password'])) {
                $data['new_password_err'] = 'Please enter new password';
            } elseif (strlen($data['new_password']) < 6) {
                $data['new_password_err'] = 'Password must be at least 6 characters';
            }

            // Validate confirm password
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm new password';
            } else {
                if ($data['new_password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }
            }

            // Make sure errors are empty
            if (empty($data['current_password_err']) && empty($data['new_password_err']) && empty($data['confirm_password_err'])) {
                // Hash new password
                $hashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);

                // Update password
                if ($this->userModel->updatePassword($_SESSION['user_id'], $hashedPassword)) {
                    flash('change_password_success', 'Password changed successfully');
                    redirect('users/profile');
                } else {
                    die('Something went wrong');
                }
            } else {
                // Load view with errors
                $this->renderLayout('users/changePassword', $data);
            }
        } else {
            // Init data
            $data = [
                'current_password' => '',
                'new_password' => '',
                'confirm_password' => '',
                'current_password_err' => '',
                'new_password_err' => '',
                'confirm_password_err' => ''
            ];

            $this->renderLayout('users/changePassword', $data);
        }
    }

    /**
     * Forgot Password - Send reset email
     */
    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $email = trim($_POST['email'] ?? '');
            $username = trim($_POST['username'] ?? '');

            $data = [
                'email' => $email,
                'username' => $username,
                'email_err' => '',
                'username_err' => '',
                'success' => false
            ];

            // Validate inputs
            if (empty($email) && empty($username)) {
                $data['email_err'] = 'Please enter your email address or username';
            }

            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Please enter a valid email address';
            }

            if (empty($data['email_err']) && empty($data['username_err'])) {
                // Find user by email or username
                $user = null;
                if (!empty($email)) {
                    $user = $this->userModel->findUserByEmail($email);
                } elseif (!empty($username)) {
                    $user = $this->userModel->findUserByUsername($username);
                }

                if ($user) {
                    // Generate reset token
                    $resetToken = bin2hex(random_bytes(32));
                    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    // Save reset token
                    if ($this->userModel->savePasswordResetToken($user->user_id, $resetToken, $expiresAt)) {
                        // Send reset email
                        if ($this->sendPasswordResetEmail($user, $resetToken)) {
                            $data['success'] = true;
                            $data['message'] = 'Password reset instructions have been sent to your email address.';
                        } else {
                            $data['email_err'] = 'Failed to send reset email. Please try again or contact support.';
                        }
                    } else {
                        $data['email_err'] = 'Failed to generate reset token. Please try again.';
                    }
                } else {
                    // Don't reveal if user exists or not for security
                    $data['success'] = true;
                    $data['message'] = 'If an account with that email/username exists, you will receive password reset instructions.';
                }
            }

            echo json_encode($data);
            return;
        }

        // If not POST, redirect to login
        redirect('users/login');
    }

    /**
     * Reset Password with token
     */
    public function resetPassword($token = null)
    {
        if (!$token) {
            flash('reset_error', 'Invalid reset link', 'alert alert-danger');
            redirect('users/login');
        }

        // Verify token
        $tokenData = $this->userModel->getPasswordResetToken($token);
        if (!$tokenData || strtotime($tokenData->expires_at) < time()) {
            flash('reset_error', 'Reset link has expired or is invalid', 'alert alert-danger');
            redirect('users/login');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $data = [
                'token' => $token,
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Validate password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter a password';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }

            // Validate confirm password
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm your password';
            } elseif ($data['password'] !== $data['confirm_password']) {
                $data['confirm_password_err'] = 'Passwords do not match';
            }

            if (empty($data['password_err']) && empty($data['confirm_password_err'])) {
                // Hash password and update
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

                if ($this->userModel->updatePasswordByToken($token, $hashedPassword)) {
                    // Delete used token
                    $this->userModel->deletePasswordResetToken($token);

                    flash('reset_success', 'Password has been reset successfully. You can now log in.', 'alert alert-success');
                    redirect('users/login');
                } else {
                    $data['password_err'] = 'Failed to update password. Please try again.';
                }
            }

            $this->renderLayout('users/resetPassword', $data, false);
        } else {
            $data = [
                'token' => $token,
                'password' => '',
                'confirm_password' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            $this->renderLayout('users/resetPassword', $data, false);
        }
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail($user, $token)
    {
        try {
            $resetUrl = URLROOT . '/users/resetPassword/' . $token;
            $subject = 'Password Reset Request';

            $message = "
            <html>
            <head>
                <title>Password Reset Request</title>
            </head>
            <body>
                <h2>Password Reset Request</h2>
                <p>Hello " . htmlspecialchars($user->username) . ",</p>
                <p>You have requested to reset your password. Click the link below to reset your password:</p>
                <p><a href='{$resetUrl}' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
                <p>Or copy and paste this link into your browser:</p>
                <p>{$resetUrl}</p>
                <p>This link will expire in 1 hour.</p>
                <p>If you did not request this password reset, please ignore this email.</p>
                <br>
                <p>Best regards,<br>" . SITENAME . " Team</p>
            </body>
            </html>
            ";

            $headers = [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=utf-8',
                'From: ' . SITENAME . ' <noreply@' . $_SERVER['HTTP_HOST'] . '>',
                'Reply-To: noreply@' . $_SERVER['HTTP_HOST'],
                'X-Mailer: PHP/' . phpversion()
            ];

            $to = $user->email ?: $user->username . '@' . $_SERVER['HTTP_HOST'];

            return mail($to, $subject, $message, implode("\r\n", $headers));

        } catch (Exception $e) {
            error_log("Failed to send password reset email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Direct Password Reset - For local development (no email required)
     */
    public function resetPasswordDirect()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $data = [
                'username' => trim($_POST['username'] ?? ''),
                'password' => trim($_POST['password'] ?? ''),
                'confirm_password' => trim($_POST['confirm_password'] ?? ''),
                'username_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'success' => false
            ];

            // Validate username
            if (empty($data['username'])) {
                $data['username_err'] = 'Please enter your username';
            } elseif (strlen($data['username']) < 3) {
                $data['username_err'] = 'Username must be at least 3 characters';
            } else {
                // Check if user exists
                if (!$this->userModel->findUserByUsername($data['username'])) {
                    $data['username_err'] = 'Username not found';
                }
            }

            // Validate password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter a password';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }

            // Validate confirm password
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm your password';
            } elseif ($data['password'] !== $data['confirm_password']) {
                $data['confirm_password_err'] = 'Passwords do not match';
            }

            // If no errors, update the password
            if (empty($data['username_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

                if ($this->userModel->updatePasswordByUsername($data['username'], $hashedPassword)) {
                    $data['success'] = true;
                    $data['message'] = 'Password updated successfully! You can now log in with your new password.';

                    // Log the password reset
                    if (defined('LOG_FILE')) {
                        $timestamp = date("Y-m-d H:i:s");
                        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                        $logMessage = "[$timestamp] PASSWORD RESET: Username '{$data['username']}' reset password directly (IP: $ip)" . PHP_EOL;
                        file_put_contents(LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
                    }
                } else {
                    $data['message'] = 'Failed to update password. Please try again.';
                }
            }

            echo json_encode($data);
            return;
        }

        // If not POST, redirect to login
        redirect('users/login');
    }
}
