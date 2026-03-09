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

$stmt = $pdo->prepare('SELECT COUNT(*) FROM categorie WHERE id = ?');
$stmt->execute([$id]);
if ($stmt->fetchColumn() == 0) {
    die('Catégorie introuvable.');
}

$stmt = $pdo->prepare('DELETE FROM categorie WHERE id = ?');
$stmt->execute([$id]);

header('Location: categories.php');
exit;
?>