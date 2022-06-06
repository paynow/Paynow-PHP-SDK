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
     * Check if transaction has been paid successfully.
     * If so the transaction will be sitting in suspense waiting on the merchant to confirm delivery of the goods.
     *
     * @return mixed|string
     */
    public function awaitingDelivery()
    {
        return $this->status() === 'Awaiting Delivery' ? true : false;
    }

    /**
     * Check if the user or merchant has acknowledged delivery of the goods but the funds are still sitting in
     * suspense awaiting the 24 hour confirmation window to close.
     *
     * @return mixed|string
     */
    public function delivered()
    {
        return $this->status() === 'Delivered' ? true : false;
    }

    /**
     * Check if transaction has been created in Paynow and an up stream system, the customer has been
     * referred to that upstream system but has not yet made payment
     *
     * @return mixed|string
     */
    public function sent()
    {
        return $this->status() === 'sent' ? true : false;
    }

    /**
     * Check if transaction has been created in Paynow, but has not yet been paid by the customer.
     *
     * @return mixed|string
     */
    public function created()
    {
        return $this->status() === 'created' ? true : false;
    }

    /**
     * Check if transaction has been cancelled in Paynow.
     * If so the transaction may not be resumed and needs to be recreated.
     *
     * @return mixed|string
     */
    public function cancelled()
    {
        return $this->status() === 'cancelled' ? true : false;
    }

    /**
     * Check if transaction has been disputed by the Customer.
     * If so funds are being held in suspense until the dispute has been resolved.
     *
     * @return mixed|string
     */
    public function disputed()
    {
        return $this->status() === 'disputed' ? true : false;
    }

    /**
     * Check if funds were refunded back to the customer.
     *
     * @return mixed|string
     */
    public function refunded()
    {
        return $this->status() === 'refunded' ? true : false;
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