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

    public function testCreatePayment()
    {
        $paynow = new Paynow(new \Paynow\Http\Client(), '', '');


        $payment = $paynow->createPayment();

        $this->assertTrue($payment instanceof \Paynow\Payments\FluentBuilder);
    }

    public function testSendNoReferenceThrowsEmptyTransactionReferenceException()
    {
        $this->expectException(\Paynow\Payments\EmptyTransactionReferenceException::class);

        $paynow = new Paynow(new \Paynow\Http\Client(), '', '');

        $payment = $paynow->createPayment([
            ['title' => 'Candles', 'amount' => 1.5],
            ['title' => 'Sandwich', 'amount' => 2],
            ['title' => 'Bacon', 'amount' => 4],
        ]);


        $response = $paynow->send($payment);
    }

    public function testSendNoItemsThrowsEmptyCartException()
    {
        $this->expectException(\Paynow\Payments\EmptyCartException::class);

        $paynow = new Paynow(new \Paynow\Http\Client(), '', '');

        $payment = $paynow->createPayment(null, 10092);


        $response = $paynow->send($payment);

        $this->assertInstanceOf(\Paynow\Core\InitResponse::class, $response);
    }

    public function testSendReturnsInitResponse()
    {
        $paynow = new Paynow(new \Paynow\Http\Client(), '', '');

        $payment = $paynow->createPayment([
            ['title' => 'Candles', 'amount' => 1.5],
            ['title' => 'Sandwich', 'amount' => 2],
            ['title' => 'Bacon', 'amount' => 4],
        ], '0B921');

        $response = $paynow->send($payment);

        $this->assertInstanceOf(\Paynow\Core\InitResponse::class, $response);
    }

    public function testSendThrowsInvalidIntegrationExceptionIfNoOrWrongIdOrIntegrationKey()
    {
        $this->expectException(\Paynow\Payments\InvalidIntegrationException::class);

        $paynow = new Paynow(new \Paynow\Http\Client(), '', '');

        $payment = $paynow->createPayment([
            ['title' => 'Candles', 'amount' => 1.5],
            ['title' => 'Sandwich', 'amount' => 2],
            ['title' => 'Bacon', 'amount' => 4],
        ], '0B921');

        $response = $paynow->send($payment);
    }


    public function testSend()
    {
        $this->expectException(\Paynow\Payments\InvalidIntegrationException::class);

        $paynow = new Paynow(new \Paynow\Http\Client(), '', '');

        $payment = $paynow->createPayment([
            ['title' => 'Candles', 'amount' => 1.5],
            ['title' => 'Sandwich', 'amount' => 2],
            ['title' => 'Bacon', 'amount' => 4],
        ], '0B921');

        $response = $paynow->send($payment);
    }
}
