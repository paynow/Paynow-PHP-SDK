<?php
namespace Paynow\Http;

/**
 * HTTP Client
 * @package Paynow\Http
 */
class Client
{
    private $logger;

    /**
     * Default Constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        if (!function_exists("curl_init")) {
            throw new \Exception("Curl module is not available on this system");
        }

        $this->logger = null;
    }

    /**
     * Executes an HTTP request
     *
     * @param RequestInfo $info
     * @return mixed
     * @throws ConnectionException
     *
     * @todo Do not parse response from execute function (SOLID). Clean up
     */
    public function execute($info)
    {
        //Initialize Curl Options
        $ch = curl_init($info->getUrl());

        curl_setopt($ch, CURLOPT_URL, $info->getUrl());
        //Determine Curl Options based on Method
        switch ($info->getMethod()) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $info->getData());
                break;
                break;
            case 'GET':
                curl_setopt($ch, CURLOPT_URL, sprintf('%s?%s', $info->getUrl(), $info->getData()));
                break;
        }


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //Execute Curl Request
        $result = curl_exec($ch);


        //Retrieve Response Status
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //Retry if Certificate Exception
        if (curl_errno($ch) == 60) {
            /** @noinspection SpellCheckingInspection */
            curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
            $result = curl_exec($ch);
            //Retrieve Response Status
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }
        /** @noinspection SpellCheckingInspection */

        //Throw Exception if Retries and Certificates doenst work
        if (curl_errno($ch)) {
            $ex = new ConnectionException(
                $info->getUrl() . '\n' .
                curl_error($ch) . '\n' .
                curl_errno($ch)
            );
            curl_close($ch);
            throw $ex;
        }
        curl_close($ch);
        //More Exceptions based on HttpStatus Code
        if ($httpStatus < 200 || $httpStatus >= 300) {
            $ex = new ConnectionException(
                $info->getUrl() . '\n' .
                "Got Http response code $httpStatus when accessing {$info->getUrl()}." . '\n' .
                $httpStatus
            );
            throw $ex;
        }
        //Return result object
        $data = [];
        parse_str($result, $data);

        return $data;
    }
}