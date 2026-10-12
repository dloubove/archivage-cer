🇫🇷 Français | [🇬🇧 English](README.en.md)

# Archivage CER — Workshop PHP Objet et Base de Données (corrigé)

Application web d'archivage des **Cahiers d'Étude et de Recherche (CERs)** et des **corbeilles d'exercices**, réalisée en **PHP orienté objet**, **MySQL/PDO** et architecture **MVC**. Ce dépôt est la correction complète du workshop « Prosit 5 : PHP Objet et Base de Données ».

## Prérequis

- PHP 8.0 ou plus, avec les extensions `pdo_mysql` et `mbstring` (activées par défaut dans XAMPP / WAMP / Laragon)
- MySQL ou MariaDB
- Apache avec `mod_rewrite` (ou le serveur intégré de PHP, voir plus bas)

## Installation

1. Copier le dossier `archivage-cer` dans le répertoire web (ex. `htdocs/` avec XAMPP).
2. Importer la base : `database/schema.sql` (via phpMyAdmin, ou `mysql -u root < database/schema.sql`).
3. Adapter si besoin `includes/config.php` (hôte, utilisateur, mot de passe).
4. Ouvrir `http://localhost/archivage-cer/public/`.

Sans Apache :

```bash
php -S localhost:8000 -t public public/router.php
```

## Structure

```
archivage-cer/
├── database/schema.sql        Script SQL : base + 3 tables
├── includes/
│   ├── config.php             Constantes de connexion
│   ├── db.php                 Connexion PDO — design pattern Singleton
│   ├── csrf.php               Génération / vérification du token CSRF
│   └── helpers.php            e() (anti-XSS), url(), session, messages flash, render()
├── models/                    MODÈLE : Model (abstraite), Utilisateur, CER, Corbeille
├── controllers/               CONTRÔLEUR : utilisateur, cer, corbeille
├── views/                     VUE : login, register, dashboard, archive, formulaires…
├── public/
│   ├── index.php              Point d'entrée + routeur
│   ├── .htaccess              Réécriture d'URL vers index.php
│   └── router.php             Équivalent du .htaccess pour `php -S`
├── exemples/                  Exemples du cours (classes, héritage, encapsulation, polymorphisme)
└── tests/                     Tests automatisés
```

## Ce qui est couvert (objectifs du workshop)

| Objectif | Où |
|---|---|
| Classes, objets, constructeurs | `models/*.php` |
| Héritage | `Model` → `Utilisateur`/`CER`/`Corbeille` ; `CER` → `CERConfidentiel` (`exemples/`) |
| Encapsulation | propriétés `private`, accesseurs publics |
| Polymorphisme | `afficherDetails()` redéfinie dans chaque classe |
| Connexion MySQL via PDO, Singleton | `includes/db.php` |
| CRUD | `create()`, `read()`, `update()`, `delete()` dans chaque modèle |
| Requêtes préparées (injections SQL) | tous les modèles |
| Validation / assainissement des entrées | contrôleurs |
| Protection XSS | `e()` dans toutes les vues |
| Protection CSRF | `includes/csrf.php` + vérification centralisée dans `public/index.php` |
| MVC et routage | `controllers/`, `views/`, `public/index.php` |

## Tests

```bash
php tests/run.php     # tests unitaires (modèles, Singleton, CSRF, XSS) — 40 tests
bash tests/e2e.sh     # parcours complet via HTTP (inscription, CRUD, CSRF, XSS) — 18 tests
php exemples/poo_demo.php
```

Les tests utilisent SQLite pour ne pas nécessiter de serveur MySQL ; les requêtes SQL utilisées sont identiques. La connexion à un vrai serveur MySQL n'a donc pas été exécutée dans ces tests : à vérifier chez vous après l'import de `schema.sql`.
