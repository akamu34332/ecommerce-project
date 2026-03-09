<?php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}

if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../connexion.php');
    exit;
}

require_once '../include/database.php';

$idUtilisateur = $_SESSION['utilisateur']['id'];
$commandes = $pdo->prepare('
    SELECT commande.*, GROUP_CONCAT(produit.libelle SEPARATOR ", ") AS produits
    FROM commande
    INNER JOIN ligne_commande ON commande.id = ligne_commande.id_commande
    INNER JOIN produit ON ligne_commande.id_produit = produit.id
    WHERE commande.id_client = ?
    GROUP BY commande.id
    ORDER BY commande.date_creation DESC
');
$commandes->execute([$idUtilisateur]);
$commandes = $commandes->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include '../include/head.php'; ?>
    <title>Suivi des Commandes</title>
</head>
<body>
<?php include '../include/nav.php'; ?>

<div class="container py-4">
    <h2 class="mb-4">Suivi de vos Commandes</h2>

    <?php if (empty($commandes)): ?>
        <div class="alert alert-info">Vous n'avez passé aucune commande pour le moment.</div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Produits</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td><?= htmlspecialchars($commande['id']) ?></td>
                        <td><?= htmlspecialchars($commande['produits']) ?></td>
                        <td><?= number_format($commande['total'], 2) ?> $</td>
                        <td>
                            <?php
                            $statuts = [
                                'En attente' => 'warning',
                                'En cours' => 'info',
                                'Expédiée' => 'primary',
                                'Livrée' => 'success'
                            ];
                            ?>
                            <span class="badge bg-<?= $statuts[$commande['statut']] ?? 'secondary' ?>">
                                <?= htmlspecialchars($commande['statut']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($commande['date_creation']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
