<?php

// Initialisation de la session et configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}

// Vérification de l'authentification de l'utilisateur
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['statut'] !== 'admin') {
    header('Location: connexion.php');
    exit;
}

// Inclusion des fichiers nécessaires
require_once 'include/database.php';

// Récupération des commandes
try {
    $stmt = $pdo->query('SELECT commande.*, utilisateur.email AS email, utilisateur.nom AS nom_client
                          FROM commande 
                          INNER JOIN utilisateur ON commande.id_client = utilisateur.id 
                          ORDER BY commande.date_creation DESC');
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erreur lors de la récupération des commandes : ' . $e->getMessage());
}
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <title>Gestion des Commandes</title>
</head>
<body>
<?php include 'include/nav.php'; ?>

<div class="container py-4">
    <h2 class="mb-4">Liste des Commandes</h2>

    <?php if (empty($commandes)): ?>
        <div class="alert alert-info" role="alert">
            Aucune commande n'a été trouvée.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Date de création</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td><?= htmlspecialchars($commande['id']) ?></td>
                        <td><?= htmlspecialchars($commande['nom_client']) ?></td>
                        <td><?= htmlspecialchars($commande['email']) ?></td>
                        <td>$<?= number_format($commande['total'], 2) ?></td>
                        <td><?= htmlspecialchars($commande['date_creation']) ?></td>
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
                        <td>
                            <a href="commande.php?id=<?= htmlspecialchars($commande['id']) ?>" 
                               class="btn btn-primary btn-sm">Détails</a>
                            <a href="valider_commande.php?id=<?= htmlspecialchars($commande['id']) ?>&etat=<?= $commande['valide'] ? 0 : 1 ?>" 
                               class="btn btn-<?= $commande['valide'] ? 'danger' : 'success' ?> btn-sm">
                                <?= $commande['valide'] ? 'Annuler' : 'Valider' ?>
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
