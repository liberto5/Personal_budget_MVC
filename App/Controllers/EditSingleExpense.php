<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Balance;
use \App\Flash;

/**
 * Expenses controller
 *
 * PHP version 7.0
 */
class EditSingleExpense extends Authenticated
{

    /**
     * EditSingleExpense index
     *
     * @return void
     */
    public function indexAction()
    {
		$balance = new Balance($_POST);
		
		$_SESSION['expense'] = $balance->expense;
		
		View::renderTemplate('EditSingleExpense/index.html');
    }
	
}