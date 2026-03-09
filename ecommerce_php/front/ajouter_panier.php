<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', realpath(__DIR__ . '/../sessions'));
    session_start();
}
if (!isset($_SESSION['utilisateur']['id'])) {
    header('Location: ../connexion.php');
    exit;
}

$id = $_POST['id'];
$qty = $_POST['qty'];
$idUtilisateur = $_SESSION['utilisateur']['id'];

if (!(is_numeric($id) && is_numeric($qty) && $qty >= 0)) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

if (!isset($_SESSION['panier'][$idUtilisateur])) {
    $_SESSION['panier'][$idUtilisateur] = [];
}

if ($qty == 0) {
    unset($_SESSION['panier'][$idUtilisateur][$id]);
} else {
    $_SESSION['panier'][$idUtilisateur][$id] = $qty;
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?>