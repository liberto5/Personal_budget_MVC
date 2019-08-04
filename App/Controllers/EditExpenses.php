<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\User;
use \App\Flash;

/**
 * Incomes controller
 *
 * PHP version 7.0
 */
class EditExpenses extends Authenticated
{

    /**
     * EditExpenses index
     *
     * @return void
     */
    public function indexAction()
    {
		View::renderTemplate('EditExpenses/index.html');
    }
	
	/**
     * Edit or remove category name
     *
     * @return void
     */
    public function editCategoryAction()
    {
		$user = new User($_POST);
		
		$_SESSION['category'] = $user->category;
		
		if (isset($_POST['edit_button'])) 
		{
			View::renderTemplate('EditExpenseCategoryName/index.html', ['user' => $user]);
		} 
		
		else if (isset($_POST['remove_button'])) 
		{
			if ($user->isExpenseCategoryEmpty())
			{
				View::renderTemplate('EditExpenses/index.html', ['user' => $user]);
			}
			
			else
			{
				if($user->removeExpenseCategory())
				{
					Flash::addMessage('Category removed successfully');
				
					$this->redirect('/MainMenu/index');
					
					unset($_SESSION['category']);
				}
				else
				{
					Flash::addMessage('Category was not removed. Please try again');
				
					$this->redirect('/MainMenu/index');
					
					unset($_SESSION['category']);
				}
			}
		}
    }
	
	/**
     * Remove category name
     *
     * @return void
     */
    public function removeCategoryAction()
    {
		$user = new User($_POST);
		
		if($user->prepareToTransferExpensesToAnotherCategory() && $user->removeExpenseCategory())
				{
					Flash::addMessage('Category removed successfully');
					
					$this->redirect('/MainMenu/index');
				}
				else
				{
					Flash::addMessage('Category was not removed. Please try again');
				
					$this->redirect('/MainMenu/index');
				}
		
		unset($_SESSION['category']);
    }
	
	/**
     * Add new category name
     *
     * @return void
     */
    public function addCategoryAction()
    {
		$user = new User($_POST);
		
		if ($user->addExpenseCategory())
		{
			Flash::addMessage('New category was added successfully');
			
			$this->redirect('/MainMenu/index');
		}
		
		else 
		{
			View::renderTemplate('EditExpenses/index.html', ['user' => $user]);
		}
    }

}