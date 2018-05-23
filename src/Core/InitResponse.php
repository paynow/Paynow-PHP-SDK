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
    private $success;

    /**
     * Boolean indicating whether the response contains a url to redirect to
     * @var bool
     */
    private $has_redirect;

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
            $this->success = strtolower($this->data['status']) === Constants::RESPONSE_OK;
        }

        if(arr_has($this->data,'browserurl')) {
            $this->has_redirect = true;
        }

        if(!$this->success()) {
            if(arr_has($this->data, 'error')) {
                $this->fail(strtolower($this->data['error']));
            }
        }
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
     * Generates and prints out a pay button that the user can click and get redirected to Paynow
     *
     * @return void
     */
    public function paymentButton()
    {
        if(!$this->has_redirect) {
            return;
        }

        $link = sprintf("<a style='text-decoration: none' class='paynow paynow-payment' href='%s'>%s</a>", $this->redirectLink(), Constants::PAYNOW_BUTTON);

        print $link;
    }

    /**
     * Returns the url the user should be taken to so they can make a payment
     *
     * @return bool|string
     */
    public function redirectLink()
    {
        if($this->has_redirect) {
            return $this->data['browserurl'];
        }

        return false;
    }

    /**
     * Redirect to the Paynow site for payment
     * @return bool
     */
    public function redirect()
    {
        if($this->has_redirect) {
           return header("Location: {$this->data['browserurl']}");
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