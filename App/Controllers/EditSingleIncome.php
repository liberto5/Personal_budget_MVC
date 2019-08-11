<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Balance;
use \App\Flash;

/**
 * Incomes controller
 *
 * PHP version 7.0
 */
class EditSingleIncome extends Authenticated
{

    /**
     * EditSingleIncomes index
     *
     * @return void
     */
    public function indexAction()
    {
		$balance = new Balance($_POST);
		
		$_SESSION['income'] = $balance->income;
		
		View::renderTemplate('EditSingleIncome/index.html');
    }
	
}