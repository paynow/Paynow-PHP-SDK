<?php
namespace Paynow\Core;

use Paynow\Payments\InvalidIntegrationException;


class InitResponse
{
    use CanFail;

    /**
     * Response data sent from Paynow
     * @var array
     */
    private $data;

    /**
     * Boolean indicating whether initiate request was successful or not
     *
     * @var bool
     */
    public $success;


    /**
     * The status of the transaction in Paynow
     *
     * @var string
     */
    public $status = '';


    /**
     * InitResponse constructor.
     *
     * @param array $response Response data sent from Paynow
     *
     * @throws InvalidIntegrationException If the error returned from paynow is
     */
    public function __construct(array $response)
    {
        $this->data = $response;
        $this->load();
    }

    /**
     * Reads through the response data sent from Paynow
     *
     * @throws InvalidIntegrationException
     */
    private function load()
    {
        if(arr_has($this->data,'status')) {
            $this->status = strtolower($this->data['status']);
            $this->success = $this->status === Constants::RESPONSE_OK;
        }
		
        if(!$this->success()) {
            if(arr_has($this->data, 'error')) {
                $this->fail(strtolower($this->data['error']));
            }
        }
    }

    public function instructions() 
    {
        return arr_has($this->data, 'instructions') ? $this->data['instructions'] : '';
    }


    /**
     * Returns the poll URL sent from Paynow
     *
     * @return bool|string
     */
    public function pollUrl()
    {
        return arr_has($this->data, 'pollurl') ? $this->data['pollurl'] : '';
    }

    /**
     * Gets a boolean indicating whether a request succeeded or failed
     * @return mixed
     */
    public function success()
    {
        return $this->success;
    }

    /**
     * Returns the url the user should be taken to so they can make a payment
     *
     * @return bool|string
     */
    public function redirectUrl()
    {
        if(arr_has($this->data,'browserurl')) {
            return $this->data['browserurl'];
        }

        return false;
    }

    /**
     * Get the original data sent from Paynow
     *
     * @return array
     */
    public function data()
    {
        return $this->data;
    }
}