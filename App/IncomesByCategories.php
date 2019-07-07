<?php

namespace App;

use App\Models\Balance;

/**
 * Get incomes of logged-in user
 *
 * PHP version 7.0
 */
class IncomesByCategories
{
	public static function getIncomesByCategories()
    {
		return Balance::getIncomesGroupedByCategories();
    }
}
