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

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libelle = trim($_POST['libelle'] ?? '');
    $prix = $_POST['prix'] ?? 0;
    $discount = $_POST['discount'] ?? 0;
    $categorie = $_POST['categorie'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $date = date('Y-m-d');
    $imageName = '';

    if (empty($libelle)) {
        $errors[] = 'Le libellé est obligatoire.';
    }
    if (empty($prix) || !is_numeric($prix) || $prix <= 0) {
        $errors[] = 'Le prix doit être un nombre positif.';
    }
    if (empty($categorie) || !filter_var($categorie, FILTER_VALIDATE_INT)) {
        $errors[] = 'Une catégorie valide est obligatoire.';
    }

    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image'];
        $imageName = uniqid() . '-' . basename($image['name']);
        $targetPath = 'upload/produit/' . $imageName;

        if (!move_uploaded_file($image['tmp_name'], $targetPath)) {
            $errors[] = 'Le téléchargement de l\'image a échoué.';
        }
    } else {
        $imageName = 'default.png'; 
    }

    if (empty($errors)) {
        $sql = 'INSERT INTO produit (libelle, prix, discount, id_categorie, date_creation, description, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$libelle, $prix, $discount, $categorie, $date, $description, $imageName]);

        header('Location: produits.php');
        exit;
    }
}

$categories = $pdo->query('SELECT * FROM categorie ORDER BY libelle')->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <title>Ajouter un produit</title>
</head>
<body>
<?php include 'include/nav.php'; ?>

<div class="container py-4">
    <h2>Ajouter un produit</h2>
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
            <input type="text" id="libelle" name="libelle" class="form-control" value="<?= htmlspecialchars($libelle ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="prix" class="form-label">Prix</label>
            <input type="number" id="prix" name="prix" class="form-control" value="<?= htmlspecialchars($prix ?? '') ?>" step="0.01" min="0" required>
        </div>

        <div class="mb-3">
            <label for="discount" class="form-label">Remise (%)</label>
            <input type="number" id="discount" name="discount" class="form-control" value="<?= htmlspecialchars($discount ?? 0) ?>" step="1" min="0" max="100">
        </div>

        <div class="mb-3">
            <label for="categorie" class="form-label">Catégorie</label>
            <select id="categorie" name="categorie" class="form-control" required>
                <option value="">Sélectionnez une catégorie</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= isset($categorie) && $categorie == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="5"><?= htmlspecialchars($description ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" id="image" name="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Ajouter</button>
        <a href="produits.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

</body>
</html>
