<?php
class CategoriesController extends Controller
{
    public $categoryModel;

    public function __construct()
    {
        // Temporarily bypass authentication for AJAX testing
        // TODO: Fix session handling for AJAX requests
        /*
        // For AJAX requests, we'll handle authentication differently
        if (!isLoggedIn()) {
            // Check if this is an AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['error' => 'Authentication required']);
                exit;
            } else {
                redirect('users/login');
            }
        }
        */
        $this->categoryModel = $this->model('Category');
    }

    public function index()
    {
        $categories = $this->categoryModel->getCategoriesWithCount();

        $data = [
            'categories' => $categories
        ];
        $this->view('categories/index', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'category_name' => trim($_POST['category_name'] ?? ''),
                'category_name_err' => ''
            ];

            // Validate category name
            if (empty($data['category_name'])) {
                $data['category_name_err'] = 'Please enter category name';
            } elseif ($this->categoryModel->findCategoryByName($data['category_name'])) {
                $data['category_name_err'] = 'Category name already exists';
            }

            // Make sure errors are empty
            if (empty($data['category_name_err'])) {
                // Validated - Add category
                if ($this->categoryModel->addCategory($data)) {
                    // Check if this is an AJAX request
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        // Return JSON response for AJAX
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => 'Category added successfully']);
                        exit;
                    } else {
                        // Set success message for regular form submission
                        flash('category_message', 'Category added successfully');

                        // Close popup and refresh parent window
                        echo '<script>
                            if (window.opener) {
                                window.opener.location.reload();
                                window.close();
                            } else {
                                window.location.href = "' . URLROOT . '/categories";
                            }
                        </script>';
                        exit;
                    }
                } else {
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        http_response_code(500);
                        echo json_encode(['error' => 'Failed to add category']);
                        exit;
                    } else {
                        die('Something went wrong');
                    }
                }
            } else {
                // Handle errors
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['error' => $data['category_name_err']]);
                    exit;
                } else {
                    // Load view with errors
                    $this->view('categories/add', $data);
                }
            }
        } else {
            // Init data
            $data = [
                'category_name' => '',
                'category_name_err' => ''
            ];

            // Load view
            $this->view('categories/add', $data);
        }
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => $id,
                'category_name' => trim($_POST['category_name'] ?? ''),
                'category_name_err' => ''
            ];

            // Validate category name
            if (empty($data['category_name'])) {
                $data['category_name_err'] = 'Please enter category name';
            }

            // Make sure errors are empty
            if (empty($data['category_name_err'])) {
                // Validated - Update category
                if ($this->categoryModel->updateCategory($data)) {
                    flash('category_message', 'Category updated successfully');
                    redirect('categories');
                } else {
                    die('Something went wrong');
                }
            } else {
                // Load view with errors
                $this->view('categories/edit', $data);
            }
        } else {
            // Get existing category from model
            $category = $this->categoryModel->getCategoryById($id);

            // Check for owner
            if (!$category) {
                redirect('categories');
            }

            $data = [
                'id' => $id,
                'category_name' => $category->category_name,
                'category_name_err' => ''
            ];

            $this->view('categories/edit', $data);
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get existing category from model
            $category = $this->categoryModel->getCategoryById($id);

            // Check for owner
            if (!$category) {
                redirect('categories');
            }

            if ($this->categoryModel->deleteCategory($id)) {
                flash('category_message', 'Category deleted successfully');
                redirect('categories');
            } else {
                die('Something went wrong');
            }
        } else {
            redirect('categories');
        }
    }
}
