<?php

namespace App;

use App\Models\FinancialOperation;

/**
 * Get categories of user's expenses
 *
 * PHP version 7.0
 */
class ExpensesCategories
{
    public static function getUserExpensesCategories()
    {
		return FinancialOperation::getExpensesCategories();
    }
}
