<?php
require_once '../vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51QapnfDBi0EQXaZ6EceFj87ihSpcseWCKTPdmvvohy1rEL9UgiD7GDeDBWeRneBtHqHQoxBQovaZgWOBjMry5g3700bkbhCYpZ'); // Remplacez par votre clé secrète

if (!\Stripe\Stripe::getApiKey()) {
    throw new Exception('La clé API Stripe est introuvable ou non définie. Veuillez vérifier la configuration.');
}
?>