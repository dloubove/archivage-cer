<?php
// Exécution : php exemples/poo_demo.php
require_once __DIR__ . '/poo_classes.php';

$cer1 = new CER("Etude sur l'IA", 'Aurelle', '2024-08-18');
echo $cer1->afficherDetails(), PHP_EOL;

$cer1->setTitre('Nouveau Titre');   // modification contrôlée (encapsulation)
echo $cer1->getTitre(), PHP_EOL;

$cerConf = new CERConfidentiel('Projet Secret', 'Eva', '2024-08-18', 'Haut');

// Polymorphisme : même appel, comportement différent selon la classe réelle de l'objet
foreach ([$cer1, $cerConf] as $cer) {
    echo $cer->afficherDetails(), PHP_EOL;
}
