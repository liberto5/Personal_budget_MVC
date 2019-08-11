<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Balance;
use \App\Models\FinancialOperation;
use \App\Flash;

/**
 * Incomes controller
 *
 * PHP version 7.0
 */
class EditExpenseForm extends Authenticated
{

    /**
     * EditExpenseForm index
     *
     * @return void
     */
    public function indexAction()
    {
		$balance = new Balance($_POST);
		
		$_SESSION['expense_id'] = $balance->expense;
		
		View::renderTemplate('EditExpenseForm/index.html', ['balance' => $balance]);
    }
	
    /**
     * Update edited expense
     *
     * @return void
     */
    public function updateAction()
    {

		$financialOperation = new FinancialOperation($_POST);
		
		if (isset($_POST['save_button'])) 
		{
			if ($financialOperation->updateExpense())
			{
				Flash::addMessage('Expense updated successfully');
				
				$this->redirect('/MainMenu/index');
			}

			else 
			{
				View::renderTemplate('EditExpenseForm/index.html', ['financialOperation' => $financialOperation]);
			}
		} 
		
		else if (isset($_POST['remove_button'])) 
		{
			if($financialOperation->removeExpense())
			{
				Flash::addMessage('Expense removed successfully');
			
				$this->redirect('/MainMenu/index');
			}
			else
			{
				Flash::addMessage('Expense was not removed. Please try again');
			
				$this->redirect('/MainMenu/index');
			}
		}
    }
	
}