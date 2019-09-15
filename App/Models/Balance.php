<?php

namespace App\Models;

use PDO;

/**
 * User model
 *
 * PHP version 7.0
 */
class Balance extends \Core\Model
{

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
     * Get the icomes categories from selected period of time
     *
     * @return array with incomes categories
     */
	public static function getIncomesGroupedByCategories()
	{				
		if(isset($_SESSION['period']) || ((isset($_SESSION['custom_start']) && (isset($_SESSION['custom_end'])))))
		{
			if (isset($_SESSION['period']))
			{
				if ($_SESSION['period'] == "current_month")
				{
					$sql = "SELECT cat.name name, SUM(inc.amount) sum FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND (EXTRACT(YEAR_MONTH FROM date_of_income)) = (EXTRACT(YEAR_MONTH FROM CURDATE())) AND inc.user_id = :user_id GROUP BY cat.name ORDER BY SUM(inc.amount) DESC";
				}
				
				else if ($_SESSION['period'] == "previous_month")
				{
					$sql = "SELECT cat.name name, SUM(inc.amount) sum FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND YEAR(date_of_income) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(date_of_income) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND inc.user_id = :user_id GROUP BY cat.name ORDER BY SUM(inc.amount) DESC";
				}
				
				else if ($_SESSION['period'] == "current_year")
				{
					$sql = "SELECT cat.name name, SUM(inc.amount) sum FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND YEAR(date_of_income) = YEAR(CURDATE()) AND inc.user_id = :user_id GROUP BY cat.name ORDER BY SUM(inc.amount) DESC";
				}
			}
				
			else if (isset($_SESSION['custom_start']) && isset($_SESSION['custom_end']))
			{
				$sql = "SELECT cat.name name, SUM(inc.amount) sum FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND date_of_income BETWEEN :start AND :end AND inc.user_id = :user_id GROUP BY cat.name ORDER BY SUM(inc.amount) DESC";
			}

			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
			
			if (isset($_SESSION['custom_start']) && isset($_SESSION['custom_end']))
			{
				$stmt->bindParam(':start', $_SESSION['custom_start'], PDO::PARAM_STR);
				$stmt->bindParam(':end', $_SESSION['custom_end'], PDO::PARAM_STR);
			}

			$stmt->execute();
			
			return $stmt->fetchAll();
		}
	}

	
    /**
     * Get the expenses categories from selected period of time
     *
     * @return array with expenses categories
     */
	public static function getExpensesGroupedByCategories()
	{
		if(isset($_SESSION['period']) || ((isset($_SESSION['custom_start']) && (isset($_SESSION['custom_end'])))))
		{
			if (isset($_SESSION['period']))
			{
				if ($_SESSION['period'] == "current_month")
				{
					$sql = "SELECT cat.name name, SUM(exp.amount) sum FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND(EXTRACT(YEAR_MONTH FROM date_of_expense)) = (EXTRACT(YEAR_MONTH FROM CURDATE())) AND exp.user_id = :user_id GROUP BY cat.name ORDER BY SUM(exp.amount) DESC";
				}
				
				else if ($_SESSION['period'] == "previous_month")
				{
					$sql = "SELECT cat.name name, SUM(exp.amount) sum FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND YEAR(date_of_expense) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(date_of_expense) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND exp.user_id = :user_id GROUP BY cat.name ORDER BY SUM(exp.amount) DESC";
				}
				
				else if ($_SESSION['period'] == "current_year")
				{
					$sql = "SELECT cat.name name, SUM(exp.amount) sum FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND YEAR(date_of_expense) = YEAR(CURDATE()) AND exp.user_id = :user_id GROUP BY cat.name ORDER BY SUM(exp.amount) DESC";
				}
			}
				
			else if (isset($_SESSION['custom_start']) && isset($_SESSION['custom_end']))
			{
				$sql = "SELECT cat.name name, SUM(exp.amount) sum FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND date_of_expense BETWEEN :start AND :end AND exp.user_id = :user_id GROUP BY cat.name ORDER BY SUM(exp.amount) DESC";
			}

			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
			
			if (isset($_SESSION['custom_start']) && isset($_SESSION['custom_end']))
			{
				$stmt->bindParam(':start', $_SESSION['custom_start'], PDO::PARAM_STR);
				$stmt->bindParam(':end', $_SESSION['custom_end'], PDO::PARAM_STR);
			}

			$stmt->execute();
			
			return $stmt->fetchAll();
		}
	}
	
