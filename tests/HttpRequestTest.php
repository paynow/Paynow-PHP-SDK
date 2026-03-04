<?php
declare(strict_types=1);

use Paynow\Http\Client;
use Paynow\Http\RequestInfo;
use PHPUnit\Framework\TestCase;

/**
 * Class HttpRequestTest
 * @noInspection
 */

final class HttpRequestTest extends TestCase
{
    public function testCanCreateGetRequestInfo(): void
    {
        $request = RequestInfo::create('https://example.org/client', 'GET', []);

        $this->assertSame('https://example.org/client', $request->getUrl());
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('', $request->getData());
    }

    public function testCanCreateRequestInfoWithQueryData(): void
    {
        $request = RequestInfo::create('https://example.org/client', 'POST', ['json' => 'true', 'fruits' => 'true']);

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('json=true&fruits=true', $request->getData());
    }

    public function testCreateRequestInfoThrowsOnInvalidUrl(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        RequestInfo::create('not-a-url', 'GET', []);
    }

    public function testCreateRequestInfoThrowsOnInvalidMethod(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        RequestInfo::create('https://example.org/client', '', []);
    }

    public function testCanConstructClient(): void
    {
        $client = new Client();

        $this->assertInstanceOf(Client::class, $client);
    }
}
