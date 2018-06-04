<?php
namespace Paynow\Payments;

use InvalidArgumentException;

/**
 * Exception thrown when return or result url's are not set
 *
 * @package Paynow\Payments
 */
class InvalidUrlException extends InvalidArgumentException { }