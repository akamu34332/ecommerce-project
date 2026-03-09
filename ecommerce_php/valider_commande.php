<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}

// Vérification de l'accès utilisateur
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['statut'] !== 'admin') {
    header('Location: connexion.php');
    exit;
}

require_once 'include/database.php';

// Récupération des paramètres GET
$idCommande = $_GET['id'] ?? null;
$etat = $_GET['etat'] ?? null;

// Validation des paramètres
if (!$idCommande || !filter_var($idCommande, FILTER_VALIDATE_INT) || ($etat !== '0' && $etat !== '1')) {
    die('Paramètres invalides.');
}

try {
    // Préparation et exécution de la mise à jour
    $stmt = $pdo->prepare('UPDATE commande SET valide = ? WHERE id = ?');
    $stmt->execute([$etat, $idCommande]);

    // Message de confirmation et redirection
    $message = ($etat === '1') ? 'Commande validée avec succès.' : 'Validation de la commande annulée.';
    $_SESSION['flash_message'] = $message;

    header("Location: commande.php?id=$idCommande");
    exit;
} catch (PDOException $e) {
    die('Erreur lors de la validation de la commande : ' . $e->getMessage());
}
?>