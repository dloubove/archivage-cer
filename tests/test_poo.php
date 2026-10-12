<?php
/** Tests des exemples du cours (classes CER / CERConfidentiel) — processus séparé du reste. */
error_reporting(E_ALL);

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
require_once $racine . '/exemples/poo_classes.php';

// ---------------------------------------------------------------- POO (exemples du cours)
echo "POO — classes, héritage, encapsulation, polymorphisme\n";
$cer = new CER("Etude sur l'IA", 'Aurelle', '2024-08-18');
check('afficherDetails() de CER', $cer->afficherDetails() === "Titre : Etude sur l'IA, Auteur : Aurelle, Date : 2024-08-18");
$cer->setTitre('Nouveau Titre');
check('setTitre()/getTitre()', $cer->getTitre() === 'Nouveau Titre');
check('setTitre() refuse un titre vide', throws(fn() => $cer->setTitre('  '), InvalidArgumentException::class));
check('propriété non accessible directement (encapsulation)', throws(fn() => $cer->titre = 'x', Error::class));
$conf = new CERConfidentiel('Projet Secret', 'Eva', '2024-08-18', 'Haut');
check('CERConfidentiel hérite de CER', $conf instanceof CER);
check('afficherDetails() redéfinie (polymorphisme)', str_ends_with($conf->afficherDetails(), 'Niveau de Confidentialité : Haut')
    && str_starts_with($conf->afficherDetails(), 'Titre : Projet Secret'));

echo "\nRésultat POO : {$GLOBALS['ok']} réussis, {$GLOBALS['ko']} échec(s)\n";
exit($GLOBALS['ko'] > 0 ? 1 : 0);
