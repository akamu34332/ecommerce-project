<?php
require_once '../include/database.php';
require_once '../include/utils.php';
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}

if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idProduit = $_POST['id_produit'];
    $idUtilisateur = $_SESSION['utilisateur']['id'];
    $note = $_POST['note'];
    $commentaire = trim($_POST['commentaire']);

    if (is_numeric($note) && $note >= 1 && $note <= 5) {
        $stmt = $pdo->prepare('INSERT INTO avis (id_produit, id_utilisateur, note, commentaire) VALUES (?, ?, ?, ?)');
        $stmt->execute([$idProduit, $idUtilisateur, $note, $commentaire]);

        header('Location: produit.php?id=' . $idProduit);
        exit;
    } else {
        echo 'Erreur : La note doit être un chiffre entre 1 et 5.';
    }
}
?>
