<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;

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

        if ($user) {
			
            Auth::login($user);

            $this->redirect(Auth::getReturnToPage());

        } else {

            View::renderTemplate('Home/index.html', [
                'email' => $_POST['email']
            ]);
        }
    }
	
    public function destroyAction()
    {
        Auth::logout();

        $this->redirect('/');       
    }
}
