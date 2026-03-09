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

// Vérification si le panier est valide
if (empty($panier) || $total <= 0) {
    header('Location: paiement.php?error=invalid_cart');
    exit;
}

try {
    // Liste des articles du panier
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
                    'unit_amount' => round($prix * 100), // Stripe utilise des centimes
                ],
                'quantity' => $quantite,
            ];
        }
    }

    // Création de la session Stripe Checkout
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => 'http://localhost/dashboard/ecommerce_php/front/success.php?payment_method=stripe',
        'cancel_url' => 'http://localhost/dashboard/ecommerce_php/front/cancel.php',
    ]);

    // Redirection vers Stripe Checkout
    header('Location: ' . $session->url);
    exit;
} catch (\Stripe\Exception\ApiErrorException $e) {
    echo '<h3>Erreur Stripe :</h3>' . htmlspecialchars($e->getMessage());
    exit;
} catch (Exception $e) {
    die('<h3>Erreur :</h3> ' . htmlspecialchars($e->getMessage()));
}
?>
