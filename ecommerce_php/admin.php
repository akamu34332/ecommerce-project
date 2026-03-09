<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");


if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}

if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit;
}

if ($_SESSION['utilisateur']['statut'] !== 'admin') {
    header('Location: ./front/index.php');
    exit;
}

require_once 'include/database.php';
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <title>Admin | Tableau de bord</title>
</head>
<body>
<?php include 'include/nav.php'; ?>

<div class="container py-4">
    <h2>Tableau de bord administrateur</h2>
    <p>Bienvenue, <strong><?= htmlspecialchars($_SESSION['utilisateur']['nom']) ?></strong> !</p>

    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gérer les catégories</h5>
                    <p class="card-text">Ajoutez, modifiez ou supprimez les catégories de produits.</p>
                    <a href="categories.php" class="btn btn-primary">Voir les catégories</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gérer les produits</h5>
                    <p class="card-text">Ajoutez, modifiez ou supprimez les produits.</p>
                    <a href="produits.php" class="btn btn-primary">Voir les produits</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gérer les utilisateurs</h5>
                    <p class="card-text">Consultez et gérez les utilisateurs de la plateforme.</p>
                    <a href="liste_utilisateurs.php" class="btn btn-primary">Voir les utilisateurs</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gérer les commandes</h5>
                    <p class="card-text">Consultez et gérez les commandes des clients.</p>
                    <a href="commandes.php" class="btn btn-primary">Voir les commandes</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
