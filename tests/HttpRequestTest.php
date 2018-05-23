<?php
declare(strict_types=1);

use Paynow\Http\RequestInfo;
use PHPUnit\Framework\TestCase;

/**
 * Class HttpRequestTest
 * @noInspection
 */

final class HttpRequestTests extends TestCase
{
    public function testCanSendGetHttpRequest(): void
    {
        $new = new \Paynow\Http\Client(new \Paynow\Core\Logger());

        $data = $new->execute(RequestInfo::create('http://localhost/client/', 'GET', []));

        $this->assertEquals('success', $data);
    }

    public function testCanSendHttpRequestWithOneArgument(): void
    {
        $new = new \Paynow\Http\Client(new \Paynow\Core\Logger());

        $data = $new->execute(RequestInfo::create('http://localhost/client/', 'GET', ['json'  => 'true']));

        $json = json_decode($data);

        $this->assertTrue(!is_null($json));
    }

    public function testCanSendHttpRequestWithMultipleArguments(): void
    {
        $new = new \Paynow\Http\Client(new \Paynow\Core\Logger());

        $data = $new->execute(RequestInfo::create('http://localhost/client/', 'GET', ['json'  => 'true', 'fruits' => 'true']));

        $json = json_decode($data);

        $this->assertTrue(is_array($json) && count($json) == 6);
    }

    public function testCanSendPostHttpRequest(): void
    {
        $new = new \Paynow\Http\Client(new \Paynow\Core\Logger());

        $data = $new->execute(RequestInfo::create('http://localhost/client/', 'POST', []));

        $this->assertEquals('Yatta!', $data);
    }

    public function testCanSendPostHttpRequestWithOneArgument(): void
    {
        $new = new \Paynow\Http\Client(new \Paynow\Core\Logger());

        $data = $new->execute(RequestInfo::create('http://localhost/client/', 'POST', ['json' => 'true']));

        $this->assertEquals('JSON!!!', $data);
    }

    public function testCanSendPostHttpRequestWithMultipleArguments(): void
    {
        $new = new \Paynow\Http\Client(new \Paynow\Core\Logger());

        $data = $new->execute(RequestInfo::create('http://localhost/client/', 'POST', ['fruits' => 'true']));

        $this->assertEquals('FRUITY JSON!!!', $data);
    }
}
