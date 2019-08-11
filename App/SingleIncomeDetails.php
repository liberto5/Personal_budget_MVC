<?php

namespace App;

use App\Models\Balance;

/**
 * Get single income details to edit them
 *
 * PHP version 7.0
 */
class SingleIncomeDetails
{
    public static function getIncomeDetails()
    {
		return Balance::getIncomeDetails();
    }
}
