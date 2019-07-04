<?php

namespace App\Models;

use PDO;

/**
 * User model
 *
 * PHP version 7.0
 */
class FinancialOperation extends \Core\Model
{

    /**
     * Error messages
     *
     * @var array
     */
    public $error_amount = [];
    public $error_comment = [];

    /**
     * Class constructor
     *
     * @param array $operation  Initial property values
     *
     * @return void
     */
    public function __construct($operation = [])
    {
        foreach ($operation as $key => $value) 
		{
            $this->$key = $value;
        };
    }

    /**
     * Save the income with the current property values
     *
     * @return boolean  True if the operation was saved, false otherwise
     */
	public function saveIncome()
	{
		$this->validate();
		
		$income_category_id = $this->getIncomeId();
		
		if (empty($this->error_amount) && empty($this->error_comment) && ($this->getIncomeId())) 
		{
			$sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment) 
					VALUES (:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment)';

			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
			$stmt->bindValue(':income_category_assigned_to_user_id', $income_category_id, PDO::PARAM_INT);
			$stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
			$stmt->bindValue(':date_of_income', $this->dates, PDO::PARAM_STR);
			$stmt->bindValue(':income_comment', $this->comment, PDO::PARAM_STR);

			return $stmt->execute();
		}
		
		return false;
		
	}
	
	/**
     * Save the expense with the current property values
     *
     * @return boolean  True if the operation was saved, false otherwise
     */
	public function saveExpense()
	{
		$this->validate();
		
		$expense_category_id = $this->getExpenseId();
		
		$payment_method_id = $this->getPaymentMethodId();
		
		if (empty($this->error_amount) && empty($this->error_comment) && ($this->getExpenseId())) 
		{
			$sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) 
					VALUES (:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)';

			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
			$stmt->bindValue(':expense_category_assigned_to_user_id', $expense_category_id, PDO::PARAM_INT);
			$stmt->bindValue(':payment_method_assigned_to_user_id', $payment_method_id, PDO::PARAM_INT);
			$stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
			$stmt->bindValue(':date_of_expense', $this->dates, PDO::PARAM_STR);
			$stmt->bindValue(':expense_comment', $this->comment, PDO::PARAM_STR);

			return $stmt->execute();
		}
		
		return false;
		
	}
	    
	/**
     * Get the income category id from database
     *
     * @return integer with id of income category
     */
	public function getIncomeId()
	{
		$user_id = $_SESSION['user_id'];
		$income_category = $this->income_category;
		
		$sql = 'SELECT id FROM incomes_category_assigned_to_users WHERE user_id = :id AND name = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $income_category, PDO::PARAM_STR);
		
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();
		
		$row = $stmt->fetch();
		
		return $row->id;
	}
	
	/**
     * Get the expense category id from database
     *
     * @return integer with id of expense category
     */
	public function getExpenseId()
	{
		$user_id = $_SESSION['user_id'];
		$expense_category = $this->category;
		
		$sql = 'SELECT id FROM expenses_category_assigned_to_users WHERE user_id = :id AND name = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $expense_category, PDO::PARAM_STR);
		
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();
		
		$row = $stmt->fetch();
		
		return $row->id;
	}

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
		// change separator if needed (comma into dot)
	   	if (strpos($this->amount, ",") == true)
		{
		   $this->amount = str_replace(",",".",$this->amount);
		}
		
		// check the amount value if it is numeric and higher than 0
		if (!is_numeric($this->amount) || $this->amount < 0) 
		{
			$this->error_amount[] = "Amount has to be a positive number";
		}
		
		// check the comment's length
		if ((strlen($this->comment) > 100)) 
		{
			$this->error_comment[] = "The comment can not exceed 100 characters";
		}		
	}
	
	/**
     * Get categories of incomes from database and display them as options
	 * in form of adding new income
     *
     * @return array with categories
     */	
	public static function getIncomesCategories()
	{
		if (isset($_SESSION['user_id']))
		{
			$user_id = $_SESSION['user_id'];
		
			$sql = "SELECT name FROM incomes_category_assigned_to_users WHERE user_id = :user_id";

			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

			$stmt->execute();
			
			return $stmt->fetchAll();
		}
	}
	
	/**
     * Get categories of expenses from database and display them as options
	 * in form of adding new expense
     *
     * @return array with categories
     */	
	public static function getExpensesCategories()
	{
		if (isset($_SESSION['user_id']))
		{
			$user_id = $_SESSION['user_id'];
		
			$sql = "SELECT name FROM expenses_category_assigned_to_users WHERE user_id = :user_id";

			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

			$stmt->execute();
			
			return $stmt->fetchAll();
		}
	}
	
	/**
     * Get categories of payment methods from database and display them as options
	 * in form of adding new expense
     *
     * @return array with categories
     */	
	public static function getPaymentMethods()
	{
		if (isset($_SESSION['user_id']))
		{
			$user_id = $_SESSION['user_id'];
		
			$sql = "SELECT name FROM payment_methods_assigned_to_users WHERE user_id = :user_id";

			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

			$stmt->execute();
			
			return $stmt->fetchAll();
		}
	}
	
	/**
     * Get the payment method id from database
     *
     * @return integer with id of payment method
     */
	public function getPaymentMethodId()
	{
		$user_id = $_SESSION['user_id'];
		$payment_method = $this->payment;
		
		$sql = 'SELECT id FROM payment_methods_assigned_to_users WHERE user_id = :id AND name = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $payment_method, PDO::PARAM_STR);
		
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();
		
		$row = $stmt->fetch();
		
		return $row->id;
	}
}
