<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Signup extends \Core\Controller
{

    /**
     * Sign up a new user
     *
     * @return void
     */
    public function createAction()
    {
        $user = new User($_POST);

        if ($user->save())
		{
            $this->redirect('/signup/success');
		}

        else 
		{
            View::renderTemplate('Home/index.html', ['user' => $user]);
		}
    }
	
	public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }
	
}
