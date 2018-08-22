<?php 

require_once '../autoloader.php';

$paynow = new Paynow\Payments\Paynow(
    '1240',
    '7fb5dcea-219c-4797-b3dd-e31c476c2bb5',
    'http://d8403290.ngrok.io/paynow-demo-php/examples/index.php?paynow-return=true',
    'http://d8403290.ngrok.io/paynow-demo-php/examples/callback.php'
);


$payment = $paynow->createPayment('Order 3');


$payment->add('Sadza and Cold Water', 12.2)
        ->add('Sadza and Hot Water', 20.50);

// Optionally set a description for the order.
// By default, a description is generated from the items
// added to a payment
$payment->setDescription("Mr Maposa\'s lunch order");


// Initiate a Payment 
$response = $paynow->send($payment);


?>


<?php if(!$response->success): ?>

    <h2>An error occured while communicating with Paynow</h2>
    <p><?= $response->error ?></p>

<?php else: ?>

    <a href="<?= $response->redirectUrl() ?>">Click here to make payment of $<?= $payment->total ?></a>

<?php endif; ?>


<?php if(isset($_GET['paynow-return'])): ?>
<script>
    alert('Thank you for your payment!');
</script>
<?php endif; ?>