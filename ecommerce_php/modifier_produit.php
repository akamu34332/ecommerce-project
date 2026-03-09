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
    die('ID de produit invalide.');
}

$sql = $pdo->prepare('SELECT * FROM produit WHERE id = ?');
$sql->execute([$id]);
$produit = $sql->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    die('Produit introuvable.');
}

$categories = $pdo->query('SELECT * FROM categorie ORDER BY libelle')->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libelle = trim($_POST['libelle'] ?? '');
    $prix = $_POST['prix'] ?? 0;
    $discount = $_POST['discount'] ?? 0;
    $categorie = $_POST['categorie'] ?? null;
    $description = trim($_POST['description'] ?? '');
    $filename = $produit['image']; 

    if (empty($libelle)) {
        $errors[] = 'Le libellé est obligatoire.';
    }
    if ($prix <= 0 || !filter_var($prix, FILTER_VALIDATE_FLOAT)) {
        $errors[] = 'Le prix doit être un nombre positif.';
    }
    if ($discount < 0 || $discount > 100 || !filter_var($discount, FILTER_VALIDATE_INT)) {
        $errors[] = 'La réduction doit être entre 0 et 100.';
    }
    if (empty($categorie) || !filter_var($categorie, FILTER_VALIDATE_INT)) {
        $errors[] = 'Une catégorie valide est obligatoire.';
    }
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $filename = uniqid() . '_' . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], 'upload/produit/' . $filename);
    }

    if (empty($errors)) {
        $sql = $pdo->prepare('UPDATE produit 
                              SET libelle = ?, prix = ?, discount = ?, id_categorie = ?, description = ?, image = ?
                              WHERE id = ?');
        $sql->execute([$libelle, $prix, $discount, $categorie, $description, $filename, $id]);

        header('Location: produits.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <title>Modifier un produit</title>
</head>
<body>
<?php include 'include/nav.php'; ?>

<div class="container py-4">
    <h2>Modifier le produit</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="libelle" class="form-label">Libellé</label>
            <input type="text" id="libelle" name="libelle" class="form-control" value="<?= htmlspecialchars($produit['libelle']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="prix" class="form-label">Prix</label>
            <input type="number" id="prix" name="prix" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($produit['prix']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="discount" class="form-label">Réduction (%)</label>
            <input type="number" id="discount" name="discount" class="form-control" min="0" max="100" value="<?= htmlspecialchars($produit['discount']) ?>">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="4"><?= htmlspecialchars($produit['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="categorie" class="form-label">Catégorie</label>
            <select id="categorie" name="categorie" class="form-control" required>
                <option value="">Choisissez une catégorie</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['id']) ?>" <?= $produit['id_categorie'] == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" id="image" name="image" class="form-control">
            <img src="upload/produit/<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['libelle']) ?>" class="img-fluid mt-2" width="150">
        </div>

        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="produits.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

</body>
</html>
