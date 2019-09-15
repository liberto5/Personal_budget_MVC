<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\FinancialOperation;
use \App\Models\Balance;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

/**
 * Expenses controller
 *
 * PHP version 7.0
 */
class AddExpense extends Authenticated
{

    /**
     * AddExpense index
     *
     * @return void
     */
    public function indexAction()
    {
		View::renderTemplate('AddExpense/index.html');
    }

    /**
     * Add a new expense to database
     *
     * @return void
     */
    public function createAction()
    {
		$financialOperation = new FinancialOperation($_POST);

		if ($financialOperation->saveExpense())
		{
			Flash::addMessage('Expense added successfully');
			
			$this->redirect('/MainMenu/index');
		}

        else 
		{
			View::renderTemplate('AddExpense/index.html', ['financialOperation' => $financialOperation]);
		}
    }
	
    /**
     * Check the expense limit in DB
     *
     * @return integer with limit
     */
    public function checkLimitAction()
    {		
		$user = new User();
		
		$_SESSION['category'] = $_GET['category'];

		if ($user->getExpenseLimit())
		{
			echo $user->getExpenseLimit();
		}

        else 
		{
			echo 'nie dziala checkLimitAction';
		}
    }
	
    /**
     * Check how much money was already spent this month
     *
     * @return integer with amount of spent money
     */
    public function checkMoneySpentAction()
    {		
		$balance = new Balance();
		
		$_SESSION['category'] = $_GET['category'];

		if ($balance->getMonthlyExpensesInCategory())
		{
			echo $balance->getMonthlyExpensesInCategory();
		}

        else 
		{
			echo '0';
		}
    }
	
}