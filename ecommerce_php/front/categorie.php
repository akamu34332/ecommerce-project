<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}
require_once '../include/database.php';

$id = $_GET['id'] ?? null;
if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    die('Catégorie invalide.');
}

$sqlCategorie = $pdo->prepare("SELECT * FROM categorie WHERE id = ?");
$sqlCategorie->execute([$id]);
$categorie = $sqlCategorie->fetch(PDO::FETCH_ASSOC);

if (!$categorie) {
    die('Catégorie introuvable.');
}

$sqlProduits = $pdo->prepare("SELECT * FROM produit WHERE id_categorie = ?");
$sqlProduits->execute([$id]);
$produits = $sqlProduits->fetchAll(PDO::FETCH_OBJ);
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include '../include/head.php'; ?>
    <title>Catégorie | <?= htmlspecialchars($categorie['libelle']) ?></title>
</head>
<body>
<?php include '../include/nav.php'; ?>

<div class="container py-2">
    <h4><?= htmlspecialchars($categorie['libelle']) ?> <span class="fa <?= htmlspecialchars($categorie['icone']) ?>"></span></h4>
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="row">
                    <?php require '../include/front/product/afficher_product.php'; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
