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
class EditIncomes extends Authenticated
{

    /**
     * EditIncomes index
     *
     * @return void
     */
    public function indexAction()
    {
		View::renderTemplate('EditIncomes/index.html');
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
			View::renderTemplate('EditIncomeCategoryName/index.html', ['user' => $user]);
		} 
		
		else if (isset($_POST['remove_button'])) 
		{
			if ($user->isIncomeCategoryEmpty())
			{
				View::renderTemplate('EditIncomes/index.html', ['user' => $user]);
			}
			
			else
			{
				if($user->removeIncomeCategory())
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
		
		if($user->prepareToTransferIncomesToAnotherCategory() && $user->removeIncomeCategory())
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
		
		if ($user->addIncomeCategory())
		{
			Flash::addMessage('New category was added successfully');
			
			$this->redirect('/MainMenu/index');
		}
		
		else 
		{
			View::renderTemplate('EditIncomes/index.html', ['user' => $user]);
		}
    }

}