[🇫🇷 Français](README.md) | 🇬🇧 English

# CER Archive — PHP Objects and Database Workshop (corrected)

A web application for archiving **Study and Research Notebooks (CERs)** and **exercise baskets**, built with **object-oriented PHP**, **MySQL/PDO** and an **MVC** architecture. This repository is the full correction of the "Prosit 5: PHP Objects and Database" workshop.

## Requirements

- PHP 8.0 or later, with the `pdo_mysql` and `mbstring` extensions (enabled by default in XAMPP / WAMP / Laragon)
- MySQL or MariaDB
- Apache with `mod_rewrite` (or PHP's built-in server, see below)

## Installation

1. Copy the `archivage-cer` folder into your web root (e.g. `htdocs/` with XAMPP).
2. Import the database: `database/schema.sql` (via phpMyAdmin, or `mysql -u root < database/schema.sql`).
3. Adjust `includes/config.php` if needed (host, user, password).
4. Open `http://localhost/archivage-cer/public/`.

Without Apache:

```bash
php -S localhost:8000 -t public public/router.php
```

## Structure

```
archivage-cer/
├── database/schema.sql        SQL script: database + 3 tables
├── includes/
│   ├── config.php             Connection constants
│   ├── db.php                 PDO connection — Singleton design pattern
│   ├── csrf.php               CSRF token generation / validation
│   └── helpers.php            e() (anti-XSS), url(), session, flash messages, render()
├── models/                    MODEL: Model (abstract), Utilisateur, CER, Corbeille
├── controllers/               CONTROLLER: utilisateur, cer, corbeille
├── views/                     VIEW: login, register, dashboard, archive, forms…
├── public/
│   ├── index.php              Entry point + router
│   ├── .htaccess              URL rewriting to index.php
│   └── router.php             .htaccess equivalent for `php -S`
├── exemples/                  Course examples (classes, inheritance, encapsulation, polymorphism)
└── tests/                     Automated tests
```

## What is covered (workshop objectives)

| Objective | Where |
|---|---|
| Classes, objects, constructors | `models/*.php` |
| Inheritance | `Model` → `Utilisateur`/`CER`/`Corbeille`; `CER` → `CERConfidentiel` (`exemples/`) |
| Encapsulation | `private` properties, public accessors |
| Polymorphism | `afficherDetails()` overridden in each class |
| MySQL connection via PDO, Singleton | `includes/db.php` |
| CRUD | `create()`, `read()`, `update()`, `delete()` in each model |
| Prepared statements (SQL injection) | all models |
| Input validation / sanitisation | controllers |
| XSS protection | `e()` in every view |
| CSRF protection | `includes/csrf.php` + centralised check in `public/index.php` |
| MVC and routing | `controllers/`, `views/`, `public/index.php` |

## Tests

```bash
php tests/run.php     # unit tests (models, Singleton, CSRF, XSS) — 40 tests
bash tests/e2e.sh     # full flow over HTTP (sign-up, CRUD, CSRF, XSS) — 18 tests
php exemples/poo_demo.php
```

The tests use SQLite so that no MySQL server is required; the SQL statements are identical. A real MySQL connection was therefore not exercised by these tests: please verify it on your machine after importing `schema.sql`.
