<?php
// Utilisé par tests/e2e.sh : remplace MySQL par un fichier SQLite pour le serveur de test.
define('DB_DSN', 'sqlite:' . (getenv('E2E_DB') ?: sys_get_temp_dir() . '/archivage_e2e.sqlite'));
$pdo = new PDO(DB_DSN);
$pdo->exec(file_get_contents(__DIR__ . '/schema_sqlite.sql'));
