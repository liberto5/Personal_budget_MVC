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
class EditIncomeForm extends Authenticated
{

    /**
     * EditIncomeForm index
     *
     * @return void
     */
    public function indexAction()
    {
		$balance = new Balance($_POST);
		
		$_SESSION['income_id'] = $balance->income;
		
		View::renderTemplate('EditIncomeForm/index.html', ['balance' => $balance]);
    }
	
    /**
     * Update edited income
     *
     * @return void
     */
    public function updateAction()
    {

		$financialOperation = new FinancialOperation($_POST);
		
		if (isset($_POST['save_button'])) 
		{
			if ($financialOperation->updateIncome())
			{
				Flash::addMessage('Income updated successfully');
				
				$this->redirect('/MainMenu/index');
			}

			else 
			{
				View::renderTemplate('EditIncomeForm/index.html', ['financialOperation' => $financialOperation]);
			}
		} 
		
		else if (isset($_POST['remove_button'])) 
		{
			if($financialOperation->removeIncome())
			{
				Flash::addMessage('Income removed successfully');
			
				$this->redirect('/MainMenu/index');
			}
			else
			{
				Flash::addMessage('Income was not removed. Please try again');
			
				$this->redirect('/MainMenu/index');
			}
		}
    }
	
}