<?php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}
require_once 'include/database.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $statut = $_POST['statut'] ?? 'user';
    $telephone = trim($_POST['telephone'] ?? '');

    if (empty($nom)) {
        $errors[] = 'Le nom est obligatoire.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Un email valide est obligatoire.';
    }
    if (empty($password) || strlen($password) < 6) {
        $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $dateCreation = date('Y-m-d');

        $sql = 'INSERT INTO utilisateur (nom, prenom, email, password, statut, telephone, date_creation) VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $prenom, $email, $hashedPassword, $statut, $telephone, $dateCreation]);

        header('Location: connexion.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <title>Créer un compte utilisateur</title>
</head>
<body>
<?php include 'include/nav.php'; ?>

<div class="container py-4">
    <h2>Créer un compte utilisateur</h2>
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
            <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($nom ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($prenom ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="telephone" class="form-label">Téléphone</label>
            <input type="text" id="telephone" name="telephone" class="form-control" value="<?= htmlspecialchars($telephone ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="statut" class="form-label">Statut</label>
            <select id="statut" name="statut" class="form-control" required>
                <option value="user" <?= ($statut ?? '') === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                <option value="admin" <?= ($statut ?? '') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Créer un compte</button>
        <a href="connexion.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

</body>
</html>
