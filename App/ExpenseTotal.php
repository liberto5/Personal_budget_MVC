<?php

namespace App;

use App\Models\Balance;

/**
 * Get user's total expense amount
 *
 * PHP version 7.0
 */
class ExpenseTotal
{
	public static function getTotalUserExpense()
    {
		return Balance::getExpenseTotalAmount();
    }
}
