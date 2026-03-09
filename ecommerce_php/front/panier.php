<?php
// Initialisation de la session et de la configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}
require_once '../include/database.php';

// Récupération des données de l'utilisateur et du panier
$idUtilisateur = $_SESSION['utilisateur']['id'] ?? 0;
$panier = $_SESSION['panier'][$idUtilisateur] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['vider'])) {
        // Vider le panier
        $_SESSION['panier'][$idUtilisateur] = [];
        header('Location: panier.php');
        exit;
    }

    if (isset($_POST['valider'])) {
        // Validation du panier
        $total = 0;
        foreach ($panier as $idProduit => $quantite) {
            $sqlProduit = $pdo->prepare('SELECT * FROM produit WHERE id = ?');
            $sqlProduit->execute([$idProduit]);
            $produit = $sqlProduit->fetch();

            if ($produit) {
                $prix = $produit['prix'] - ($produit['prix'] * $produit['discount'] / 100);
                $total += $prix * $quantite;
            }
        }

        if ($total > 0) {
            $_SESSION['total_panier'] = $total;
            header('Location: paiement.php');
            exit;
        } else {
            header('Location: panier.php?error=invalid_total');
            exit;
        }
    }

    if (isset($_POST['modifier_quantite'])) {
        // Modification de la quantité d'un produit
        $idProduit = $_POST['idProduit'];
        $nouvelleQuantite = max(1, (int)$_POST['quantite']);
        $_SESSION['panier'][$idUtilisateur][$idProduit] = $nouvelleQuantite;
        header('Location: panier.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include '../include/head.php'; ?>
    <title>Votre Panier</title>
</head>
<body>
<?php include '../include/nav.php'; ?>

<div class="container py-4">
    <h1 class="text-center mb-4">Votre Panier</h1>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_total'): ?>
        <div class="alert alert-danger">
            Une erreur est survenue lors du calcul du total. Veuillez réessayer.
        </div>
    <?php elseif (empty($panier)): ?>
        <div class="alert alert-warning text-center">
            Votre panier est vide.
            <a href="index.php" class="btn btn-success btn-sm">Acheter des produits</a>
        </div>
    <?php else: ?>
        <form method="post" action="panier.php">
            <table class="table table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Prix Unitaire</th>
                        <th>Prix Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($panier as $idProduit => $quantite):
                        $sqlProduit = $pdo->prepare('SELECT * FROM produit WHERE id = ?');
                        $sqlProduit->execute([$idProduit]);
                        $produit = $sqlProduit->fetch();

                        if ($produit):
                            $prix = $produit['prix'] - ($produit['prix'] * $produit['discount'] / 100);
                            $totalProduit = $prix * $quantite;
                            $total += $totalProduit;
                    ?>
                    <tr>
                        <td><?= $produit['id'] ?></td>
                        <td><?= htmlspecialchars($produit['libelle']) ?></td>
                        <td>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="idProduit" value="<?= $idProduit ?>">
                                <input type="number" name="quantite" value="<?= $quantite ?>" min="1" class="form-control w-50 d-inline">
                                <button type="submit" name="modifier_quantite" class="btn btn-sm btn-primary">
                                    <i class="fa fa-sync"></i>
                                </button>
                            </form>
                        </td>
                        <td>$<?= number_format($prix, 2) ?></td>
                        <td>$<?= number_format($totalProduit, 2) ?></td>
                        <td>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="idProduit" value="<?= $idProduit ?>">
                                <button type="submit" name="supprimer" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i> Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endif; endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total :</strong></td>
                        <td colspan="2">$<?= number_format($total, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
            <div class="text-end mt-3">
                <button type="submit" name="valider" class="btn btn-primary me-2">
                    <i class="fa fa-check"></i> Valider la commande
                </button>
                <button type="submit" name="vider" class="btn btn-danger">
                    <i class="fa fa-trash"></i> Vider le panier
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
