<?php
/**
 * Tests automatisés (sans dépendance) — exécution : php tests/run.php
 * Les modèles sont testés sur SQLite en mémoire ; les requêtes sont les mêmes qu'avec MySQL.
 */
error_reporting(E_ALL);
ob_start(); // évite « headers already sent » pour la session en ligne de commande

$GLOBALS['ok'] = 0;
$GLOBALS['ko'] = 0;

function check(string $nom, bool $condition): void
{
    if ($condition) {
        $GLOBALS['ok']++;
        echo "  [OK] $nom\n";
    } else {
        $GLOBALS['ko']++;
        echo "  [ÉCHEC] $nom\n";
    }
}

function throws(callable $fn, string $classe = Throwable::class): bool
{
    try {
        $fn();
    } catch (Throwable $e) {
        return $e instanceof $classe;
    }
    return false;
}

$racine = dirname(__DIR__);

// ---------------------------------------------------------------- Singleton
define('DB_DSN', 'sqlite::memory:');
require_once $racine . '/includes/db.php';
require_once $racine . '/includes/helpers.php';
require_once $racine . '/includes/csrf.php';
require_once $racine . '/models/Utilisateur.php';
require_once $racine . '/models/CER.php';
require_once $racine . '/models/Corbeille.php';

echo "Database — design pattern Singleton\n";
check('getInstance() renvoie toujours la même instance', Database::getInstance() === Database::getInstance());
check('constructeur privé', (new ReflectionClass('Database'))->getConstructor()->isPrivate());
check('__clone privé', (new ReflectionMethod('Database', '__clone'))->isPrivate());
check('la connexion PDO est réutilisée', Database::getInstance()->getConnection() === getDbConnection());
check('mode exceptions PDO activé', getDbConnection()->getAttribute(PDO::ATTR_ERRMODE) === PDO::ERRMODE_EXCEPTION);
getDbConnection()->exec(file_get_contents(__DIR__ . '/schema_sqlite.sql'));

// ---------------------------------------------------------------- Sécurité
echo "Sécurité — XSS et CSRF\n";
check('e() neutralise <script>', e("<script>alert('Hacked!');</script>") === '&lt;script&gt;alert(&#039;Hacked!&#039;);&lt;/script&gt;');
check('csrf : token généré (64 hex)', preg_match('/^[a-f0-9]{64}$/', $_SESSION['csrf_token']) === 1);
check('csrf : token valide accepté', csrf_token_is_valid($_SESSION['csrf_token']));
check('csrf : token faux refusé', !csrf_token_is_valid('faux'));
check('csrf : token absent refusé', !csrf_token_is_valid(null));
check('csrf : champ caché contient le token', str_contains(csrf_token_input(), $_SESSION['csrf_token']));

// ---------------------------------------------------------------- Utilisateur
echo "Utilisateur — CRUD et authentification\n";
$u = new Utilisateur();
$id = $u->create('John Doe', 'john@example.com', 'password123');
check('create() retourne un id', $id > 0);
$lu = $u->read($id);
check('read() retourne nom et email', $lu['nom'] === 'John Doe' && $lu['email'] === 'john@example.com');
check('read() ne renvoie pas le hash', !array_key_exists('mot_de_passe', $lu));
$hash = getDbConnection()->query("SELECT mot_de_passe FROM utilisateurs WHERE id = $id")->fetchColumn();
check('le mot de passe est haché en base', $hash !== 'password123' && password_verify('password123', $hash));
check('emailExiste()', $u->emailExiste('john@example.com') && !$u->emailExiste('autre@example.com'));
check('authentifier() : bons identifiants', is_array($u->authentifier('john@example.com', 'password123')));
check('authentifier() : mauvais mot de passe', $u->authentifier('john@example.com', 'mauvais') === false);
check("authentifier() : injection SQL dans l'email sans effet", $u->authentifier("' OR '1'='1", 'x') === false);
$u->update($id, 'John D.', 'john@example.com');
check('update() sans mot de passe conserve le hash', password_verify('password123', getDbConnection()->query("SELECT mot_de_passe FROM utilisateurs WHERE id = $id")->fetchColumn())
    && $u->read($id)['nom'] === 'John D.');
$u->update($id, 'John D.', 'john@example.com', 'nouveau-mdp-123');
check('update() avec mot de passe le remplace', is_array($u->authentifier('john@example.com', 'nouveau-mdp-123')));
check('email unique (doublon refusé)', throws(fn() => (new Utilisateur())->create('Bis', 'john@example.com', 'abcdefgh'), PDOException::class));
(new Utilisateur())->create("Robert'); DROP TABLE utilisateurs;--", 'bobby@example.com', 'abcdefgh');
check('injection SQL dans le nom : la table existe toujours', (int) getDbConnection()->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn() === 2);
check('delete() supprime', $u->delete($id) === true && $u->read($id) === false);

// ---------------------------------------------------------------- CER
echo "CER — CRUD\n";
$c = new CER();
$cid = $c->create('Étude IA', 'Contenu détaillé');
check('create()', $cid > 0 && $c->read($cid)['titre'] === 'Étude IA');
check('date_creation renseignée par défaut', !empty($c->read($cid)['date_creation']));
$c->update($cid, 'Étude IA v2', 'Contenu modifié');
check('update()', $c->read($cid)['titre'] === 'Étude IA v2' && $c->read($cid)['contenu'] === 'Contenu modifié');
check('readAll()', count($c->readAll()) === 1);
check('delete()', $c->delete($cid) && $c->read($cid) === false);
check('delete() d\'un id inexistant renvoie false', $c->delete(9999) === false);

// ---------------------------------------------------------------- Corbeille
echo "Corbeille — CRUD\n";
$b = new Corbeille();
$bid = $b->create('Algèbre', null);
check('create() avec description optionnelle', $b->read($bid)['description'] === null);
$b->update($bid, 'Algèbre linéaire', 'Séries 1 à 5');
check('update()', $b->read($bid)['nom'] === 'Algèbre linéaire' && $b->read($bid)['description'] === 'Séries 1 à 5');
check('delete()', $b->delete($bid) && $b->read($bid) === false);

// ---------------------------------------------------------------- Polymorphisme sur les modèles
echo "Modèles — polymorphisme\n";
$modeles = [new Utilisateur(1, 'Ana', 'ana@x.cm', null), new CER(2, 'Titre', 'c', '2024-08-18'), new Corbeille(3, 'Nom', 'Desc')];
$sorties = array_map(fn(Model $m) => $m->afficherDetails(), $modeles);
check('chaque modèle redéfinit afficherDetails()', $sorties === [
    'Utilisateur #1 : Ana <ana@x.cm>',
    'CER #2 : Titre (créé le 2024-08-18)',
    'Corbeille #3 : Nom — Desc',
]);

echo "\nRésultat : {$GLOBALS['ok']} réussis, {$GLOBALS['ko']} échec(s)\n";
ob_end_flush();

// Les exemples POO déclarent une classe « CER » : ils tournent dans un processus à part
passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__DIR__ . '/test_poo.php'), $codePoo);
exit(($GLOBALS['ko'] > 0 || $codePoo !== 0) ? 1 : 0);
