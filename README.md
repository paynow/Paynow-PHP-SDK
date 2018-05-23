# Paynow PHP SDK

[![N|Solid](https://cldup.com/dTxpPi9lDf.thumb.png)](https://nodesource.com/products/nsolid)

# Prerequisites
This library has a set of prerequisites that must be met for it to work
1. PHP version 5.6 or higher
2. Curl extension

# Installation
Install the library using composer

```sh
$ composer require paynowzw/paynow-php-sdk
```

# Usage example
Create an instance of the Paynow class optionally setting the result and return url(s)
```php
$paynow = new Paynow\Payments\Paynow(
	new Paynow\Http\Client(),
	'INTEGRATION_ID',
	'INTEGRATION_KEY'
);
$paynow->setResultUrl('http://example.com/gateways/paynow/update');
$paynow->setReturnUrl('http://example.com/return?gateway=paynow');
// The return url can be set at later stages. You might want to do this if you want to pass data to the return url (like the reference of the transaction)
```

Create a new payment passing in the reference for that payment (e.g invoice id, or anything that you can use to identify the transaction.

```php
$payment = $paynow->createPayment('Invoice 35');
```

You can then start adding items to the payment 
```php
// Passing in the name of the item and the price of the item
$payment->add('Bananas', 2.50);
$payment->add('Apples', 3.40);
```

When you're finally ready to send your payment to Paynow, you can use the `send` method in the `$paynow` object. 

```php
// Save the response from paynow in a variable
$response = $paynow->send($payment);
```

The response from Paynow will have some useful information like whether the request was successful or not. If it was, for example, it contains the url to redirect the user so they can make the payment. You can view the full list of data contained in the response in our wiki

If request was successful, you should consider saving the poll url sent from Paynow in the database

```php
if($response->success()) {
    // Redirect the user to Paynow
    $response->redirect();
    
    // Or if you prefer more control, get the link to redirect to the use it as you see fit 
    $link = $response->redirectLink();
}
```

# Full Usage Example

```php
require_once '/path/to/vendor/autoload.php';

$paynow = new Paynow\Payments\Paynow(
	new Paynow\Http\Client(),
	'INTEGRATION_ID',
	'INTEGRATION_KEY'
);
$paynow->setResultUrl('http://example.com/gateways/paynow/update');
$paynow->setReturnUrl('http://example.com/return?gateway=paynow');

$payment = $paynow->createPayment('Invoice 35');

$payment->add('Bananas', 2.50);
$payment->add('Apples', 3.40);

$response = $paynow->send($payment);

if($response->success()) {
	echo $response->redirectLink();
}

```