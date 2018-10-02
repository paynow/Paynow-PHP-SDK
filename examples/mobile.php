<?php 

require_once '../autoloader.php';

$paynow = new Paynow\Payments\Paynow(
    'INTEGRATION_ID',
    'INTEGRATION_KEY',
    'http://d8403290.ngrok.io/paynow-demo-php/examples/index.php?paynow-return=true',
    'http://d8403290.ngrok.io/paynow-demo-php/examples/callback.php'
);


$payment = $paynow->createPayment('Order 3', 'testmerchant@mailinator.com');


$payment->add('Sadza and Cold Water', 0.5)
        ->add('Sadza and Hot Water', 0.5);

// Optionally set a description for the order.
// By default, a description is generated from the items
// added to a payment
$payment->setDescription("Mr Maposa\'s lunch order");


// Initiate a Payment 
$response = $paynow->sendMobile($payment, '0777832735', 'ecocash');


?>


<?php if(!$response->success): ?>

    <!-- Something went wrong while initating payment -->
    <h2>An error occured while communicating with Paynow</h2>
    <p><?= $response->error ?></p>

<?php else: ?>

    <!-- Maybe write some script that checks if Paynow sent a status update -->
    <p><?= $response->instructions() ?></p>

<?php endif; ?>


<?php if(isset($_GET['paynow-return'])): ?>
<script>
    alert('Thank you for your payment!');
</script>
<?php endif; ?>