<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}


if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['statut'] !== 'admin') {
    header('Location: connexion.php');
    exit;
}

require_once 'include/database.php';

$produits = $pdo->query('
    SELECT produit.*, categorie.libelle AS categorie_libelle 
    FROM produit 
    INNER JOIN categorie ON produit.id_categorie = categorie.id
    ORDER BY produit.date_creation DESC
')->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <title>Liste des produits</title>
</head>
<body>
<?php include 'include/nav.php'; ?>

<div class="container py-4">
    <h2>Liste des produits</h2>
    <a href="ajouter_produit.php" class="btn btn-primary mb-3">Ajouter un produit</a>

    <?php if (empty($produits)): ?>
        <div class="alert alert-info">Aucun produit trouvé.</div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Libellé</th>
                    <th>Prix</th>
                    <th>Réduction (%)</th>
                    <th>Catégorie</th>
                    <th>Date de création</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produits as $produit): ?>
                    <tr>
                        <td><?= htmlspecialchars($produit['id']) ?></td>
                        <td><?= htmlspecialchars($produit['libelle']) ?></td>
                        <td><?= number_format($produit['prix'], 2) ?> $</td>
                        <td><?= $produit['discount'] ?>%</td>
                        <td><?= htmlspecialchars($produit['categorie_libelle']) ?></td>
                        <td><?= htmlspecialchars($produit['date_creation']) ?></td>
                        <td>
                            <img src="upload/produit/<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['libelle']) ?>" class="img-fluid" width="80">
                        </td>
                        <td>
                            <a href="modifier_produit.php?id=<?= htmlspecialchars($produit['id']) ?>" class="btn btn-primary btn-sm">Modifier</a>
                            <a href="supprimer_produit.php?id=<?= htmlspecialchars($produit['id']) ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Voulez-vous vraiment supprimer le produit <?= htmlspecialchars($produit['libelle']) ?> ?');">
                                Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
