<?php
require_once 'include/database.php';
require_once 'include/utils.php';
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}
requireAdmin();

$categories = $pdo->query('SELECT * FROM categorie ORDER BY date_creation DESC')->fetchAll(PDO::FETCH_ASSOC);

$flash = getFlashMessage();
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <title>Liste des catégories</title>
</head>
<body>
<?php include 'include/nav.php'; ?>

<div class="container py-4">
    <?php if ($flash): ?>
        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>

    <h2>Liste des catégories</h2>
    <a href="ajouter_categorie.php" class="btn btn-primary mb-3">Ajouter une catégorie</a>

    <?php if (empty($categories)): ?>
        <div class="alert alert-info" role="alert">
            Aucune catégorie n'a été trouvée.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Libellé</th>
                    <th>Description</th>
                    <th>Icône</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $categorie): ?>
                    <tr>
                        <td><?= htmlspecialchars($categorie['id']) ?></td>
                        <td><?= htmlspecialchars($categorie['libelle']) ?></td>
                        <td><?= htmlspecialchars($categorie['description']) ?></td>
                        <td><i class="fa <?= htmlspecialchars($categorie['icone']) ?>"></i></td>
                        <td><?= htmlspecialchars($categorie['date_creation']) ?></td>
                        <td>
                            <a href="modifier_categorie.php?id=<?= htmlspecialchars($categorie['id']) ?>" class="btn btn-primary btn-sm">Modifier</a>
                            <a href="supprimer_categorie.php?id=<?= htmlspecialchars($categorie['id']) ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Voulez-vous vraiment supprimer cette catégorie ?');">
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
