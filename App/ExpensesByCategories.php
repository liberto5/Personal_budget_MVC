<?php

namespace App;

use App\Models\Balance;

/**
 * Get incomes of logged-in user
 *
 * PHP version 7.0
 */
class ExpensesByCategories
{
	public static function getExpensesByCategories()
    {
		return Balance::getExpensesGroupedByCategories();
    }
}
