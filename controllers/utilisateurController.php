<?php
require_once __DIR__ . '/../models/Utilisateur.php';
require_once __DIR__ . '/../models/CER.php';
require_once __DIR__ . '/../models/Corbeille.php';

class UtilisateurController
{
    public function showLogin(): void
    {
        render('login', ['titre' => 'Connexion', 'old' => []]);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';

        $utilisateur = (new Utilisateur())->authentifier($email, $mot_de_passe);
        if ($utilisateur === false) {
            // Message volontairement générique : on ne révèle pas si l'email existe
            flash('Email ou mot de passe incorrect.', 'error');
            render('login', ['titre' => 'Connexion', 'old' => ['email' => $email]]);
            return;
        }

        session_regenerate_id(true); // prévention de la fixation de session
        $_SESSION['user_id'] = (int) $utilisateur['id'];
        $_SESSION['user_nom'] = $utilisateur['nom'];
        redirect('dashboard');
    }

    public function showRegister(): void
    {
        render('register', ['titre' => 'Inscription', 'old' => []]);
    }

    /** Inscription : validation, puis création en base via le modèle (requête préparée). */
    public function createUser(): void
    {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';

        $erreurs = [];
        if (mb_strlen($nom) < 2 || mb_strlen($nom) > 100) {
            $erreurs[] = 'Le nom doit contenir entre 2 et 100 caractères.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
            $erreurs[] = "L'adresse email n'est pas valide.";
        }
        if (strlen($mot_de_passe) < 8) {
            $erreurs[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }

        $modele = new Utilisateur();
        if (!$erreurs && $modele->emailExiste($email)) {
            $erreurs[] = 'Cet email est déjà utilisé.';
        }

        if ($erreurs) {
            flash(implode(' ', $erreurs), 'error');
            render('register', ['titre' => 'Inscription', 'old' => ['nom' => $nom, 'email' => $email]]);
            return;
        }

        $id = $modele->create($nom, $email, $mot_de_passe);
        session_regenerate_id(true);
        $_SESSION['user_id'] = $id;
        $_SESSION['user_nom'] = $nom;
        redirect('dashboard');
    }

    public function dashboard(): void
    {
        render('dashboard', [
            'titre' => 'Tableau de bord',
            'nbCers' => count((new CER())->readAll()),
            'nbCorbeilles' => count((new Corbeille())->readAll()),
        ]);
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        session_start();
        flash('Vous êtes déconnecté.', 'info');
        redirect('login');
    }
}
