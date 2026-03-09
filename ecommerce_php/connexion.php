<?php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}

if (isset($_SESSION['utilisateur'])) {
    header('Location: ./index.php');
    exit;
}

require_once 'include/database.php';

$errors = [];
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Veuillez entrer un email valide.';
    }
    if (empty($password)) {
        $errors[] = 'Le mot de passe est obligatoire.';
    }

    if (empty($errors)) {
        $sql = 'SELECT * FROM utilisateur WHERE email = ? LIMIT 1';
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$email])) {
            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($utilisateur && password_verify($password, $utilisateur['password'])) {
                $_SESSION['utilisateur'] = [
                    'id' => $utilisateur['id'],
                    'nom' => $utilisateur['nom'],
                    'prenom' => $utilisateur['prenom'],
                    'email' => $utilisateur['email'],
                    'statut' => $utilisateur['statut']
                ];

                header('Location: ' . ($_SESSION['utilisateur']['statut'] === 'admin' ? './admin.php' : './front/index.php'));
                exit;
            } else {
                $errors[] = 'Identifiants incorrects.';
            }
        } else {
            $errors[] = 'Erreur lors de la vérification des identifiants.';
            $errors[] = implode(', ', $stmt->errorInfo());
        }
    }
}
?>

<!doctype html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <title>Connexion</title>
</head>
<body>
<?php include 'include/nav.php'; ?>

<div class="container py-4">
    <h2>Connexion</h2>

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
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Se connecter</button>
        <a href="ajouter_utilisateur.php" class="btn btn-secondary">Créer un compte</a>
    </form>
</div>

</body>
</html>
