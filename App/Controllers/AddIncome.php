<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\FinancialOperation;
use \App\Auth;
use \App\Flash;

/**
 * Incomes controller
 *
 * PHP version 7.0
 */
class AddIncome extends Authenticated
{

    /**
     * AddIncome index
     *
     * @return void
     */
    public function indexAction()
    {
		View::renderTemplate('AddIncome/index.html');
    }

    /**
     * Add a new income to database
     *
     * @return void
     */
    public function createAction()
    {
		$financialOperation = new FinancialOperation($_POST);

		if ($financialOperation->saveIncome())
		{
			Flash::addMessage('Income added successfully');
			
			$this->redirect('/MainMenu/index');
		}

        else 
		{
			View::renderTemplate('AddIncome/index.html', ['financialOperation' => $financialOperation]);
		}
    }
}