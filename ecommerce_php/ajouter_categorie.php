<?php
require_once 'include/database.php';
require_once 'include/utils.php';
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}
requireAdmin();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $errors[] = 'Jeton CSRF invalide.';
    }

    $libelle = trim($_POST['libelle'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icone = trim($_POST['icone'] ?? '');

    if (empty($libelle)) {
        $errors[] = 'Le libellé est obligatoire.';
    }
    if (empty($description)) {
        $errors[] = 'La description est obligatoire.';
    }

    if (empty($errors)) {
        $sql = 'INSERT INTO categorie (libelle, description, icone) VALUES (?, ?, ?)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$libelle, $description, $icone]);
        redirect('categories.php', 'Catégorie ajoutée avec succès.', 'success');
    }
}

$csrfToken = generateCsrfToken();
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <title>Ajouter une catégorie</title>
</head>
<body>
<?php include 'include/nav.php'; ?>

<div class="container py-4">
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <div class="mb-3">
            <label for="libelle" class="form-label">Libellé</label>
            <input type="text" id="libelle" name="libelle" class="form-control" value="<?= htmlspecialchars($libelle ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" required><?= htmlspecialchars($description ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="icone" class="form-label">Icône (classe FontAwesome, ex : "fa-solid fa-star")</label>
            <input type="text" id="icone" name="icone" class="form-control" value="<?= htmlspecialchars($icone ?? '') ?>">
        </div>

        <button type="submit" class="btn btn-primary">Ajouter</button>
        <a href="categories.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>
