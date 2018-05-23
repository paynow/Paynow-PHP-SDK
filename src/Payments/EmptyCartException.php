<?php
namespace Paynow\Payments;

use InvalidArgumentException;

/**
 * Exception thrown when there's an attempt to send an empty cart to Paynow
 *
 * @package Paynow\Payments
 */
class EmptyCartException extends InvalidArgumentException
{

    /**
     * EmptyCartException constructor.
     * @param FluentBuilder $builder
     */
    public function __construct($builder)
    {
        parent::__construct("At least one item is required to send the transaction",0,null);
    }
}