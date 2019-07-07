<?php

namespace App;

use App\Models\Balance;

/**
 * Get user's total income amount
 *
 * PHP version 7.0
 */
class IncomeTotal
{
	public static function getTotalUserIncome()
    {
		return Balance::getIncomeTotalAmount();
    }
}
