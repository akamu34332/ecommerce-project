<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../connexion.php');
    exit;
}

$idUtilisateur = $_SESSION['utilisateur']['id'];
$idProduit = $_POST['id'] ?? null;

if (!$idProduit || !filter_var($idProduit, FILTER_VALIDATE_INT)) {
    die('Produit invalide.');
}

if (isset($_SESSION['panier'][$idUtilisateur][$idProduit])) {
    unset($_SESSION['panier'][$idUtilisateur][$idProduit]);
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
exit;
?>
