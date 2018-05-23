<?php
namespace Paynow\Core;


class StatusResponse
{
    use CanFail;

    /**
     * Response data sent from Paynow
     * @var array
     */
    private $data = [];

    /**
     * Default constructor
     *
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->data = $response;
    }

    /**
     * Get the original amount of the transaction
     *
     * @return float|mixed Returns the amount of the transaction, -1 if not available
     */
    public function amount()
    {
        return arr_has($this->data, 'amount') ? $this->data['amount'] : -1;
    }
    
    public function reference()
    {
        return arr_has($this->data, 'reference') ? $this->data['reference'] : '';
    }

    /**
     * Get the status of the transaction
     *
     * @return mixed|string
     */
    public function status()
    {
        return arr_has($this->data, 'status') ? $this->data['status'] : 'Unavailable';
    }

    /**
     * Check if the transaction was paid
     *
     * @return bool
     */
    public function paid()
    {
        return arr_has($this->data, 'status') && strtolower($this->data['status']) === Constants::RESPONSE_PAID;
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