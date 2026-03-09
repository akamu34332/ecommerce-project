<?php
// include/utils.php

function redirect($url, $message = null, $type = 'info') {
    if ($message) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    }
    header("Location: $url");
    exit;
}

function requireAdmin() {
    if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['statut'] !== 'admin') {
        redirect('connexion.php', 'Vous devez être administrateur pour accéder à cette page.', 'danger');
    }
}

function requireLogin() {
    if (!isset($_SESSION['utilisateur'])) {
        redirect('connexion.php', 'Vous devez être connecté pour accéder à cette page.', 'danger');
    }
}

function getFlashMessage() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function paginate($query, $perPage = 10) {
    global $pdo;
    $page = max(1, (int)($_GET['page'] ?? 1));
    $offset = ($page - 1) * $perPage;

    $stmt = $pdo->query("SELECT COUNT(*) FROM ($query) AS count_query");
    $total = $stmt->fetchColumn();
    $pages = ceil($total / $perPage);

    $stmt = $pdo->query("$query LIMIT $perPage OFFSET $offset");
    return [$stmt->fetchAll(PDO::FETCH_ASSOC), $pages];
}

?>
