<?php
/**
 * Exemples du cours — version corrigée et complète.
 * Corrections par rapport au sujet :
 *  - propriétés `protected` (et non `private`) quand une classe fille doit y accéder ;
 *  - afficherDetails() de CERConfidentiel complétée (le sujet s'arrêtait en plein milieu) ;
 *  - le polymorphisme est démontré par une boucle sur des objets de classes différentes.
 */

// ---------- Exemples 1 et 3 : classe + encapsulation ----------
class CER
{
    protected string $titre;
    protected string $auteur;
    protected string $date;

    public function __construct(string $titre, string $auteur, string $date)
    {
        $this->titre = $titre;
        $this->auteur = $auteur;
        $this->date = $date;
    }

    public function getTitre(): string { return $this->titre; }

    public function setTitre(string $titre): void
    {
        if (trim($titre) === '') {
            throw new InvalidArgumentException('Le titre ne peut pas être vide.');
        }
        $this->titre = $titre;
    }

    public function afficherDetails(): string
    {
        return "Titre : {$this->titre}, Auteur : {$this->auteur}, Date : {$this->date}";
    }
}

// ---------- Exemple 2 : héritage ----------
class CERConfidentiel extends CER
{
    private string $niveauConfidentialite;

    public function __construct(string $titre, string $auteur, string $date, string $niveauConfidentialite)
    {
        parent::__construct($titre, $auteur, $date);
        $this->niveauConfidentialite = $niveauConfidentialite;
    }

    public function getNiveauConfidentialite(): string { return $this->niveauConfidentialite; }

    // ---------- Exemple 4 : polymorphisme (redéfinition de la méthode) ----------
    public function afficherDetails(): string
    {
        return parent::afficherDetails() . ", Niveau de Confidentialité : {$this->niveauConfidentialite}";
    }
}
