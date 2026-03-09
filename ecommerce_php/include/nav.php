<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}

define('BASE_URL', '/dashboard/ecommerce_php/');

$connecte = $_SESSION['utilisateur'] ?? false;
$isAdmin = $connecte && $_SESSION['utilisateur']['statut'] === 'admin';
$currentPage = basename($_SERVER['PHP_SELF']);
$productCount = $connecte && !$isAdmin ? count($_SESSION['panier'][$_SESSION['utilisateur']['id']] ?? []) : 0;

$nomUtilisateur = htmlspecialchars($_SESSION['utilisateur']['nom'] ?? '');
$statutUtilisateur = htmlspecialchars($_SESSION['utilisateur']['statut'] ?? '');
$photoProfil = $connecte && !empty($_SESSION['utilisateur']['photo_profil']) 
    ? BASE_URL . htmlspecialchars($_SESSION['utilisateur']['photo_profil']) 
    : null;
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand <?= $currentPage === 'index.php' ? 'active' : '' ?>" 
           href="<?= $isAdmin ? BASE_URL . 'admin.php' : BASE_URL . 'index.php' ?>">
           <img src="<?= BASE_URL . 'assets/logo.webp' ?>" alt="Logo E-Kerie" style="height: 40px;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>" 
                       href="<?= $isAdmin ? BASE_URL . 'index.php' : BASE_URL . 'front/index.php' ?>">
                        <i class="fa fa-home"></i> Accueil
                    </a>
                </li>

                <?php if ($connecte): ?>
                    <?php if ($isAdmin): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= in_array($currentPage, ['categories.php', 'produits.php', 'liste_utilisateurs.php', 'commandes.php']) ? 'active' : '' ?>" 
                               href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-list"></i> Gestion
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item <?= $currentPage === 'categories.php' ? 'active' : '' ?>" href="<?= BASE_URL . 'categories.php' ?>"><i class="fa fa-box"></i> Catégories</a></li>
                                <li><a class="dropdown-item <?= $currentPage === 'produits.php' ? 'active' : '' ?>" href="<?= BASE_URL . 'produits.php' ?>"><i class="fa fa-tag"></i> Produits</a></li>
                                <li><a class="dropdown-item <?= $currentPage === 'liste_utilisateurs.php' ? 'active' : '' ?>" href="<?= BASE_URL . 'liste_utilisateurs.php' ?>"><i class="fa fa-users"></i> Utilisateurs</a></li>
                                <li><a class="dropdown-item <?= $currentPage === 'commandes.php' ? 'active' : '' ?>" href="<?= BASE_URL . 'commandes.php' ?>"><i class="fa fa-shopping-cart"></i> Commandes</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL . 'admin/avis_admin.php' ?>">
                                <i class="fa fa-comments"></i> Gérer les avis
                            </a>
                        </li>

                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentPage === 'panier.php' ? 'active' : '' ?>" href="<?= BASE_URL . 'front/panier.php' ?>">
                                <i class="fa fa-shopping-cart"></i> Panier (<?= $productCount ?>)
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <div class="text-end d-flex align-items-center">
                <?php if ($connecte): ?>
                    <div>
                        <a href="<?= BASE_URL . 'profil.php' ?>" class="me-3 text-decoration-none">
                            <?php if (!empty($utilisateur['photo_profil'])): ?>
                                <img src="<?= htmlspecialchars(BASE_URL . $_SESSION['utilisateur']['photo_profil'] . '?' . time()) ?>" 
                                    alt="Photo de profil" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            <?php else: ?>
                                <i class="fa fa-user-circle fa-2x text-secondary"></i>
                            <?php endif; ?>

                            <strong><?= $nomUtilisateur ?></strong> (<?= ucfirst($statutUtilisateur) ?>)
                        </a>
                        <a href="<?= BASE_URL . 'deconnexion.php' ?>" class="btn btn-outline-danger btn-sm">
                            <i class="fa fa-sign-out-alt me-1"></i> Déconnexion
                        </a>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL . 'ajouter_utilisateur.php' ?>" class="btn btn-outline-secondary btn-sm me-2">
                        <i class="fa fa-user-plus me-1"></i> Inscription
                    </a>
                    <a href="<?= BASE_URL . 'connexion.php' ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-sign-in-alt me-1"></i> Connexion
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
