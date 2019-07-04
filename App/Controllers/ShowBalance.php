<?php

namespace App\Controllers;

use \Core\View;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class ShowBalance extends Authenticated
{

    /**
     * ShowBalance index
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('ShowBalance/index.html');
    }

    /**
     * Add a new item
     *
     * @return void
     */
    public function newAction()
    {
        echo "new action";
    }

    /**
     * Show an item
     *
     * @return void
     */
    public function showAction()
    {
        echo "show action";
    }
}