    /**
     * Get the income total amount from selected period of time
     *
     * @return integer with income total amount
     */
	public static function getIncomeTotalAmount()
	{		
		if(isset($_SESSION['period']) || ((isset($_SESSION['custom_start']) && (isset($_SESSION['custom_end'])))))
		{
			if (isset($_SESSION['period']))
			{
				if ($_SESSION['period'] == "current_month")
				{
					$sql = "SELECT SUM(inc.amount) sum FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND (EXTRACT(YEAR_MONTH FROM date_of_income)) = (EXTRACT(YEAR_MONTH FROM CURDATE())) AND inc.user_id = :user_id";
				}
				
				else if ($_SESSION['period'] == "previous_month")
				{
					$sql = "SELECT SUM(inc.amount) sum FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND YEAR(date_of_income) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(date_of_income) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND inc.user_id = :user_id";
				}
				
				else if ($_SESSION['period'] == "current_year")
				{
					$sql = "SELECT SUM(inc.amount) sum FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND YEAR(date_of_income) = YEAR(CURDATE()) AND inc.user_id = :user_id";
				}
			}
				
			else if (isset($_SESSION['custom_start']) && isset($_SESSION['custom_end']))
			{
				$sql = "SELECT SUM(inc.amount) sum FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND date_of_income BETWEEN :start AND :end AND inc.user_id = :user_id";
			}

				$db = static::getDB();
				$stmt = $db->prepare($sql);
				
				$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
				
				if (isset($_SESSION['custom_start']) && isset($_SESSION['custom_end']))
				{
					$stmt->bindParam(':start', $_SESSION['custom_start'], PDO::PARAM_STR);
					$stmt->bindParam(':end', $_SESSION['custom_end'], PDO::PARAM_STR);
				}
				
				$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

				$stmt->execute();
				
				return $stmt->fetch();
		}
	}
	
	
    /**
     * Get the expense total amount from selected period of time
     *
     * @return integer with expense total amount
     */
	public static function getExpenseTotalAmount()
	{		
		if(isset($_SESSION['period']) || ((isset($_SESSION['custom_start']) && (isset($_SESSION['custom_end'])))))
		{
			if (isset($_SESSION['period']))
			{
				if ($_SESSION['period'] == "current_month")
				{
					$sql = "SELECT SUM(exp.amount) sum FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND (EXTRACT(YEAR_MONTH FROM date_of_expense)) = (EXTRACT(YEAR_MONTH FROM CURDATE())) AND exp.user_id = :user_id";
				}
				
				else if ($_SESSION['period'] == "previous_month")
				{
					$sql = "SELECT SUM(exp.amount) sum FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND YEAR(date_of_expense) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(date_of_expense) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND exp.user_id = :user_id";
				}
				
				else if ($_SESSION['period'] == "current_year")
				{
					$sql = "SELECT SUM(exp.amount) sum FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND YEAR(date_of_expense) = YEAR(CURDATE()) AND exp.user_id = :user_id";
				}
			}
				
			else if (isset($_SESSION['custom_start']) && isset($_SESSION['custom_end']))
			{
				$sql = "SELECT SUM(exp.amount) sum FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND date_of_expense BETWEEN :start AND :end AND exp.user_id = :user_id";
			}
							
				$db = static::getDB();
				$stmt = $db->prepare($sql);
				
				$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
				
				if (isset($_SESSION['custom_start']) && isset($_SESSION['custom_end']))
				{
					$stmt->bindParam(':start', $_SESSION['custom_start'], PDO::PARAM_STR);
					$stmt->bindParam(':end', $_SESSION['custom_end'], PDO::PARAM_STR);
				}
				
				$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

				$stmt->execute();
				
				return $stmt->fetch();
		}
	}
	
