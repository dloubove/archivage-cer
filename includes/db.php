<?php
require_once __DIR__ . '/config.php';

/**
 * Connexion à la base de données — design pattern Singleton.
 *
 * - le constructeur est privé : impossible de faire "new Database()" ;
 * - __clone() est privé et __wakeup() lève une exception : l'instance ne
 *   peut être ni clonée ni désérialisée ;
 * - getInstance() renvoie toujours la même instance ;
 * - la connexion PDO est ouverte à la première utilisation (lazy).
 */
final class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
        throw new Exception('Un singleton ne peut pas être désérialisé.');
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            // DB_DSN peut être défini avant l'inclusion (ex. SQLite pour les tests automatisés)
            $dsn = defined('DB_DSN')
                ? DB_DSN
                : 'mysql:host=' . DB_SERVER . ';dbname=' . DB_DATABASE . ';charset=' . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // vraies requêtes préparées
            ]);
        }
        return $this->connection;
    }
}

/** Raccourci utilisé dans le reste de l'application. */
function getDbConnection(): PDO
{
    return Database::getInstance()->getConnection();
}
