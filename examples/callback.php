<?php

require_once '../autoloader.php';



/**
 * Just a small dummy logger. Remove in production
 *
 * @param StatusResponse $status
 * @return void
 */
function dummy_logger($status) {

    $str =  sprintf("Recieved updated from Paynow --> Payment Status: %s || ", $status->status());

    $str .= sprintf("Transaction ID: %s || ", $status->reference());
    $str .= sprintf("Paynow Reference: %s \n\n", $status->paynowReference());

    file_put_contents(__DIR__ . '/status.logs', $str);

}

$paynow = new Paynow\Payments\Paynow(
    'INTEGRATION_ID',
    'INTEGRATION_KEY',
    'http://d8403290.ngrok.io/paynow-demo-php/examples/index.php?paynow-return=true',
    'http://d8403290.ngrok.io/paynow-demo-php/examples/callback.php'
);


$status = $paynow->processStatusUpdate();


// Check if the transaction was paid
if($status->paid()) {

    // Update transaction in DB maybe? 
    $reference =  $status->reference();


    // Get the reference of the Payment in paynow
    $paynowReference = $status->paynowReference();

    // Log out the data
    dummy_logger($status);
}