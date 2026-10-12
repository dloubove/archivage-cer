<?php
require_once __DIR__ . '/../includes/db.php';

/**
 * Classe parente de tous les modèles : elle porte la connexion PDO
 * (injectable pour les tests) et les opérations communes (héritage).
 */
abstract class Model
{
    protected PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? getDbConnection();
    }

    /** Nom de la table gérée par le modèle (valeur fixe, jamais issue de l'utilisateur). */
    abstract protected function table(): string;

    /** Description textuelle d'un enregistrement — redéfinie par chaque classe (polymorphisme). */
    abstract public function afficherDetails(): string;

    /** Suppression d'un enregistrement par id (commune à tous les modèles). */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM ' . $this->table() . ' WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /** Liste de tous les enregistrements, les plus récents d'abord. */
    public function readAll(): array
    {
        return $this->pdo->query('SELECT * FROM ' . $this->table() . ' ORDER BY id DESC')->fetchAll();
    }
}