    /**
     * Get single icomes from selected period of time
     *
     * @return array with incomes
     */
	public static function getSingleIncomesFromCategory()
	{				
		if(isset($_SESSION['period']) || ((isset($_SESSION['custom_start']) && (isset($_SESSION['custom_end'])))))
		{
			if (isset($_SESSION['period']))
			{
				if ($_SESSION['period'] == "current_month")
				{
					$sql = "SELECT cat.name name, inc.id id, inc.amount amount, inc.date_of_income date, inc.income_comment comment FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND (EXTRACT(YEAR_MONTH FROM date_of_income)) = (EXTRACT(YEAR_MONTH FROM CURDATE())) AND inc.user_id = :user_id AND cat.name = :name ORDER BY inc.date_of_income DESC";
				}
				
				else if ($_SESSION['period'] == "previous_month")
				{
					$sql = "SELECT cat.name name, inc.id id, inc.amount amount, inc.date_of_income date, inc.income_comment comment FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND YEAR(date_of_income) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(date_of_income) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND inc.user_id = :user_id AND cat.name = :name ORDER BY inc.date_of_income DESC";
				}
				
				else if ($_SESSION['period'] == "current_year")
				{
					$sql = "SELECT cat.name name, inc.id id, inc.amount amount, inc.date_of_income date, inc.income_comment comment FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND YEAR(date_of_income) = YEAR(CURDATE()) AND inc.user_id = :user_id AND cat.name = :name ORDER BY inc.date_of_income DESC";
				}
			}
				
			else if (isset($_SESSION['custom_start']) && isset($_SESSION['custom_end']))
			{
				$sql = "SELECT cat.name name, inc.id id, inc.amount amount, inc.date_of_income date, inc.income_comment comment FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND date_of_income BETWEEN :start AND :end AND inc.user_id = :user_id AND cat.name = :name ORDER BY inc.date_of_income DESC";
			}

			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
			$stmt->bindParam(':name', $_SESSION['income'], PDO::PARAM_STR);
			
			if (isset($_SESSION['custom_start']) && isset($_SESSION['custom_end']))
			{
				$stmt->bindParam(':start', $_SESSION['custom_start'], PDO::PARAM_STR);
				$stmt->bindParam(':end', $_SESSION['custom_end'], PDO::PARAM_STR);
			}

			$stmt->execute();
			
			return $stmt->fetchAll();
		}
	}
	
