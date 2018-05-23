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


    public function testBuilderParseListOfItems()
    {
        $paynow = new Paynow(new \Paynow\Http\Client(), '', '');


        $payment = $paynow->createPayment([
            ['title' => 'Candles', 'amount' => 1.5],
            ['title' => 'Sandwich', 'amount' => 2],
            ['title' => 'Bacon', 'amount' => 4],
        ]);

        $this->assertEquals(3, $payment->count);
    }

    public function testBuilderCanComputeTotalOfItems()
    {
        $paynow = new Paynow(new \Paynow\Http\Client(), '', '');


        $payment = $paynow->createPayment([
            ['title' => 'Candles', 'amount' => 1.5],
            ['title' => 'Sandwich', 'amount' => 2],
            ['title' => 'Bacon', 'amount' => 4],
        ]);

        $this->assertEquals(7.5, $payment->total);
    }

    public function testBuilderCanAddItemsFluentsAfterInit()
    {
        $paynow = new Paynow(new \Paynow\Http\Client(), '', '');


        $payment = $paynow->createPayment([
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
        $paynow = new Paynow(new \Paynow\Http\Client(), '', '');


        $payment = $paynow->createPayment();

        $payment
            ->add('Green Beans', 3)
            ->add('Tomatoes', 3)
            ->add('Pork', 12)
            ->add('Apple Pie', 2);


        $this->assertEquals(4, $payment->count);
    }

}
