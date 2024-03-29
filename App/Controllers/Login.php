<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

/**
 * Login controller
 *
 * PHP version 7.0
 */
class Login extends \Core\Controller
{
	
    /**
     * Log in a user
     *
     * @return void
     */
    public function createAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);
		
		$remember_me = isset($_POST['remember_me']);

        if ($user) 
		{
            Auth::login($user, $remember_me);
			
			$_SESSION['user_id'] = $user->id;
			
			if ($this->redirect(Auth::getReturnToPage()))
			{
				$this->redirect(Auth::getReturnToPage());
			}
			
			else
			{
				$this->redirect('/MainMenu/index');
			}

        } 
		
		else 
		{
            Flash::addMessage('Incorrect e-mail or password!');

            View::renderTemplate('Home/index.html', ['email' => $_POST['email'], 'remember_me' => $remember_me]);
        }
    }
	
    public function destroyAction()
    {
        Auth::logout();

        $this->redirect('/login/show-logout-message');     
    }
	
	/**
     * Show a "logged out" flash message and redirect to the homepage. Necessary to use the flash messages
     * as they use the session and at the end of the logout method (destroyAction) the session is destroyed
     * so a new action needs to be called in order to use the session.
     *
     * @return void
     */
    public function showLogoutMessageAction()
    {
      Flash::addMessage('Logout successful');

      $this->redirect('/');
    }
}
