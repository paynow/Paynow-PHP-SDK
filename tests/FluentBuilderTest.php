<?php
/**
 * Created by PhpStorm.
 * User: Melvin
 * Date: 14/5/2018
 * Time: 11:29
 */
use PHPUnit\Framework\TestCase;

use Paynow\Payments\Paynow;

class FluentBuilderTest extends TestCase
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


    public function testBuilderParseListOfItems()
    {
        $paynow = $this->makePaynow();

        $payment = $paynow->createPayment('INV-1', 'user@example.org');
        $payment->add([
            ['title' => 'Candles', 'amount' => 1.5],
            ['title' => 'Sandwich', 'amount' => 2],
            ['title' => 'Bacon', 'amount' => 4],
        ]);

        $this->assertEquals(3, $payment->count);
    }

    public function testBuilderCanComputeTotalOfItems()
    {
        $paynow = $this->makePaynow();

        $payment = $paynow->createPayment('INV-2', 'user@example.org');
        $payment->add([
            ['title' => 'Candles', 'amount' => 1.5],
            ['title' => 'Sandwich', 'amount' => 2],
            ['title' => 'Bacon', 'amount' => 4],
        ]);

        $this->assertEquals(7.5, $payment->total);
    }

    public function testBuilderCanAddItemsFluentsAfterInit()
    {
        $paynow = $this->makePaynow();

        $payment = $paynow->createPayment('INV-3', 'user@example.org');
        $payment->add([
            ['title' => 'Candles', 'amount' => 1.5],
            ['title' => 'Sandwich', 'amount' => 2],
            ['title' => 'Bacon', 'amount' => 4],
        ]);

        $payment->add('Tomatoes', 3);
        $payment->add('Pork', 12);
        $payment->add('Apple Pie', 2);


        $this->assertEquals(6, $payment->count);
    }

    public function testBuilderCanAddItemsFluently()
    {
        $paynow = $this->makePaynow();

        $payment = $paynow->createPayment('INV-4', 'user@example.org');

        $payment
            ->add('Green Beans', 3)
            ->add('Tomatoes', 3)
            ->add('Pork', 12)
            ->add('Apple Pie', 2);


        $this->assertEquals(4, $payment->count);
    }

}
