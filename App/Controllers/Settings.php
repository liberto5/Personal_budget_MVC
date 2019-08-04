<?php

namespace App\Controllers;

use \Core\View;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class Settings extends Authenticated
{

    /**
     * Settings index
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('Settings/index.html');
    }
	
}