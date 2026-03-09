<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}
require_once '../include/database.php';

echo "<h1>Paiement annulé.</h1>";
echo "<p>Vous pouvez réessayer ou retourner à votre panier pour modifier votre commande.</p>";
echo "<a href='panier.php' class='btn btn-primary'>Retour au panier</a>";
?>