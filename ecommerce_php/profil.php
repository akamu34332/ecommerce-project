<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}

if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit;
}

$utilisateur = $_SESSION['utilisateur'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <title>Profil Utilisateur</title>
</head>
<body>
<?php include 'include/nav.php'; ?>

<div class="container py-4">
    <h2 class="mb-4">Profil Utilisateur</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="fa fa-user me-2"></i>Informations personnelles</h5>
            <div class="row">
                <div class="col-md-4 text-center">
                    <?php if (!empty($utilisateur['photo_profil'])): ?>
                        <img src="<?php echo htmlspecialchars($utilisateur['photo_profil']) . '?t=' . time(); ?>" 
                            alt="Photo de profil" class="img-thumbnail" style="max-width: 150px;">

                    <?php else: ?>
                        <i class="fa fa-user fa-5x text-secondary"></i>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <form id="photoForm" action="upload_photo.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="photo" class="form-label">Changer votre photo de profil :</label>
                            <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-upload me-1"></i> Mettre à jour la photo
                        </button>
                    </form>
                    <form id="deletePhotoForm" action="upload_photo.php" method="POST">
                        <input type="hidden" name="action" value="delete_photo">
                        <button type="submit" class="btn btn-danger mt-3">
                            <i class="fa fa-trash me-1"></i> Supprimer la photo de profil
                        </button>
                    </form>
                </div>
            </div>
            <hr class="my-4">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Nom :</strong> <?= htmlspecialchars($utilisateur['nom']) ?></li>
                <li class="list-group-item"><strong>Prénom :</strong> <?= htmlspecialchars($utilisateur['prenom']) ?></li>
                <li class="list-group-item"><strong>Email :</strong> <?= htmlspecialchars($utilisateur['email']) ?></li>
                <li class="list-group-item"><strong>Statut :</strong> <?= ucfirst(htmlspecialchars($utilisateur['statut'])) ?></li>
            </ul>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="modifier_utilisateur.php?id=<?= htmlspecialchars($utilisateur['id']) ?>" class="btn btn-primary">
            <i class="fa fa-edit me-1"></i> Modifier le profil
        </a>

        <a href="deconnexion.php" class="btn btn-danger">
            <i class="fa fa-sign-out-alt me-1"></i> Déconnexion
        </a>
    </div>
</div>

<footer class="mt-5 py-3 bg-light text-center">
    <p>&copy; 2024 VotreEntreprise. Tous droits réservés.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('photoForm').addEventListener('submit', function(event) {
        const fileInput = document.getElementById('photo');
        if (!fileInput.value) {
            event.preventDefault();
            alert('Veuillez sélectionner une image avant de soumettre le formulaire.');
        }
    });
</script>
</body>
</html>
