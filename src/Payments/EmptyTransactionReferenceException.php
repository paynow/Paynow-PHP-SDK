<?php
namespace Paynow\Payments;

use InvalidArgumentException;

/**
 * Exception thrown when there's an attempt to send a transaction without a reference to Paynow
 *
 * @package Paynow\Payments
 */
class EmptyTransactionReferenceException extends InvalidArgumentException
{
    public function __construct($builder)
    {
        parent::__construct(json_encode($builder), 0, null);
    }
}