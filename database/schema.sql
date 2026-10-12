-- Base de données de l'application d'archivage des CERs
CREATE DATABASE IF NOT EXISTS archivage_cer
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE archivage_cer;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Table des Cahiers d'Étude et de Recherche (CERs)
CREATE TABLE IF NOT EXISTS cers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des corbeilles d'exercices
CREATE TABLE IF NOT EXISTS corbeilles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT NULL
) ENGINE=InnoDB;
