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
		
		if(isset($balance->periodsOptions))
		{
			$_SESSION['period'] = $balance->periodsOptions;
		}
		
		else if (isset($balance->custom_start) && isset($balance->custom_end))
		{
			$_SESSION['custom_start'] = $balance->custom_start;
			$_SESSION['custom_end'] = $balance->custom_end;
		}

		if(isset($_SESSION['period']) || ((isset($_SESSION['custom_start']) && (isset($_SESSION['custom_end'])))))
		{
			if (!$balance->getIncomesGroupedByCategories() || !$balance->getIncomeTotalAmount())
			{
				//Flash::addMessage('You have no incomes in selected period of time');
				echo "You have no incomes in selected period of time";
				View::renderTemplate('ShowBalance/index.html');
			}
		
			if (!$balance->getExpensesGroupedByCategories() || !$balance->getExpenseTotalAmount())
			{
				//Flash::addMessage('You have no expenses in selected period of time');
				echo "You have no expenses in selected period of time";
				View::renderTemplate('ShowBalance/index.html');
			}
		
		else 
		{
			View::renderTemplate('ShowBalance/index.html');
		}
		
		$balance->unsetAllSessionVariable();
		
		}
    }
}