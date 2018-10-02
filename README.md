# Paynow Zimbabwe PHP SDK

PHP SDK for Paynow Zimbabwe's API

# Prerequisites

This library has a set of prerequisites that must be met for it to work

1.  PHP version 5.6 or higher
2.  Curl extension

# Installation

Install the library using composer

```sh
$ composer require paynow/php-sdk
```

and include the composer autoloader

```php
<?php
	require_once 'path/to/vendor/autoload.php';

	// Do stuff
```
---
---

# Or 

Alternatively, if you do not have composer installed, [first download the library here](https://gitlab.com/paynow-developer-hub/Paynow-PHP-SDK/-/archive/master/Paynow-PHP-SDK-master.zip). And include the autoloader file included with the library

```php
<?php
	require_once 'path/to/library/autoloader.php';

	// Do stuff
```

# Usage example

Create an instance of the Paynow class optionally setting the result and return url(s)

```php
$paynow = new Paynow\Payments\Paynow(
	'INTEGRATION_ID',
	'INTEGRATION_KEY',
	'http://example.com/gateways/paynow/update',

	// The return url can be set at later stages. You might want to do this if you want to pass data to the return url (like the reference of the transaction)
	'http://example.com/return?gateway=paynow'
);
```

Create a new payment passing in the reference for that payment (e.g invoice id, or anything that you can use to identify the transaction and the user's email address

```php
$payment = $paynow->createPayment('Invoice 35', 'user@example.com');
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

The response from Paynow will b have some useful information like whether the request was successful or not. If it was, for example, it contains the url to redirect the user so they can make the payment. You can view the full list of data contained in the response in our wiki

If request was successful, you should consider saving the poll url sent from Paynow in the database

```php
if($response->success()) {
    // Redirect the user to Paynow
    $response->redirect();

    // Or if you prefer more control, get the link to redirect the user to, then use it as you see fit
	$link = $response->redirectLink();

	// Get the poll url (used to check the status of a transaction). You might want to save this in your DB
	$pollUrl = $response->pollUrl();
}
```

---

> Mobile Transactions

If you want to send an express (mobile) checkout request instead, the only thing that differs is the last step. You make a call to the `sendMobile` in the `$paynow` object
instead of the `send` method.

The `sendMobile` method unlike the `send` method takes in two additional arguments i.e The phone number to send the payment request to and the mobile money method to use for the request. **Note that currently only ecocash is supported**

```php
// Save the response from paynow in a variable
$response = $paynow->sendMobile($payment, '077777777', 'ecocash');
```

The response object is almost identical to the one you get if you send a normal request. With a few differences, firstly, you don't get a url to redirect to. Instead you instructions (which ideally should be shown to the user instructing them how to make payment on their mobile phone)

```php
if($response->success()) {
	// Get the poll url (used to check the status of a transaction). You might want to save this in your DB
	$pollUrl = $response->pollUrl();

	// Get the instructions
	$instrutions = $response->instructions();
}
```

# Checking transaction status

The SDK exposes a handy method that you can use to check the status of a transaction. Once you have instantiated the Paynow class.

```php
// Check the status of the transaction with the specified pollUrl
// Now you see why you need to save that url ;-)
$status = $paynow->pollTransaction($pollUrl);

if($status->paid()) {
	// Yay! Transaction was paid for
} else {
	print("Why you no pay?");
}
```

# Full Usage Example

```php
require_once('./paynow/vendor/autoload.php');

$paynow = new Paynow\Payments\Paynow(
	'INTEGRATION_ID',
	'INTEGRATION_KEY',
	'http://example.com/gateways/paynow/update',

	// The return url can be set at later stages. You might want to do this if you want to pass data to the return url (like the reference of the transaction)
	'http://example.com/return?gateway=paynow'
);

# $paynow->setResultUrl('');
# $paynow->setReturnUrl('');

$payment = $paynow->createPayment('Invoice 35', 'melmups@outlook.com');

$payment->add('Sadza and Beans', 1.25);

$response = $paynow->send($payment);


if($response->success()) {
    // Redirect the user to Paynow
    $response->redirect();

    // Or if you prefer more control, get the link to redirect the user to, then use it as you see fit
    $link = $response->redirectLink();

	$pollUrl = $response->pollUrl();


	// Check the status of the transaction
	$status = $paynow->pollTransaction($pollUrl);

}
```
