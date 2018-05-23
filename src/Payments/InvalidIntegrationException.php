<?php
namespace Paynow\Payments;

use Exception;

/**
 * Exception thrown if wrong credentials are used to make a request to Paynow
 *
 * @package Paynow\Payments
 */
class InvalidIntegrationException extends Exception { }