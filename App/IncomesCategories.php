<?php

namespace App;

use App\Models\FinancialOperation;

/**
 * Get categories of user's incomes
 *
 * PHP version 7.0
 */
class IncomesCategories
{
    public static function getUserIncomesCategories()
    {
		return FinancialOperation::getIncomesCategories();
    }
}
