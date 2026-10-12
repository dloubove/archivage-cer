<?php
require_once __DIR__ . '/Model.php';

class Utilisateur extends Model
{
    private ?int $id;
    private ?string $nom;
    private ?string $email;
    private ?string $mot_de_passe; // toujours un hash, jamais le mot de passe en clair

    public function __construct(?int $id = null, ?string $nom = null, ?string $email = null,
                                ?string $mot_de_passe = null, ?PDO $pdo = null)
    {
        parent::__construct($pdo);
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->mot_de_passe = $mot_de_passe;
    }

    protected function table(): string
    {
        return 'utilisateurs';
    }

    // --- Encapsulation : accesseurs (pas de getter pour le hash) ---
    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function getEmail(): ?string { return $this->email; }

    public function afficherDetails(): string
    {
        return "Utilisateur #{$this->id} : {$this->nom} <{$this->email}>";
    }

    // --- CRUD ---

    /** Création d'un utilisateur (mot de passe haché). Retourne l'id créé. */
    public function create(string $nom, string $email, string $mot_de_passe): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)'
        );
        $stmt->execute([$nom, $email, password_hash($mot_de_passe, PASSWORD_DEFAULT)]);

        $this->id = (int) $this->pdo->lastInsertId();
        $this->nom = $nom;
        $this->email = $email;
        return $this->id;
    }

    /** Lecture d'un utilisateur par ID (le hash n'est jamais renvoyé). */
    public function read(int $id)
    {
        $stmt = $this->pdo->prepare('SELECT id, nom, email FROM utilisateurs WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** Mise à jour des informations ; le mot de passe n'est modifié que s'il est fourni. */
    public function update(int $id, string $nom, string $email, ?string $mot_de_passe = null): bool
    {
        if ($mot_de_passe !== null && $mot_de_passe !== '') {
            $stmt = $this->pdo->prepare(
                'UPDATE utilisateurs SET nom = ?, email = ?, mot_de_passe = ? WHERE id = ?'
            );
            return $stmt->execute([$nom, $email, password_hash($mot_de_passe, PASSWORD_DEFAULT), $id]);
        }
        $stmt = $this->pdo->prepare('UPDATE utilisateurs SET nom = ?, email = ? WHERE id = ?');
        return $stmt->execute([$nom, $email, $id]);
    }

    // --- Méthodes métier ---

    public function emailExiste(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        return (bool) $stmt->fetchColumn();
    }

    /** Vérifie les identifiants. Retourne la ligne utilisateur (sans hash) ou false. */
    public function authentifier(string $email, string $mot_de_passe)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($mot_de_passe, $row['mot_de_passe'])) {
            unset($row['mot_de_passe']);
            return $row;
        }
        return false;
    }
}
