<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}
require_once '../include/database.php';

$id = $_GET['id'] ?? null;
if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    die('Produit invalide.');
}

// Récupération des informations du produit
$sqlProduit = $pdo->prepare('SELECT * FROM produit WHERE id = ?');
$sqlProduit->execute([$id]);
$produit = $sqlProduit->fetch(PDO::FETCH_ASSOC);
if (!$produit) {
    die('Produit introuvable.');
}

// Récupération des statistiques d'avis
$sqlStatsAvis = $pdo->prepare('SELECT COUNT(*) AS nombre_avis, COALESCE(AVG(note), 0) AS moyenne_note FROM avis WHERE id_produit = ?');
$sqlStatsAvis->execute([$id]);
$statsAvis = $sqlStatsAvis->fetch(PDO::FETCH_ASSOC);

// Récupération des avis utilisateurs
$sqlAvis = $pdo->prepare('SELECT avis.*, utilisateur.nom AS auteur 
                          FROM avis 
                          JOIN utilisateur ON avis.id_utilisateur = utilisateur.id 
                          WHERE avis.id_produit = ? 
                          ORDER BY avis.date_creation DESC');
$sqlAvis->execute([$id]);
$listeAvis = $sqlAvis->fetchAll(PDO::FETCH_ASSOC);

$idUtilisateur = $_SESSION['utilisateur']['id'] ?? null;
$isLoggedIn = isset($_SESSION['utilisateur']);
?>

<!doctype html>
<html lang="fr">
<head>
    <?php include '../include/head.php'; ?>
    <title>Produit | <?= htmlspecialchars($produit['libelle']) ?></title>
</head>
<body>
<?php include '../include/nav.php'; ?>

<div class="container py-4">
    <h1 class="text-center mb-4">Détails du produit</h1>

    <div class="row">
        <!-- Image du produit -->
        <div class="col-md-6">
            <img src="../upload/produit/<?= htmlspecialchars($produit['image']) ?>" 
                 class="img-fluid product-image rounded" 
                 alt="<?= htmlspecialchars($produit['libelle']) ?>">
        </div>

        <!-- Détails du produit -->
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-primary mb-3"><?= htmlspecialchars($produit['libelle']) ?></h3>
                    <p class="mb-4"><strong>Description :</strong> <?= nl2br(htmlspecialchars($produit['description'])) ?></p>

                    <p><strong>Prix :</strong>
                        <?php if ($produit['discount'] > 0): ?>
                            <span class="price-original text-muted text-decoration-line-through">
                                $<?= number_format($produit['prix'], 2) ?>
                            </span>
                            <span class="price-discount text-danger">
                                $<?= number_format($produit['prix'] - ($produit['prix'] * $produit['discount'] / 100), 2) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-success">$<?= number_format($produit['prix'], 2) ?></span>
                        <?php endif; ?>
                    </p>

                    <p><strong>Note moyenne :</strong> <?= number_format($statsAvis['moyenne_note'], 1) ?>/5 (<?= $statsAvis['nombre_avis'] ?> avis)</p>

                    <?php if ($isLoggedIn): ?>
                        <form method="post" action="ajouter_panier.php">
                            <div class="mb-3">
                                <label for="quantite" class="form-label">Quantité :</label>
                                <input type="number" name="qty" id="quantite" class="form-control w-50" value="1" min="1" required>
                            </div>
                            <input type="hidden" name="id" value="<?= htmlspecialchars($produit['id']) ?>">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-cart-plus"></i> Ajouter au panier
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning mt-3">
                            Vous devez <a href="../connexion.php" class="text-decoration-none">vous connecter</a> pour ajouter ce produit à votre panier.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <!-- Section Avis -->
    <div class="product-reviews mt-4">
        <h3>Avis des utilisateurs</h3>
        <p><strong>Note moyenne :</strong> <?= number_format($statsAvis['moyenne_note'], 1) ?>/5 (<?= $statsAvis['nombre_avis'] ?> avis)</p>

        <?php if (empty($listeAvis)): ?>
            <div class="alert alert-info">Aucun avis pour ce produit. Soyez le premier à laisser un avis !</div>
        <?php else: ?>
            <?php foreach ($listeAvis as $avis): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Par <?= htmlspecialchars($avis['auteur']) ?> <small class="text-muted">le <?= htmlspecialchars($avis['date_creation']) ?></small></h5>
                        <p class="card-text">"<?= nl2br(htmlspecialchars($avis['commentaire'])) ?>"</p>
                        <p class="mb-0"><strong>Note :</strong> <?= htmlspecialchars($avis['note']) ?>/5</p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($isLoggedIn): ?>
            <form method="post" action="../front/ajouter_avis.php" class="mt-4">
                <h4>Laissez votre avis</h4>
                <div class="mb-3">
                    <label for="note" class="form-label">Note (1 à 5)</label>
                    <select id="note" name="note" class="form-select" required>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Bon</option>
                        <option value="3">3 - Moyen</option>
                        <option value="2">2 - Médiocre</option>
                        <option value="1">1 - Mauvais</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="commentaire" class="form-label">Commentaire</label>
                    <textarea id="commentaire" name="commentaire" class="form-control" rows="4" required></textarea>
                </div>
                <input type="hidden" name="id_produit" value="<?= htmlspecialchars($id) ?>">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-comment"></i> Soumettre
                </button>
            </form>
        <?php else: ?>
            <div class="alert alert-warning mt-3">
                Vous devez <a href="../connexion.php" class="text-decoration-none">vous connecter</a> pour laisser un avis.
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
