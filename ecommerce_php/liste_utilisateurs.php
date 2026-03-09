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

$utilisateurs = $pdo->query('SELECT * FROM utilisateur ORDER BY date_creation DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <title>Liste des utilisateurs</title>
</head>
<body>
<?php include 'include/nav.php'; ?>

<div class="container py-4">
    <h2>Liste des utilisateurs</h2>

    <?php if (empty($utilisateurs)): ?>
        <div class="alert alert-info">Aucun utilisateur trouvé.</div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Téléphone</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $utilisateur): ?>
                    <tr>
                        <td><?= htmlspecialchars($utilisateur['id']) ?></td>
                        <td><?= htmlspecialchars($utilisateur['nom']) ?></td>
                        <td><?= htmlspecialchars($utilisateur['prenom']) ?></td>
                        <td><?= htmlspecialchars($utilisateur['email']) ?></td>
                        <td><?= htmlspecialchars($utilisateur['statut']) ?></td>
                        <td><?= htmlspecialchars($utilisateur['telephone'] ?? 'Non fourni') ?></td>
                        <td><?= htmlspecialchars($utilisateur['date_creation']) ?></td>
                        <td>
                            <a href="modifier_utilisateur.php?id=<?= htmlspecialchars($utilisateur['id']) ?>" class="btn btn-primary btn-sm">Modifier</a>
                            <a href="supprimer_utilisateur.php?id=<?= htmlspecialchars($utilisateur['id']) ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                Supprimer
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
