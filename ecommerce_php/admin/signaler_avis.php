<?php
require_once '../include/database.php';

$idAvis = $_POST['id_avis'] ?? null;

if ($idAvis && filter_var($idAvis, FILTER_VALIDATE_INT)) {
    try {
        $stmt = $pdo->prepare('UPDATE avis SET signale = TRUE WHERE id = ?');
        $stmt->execute([$idAvis]);
    } catch (PDOException $e) {
        die('Erreur lors du signalement de l\'avis : ' . $e->getMessage());
    }
}

header('Location: avis_admin.php');
exit;
