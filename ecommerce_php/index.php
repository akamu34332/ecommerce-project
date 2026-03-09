<?php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}
require_once 'include/database.php';

$categories = $pdo->query('SELECT * FROM categorie ORDER BY libelle')->fetchAll(PDO::FETCH_OBJ);

$categoryId = $_GET['id'] ?? null;
if ($categoryId && filter_var($categoryId, FILTER_VALIDATE_INT)) {
    $stmt = $pdo->prepare('SELECT * FROM produit WHERE id_categorie = ? ORDER BY date_creation DESC');
    $stmt->execute([$categoryId]);
    $produits = $stmt->fetchAll(PDO::FETCH_OBJ);
} else {
    $produits = $pdo->query('SELECT * FROM produit ORDER BY date_creation DESC')->fetchAll(PDO::FETCH_OBJ);
}
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <title>Accueil</title>
</head>
<body>
<?php include 'include/nav.php'; ?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <ul class="list-group">
                <h4 class="mt-4"><i class="fa fa-list"></i> Catégories</h4>
                <li class="list-group-item <?= !$categoryId ? 'active bg-success text-white' : '' ?>">
                    <a href="index.php" class="text-decoration-none <?= !$categoryId ? 'text-white' : '' ?>">Tous les produits</a>
                </li>
                <?php foreach ($categories as $categorie): ?>
                    <li class="list-group-item <?= $categoryId == $categorie->id ? 'active bg-success text-white' : '' ?>">
                        <a href="index.php?id=<?= $categorie->id ?>" class="text-decoration-none <?= $categoryId == $categorie->id ? 'text-white' : '' ?>">
                            <i class="fa <?= htmlspecialchars($categorie->icone) ?>"></i> <?= htmlspecialchars($categorie->libelle) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="col-md-9">
            <div class="row">
                <?php if (empty($produits)): ?>
                    <div class="alert alert-info">Aucun produit trouvé.</div>
                <?php else: ?>
                    <?php foreach ($produits as $produit): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <?php if ($produit->discount > 0): ?>
                                    <span class="badge bg-warning text-dark position-absolute m-2" style="right: 0;">
                                        -<?= $produit->discount ?>%
                                    </span>
                                <?php endif; ?>
                                <img src="upload/produit/<?= htmlspecialchars($produit->image) ?>" class="card-img-top" alt="<?= htmlspecialchars($produit->libelle) ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($produit->libelle) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($produit->description) ?></p>
                                    <p>
                                        <strong>Prix : </strong>
                                        <?php if ($produit->discount > 0): ?>
                                            <span class="text-danger"><strike><?= number_format($produit->prix, 2) ?> $</strike></span>
                                            <?= number_format($produit->prix - ($produit->prix * $produit->discount / 100), 2) ?> $
                                        <?php else: ?>
                                            <?= number_format($produit->prix, 2) ?> $
                                        <?php endif; ?>
                                    </p>
                                    <a href="front/produit.php?id=<?= $produit->id ?>" class="btn btn-primary">Voir le produit</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
