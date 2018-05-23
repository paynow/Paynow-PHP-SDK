<?php
namespace Paynow\Payments;

use Exception;

/**
 * Exception thrown if the hash sent from Paynow does not match the one generated locally
 *
 * @package Paynow\Payments
 */
class HashMismatchException extends Exception { }