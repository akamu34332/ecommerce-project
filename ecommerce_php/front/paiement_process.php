<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}

require_once '../include/database.php';

$idUtilisateur = $_SESSION['utilisateur']['id'] ?? null;
$panier = $_SESSION['panier'][$idUtilisateur] ?? [];
$total = $_SESSION['total_panier'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = $_POST['payment_method'] ?? null;

    if (!$paymentMethod || empty($panier) || $total <= 0) {
        header('Location: paiement.php?error=invalid_payment');
        exit;
    }

    switch ($paymentMethod) {
        case 'stripe':
            // Redirection vers Stripe
            header('Location: ../payments/stripe_payment.php');
            exit;

        case 'paypal':
            // Redirection vers PayPal
            header('Location: ../payments/paypal_payment.php');
            exit;

        default:
            // Méthode de paiement invalide
            header('Location: paiement.php?error=unknown_method');
            exit;
    }
} else {
    header('Location: panier.php');
    exit;
}
?>
