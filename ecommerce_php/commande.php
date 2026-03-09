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

$idCommande = $_GET['id'] ?? null;
if (!$idCommande || !filter_var($idCommande, FILTER_VALIDATE_INT)) {
    die('Commande invalide.');
}

// Récupération des informations de la commande
$sqlCommande = $pdo->prepare('SELECT commande.*, utilisateur.email AS email 
                              FROM commande 
                              INNER JOIN utilisateur ON commande.id_client = utilisateur.id 
                              WHERE commande.id = ?');
$sqlCommande->execute([$idCommande]);
$commande = $sqlCommande->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    die('Commande introuvable.');
}

// Récupération des lignes de commande
$sqlLignes = $pdo->prepare('SELECT ligne_commande.*, produit.libelle AS produit, produit.image AS image 
                            FROM ligne_commande 
                            INNER JOIN produit ON ligne_commande.id_produit = produit.id 
                            WHERE ligne_commande.id_commande = ?');
$sqlLignes->execute([$idCommande]);
$lignesCommande = $sqlLignes->fetchAll(PDO::FETCH_ASSOC);

// Vérification des avis déjà laissés
$avisDejaFait = [];
if (!empty($lignesCommande)) {
    $idsProduits = array_column($lignesCommande, 'id_produit');
    $placeholders = implode(',', array_fill(0, count($idsProduits), '?'));

    $sqlAvis = $pdo->prepare("SELECT id_produit FROM avis WHERE id_utilisateur = ? AND id_produit IN ($placeholders)");
    $sqlAvis->execute(array_merge([$_SESSION['utilisateur']['id']], $idsProduits));
    $avisDejaFait = $sqlAvis->fetchAll(PDO::FETCH_COLUMN);
}
?>

<!doctype html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <title>Détails de la commande</title>
</head>
<body>
<?php include 'include/nav.php'; ?>

<div class="container py-4">
    <h2 class="mb-4">Détails de la commande #<?= htmlspecialchars($commande['id']) ?></h2>

    <!-- Informations sur la commande -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Informations sur la commande</h5>
            <p><strong>Client :</strong> <?= htmlspecialchars($commande['email']) ?></p>
            <p><strong>Total :</strong> $<?= number_format($commande['total'], 2) ?></p>
            <p><strong>Date :</strong> <?= htmlspecialchars($commande['date_creation']) ?></p>
            <p><strong>Statut :</strong> <?= $commande['valide'] ? '<span class="badge bg-success">Validée</span>' : '<span class="badge bg-warning">En attente</span>' ?></p>
            <a href="valider_commande.php?id=<?= htmlspecialchars($commande['id']) ?>&etat=<?= $commande['valide'] ? 0 : 1 ?>" 
               class="btn btn-<?= $commande['valide'] ? 'danger' : 'success' ?>">
                <?= $commande['valide'] ? 'Annuler la commande' : 'Valider la commande' ?>
            </a>
        </div>
    </div>

    <!-- Produits commandés -->
    <h5 class="mb-4">Produits commandés</h5>
    <?php if (empty($lignesCommande)): ?>
        <div class="alert alert-warning">Aucun produit dans cette commande.</div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead class="table-primary">
                <tr>
                    <th>#ID</th>
                    <th>Produit</th>
                    <th>Image</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lignesCommande as $ligne): ?>
                    <tr>
                        <td><?= htmlspecialchars($ligne['id']) ?></td>
                        <td><?= htmlspecialchars($ligne['produit']) ?></td>
                        <td>
                            <img src="../upload/produit/<?= htmlspecialchars($ligne['image']) ?>" alt="<?= htmlspecialchars($ligne['produit']) ?>" class="img-fluid" width="80">
                        </td>
                        <td>$<?= number_format($ligne['prix'], 2) ?></td>
                        <td><?= htmlspecialchars($ligne['quantite']) ?></td>
                        <td>$<?= number_format($ligne['total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Section Avis -->
    <?php if (!$commande['valide']): ?>
        <h5 class="mt-5">Ajouter un avis</h5>
        <?php foreach ($lignesCommande as $ligne): ?>
            <?php if (!in_array($ligne['id_produit'], $avisDejaFait)): ?>
                <form method="post" action="ajouter_avis.php" class="mb-4">
                    <input type="hidden" name="id_produit" value="<?= htmlspecialchars($ligne['id_produit']) ?>">
                    <div class="mb-3">
                        <label for="note_<?= $ligne['id_produit'] ?>">Note (1 à 5) :</label>
                        <select name="note" id="note_<?= $ligne['id_produit'] ?>" class="form-select" required>
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Bon</option>
                            <option value="3">3 - Moyen</option>
                            <option value="2">2 - Mauvais</option>
                            <option value="1">1 - Très Mauvais</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="commentaire_<?= $ligne['id_produit'] ?>">Commentaire :</label>
                        <textarea name="commentaire" id="commentaire_<?= $ligne['id_produit'] ?>" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Soumettre l'avis</button>
                </form>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
