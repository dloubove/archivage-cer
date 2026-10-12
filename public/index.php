<?php
// Point d'entrée de l'application (contrôleur frontal + routeur)
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/csrf.php';
require_once '../includes/helpers.php';

require_once '../controllers/utilisateurController.php';
require_once '../controllers/cerController.php';
require_once '../controllers/corbeilleController.php';

// [méthode HTTP, motif d'URL, contrôleur, action, connexion requise ?]
$routes = [
    ['GET',  'login',                        'UtilisateurController', 'showLogin',    false],
    ['POST', 'login',                        'UtilisateurController', 'login',        false],
    ['GET',  'register',                     'UtilisateurController', 'showRegister', false],
    ['POST', 'register',                     'UtilisateurController', 'createUser',   false],
    ['POST', 'logout',                       'UtilisateurController', 'logout',       true],
    ['GET',  'dashboard',                    'UtilisateurController', 'dashboard',    true],

    ['GET',  'archive',                      'CERController', 'index',   true],
    ['GET',  'archive/create',               'CERController', 'create',  true],
    ['POST', 'archive',                      'CERController', 'store',   true],
    ['GET',  'archive/(\d+)/edit',           'CERController', 'edit',    true],
    ['POST', 'archive/(\d+)/update',         'CERController', 'update',  true],
    ['POST', 'archive/(\d+)/delete',         'CERController', 'destroy', true],

    ['GET',  'corbeilles',                   'CorbeilleController', 'index',   true],
    ['GET',  'corbeilles/create',            'CorbeilleController', 'create',  true],
    ['POST', 'corbeilles',                   'CorbeilleController', 'store',   true],
    ['GET',  'corbeilles/(\d+)/edit',        'CorbeilleController', 'edit',    true],
    ['POST', 'corbeilles/(\d+)/update',      'CorbeilleController', 'update',  true],
    ['POST', 'corbeilles/(\d+)/delete',      'CorbeilleController', 'destroy', true],
];

$path = trim($_GET['url'] ?? '', '/');
$method = $_SERVER['REQUEST_METHOD'];

if ($path === '') {
    redirect(is_logged_in() ? 'dashboard' : 'login');
}

try {
    foreach ($routes as [$routeMethod, $pattern, $controller, $action, $authRequired]) {
        if ($routeMethod === $method && preg_match('#^' . $pattern . '$#', $path, $matches)) {
            if ($authRequired) {
                require_login();
            }
            if ($method === 'POST') {
                csrf_check(); // tout formulaire POST doit porter un token valide
            }
            array_shift($matches);
            (new $controller())->$action(...$matches);
            exit;
        }
    }

    http_response_code(404);
    render('404', ['titre' => 'Page introuvable']);
} catch (PDOException $e) {
    error_log($e->getMessage()); // détail dans les logs, jamais affiché à l'utilisateur
    http_response_code(500);
    echo 'Une erreur est survenue. Veuillez réessayer plus tard.';
}
