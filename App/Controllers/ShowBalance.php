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
			if (isset($_SESSION['custom_start']) && isset($_SESSION['custom_end']))
			{
				unset($_SESSION['custom_start']);
				unset($_SESSION['custom_end']);
			}
		}
		
		else if (isset($balance->custom_start) && isset($balance->custom_end))
		{
			$_SESSION['custom_start'] = $balance->custom_start;
			$_SESSION['custom_end'] = $balance->custom_end;
			if (isset($_SESSION['period']))
			{
				unset($_SESSION['period']);
			}
		}

		View::renderTemplate('ShowBalance/index.html');
    }
}