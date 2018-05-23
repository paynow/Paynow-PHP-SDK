<?php
/**
 * Created by PhpStorm.
 * User: Melvin
 * Date: 15/5/2018
 * Time: 09:47
 */

namespace Paynow\Core;

use Paynow\Payments\InvalidIntegrationException;


trait CanFail
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * Throws an exception for fatal errors and stores oher
     *
     * @param $error
     *
     * @throws InvalidIntegrationException
     * @throws \Exception
     */
    private function fail($error)
    {
        switch ($error)
        {
            case Constants::RESPONSE_INVALID_ID:
                throw new InvalidIntegrationException;
            default:
                $this->errors[] = $error;
        }
    }


    /**
     * Get the errors sent by Paynow
     *
     * @param bool $pretty Boolean to indicate whether to get errors as string or as an array
     *
     * @return string|array
     */
    public function errors($pretty = true)
    {
        if(!$pretty) {
            return $this->errors;
        }

        return implode(' ', $this->errors);
    }
}