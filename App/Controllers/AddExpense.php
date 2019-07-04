<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\FinancialOperation;
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
			
			$this->redirect('/mainmenu/index');
		}

        else 
		{
			View::renderTemplate('AddExpense/index.html', ['financialOperation' => $financialOperation]);
		}
    }
}