<?php

namespace App\Controllers;

use \Core\View;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class MainMenu extends Authenticated
{

    /**
     * MainMenu index
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('MainMenu/index.html');
    }
	
}