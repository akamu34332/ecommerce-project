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
    die('ID utilisateur invalide.');
}

$sql = $pdo->prepare('SELECT * FROM utilisateur WHERE id = ?');
$sql->execute([$id]);
$utilisateur = $sql->fetch(PDO::FETCH_ASSOC);

if (!$utilisateur) {
    die('Utilisateur introuvable.');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? null;
    $statut = $_POST['statut'] ?? '';
    $telephone = trim($_POST['telephone'] ?? '');

    if (empty($nom)) {
        $errors[] = 'Le nom est obligatoire.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Un email valide est obligatoire.';
    }
    if (empty($statut) || !in_array($statut, ['user', 'admin'])) {
        $errors[] = 'Le statut est invalide.';
    }

    if (empty($errors)) {
        $sql = 'UPDATE utilisateur 
                SET nom = ?, prenom = ?, email = ?, statut = ?, telephone = ?';
        $params = [$nom, $prenom, $email, $statut, $telephone];

        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ', password = ?';
            $params[] = $hashedPassword;
        }

        $sql .= ' WHERE id = ?';
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        header('Location: liste_utilisateurs.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <title>Modifier un utilisateur</title>
</head>
<body>
<?php include 'include/nav.php'; ?>

<div class="container py-4">
    <h2>Modifier l'utilisateur</h2>

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
            <label for="nom" class="form-label">Nom</label>
            <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($utilisateur['nom']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($utilisateur['prenom']) ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($utilisateur['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe (laisser vide pour ne pas modifier)</label>
            <input type="password" id="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label for="statut" class="form-label">Statut</label>
            <select id="statut" name="statut" class="form-select" required>
                <option value="user" <?= $utilisateur['statut'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                <option value="admin" <?= $utilisateur['statut'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="telephone" class="form-label">Téléphone</label>
            <input type="text" id="telephone" name="telephone" class="form-control" value="<?= htmlspecialchars($utilisateur['telephone']) ?>">
        </div>

        <form action="upload_photo.php" method="POST" enctype="multipart/form-data">
            <label for="photo">Photo de profil :</label>
            <input type="file" name="photo" id="photo" accept="image/*">
            <button type="submit">Télécharger</button>
        </form>


        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="liste_utilisateurs.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

</body>
</html>