    /**
     * Get single expenses from selected period of time
     *
     * @return array with expenses
     */
	public static function getSingleExpensesFromCategory()
	{
		if(isset($_SESSION['period']) || ((isset($_SESSION['custom_start']) && (isset($_SESSION['custom_end'])))))
		{
			if (isset($_SESSION['period']))
			{
				if ($_SESSION['period'] == "current_month")
				{
					$sql = "SELECT cat.name name, exp.id id, exp.amount amount, exp.date_of_expense date, pay.name payment, exp.expense_comment comment FROM ((expenses exp INNER JOIN expenses_category_assigned_to_users cat ON exp.expense_category_assigned_to_user_id = cat.id) INNER JOIN payment_methods_assigned_to_users pay ON exp.payment_method_assigned_to_user_id = pay.id) WHERE exp.user_id = :user_id AND cat.name = :name AND (EXTRACT(YEAR_MONTH FROM date_of_expense)) = (EXTRACT(YEAR_MONTH FROM CURDATE())) ORDER BY exp.date_of_expense DESC";
				}
				
				else if ($_SESSION['period'] == "previous_month")
				{
					$sql = "SELECT cat.name name, exp.id id, exp.amount amount, exp.date_of_expense date, pay.name payment, exp.expense_comment comment FROM ((expenses exp INNER JOIN expenses_category_assigned_to_users cat ON exp.expense_category_assigned_to_user_id = cat.id) INNER JOIN payment_methods_assigned_to_users pay ON exp.payment_method_assigned_to_user_id = pay.id) WHERE exp.user_id = :user_id AND cat.name = :name AND YEAR(date_of_expense) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(date_of_expense) = MONTH(CURDATE() - INTERVAL 1 MONTH) ORDER BY exp.date_of_expense DESC";
				}
				
				else if ($_SESSION['period'] == "current_year")
				{
					$sql = "SELECT cat.name name, exp.id id, exp.amount amount, exp.date_of_expense date, pay.name payment, exp.expense_comment comment FROM ((expenses exp INNER JOIN expenses_category_assigned_to_users cat ON exp.expense_category_assigned_to_user_id = cat.id) INNER JOIN payment_methods_assigned_to_users pay ON exp.payment_method_assigned_to_user_id = pay.id) WHERE exp.user_id = :user_id AND cat.name = :name AND YEAR(date_of_expense) = YEAR(CURDATE()) ORDER BY exp.date_of_expense DESC";
				}
			}
				
			else if (isset($_SESSION['custom_start']) && isset($_SESSION['custom_end']))
			{
				$sql = "SELECT cat.name name, exp.id id, exp.amount amount, exp.date_of_expense date, pay.name payment, exp.expense_comment comment FROM ((expenses exp INNER JOIN expenses_category_assigned_to_users cat ON exp.expense_category_assigned_to_user_id = cat.id) INNER JOIN payment_methods_assigned_to_users pay ON exp.payment_method_assigned_to_user_id = pay.id) WHERE exp.user_id = :user_id AND cat.name = :name AND date_of_expense BETWEEN :start AND :end ORDER BY exp.date_of_expense DESC";
			}

			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
			$stmt->bindParam(':name', $_SESSION['expense'], PDO::PARAM_STR);
			
			if (isset($_SESSION['custom_start']) && isset($_SESSION['custom_end']))
			{
				$stmt->bindParam(':start', $_SESSION['custom_start'], PDO::PARAM_STR);
				$stmt->bindParam(':end', $_SESSION['custom_end'], PDO::PARAM_STR);
			}

			$stmt->execute();
			
			return $stmt->fetchAll();
		}
	}
	
    /**
     * Get income details from DB
     *
     * @return Balance object
     */
	public static function getIncomeDetails()
	{				
		$sql = "SELECT cat.name name, inc.id id, inc.amount amount, inc.date_of_income date, inc.income_comment comment FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.id = :id";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindParam(':id', $_SESSION['income_id'], PDO::PARAM_INT);

		$stmt->execute();
		
		return $stmt->fetchAll();
	}
	
    /**
     * Get expense details from DB
     *
     * @return Balance object
     */
	public static function getExpenseDetails()
	{				
		$sql = "SELECT cat.name name, exp.id id, exp.amount amount, exp.date_of_expense date, exp.expense_comment comment FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.id = :id";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindParam(':id', $_SESSION['expense_id'], PDO::PARAM_INT);

		$stmt->execute();
		
		return $stmt->fetchAll();
	}
	
    /**
     * Get monthly amount spent on the category
     *
     * @return integer with monthly amount spent already in the category
     */
	public static function getMonthlyExpensesInCategory()
	{
		$sql = "SELECT SUM(exp.amount) sum FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND (EXTRACT(YEAR_MONTH FROM date_of_expense)) = (EXTRACT(YEAR_MONTH FROM CURDATE())) AND exp.user_id = :user_id AND cat.name = :category";
				
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindParam(':category', $_SESSION['category'], PDO::PARAM_STR);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

		$stmt->execute();
		
		$row = $stmt->fetch();
		
		return $row->sum;
		
	}
	
	public function unsetAllSessionVariable()
	{
		if(isset($_SESSION['period'])) 
		{
			unset($_SESSION['period']);
		}
		
		else if(isset($_SESSION['custom_start']) && isset($_SESSION['custom_end'])) 
		{
			unset($_SESSION['custom_start']); 
			unset($_SESSION['custom_end']); 
		}
	}
}