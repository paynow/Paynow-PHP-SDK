<?php
namespace Paynow\Payments;

use Paynow\Util\Hash;
use Paynow\Http\Client;
use Paynow\Core\Constants;
use Paynow\Http\RequestInfo;
use InvalidArgumentException;
use Paynow\Core\InitResponse;
use Paynow\Core\StatusResponse;


class Paynow
{
    /**
     * Merchant's return url
     * @var string
     */
    protected $returnUrl = null;
    /**
     * Merchant's result url
     * @var string
     */
    protected $resultUrl = null;
    /**
     * Client for making http requests
     * @var Client
     */
    private $client;
    /**
     * Merchant's integration id
     * @var string
     */
    private $integrationId = "";
    /**
     * Merchant's integration key
     * @var string
     */
    private $integrationKey = "";


    /**
     * Default constructor.
     *
     * @param Client $client Client for making http requests
     * @param string $id Merchant's integration id
     * @param string $key Merchant's integration key
     */
    public function __construct($id, $key, $returnUrl, $resultUrl)
    {
        $this->client = new Client();

        $this->integrationId = $id;
        $this->integrationKey = strtolower($key);
        $this->returnUrl = $returnUrl;
        $this->resultUrl = $resultUrl;
    }

    /**
     * @param string|null $ref Transaction reference
     * @param string|null $authEmail The email of the person making payment
     *
     * @return FluentBuilder
     */
    public function createPayment($ref, $authEmail)
    {
        return new FluentBuilder($ref, $authEmail);
    }


    /**
     * Send a transaction to Paynow
     *
     * @param FluentBuilder|array $builder
     *
     * @throws HashMismatchException
     * @throws \Paynow\Http\ConnectionException
     * @throws \Paynow\Payments\EmptyCartException
     * @throws \Paynow\Payments\EmptyTransactionReferenceException
     * @throws InvalidIntegrationException
     * @throws InvalidUrlException
     *
     * @return InitResponse
     */
    public function send($builder)
    {
        if(is_null($this->returnUrl) || is_null($this->returnUrl)) {
            throw new InvalidUrlException();
        }

        if(is_array($builder)) {
            $builder = $this->createBuilder($builder);
        }

        if (is_null($builder->ref)) {
            throw new EmptyTransactionReferenceException($builder);
        }

        if ($builder->count == 0) {
            throw new EmptyCartException($builder);
        }

        return $this->init($builder);
    }

    /**
     * Create an instance of the fluent builder from the provided array of items
     *
     * @param array $items
     * @return void
     */
    private function createBuilder($items)
    {
        if(!isset($items['reference'], $items['amount'])) {
            throw new InvalidArgumentException("Payment array should have the following keys: reference, total");
        }

        $description = isset($items['description']) ? $items['description'] : "Payment";

        $builder = new FluentBuilder($description, $items['reference'], $items['amount']);
        $builder->setDescription($description);

        return $builder;
    }

    /**
     * Send a mobile transaction
     *
     * @param $phone
     * @param FluentBuilder $builder
     *
     * @return InitResponse
     *
     * @throws HashMismatchException
     * @throws NotImplementedException
     * @throws InvalidIntegrationException
     * @throws \Paynow\Http\ConnectionException
     */
    public function sendMobile(FluentBuilder $builder, $phone, $method)
    {
        if (is_null($builder->ref)) {
            throw new EmptyTransactionReferenceException($builder);
        }

        if ($builder->count == 0) {
            throw new EmptyCartException($builder);
        }

        return $this->initMobile($builder, $phone, $method);
    }

    /**
     * Initiate a new Paynow transaction
     *
     * @param FluentBuilder $builder The transaction to be sent to Paynow
     *
     * @throws HashMismatchException
     * @throws InvalidIntegrationException
     * @throws InvalidIntegrationException
     * @throws \Paynow\Http\ConnectionException
     *
     * @return InitResponse The response from Paynow
     */
    protected function init(FluentBuilder $builder)
    {
        $request = $this->formatInit($builder);

        $response = $this->client->execute($request);

        if (arr_has($response, 'hash')) {
            if (!Hash::verify($response, $this->integrationKey)) {
                throw new HashMismatchException();
            }
        }

        return new InitResponse($response);
    }

