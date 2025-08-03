<?php
class UsersController extends BaseController
{
    public $userModel;

    public function __construct()
    {
        parent::__construct(); // Add if parent has a constructor
        $this->userModel = $this->model('User');
    }

    public function register()
    {
        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form

            // Sanitize POST data
            $_POST = sanitizePost($_POST);

            $data = [
                'username' => isset($_POST['username']) ? trim($_POST['username']) : '',
                'password' => isset($_POST['password']) ? trim($_POST['password']) : '',
                'confirm_password' => isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '',
                'role_id' => isset($_POST['role_id']) ? trim($_POST['role_id']) : '',
                'username_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Validate username
            if (empty($data['username'])) {
                $data['username_err'] = 'Please enter username';
            } else {
                // Use comprehensive username validation
                $usernameErrors = $this->userModel->validateUsername($data['username']);
                if (!empty($usernameErrors)) {
                    $data['username_err'] = implode('. ', $usernameErrors);
                }
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }

            // Validate Confirm Password
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm password';
            } else {
                if ($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }
            }

            // Make sure errors are empty
            if (empty($data['username_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                // Validated

                // Hash Password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Set default role_id (3 = employee, or 1 = admin for testing)
                // For now, setting all new registrations as employee (role_id = 3)
                $data['role_id'] = 3; // Default to employee role

                // Register User
                if ($this->userModel->register($data)) {
                    flash('register_success', 'You are registered and can log in');
                    redirect('users/login');
                } else {
                    die('Something went wrong');
                }

            } else {
                // Load view with errors
                $this->renderLayout('users/register', $data);
            }

        } else {
            // Init data
            $data = [
                'username' => '',
                'password' => '',
                'confirm_password' => '',
                'role_id' => '',
                'username_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Load view
            $this->renderLayout('users/register', $data);
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
                    $this->renderLayout('users/login', $data);
                }
            } else {
                // Load view with errors
                $this->renderLayout('users/login', $data);
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
            $this->renderLayout('users/login', $data);
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
}
