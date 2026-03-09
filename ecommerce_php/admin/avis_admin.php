<?php
// Inclusion de la connexion à la base de données
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}
require_once '../include/database.php';

try {
    // Requête pour récupérer les avis avec les détails du produit, utilisateur, et état signalé
    $stmt = $pdo->query('
        SELECT avis.*, 
               produit.libelle AS produit, 
               utilisateur.nom AS utilisateur 
        FROM avis 
        JOIN produit ON avis.id_produit = produit.id 
        JOIN utilisateur ON avis.id_utilisateur = utilisateur.id 
        ORDER BY avis.date_creation DESC
    ');
    $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erreur lors de la récupération des avis : ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include '../include/head.php'; ?>
    <title>Modération des Avis</title>
    <style>
        .badge-signaled {
            background-color: red;
            color: white;
            padding: 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
<?php include '../include/nav.php'; ?>

<div class="container py-4">
    <h3 class="mb-4">Modération des avis</h3>

    <?php if (empty($avis)): ?>
        <div class="alert alert-info">
            Aucun avis à modérer pour le moment.
        </div>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th>Produit</th>
                    <th>Utilisateur</th>
                    <th>Note</th>
                    <th>Commentaire</th>
                    <th>Date</th>
                    <th>Signalé</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($avis as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['produit']) ?></td>
                        <td><?= htmlspecialchars($a['utilisateur']) ?></td>
                        <td><?= htmlspecialchars($a['note']) ?>/5</td>
                        <td><?= htmlspecialchars($a['commentaire']) ?></td>
                        <td><?= htmlspecialchars($a['date_creation']) ?></td>
                        <td>
                            <?= isset($a['signale']) && $a['signale'] ? '<span class="badge-signaled">Signalé</span>' : 'Non' ?>
                        </td>
                        <td>
                            <a href="supprimer_avis.php?id=<?= htmlspecialchars($a['id']) ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?');">
                                Supprimer
                            </a>
                            <?php if (isset($a['signale']) && $a['signale']): ?>
                                <a href="retirer_signalement.php?id=<?= htmlspecialchars($a['id']) ?>" 
                                   class="btn btn-warning btn-sm">
                                    Retirer le signalement
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
