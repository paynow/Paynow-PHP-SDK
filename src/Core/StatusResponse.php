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

    public function paynowReference()
    {
        return arr_has($this->data, 'paynowreference') ? $this->data['paynowreference'] : '';
    }

    /**
     * Get the status of the transaction
     *
     * @return mixed|string
     */
    public function paid()
    {
        return $this->status() === 'paid' ? true : false;
    }

    /**
     * Get the status of the transaction
     *
     * @return mixed|string
     */
    public function status()
    {
        return arr_has($this->data, 'status') ? strtolower($this->data['status']) : 'Unavailable';
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