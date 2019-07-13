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
				
			//echo $_SESSION['period'];
			//echo $_SESSION['user_id'];
			//echo $_SESSION['custom_start'];
			//echo $_SESSION['custom_end'];

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