<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['statut'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}

require_once '../include/database.php';

$idCommande = $_GET['id'] ?? null;
$nouveauStatut = $_POST['statut'] ?? null;

if (!$idCommande || !filter_var($idCommande, FILTER_VALIDATE_INT) || $nouveauStatut === null) {
    die('Paramètres invalides.');
}

// Mise à jour du statut de la commande
$sql = $pdo->prepare('UPDATE commande SET statut = ? WHERE id = ?');
$sql->execute([$nouveauStatut, $idCommande]);

header('Location: ../commandes.php');
exit;
?>
