<?php

require __DIR__ . '/../vendor/autoload.php';
// require __DIR__.'/src/helper/util.php';

use RevenueMonster\SDK\RevenueMonster;
use RevenueMonster\SDK\Exceptions\ApiException;
use RevenueMonster\SDK\Exceptions\ValidationException;
use RevenueMonster\SDK\Request\DuitnowCheckout;
use RevenueMonster\SDK\Request\QRCheckout;
use RevenueMonster\SDK\Request\URLCheckout;
use RevenueMonster\SDK\Request\WebPayment;

echo '<div style="width: 100%; word-break: break-all;">';
echo round(microtime(true) * 1000) . '<br/>';
$rm = new RevenueMonster([
    'clientId' => '1553826822294112891',
    'clientSecret' => 'nbPqwJtxdiZBiSQkyWLOYPQEufOABAuv',
    'privateKey' => file_get_contents(__DIR__ . '/private_key.pem'),
    'publicKey' => file_get_contents(__DIR__ . '/public_key.pem'),
    'version' => 'stable',
    'isSandbox' => true,
]);

try {
    $wp = new WebPayment();
    $wp->order->id = uniqid();
    $wp->order->title = 'Testing Web Payment';
    $wp->order->currencyType = 'MYR';
    $wp->order->amount = 100;
    $wp->order->detail = '';
    $wp->order->additionalData = '';
    $wp->storeId = "1553067342153519097";
    $wp->redirectUrl = 'https://google.com';
    $wp->notifyUrl = 'https://google.com';

    $response = $rm->payment->createWebPayment($wp);
    echo '<p>' . $response->checkoutId . '</p>'; // Checkout ID
    echo '<p>' . $response->url . '</p>'; // Payment gateway url

    $checkout = new QRCheckout();
    $checkout->checkoutId = $response->checkoutId;
    $checkout->method = 'MAYBANK_MY';
    $checkoutResponse = $rm->payment->createQRCheckout($checkout);
    echo '<p>' . $checkoutResponse->qrcode->data . '</p>'; // Checkout ID
    echo '<img style="max-height: 300px" src="data:image/jpeg;base64,' . $checkoutResponse->qrcode->base64Image . '" />'; // Payment gateway url

    $checkout = new DuitnowCheckout();
    $checkout->checkoutId = $response->checkoutId;
    $checkoutResponse = $rm->payment->createQRCheckout($checkout);
    echo '<img style="max-height: 300px" src="data:image/jpeg;base64,' . $checkoutResponse->qrcode->base64Image . '" />'; // Payment gateway url

    $checkout = new URLCheckout();
    $checkout->checkoutId = $response->checkoutId;
    $checkout->method = 'FPX_MY';
    $checkout->bankCode = 'TEST';
    $checkoutResponse = $rm->payment->createURLCheckout($checkout);
    echo '<p>' . $checkoutResponse->url . '</p>';
} catch (ApiException $e) {
    echo "statusCode : {$e->getCode()}, errorCode : {$e->getErrorCode()}, errorMessage : {$e->getMessage()}";
} catch (ValidationException $e) {
    var_dump($e->getMessage());
} catch (Exception $e) {
    echo $e->getMessage();
}
