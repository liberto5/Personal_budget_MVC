<?php

namespace App;

use App\Models\Balance;

/**
 * Get single expense details to edit them
 *
 * PHP version 7.0
 */
class SingleExpenseDetails
{
    public static function getExpenseDetails()
    {
		return Balance::getExpenseDetails();
    }
}
