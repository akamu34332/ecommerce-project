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

$id = $_GET['id'] ?? null;

if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    die('ID de catégorie invalide.');
}

$sql = $pdo->prepare('SELECT * FROM categorie WHERE id = ?');
$sql->execute([$id]);
$categorie = $sql->fetch(PDO::FETCH_ASSOC);

if (!$categorie) {
    die('Catégorie introuvable.');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $sql = $pdo->prepare('UPDATE categorie 
                              SET libelle = ?, description = ?, icone = ? 
                              WHERE id = ?');
        $sql->execute([$libelle, $description, $icone, $id]);

        header('Location: categories.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <title>Modifier une catégorie</title>
</head>
<body>
<?php include 'include/nav.php'; ?>

<div class="container py-4">
    <h2>Modifier la catégorie</h2>

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
        <div class="mb-3">
            <label for="libelle" class="form-label">Libellé</label>
            <input type="text" id="libelle" name="libelle" class="form-control" value="<?= htmlspecialchars($categorie['libelle']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="4" required><?= htmlspecialchars($categorie['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="icone" class="form-label">Icône (class CSS)</label>
            <input type="text" id="icone" name="icone" class="form-control" value="<?= htmlspecialchars($categorie['icone']) ?>">
        </div>

        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="categories.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

</body>
</html>
