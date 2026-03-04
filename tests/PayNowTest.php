<?php
/**
 * Created by PhpStorm.
 * User: Melvin
 * Date: 14/5/2018
 * Time: 12:34
 */

use Paynow\Payments\Paynow;
use PHPUnit\Framework\TestCase;

class PaynowTest extends TestCase
{
    private function makePaynow()
    {
        return new Paynow(
            'INTEGRATION_ID',
            'INTEGRATION_KEY',
            'https://example.org/return',
            'https://example.org/result'
        );
    }

    public function testCreatePayment()
    {
        $paynow = $this->makePaynow();
        $payment = $paynow->createPayment('INV-100', 'user@example.org');

        $this->assertTrue($payment instanceof \Paynow\Payments\FluentBuilder);
    }

    public function testSendNoReferenceThrowsEmptyTransactionReferenceException()
    {
        $this->expectException(\Paynow\Payments\EmptyTransactionReferenceException::class);

        $paynow = $this->makePaynow();

        $payment = $paynow->createPayment(null, 'user@example.org');
        $payment->add('Candles', 1.5);

        $paynow->send($payment);
    }

    public function testSendNoItemsThrowsEmptyCartException()
    {
        $this->expectException(\Paynow\Payments\EmptyCartException::class);

        $paynow = $this->makePaynow();

        $payment = $paynow->createPayment('INV-200', 'user@example.org');

        $paynow->send($payment);
    }

    public function testSendWithArrayRequiresReferenceAndAmount()
    {
        $this->expectException(\InvalidArgumentException::class);

        $paynow = $this->makePaynow();

        $paynow->send(['description' => 'Missing required keys']);
    }

    public function testCanSetAndGetUrls()
    {
        $paynow = $this->makePaynow();

        $paynow->setReturnUrl('https://example.org/new-return');
        $paynow->setResultUrl('https://example.org/new-result');

        $this->assertSame('https://example.org/new-return', $paynow->getReturnUrl());
        $this->assertSame('https://example.org/new-result', $paynow->getResultUrl());
    }

    public function testSendThrowsInvalidUrlExceptionWhenUrlsMissing()
    {
        $this->expectException(\Paynow\Payments\InvalidUrlException::class);

        $paynow = new Paynow('INTEGRATION_ID', 'INTEGRATION_KEY', null, null);

        $payment = $paynow->createPayment('INV-300', 'user@example.org');
        $payment->add('Item', 1.0);

        $paynow->send($payment);
    }
}
