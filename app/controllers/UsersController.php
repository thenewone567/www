<?php
class UsersController extends Controller
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
            $_POST = sanitizePost();

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
                // Check username
                if ($this->userModel->findUserByUsername($data['username'])) {
                    $data['username_err'] = 'Username is already taken';
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
                $this->view('users/register', $data);
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
            $this->view('users/register', $data);
        }
    }

    public function login()
    {
        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'username' => isset($_POST['username']) ? trim($_POST['username']) : '',
                'password' => isset($_POST['password']) ? trim($_POST['password']) : '',
                'username_err' => '',
                'password_err' => '',
            ];

            // Validate username
            if (empty($data['username'])) {
                $data['username_err'] = 'Please enter username';
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            // Check for user/username
            if ($this->userModel->findUserByUsername($data['username'])) {
                // User found
            } else {
                // User not found
                $data['username_err'] = 'No user found';
            }

            // Make sure errors are empty
            if (empty($data['username_err']) && empty($data['password_err'])) {
                // Validated
                // Check and set logged in user
                $loggedInUser = $this->userModel->login($data['username'], $data['password']);

                if ($loggedInUser) {
                    // Create Session
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Password incorrect';

                    $this->view('users/login', $data);
                }
            } else {
                // Load view with errors
                $this->view('users/login', $data);
            }


        } else {
            // Init data
            $data = [
                'username' => '',
                'password' => '',
                'username_err' => '',
                'password_err' => '',
            ];

            // Load view
            $this->view('users/login', $data);
        }
    }

    public function createUserSession($user)
    {
        if (isset($user->user_id) && isset($user->username)) {
            // Get user role information
            $userWithRole = $this->userModel->getUserWithRole($user->user_id);

            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['user_username'] = $user->username;
            $_SESSION['user_name'] = $user->username;
            $_SESSION['user_role'] = $userWithRole->role_name ?? 'employee'; // Default to employee if no role found
            $_SESSION['role_id'] = $userWithRole->role_id ?? 3; // Default to employee role_id

            redirect('pages/index');
        } else {
            flash('login_error', 'Invalid user session data');
            redirect('users/login');
        }
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_username']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);
        unset($_SESSION['role_id']);
        session_destroy();
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

        $this->view('users/profile', $data);
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
                $this->view('users/changePassword', $data);
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

            $this->view('users/changePassword', $data);
        }
    }
}
