<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}

require_once '../include/database.php';
require_once '../include/paypal_init.php'; // Assurez-vous que ce fichier initialise l'objet $paypal correctement

$idUtilisateur = $_SESSION['utilisateur']['id'] ?? null;
$panier = $_SESSION['panier'][$idUtilisateur] ?? [];
$total = $_SESSION['total_panier'] ?? 0;

// Vérification si le panier est valide
if (empty($panier) || $total <= 0) {
    header('Location: paiement.php?error=invalid_cart');
    exit;
}

try {
    // Création de l'objet Payer pour PayPal
    $payer = new PayPal\Api\Payer();
    $payer->setPaymentMethod("paypal");

    // Création de la liste des articles
    $items = [];
    foreach ($panier as $idProduit => $quantite) {
        $sqlProduit = $pdo->prepare('SELECT * FROM produit WHERE id = ?');
        $sqlProduit->execute([$idProduit]);
        $produit = $sqlProduit->fetch();

        if ($produit) {
            $prix = $produit['prix'] - ($produit['prix'] * $produit['discount'] / 100);
            $item = new PayPal\Api\Item();
            $item->setName($produit['libelle'])
                 ->setCurrency('USD')
                 ->setQuantity($quantite)
                 ->setPrice(number_format($prix, 2, '.', ''));
            $items[] = $item;
        }
    }

    $itemList = new PayPal\Api\ItemList();
    $itemList->setItems($items);

    // Définir le montant total
    $amount = new PayPal\Api\Amount();
    $amount->setCurrency("USD")->setTotal(number_format($total, 2, '.', ''));

    // Création de la transaction
    $transaction = new PayPal\Api\Transaction();
    $transaction->setAmount($amount)
                ->setItemList($itemList)
                ->setDescription("Paiement commande - E-commerce");

    // Définir les URLs de redirection
    $redirectUrls = new PayPal\Api\RedirectUrls();
    $redirectUrls->setReturnUrl('http://localhost/dashboard/ecommerce_php/success.php') // URL après succès
                 ->setCancelUrl('http://localhost/dashboard/ecommerce_php/cancel.php'); // URL après annulation

    // Création de l'objet Payment
    $payment = new PayPal\Api\Payment();
    $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions([$transaction]);

    // Vérifiez que $paypal est bien initialisé dans paypal_init.php
    $payment->create($paypal);

    // Redirection vers PayPal pour l'approbation
    header('Location: ' . $payment->getApprovalLink());
    exit;
} catch (PayPal\Exception\PayPalConnectionException $e) {
    echo '<h3>Erreur PayPal :</h3>' . $e->getData();
    exit;
} catch (Exception $e) {
    die('<h3>Erreur PayPal :</h3> ' . $e->getMessage());
}
?>
