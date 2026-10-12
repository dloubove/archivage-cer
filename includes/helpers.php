<?php
// Fonctions utilitaires : échappement XSS, URLs, authentification, messages flash.

/** Échappe une valeur avant affichage HTML (protection XSS). */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** URL de base de l'application (dossier public/), compatible XAMPP/WAMP. */
function base_url(): string
{
    return rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
}

function url(string $path = ''): string
{
    return base_url() . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('login');
    }
}

function flash(?string $message = null, string $type = 'info')
{
    if ($message !== null) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        return null;
    }
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/** Rend une vue en lui passant des variables (utiliser e() dans la vue pour tout afficher). */
function render(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require __DIR__ . '/../views/partials/header.php';
    require __DIR__ . '/../views/' . $view . '.php';
    require __DIR__ . '/../views/partials/footer.php';
}
