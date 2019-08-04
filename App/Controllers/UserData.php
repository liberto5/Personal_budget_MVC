<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class UserData extends Authenticated
{

    /**
     * UserData index
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('UserData/index.html');
    }
	
	/**
     * User's name change
     *
     * @return void
     */
    public function usernameChangeAction()
    {
        $user = new User($_POST);
		
		if ($user->changeUserName())
		{
			Flash::addMessage('Name was saved successfully');
			
			View::renderTemplate('UserData/index.html');
		}
		
		else 
		{
			Flash::addMessage('Changes were not saved. Please try again');
			
            View::renderTemplate('UserData/index.html', ['user' => $user]);
		}
    }
	
	/**
     * User's e-mail change
     *
     * @return void
     */
    public function emailChangeAction()
    {
        $user = new User($_POST);
		
		if ($user->changeUserEmail())
		{
			Flash::addMessage('E-mail was saved successfully');
			
			View::renderTemplate('UserData/index.html');
		}
		
		else 
		{
			Flash::addMessage('Changes were not saved. Please try again');
			
            View::renderTemplate('UserData/index.html', ['user' => $user]);
		}
    }
	
	/**
     * User's password change
     *
     * @return void
     */
    public function passwordChangeAction()
    {
        $user = new User($_POST);
		
		if ($user->checkPassword())
		{
			if ($user->changeUserPassword())
			{
				Flash::addMessage('Password was saved successfully');
			
				View::renderTemplate('UserData/index.html');
			}
			
			else 
			{
				Flash::addMessage('Changes were not saved. Please try again');
				
				View::renderTemplate('UserData/index.html', ['user' => $user]);
			}
		}
		
		else 
		{
			Flash::addMessage('Changes were not saved. Please try again');
			
            View::renderTemplate('UserData/index.html', ['user' => $user]);
		}
    }
	
}