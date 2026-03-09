<?php
// Initialisation de la session
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}

// Inclusion des fichiers nécessaires
require_once '../include/database.php';
require_once '../include/paypal_init.php'; // Pour PayPal
require_once '../include/stripe_init.php'; // Pour Stripe

// Vérification de la méthode de paiement
$paymentMethod = $_GET['payment_method'] ?? null;

if (!$paymentMethod) {
    displayError("Aucune méthode de paiement spécifiée.");
    exit;
}

// Traitement du paiement via PayPal
if ($paymentMethod === 'paypal' && isset($_GET['paymentId'], $_GET['PayerID'])) {
    try {
        $paymentId = $_GET['paymentId'];
        $payerId = $_GET['PayerID'];

        // Récupération du paiement via PayPal
        $payment = PayPal\Api\Payment::get($paymentId, $paypal);
        $execution = new PayPal\Api\PaymentExecution();
        $execution->setPayerId($payerId);

        // Exécution du paiement
        $result = $payment->execute($execution, $paypal);

        if ($result->getState() === 'approved') {
            $idCommande = $_SESSION['id_commande'] ?? null;
            if ($idCommande) {
                $pdo->prepare("UPDATE commande SET valide = 1 WHERE id = ?")->execute([$idCommande]);

                // Vider le panier
                unset($_SESSION['panier']);
                unset($_SESSION['id_commande']);

                displaySuccess("Paiement PayPal réussi !", "Merci pour votre commande. Vous recevrez un email de confirmation sous peu.");
                exit;
            }
        }
    } catch (Exception $e) {
        displayError("Erreur lors de la validation du paiement PayPal : " . htmlspecialchars($e->getMessage()));
        exit;
    }
}

// Traitement du paiement via Stripe
if ($paymentMethod === 'stripe' && isset($_GET['session_id'])) {
    try {
        $sessionId = $_GET['session_id'];
        $checkoutSession = \Stripe\Checkout\Session::retrieve($sessionId);

        if ($checkoutSession && $checkoutSession->payment_status === 'paid') {
            $idCommande = $_SESSION['id_commande'] ?? null;
            if ($idCommande) {
                $pdo->prepare("UPDATE commande SET valide = 1 WHERE id = ?")->execute([$idCommande]);

                // Vider le panier
                unset($_SESSION['panier']);
                unset($_SESSION['id_commande']);

                displaySuccess("Paiement Stripe réussi !", "Merci pour votre commande. Vous recevrez un email de confirmation sous peu.");
                exit;
            }
        }
    } catch (Exception $e) {
        displayError("Erreur lors de la validation du paiement Stripe : " . htmlspecialchars($e->getMessage()));
        exit;
    }
}

// Si aucune validation n'a été effectuée
displayError("Impossible de valider le paiement.");
exit;

/**
 * Fonction pour afficher un message de succès
 */
function displaySuccess($title, $details) {
    echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Succès du paiement</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="text-center">
            <h1 class="text-success">$title</h1>
            <p class="lead">$details</p>
            <a href="index.php" class="btn btn-primary">Retour à la boutique</a>
        </div>
    </div>
</body>
</html>
HTML;
}

/**
 * Fonction pour afficher un message d'erreur
 */
function displayError($message) {
    echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur de paiement</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="text-center">
            <h1 class="text-danger">Erreur de paiement</h1>
            <p class="lead">$message</p>
            <a href="panier.php" class="btn btn-warning">Retour au panier</a>
        </div>
    </div>
</body>
</html>
HTML;
}
?>
