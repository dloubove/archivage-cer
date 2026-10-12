<?php
// Routeur pour le serveur intégré de PHP (php -S) : équivalent du .htaccess d'Apache.
// Usage : php -S localhost:8000 -t public public/router.php
$chemin = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($chemin !== '/' && is_file(__DIR__ . $chemin)) {
    return false; // fichier statique existant
}
if (!isset($_GET['url']) && $chemin !== '/index.php') {
    $_GET['url'] = ltrim($chemin, '/');
}
require __DIR__ . '/index.php';
