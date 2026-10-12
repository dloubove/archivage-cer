<?php
require_once __DIR__ . '/Model.php';

/** Cahier d'Étude et de Recherche. */
class CER extends Model
{
    private ?int $id;
    private ?string $titre;
    private ?string $contenu;
    private ?string $date_creation;

    public function __construct(?int $id = null, ?string $titre = null, ?string $contenu = null,
                                ?string $date_creation = null, ?PDO $pdo = null)
    {
        parent::__construct($pdo);
        $this->id = $id;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->date_creation = $date_creation;
    }

    protected function table(): string
    {
        return 'cers';
    }

    public function getId(): ?int { return $this->id; }
    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): void { $this->titre = $titre; }
    public function getContenu(): ?string { return $this->contenu; }
    public function getDateCreation(): ?string { return $this->date_creation; }

    public function afficherDetails(): string
    {
        return "CER #{$this->id} : {$this->titre} (créé le {$this->date_creation})";
    }

    // --- CRUD ---

    public function create(string $titre, string $contenu): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO cers (titre, contenu) VALUES (?, ?)');
        $stmt->execute([$titre, $contenu]);

        $this->id = (int) $this->pdo->lastInsertId();
        $this->titre = $titre;
        $this->contenu = $contenu;
        return $this->id;
    }

    public function read(int $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM cers WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(int $id, string $titre, string $contenu): bool
    {
        $stmt = $this->pdo->prepare('UPDATE cers SET titre = ?, contenu = ? WHERE id = ?');
        return $stmt->execute([$titre, $contenu, $id]);
    }
}
