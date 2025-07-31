<?php
/**
 * Expense Model
 * Handles expense data operations
 */
class Expense
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all expenses
     * @return array
     */
    public function getExpenses($limit = null)
    {
        try {
            $sql = "SELECT e.*, ec.category_name 
                    FROM expenses e 
                    LEFT JOIN expense_categories ec ON e.expense_category_id = ec.expense_category_id 
                    ORDER BY e.expense_date DESC";

            if ($limit) {
                $sql .= " LIMIT :limit";
            }

            $this->db->query($sql);

            if ($limit) {
                $this->db->bind(':limit', $limit);
            }

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getExpenses: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get expense by ID
     * @param int $id
     * @return object|false
     */
    public function getExpenseById($id)
    {
        try {
            $this->db->query("SELECT e.*, ec.category_name 
                             FROM expenses e 
                             LEFT JOIN expense_categories ec ON e.expense_category_id = ec.expense_category_id 
                             WHERE e.expense_id = :id");
            $this->db->bind(':id', $id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getExpenseById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add new expense
     * @param array $data
     * @return bool
     */
    public function addExpense($data)
    {
        try {
            $this->db->query("INSERT INTO expenses (expense_category_id, amount, expense_date, description) 
                             VALUES (:expense_category_id, :amount, :expense_date, :description)");

            $this->db->bind(':expense_category_id', $data['expense_category_id']);
            $this->db->bind(':amount', $data['amount']);
            $this->db->bind(':expense_date', $data['expense_date']);
            $this->db->bind(':description', $data['description']);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in addExpense: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update expense
     * @param array $data
     * @return bool
     */
    public function updateExpense($data)
    {
        try {
            $this->db->query("UPDATE expenses 
                             SET expense_category_id = :expense_category_id, 
                                 amount = :amount, 
                                 expense_date = :expense_date, 
                                 description = :description 
                             WHERE expense_id = :expense_id");

            $this->db->bind(':expense_id', $data['expense_id']);
            $this->db->bind(':expense_category_id', $data['expense_category_id']);
            $this->db->bind(':amount', $data['amount']);
            $this->db->bind(':expense_date', $data['expense_date']);
            $this->db->bind(':description', $data['description']);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in updateExpense: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete expense
     * @param int $id
     * @return bool
     */
    public function deleteExpense($id)
    {
        try {
            $this->db->query("DELETE FROM expenses WHERE expense_id = :id");
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in deleteExpense: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get expense categories
     * @return array
     */
    public function getExpenseCategories()
    {
        try {
            $this->db->query("SELECT * FROM expense_categories ORDER BY category_name");
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getExpenseCategories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Add expense category
     * @param array $data
     * @return bool
     */
    public function addExpenseCategory($data)
    {
        try {
            $this->db->query("INSERT INTO expense_categories (category_name) VALUES (:category_name)");
            $this->db->bind(':category_name', $data['category_name']);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in addExpenseCategory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get expense summary
     * @param string $period Optional period (month, year)
     * @return object|false
     */
    public function getExpenseSummary($period = 'month')
    {
        try {
            $whereClause = '';
            switch ($period) {
                case 'month':
                    $whereClause = 'WHERE MONTH(expense_date) = MONTH(CURRENT_DATE()) AND YEAR(expense_date) = YEAR(CURRENT_DATE())';
                    break;
                case 'year':
                    $whereClause = 'WHERE YEAR(expense_date) = YEAR(CURRENT_DATE())';
                    break;
            }

            $this->db->query("SELECT 
                                COUNT(*) as total_expenses,
                                COALESCE(SUM(amount), 0) as total_amount,
                                COALESCE(AVG(amount), 0) as average_amount,
                                COALESCE(MAX(amount), 0) as max_amount
                             FROM expenses $whereClause");

            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getExpenseSummary: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get expenses by category
     * @return array
     */
    public function getExpensesByCategory()
    {
        try {
            $this->db->query("SELECT ec.category_name, 
                                    COUNT(e.expense_id) as expense_count,
                                    COALESCE(SUM(e.amount), 0) as total_amount
                             FROM expense_categories ec
                             LEFT JOIN expenses e ON ec.expense_category_id = e.expense_category_id
                             GROUP BY ec.expense_category_id, ec.category_name
                             ORDER BY total_amount DESC");

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getExpensesByCategory: " . $e->getMessage());
            return [];
        }
    }
}