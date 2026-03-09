<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (session_status() === PHP_SESSION_NONE) {
    $sessionPath = __DIR__ . '/../sessions';
    if (!is_dir($sessionPath)) {
        if (!mkdir($sessionPath, 0777, true)) {
            die('Erreur : Impossible de créer le répertoire des sessions.');
        }
    }

    ini_set('session.save_path', $sessionPath);
    ini_set('session.gc_maxlifetime', 3600);
    ini_set('session.cookie_lifetime', 3600);

    session_start();
}
?>
