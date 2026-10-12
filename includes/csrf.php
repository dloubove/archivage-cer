<?php
// Protection CSRF : génération, insertion dans les formulaires, vérification.

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Génération du token CSRF unique pour la session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/** Champ caché à placer dans chaque formulaire POST. */
function csrf_token_input(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') . '">';
}

/** Vérifie le token reçu (comparaison à temps constant). */
function csrf_token_is_valid(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

/** Arrête la requête si le token est absent ou invalide. */
function csrf_check(): void
{
    if (!csrf_token_is_valid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        die('Échec de la validation CSRF');
    }
}
