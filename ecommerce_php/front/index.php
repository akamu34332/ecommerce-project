<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}
require_once '../include/database.php';

$categoryId = $_GET['id'] ?? null;
if ($categoryId && !filter_var($categoryId, FILTER_VALIDATE_INT)) {
    die('Catégorie invalide.');
}

$sqlCategories = $pdo->query("SELECT * FROM categorie ORDER BY date_creation DESC");
$categories = $sqlCategories->fetchAll(PDO::FETCH_OBJ);

if ($categoryId) {
    $sqlProduits = $pdo->prepare("SELECT * FROM produit WHERE id_categorie = ? ORDER BY date_creation DESC");
    $sqlProduits->execute([$categoryId]);
} else {
    $sqlProduits = $pdo->query("SELECT * FROM produit ORDER BY date_creation DESC");
}
$produits = $sqlProduits->fetchAll(PDO::FETCH_OBJ);
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include '../include/head.php'; ?>
    <title>Accueil</title>
</head>
<body>
<?php include '../include/nav.php'; ?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <ul class="list-group list-group-flush">
                <h4 class="mt-4"><i class="fa fa-list"></i> Catégories</h4>
                <li class="list-group-item <?= !$categoryId ? 'active bg-success text-white' : '' ?>">
                    <a href="./" class="text-decoration-none">
                        <i class="fa fa-border-all"></i> Tous les produits
                    </a>
                </li>
                <?php foreach ($categories as $categorie): ?>
                    <li class="list-group-item <?= $categoryId == $categorie->id ? 'active bg-success text-white' : '' ?>">
                        <a href="?id=<?= $categorie->id ?>" class="text-decoration-none">
                            <i class="fa <?= htmlspecialchars($categorie->icone) ?>"></i> <?= htmlspecialchars($categorie->libelle) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-9">
            <div class="row">
                <?php require '../include/front/product/afficher_product.php'; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
