<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class EditExpenseCategoryName extends Authenticated
{
	
    /**
     * Change the name of existing category of expenses
     *
     * @return void
     */
    public function editNameAction()
    {
		$user = new User($_POST);
		
		if ($user->editExpenseCategoryName())
		{
			Flash::addMessage('Category\'s name changed successfully');
			
			$this->redirect('/MainMenu/index');
		}
		
		else 
		{
			Flash::addMessage('Category\'s name was not changed. Please try again');
			
			$this->redirect('/MainMenu/index');
		}
    }
	
}