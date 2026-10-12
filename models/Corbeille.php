<?php
require_once __DIR__ . '/Model.php';

/** Corbeille d'exercices. */
class Corbeille extends Model
{
    private ?int $id;
    private ?string $nom;
    private ?string $description;

    public function __construct(?int $id = null, ?string $nom = null, ?string $description = null,
                                ?PDO $pdo = null)
    {
        parent::__construct($pdo);
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
    }

    protected function table(): string
    {
        return 'corbeilles';
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function getDescription(): ?string { return $this->description; }

    public function afficherDetails(): string
    {
        return "Corbeille #{$this->id} : {$this->nom}" . ($this->description ? " — {$this->description}" : '');
    }

    // --- CRUD ---

    public function create(string $nom, ?string $description = null): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO corbeilles (nom, description) VALUES (?, ?)');
        $stmt->execute([$nom, $description]);

        $this->id = (int) $this->pdo->lastInsertId();
        $this->nom = $nom;
        $this->description = $description;
        return $this->id;
    }

    public function read(int $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM corbeilles WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(int $id, string $nom, ?string $description = null): bool
    {
        $stmt = $this->pdo->prepare('UPDATE corbeilles SET nom = ?, description = ? WHERE id = ?');
        return $stmt->execute([$nom, $description, $id]);
    }
}
