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

$stmt = $pdo->prepare('SELECT image FROM produit WHERE id = ?');
$stmt->execute([$id]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    die('Produit introuvable.');
}

$imagePath = 'upload/produit/' . $produit['image'];
if (file_exists($imagePath)) {
    unlink($imagePath);
}

$stmt = $pdo->prepare('DELETE FROM produit WHERE id = ?');
$stmt->execute([$id]);

header('Location: produits.php');
exit;

?>