    /**
     * Initiate a new Paynow transaction
     *
     * @param FluentBuilder $builder The transaction to be sent to Paynow
     * @param string $phone The user's phone number
     * @param string $method The mobile transaction method i.e ecocash, telecash
     *
     * @throws HashMismatchException
     * @throws NotImplementedException
     * @throws InvalidIntegrationException
     * @throws \Paynow\Http\ConnectionException
     *
     * @note Only ecocash is currently supported
     *
     * @return InitResponse The response from Paynow
     */
    protected function initMobile(FluentBuilder $builder, $phone, $method)
    {
        if (!isset($method)) {
            throw new InvalidArgumentException("The mobile money method should be specified");
        }

        $request = $this->formatInitMobile($builder, $phone, $method);

        if(!$builder->auth_email || !filter_var($builder->auth_email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Auth email is required for mobile transactions. When creating a mobile payment, please make sure you pass the auth email as the second parameter to the createPayment method');
        }

        $response = $this->client->execute($request);

        if (arr_has($response, 'hash')) {
            if (!Hash::verify($response, $this->integrationKey)) {
                throw new HashMismatchException();
            }
        }

        return new InitResponse($response);
    }


    /**
     * Format a request before it's sent to Paynow
     *
     * @param FluentBuilder $builder The transaction to send to Paynow
     *
     * @return RequestInfo The formatted transaction
     */
    private function formatInit(FluentBuilder $builder)
    {
        $items = $builder->toArray();
        $items['resulturl'] = $this->resultUrl;
        $items['returnurl'] = $this->returnUrl;
        $items['id'] = $this->integrationId;
		$items['authemail'] = $builder->auth_email;

        foreach ($items as $key => $item) {
            $items[$key] = trim(utf8_encode($item));
        }

        $items['hash'] = Hash::make($items, $this->integrationKey);

        return RequestInfo::create(Constants::URL_INITIATE_TRANSACTION, 'POST', $items);
    }

    /**
     * Format a request before it's sent to Paynow
     *
     * @param FluentBuilder $builder The transaction to send to Paynow
     *
     * @param string $phone The mobile phone making the payment
     * @param string $method The mobile money method
     *
     * @return RequestInfo The formatted transaction
     */
    private function formatInitMobile(FluentBuilder $builder, $phone, $method)
    {
        $items = $builder->toArray();

        $items['resulturl'] = $this->resultUrl;
        $items['returnurl'] = $this->returnUrl;
        $items['id'] = $this->integrationId;
        $items['phone'] = $phone;
        $items['method'] = $method;
        $items['authemail'] = $builder->auth_email;

        foreach ($items as $key => $item) {
            $items[$key] = trim(utf8_encode($item));
        }

        $items['hash'] = Hash::make($items, strtolower($this->integrationKey));


        return RequestInfo::create(Constants::URL_INITIATE_MOBILE_TRANSACTION, 'POST', $items);
    }

    /**
     * Get the merchant's return url
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * Sets the merchant's return url
     *
     * @param string $returnUrl
     */
    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;
    }

    /**
     * Check the status of a transaction
     *
     * @param $url
     *
     * @throws \Paynow\Http\ConnectionException
     * @throws HashMismatchException
     *
     * @return StatusResponse
     */
    public function pollTransaction($url)
    {
        $response = $this->client->execute(RequestInfo::create(trim($url), 'METHOD', []));

        if (arr_has($response, 'hash')) {
            if (!Hash::verify($response, $this->integrationKey)) {
                throw new HashMismatchException();
            }
        }

        return new StatusResponse($response);
    }

    /**
     * Process a status update from Paynow
     *
     * @return StatusResponse
     * @throws HashMismatchException
     */
    public function processStatusUpdate()
    {
        $data = $_POST;
        if (!arr_has($data, 'hash') || !Hash::verify($data, $this->integrationKey)) {
            throw new HashMismatchException();
        }

        return new StatusResponse($data);
    }

    /**
     * Get the result url for the merchant
     *
     * @return string
     */
    public function getResultUrl()
    {
        return $this->resultUrl;
    }

    /**
     * Sets the merchant's result url
     *
     * @param string $resultUrl
     */
    public function setResultUrl($resultUrl)
    {
        $this->resultUrl = $resultUrl;
    }
}
