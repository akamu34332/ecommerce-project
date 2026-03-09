<?php
require_once '../include/database.php';

$idAvis = $_GET['id'] ?? null;

if (!$idAvis || !filter_var($idAvis, FILTER_VALIDATE_INT)) {
    die('ID de l\'avis invalide.');
}

try {
    $stmt = $pdo->prepare('DELETE FROM avis WHERE id = ?');
    $stmt->execute([$idAvis]);
    header('Location: avis_admin.php?success=true');
    exit;
} catch (PDOException $e) {
    die('Erreur lors de la suppression de l\'avis : ' . $e->getMessage());
}
?>