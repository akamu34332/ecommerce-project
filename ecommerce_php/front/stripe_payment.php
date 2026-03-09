<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}

require_once '../include/database.php';
require_once '../include/stripe_init.php';

$idUtilisateur = $_SESSION['utilisateur']['id'] ?? null;
$panier = $_SESSION['panier'][$idUtilisateur] ?? [];
$total = $_SESSION['total_panier'] ?? 0;

if (empty($panier) || $total <= 0) {
    header('Location: paiement.php?error=invalid_cart');
    exit;
}

try {
    $lineItems = [];
    foreach ($panier as $idProduit => $quantite) {
        $sqlProduit = $pdo->prepare('SELECT * FROM produit WHERE id = ?');
        $sqlProduit->execute([$idProduit]);
        $produit = $sqlProduit->fetch();

        if ($produit) {
            $prix = $produit['prix'] - ($produit['prix'] * $produit['discount'] / 100);
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $produit['libelle'],
                    ],
                    'unit_amount' => $prix * 100,
                ],
                'quantity' => $quantite,
            ];
        }
    }

    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => 'http://votre_site.com/front/paiement_success.php?method=stripe',
        'cancel_url' => 'http://votre_site.com/front/paiement.php?error=cancel',
    ]);

    header('Location: ' . $session->url);
    exit;
} catch (Exception $e) {
    die('Erreur Stripe : ' . $e->getMessage());
}
?>
