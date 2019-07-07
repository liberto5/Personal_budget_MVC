<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Balance;
use \App\Auth;
use \App\Flash;

/**
 * ShowBalance controller
 *
 * PHP version 7.0
 */
class ShowBalance extends Authenticated
{

    /**
     * Show balance index
     *
     * @return void
     */
    public function indexAction()
    {
		View::renderTemplate('ShowBalance/index.html');
    }

    /**
     * Show balance from selected period of time
     *
     * @return void
     */
	public function showAction()
    {
		$balance = new Balance($_POST);
		
		$_SESSION['period'] = $balance->periodsOptions;
		
		if ($_SESSION['period'] == "custom")
		{
			$_SESSION['custom_start'] = $balance->custom_start;
			$_SESSION['custom_end'] = $balance->custom_end;
		}

		if (!$balance->getIncomesGroupedByCategories() || !$balance->getIncomeTotalAmount())
		{
			Flash::addMessage('You have no incomes in selected period of time');
			//View::renderTemplate('ShowBalance/index.html');
		}
		
		else if (!$balance->getExpensesGroupedByCategories() || !$balance->getExpenseTotalAmount())
		{
			//Flash::addMessage('You have no expenses in selected period of time');
			//View::renderTemplate('ShowBalance/index.html');
		}
		
		else 
		{
			View::renderTemplate('ShowBalance/index.html');
		}

    }
}