<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Composer autoload

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

$paypal = new ApiContext(
    new OAuthTokenCredential(
        'AZxTH-vQmgxnSg3-uket985CLyIvcpP2S48-Ch6t8aZI5KFQOuos6tjXdd6fSxFJd_5AaoN-m1sgiW9X', // Remplacez par votre CLIENT_ID PayPal
        'EP_EpoQoREE1dnkyS77urgCEqLJOw-IsUXIlQWk_dSNeh9rIgnfm13OmpMoioDeBCP48a4KQlVnZXxnr' // Remplacez par votre CLIENT_SECRET PayPal
    )
);

$paypal->setConfig(
    [
        'mode' => 'sandbox', // ou 'live'
        'http.ConnectionTimeOut' => 30,
        'log.LogEnabled' => true,
        'log.FileName' => '../PayPal.log',
        'log.LogLevel' => 'FINE'
    ]
);
?>

