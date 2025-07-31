<?php
/**
 * Expenses Controller
 * Handles expense management operations
 */
class ExpensesController extends Controller
{
    public $expenseModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->expenseModel = $this->model('Expense');
    }

    /**
     * Expenses overview page
     */
    public function index()
    {
        $expenses = $this->expenseModel->getExpenses();
        if (!$expenses) {
            $expenses = [];
            flash('expense_message', 'No expenses found');
        }

        // Get expense summary
        $summary = $this->expenseModel->getExpenseSummary();

        $data = [
            'title' => 'Expense Management',
            'expenses' => $expenses,
            'summary' => $summary
        ];

        $this->view('expenses/index', $data);
    }

    /**
     * Add expense page
     */
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'expense_category_id' => trim($_POST['expense_category_id']),
                'amount' => trim($_POST['amount']),
                'expense_date' => trim($_POST['expense_date']),
                'description' => trim($_POST['description']),
                'expense_category_id_err' => '',
                'amount_err' => '',
                'expense_date_err' => '',
                'description_err' => ''
            ];

            // Validate inputs
            if (empty($data['expense_category_id'])) {
                $data['expense_category_id_err'] = 'Please select a category';
            }

            if (empty($data['amount'])) {
                $data['amount_err'] = 'Please enter amount';
            } elseif (!is_numeric($data['amount']) || $data['amount'] <= 0) {
                $data['amount_err'] = 'Amount must be a positive number';
            }

            if (empty($data['expense_date'])) {
                $data['expense_date_err'] = 'Please enter expense date';
            }

            if (empty($data['description'])) {
                $data['description_err'] = 'Please enter description';
            }

            // If no errors, add expense
            if (
                empty($data['expense_category_id_err']) && empty($data['amount_err']) &&
                empty($data['expense_date_err']) && empty($data['description_err'])
            ) {

                if ($this->expenseModel->addExpense($data)) {
                    flash('expense_message', 'Expense added successfully');
                    redirect('expenses');
                } else {
                    die('Something went wrong');
                }
            } else {
                $data['categories'] = $this->expenseModel->getExpenseCategories();
                $this->view('expenses/add', $data);
            }
        } else {
            $categories = $this->expenseModel->getExpenseCategories();

            $data = [
                'title' => 'Add Expense',
                'categories' => $categories,
                'expense_category_id' => '',
                'amount' => '',
                'expense_date' => date('Y-m-d'),
                'description' => '',
                'expense_category_id_err' => '',
                'amount_err' => '',
                'expense_date_err' => '',
                'description_err' => ''
            ];

            $this->view('expenses/add', $data);
        }
    }

    /**
     * Edit expense page
     */
    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'expense_id' => $id,
                'expense_category_id' => trim($_POST['expense_category_id']),
                'amount' => trim($_POST['amount']),
                'expense_date' => trim($_POST['expense_date']),
                'description' => trim($_POST['description']),
                'expense_category_id_err' => '',
                'amount_err' => '',
                'expense_date_err' => '',
                'description_err' => ''
            ];

            // Validate inputs (same as add)
            if (empty($data['expense_category_id'])) {
                $data['expense_category_id_err'] = 'Please select a category';
            }

            if (empty($data['amount'])) {
                $data['amount_err'] = 'Please enter amount';
            } elseif (!is_numeric($data['amount']) || $data['amount'] <= 0) {
                $data['amount_err'] = 'Amount must be a positive number';
            }

            if (empty($data['expense_date'])) {
                $data['expense_date_err'] = 'Please enter expense date';
            }

            if (empty($data['description'])) {
                $data['description_err'] = 'Please enter description';
            }

            // If no errors, update expense
            if (
                empty($data['expense_category_id_err']) && empty($data['amount_err']) &&
                empty($data['expense_date_err']) && empty($data['description_err'])
            ) {

                if ($this->expenseModel->updateExpense($data)) {
                    flash('expense_message', 'Expense updated successfully');
                    redirect('expenses');
                } else {
                    die('Something went wrong');
                }
            } else {
                $data['categories'] = $this->expenseModel->getExpenseCategories();
                $this->view('expenses/edit', $data);
            }
        } else {
            // Get expense data
            $expense = $this->expenseModel->getExpenseById($id);
            if (!$expense) {
                redirect('expenses');
            }

            $categories = $this->expenseModel->getExpenseCategories();

            $data = [
                'title' => 'Edit Expense',
                'categories' => $categories,
                'expense_id' => $id,
                'expense_category_id' => $expense->expense_category_id,
                'amount' => $expense->amount,
                'expense_date' => $expense->expense_date,
                'description' => $expense->description,
                'expense_category_id_err' => '',
                'amount_err' => '',
                'expense_date_err' => '',
                'description_err' => ''
            ];

            $this->view('expenses/edit', $data);
        }
    }

    /**
     * Delete expense
     */
    public function delete($id)
    {
        if ($this->expenseModel->deleteExpense($id)) {
            flash('expense_message', 'Expense deleted successfully');
        } else {
            flash('expense_message', 'Error deleting expense', 'alert alert-danger');
        }
        redirect('expenses');
    }

    /**
     * Categories management
     */
    public function categories()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'category_name' => trim($_POST['category_name']),
                'category_name_err' => ''
            ];

            if (empty($data['category_name'])) {
                $data['category_name_err'] = 'Please enter category name';
            }

            if (empty($data['category_name_err'])) {
                if ($this->expenseModel->addExpenseCategory($data)) {
                    flash('expense_message', 'Category added successfully');
                    redirect('expenses/categories');
                } else {
                    die('Something went wrong');
                }
            } else {
                $data['categories'] = $this->expenseModel->getExpenseCategories();
                $this->view('expenses/categories', $data);
            }
        } else {
            $categories = $this->expenseModel->getExpenseCategories();

            $data = [
                'title' => 'Expense Categories',
                'categories' => $categories,
                'category_name' => '',
                'category_name_err' => ''
            ];

            $this->view('expenses/categories', $data);
        }
    }
}