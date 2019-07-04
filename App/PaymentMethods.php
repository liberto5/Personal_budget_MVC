<?php

namespace App;

use App\Models\FinancialOperation;

/**
 * Get categories of user's payment methods
 *
 * PHP version 7.0
 */
class PaymentMethods
{
    public static function getUserPaymentMethods()
    {
		return FinancialOperation::getPaymentMethods();
    }
}
