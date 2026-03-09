<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}

require_once 'include/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Action pour supprimer la photo de profil
    if (isset($_POST['action']) && $_POST['action'] === 'delete_photo') {
        $userId = $_SESSION['utilisateur']['id'];

        // Récupérer le chemin actuel de la photo
        $stmt = $pdo->prepare("SELECT photo_profil FROM utilisateur WHERE id = ?");
        $stmt->execute([$userId]);
        $photo = $stmt->fetchColumn();

        // Supprimer le fichier physique si présent
        if ($photo && file_exists(__DIR__ . '/' . $photo)) {
            unlink(__DIR__ . '/' . $photo);
        }

        // Réinitialiser le champ photo_profil dans la base de données
        $stmt = $pdo->prepare("UPDATE utilisateur SET photo_profil = NULL WHERE id = ?");
        $stmt->execute([$userId]);

        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id = ?");
        $stmt->execute([$userId]);
        $_SESSION['utilisateur'] = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "<script>alert('Photo de profil supprimée avec succès.'); window.location.href = 'profil.php';</script>";
        exit;
    }

    // Vérifier si un fichier a été envoyé
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['photo']['tmp_name'];
        $fileName = basename($_FILES['photo']['name']);
        $uploadDir = __DIR__ . '/upload/profiles/';
        $filePath = $uploadDir . uniqid() . '_' . $fileName;

        // Créer le répertoire si nécessaire
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Déplacer le fichier téléchargé
        if (move_uploaded_file($tmpName, $filePath)) {
            $userId = $_SESSION['utilisateur']['id'];
            $relativePath = 'upload/profiles/' . basename($filePath);

            // Mettre à jour la base de données
            $stmt = $pdo->prepare("UPDATE utilisateur SET photo_profil = ? WHERE id = ?");
            $stmt->execute([$relativePath, $userId]);

            // Mettre à jour la session utilisateur
            $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id = ?");
            $stmt->execute([$userId]);
            $_SESSION['utilisateur'] = $stmt->fetch(PDO::FETCH_ASSOC);

            // Redirection
            header('Location: profil.php');
            exit;
        } else {
            echo "<script>alert('Erreur lors du téléchargement. Veuillez réessayer.'); window.location.href = 'profil.php';</script>";
        }
    } else {
        // Aucun fichier envoyé
        echo "<script>alert('Aucun fichier sélectionné. Veuillez choisir une image.'); window.location.href = 'profil.php';</script>";
    }
}
?>
