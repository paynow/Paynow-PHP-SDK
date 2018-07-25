<?php
namespace Paynow\Http;


/**
 * Stores parameters for an http request
 *
 * @package Paynow\Http
 */
class RequestInfo
{
    /**
     * URL of the http request
     * @var string
     */
    private $url;

    /**
     * The http request method being used for the request
     * @var string
     */
    private $method;

    /**
     * Data to be sent with the http request
     * @var array
     */
    private $data;

    /**
     * Default constructor
     *
     * @param string $url URL of the http request
     * @param string $method Data to be sent with the http request
     * @param array $data ata to be sent with the http request
     */
    private function __construct($url, $method, $data)
    {
        $this->url = $url;
        $this->method = $method;

        $this->data = http_build_query($data);
    }

    /**
     * Gets the data for the http request as an http query string
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the url for the http request
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * Get the method for the http request
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Create a new RequestInfo object
     *
     * @param string $url URL of the http request
     * @param string $method Data to be sent with the http request
     * @param array $data ata to be sent with the http request
     *
     * @return RequestInfo
     */
    public static function create($url, $method, $data = [])
    {
        if(!is_string($url) || empty($url) || !filter_var($url, FILTER_VALIDATE_URL))
            throw new \InvalidArgumentException('Invalid URL');
        if(!is_string($method) || empty($method))
            throw new \InvalidArgumentException('Invalid Method');


        return new static($url, $method, $data);
    }
}