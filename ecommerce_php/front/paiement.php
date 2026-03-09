<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}

require_once '../include/database.php';

$idUtilisateur = $_SESSION['utilisateur']['id'] ?? null;
$panier = $_SESSION['panier'][$idUtilisateur] ?? [];
$total = 0;

// Vérifier si le panier est vide ou si le total est invalide
if (empty($panier)) {
    $error_message = 'Votre panier est vide ou le montant total est invalide.';
} else {
    foreach ($panier as $idProduit => $quantite) {
        $sqlProduit = $pdo->prepare('SELECT * FROM produit WHERE id = ?');
        $sqlProduit->execute([$idProduit]);
        $produit = $sqlProduit->fetch();

        if ($produit) {
            $prix = $produit['prix'] - ($produit['prix'] * $produit['discount'] / 100);
            $total += $prix * $quantite;
        }
    }
    if ($total <= 0) {
        $error_message = 'Le montant total calculé est invalide.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include '../include/head.php'; ?>
    <title>Paiement</title>
</head>
<body>
<?php include '../include/nav.php'; ?>

<div class="container mt-5">
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-warning text-center">
            <?= htmlspecialchars($error_message) ?>
            <a href="index.php" class="btn btn-success btn-sm">Retour à la boutique</a>
        </div>
    <?php else: ?>
        <h1 class="text-center mb-4">Paiement</h1>
        <div class="alert alert-info text-center">
            Montant total à payer : <strong>$<?= number_format($total, 2) ?></strong>
        </div>
        <form method="post" action="paiement_process.php">
            <h3>Choisissez votre méthode de paiement :</h3>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="stripe" name="payment_method" value="stripe" required>
                <label class="form-check-label" for="stripe">Stripe</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="paypal" name="payment_method" value="paypal" required>
                <label class="form-check-label" for="paypal">PayPal</label>
            </div>
            <button type="submit" class="btn btn-primary mt-4">Procéder au paiement</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
