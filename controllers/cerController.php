<?php
require_once __DIR__ . '/../models/CER.php';

class CERController
{
    private CER $modele;

    public function __construct()
    {
        $this->modele = new CER();
    }

    /** Valide les champs du formulaire ; retourne [titre, contenu, erreurs]. */
    private function valider(): array
    {
        $titre = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');
        $erreurs = [];
        if ($titre === '' || mb_strlen($titre) > 255) {
            $erreurs[] = 'Le titre est obligatoire (255 caractères maximum).';
        }
        if ($contenu === '') {
            $erreurs[] = 'Le contenu est obligatoire.';
        }
        return [$titre, $contenu, $erreurs];
    }

    public function index(): void
    {
        render('archive', ['titre' => 'Archives des CERs', 'cers' => $this->modele->readAll()]);
    }

    public function create(): void
    {
        render('cer_form', ['titre' => 'Nouveau CER', 'cer' => [], 'action' => 'archive']);
    }

    public function store(): void
    {
        [$titre, $contenu, $erreurs] = $this->valider();
        if ($erreurs) {
            flash(implode(' ', $erreurs), 'error');
            render('cer_form', [
                'titre' => 'Nouveau CER', 'action' => 'archive',
                'cer' => ['titre' => $titre, 'contenu' => $contenu],
            ]);
            return;
        }
        $this->modele->create($titre, $contenu);
        flash('CER archivé avec succès.', 'success');
        redirect('archive');
    }

    public function edit(string $id): void
    {
        $cer = $this->modele->read((int) $id);
        if (!$cer) {
            $this->notFound();
        }
        render('cer_form', ['titre' => 'Modifier le CER', 'cer' => $cer, 'action' => "archive/{$cer['id']}/update"]);
    }

    public function update(string $id): void
    {
        if (!$this->modele->read((int) $id)) {
            $this->notFound();
        }
        [$titre, $contenu, $erreurs] = $this->valider();
        if ($erreurs) {
            flash(implode(' ', $erreurs), 'error');
            render('cer_form', [
                'titre' => 'Modifier le CER', 'action' => "archive/" . (int) $id . "/update",
                'cer' => ['id' => (int) $id, 'titre' => $titre, 'contenu' => $contenu],
            ]);
            return;
        }
        $this->modele->update((int) $id, $titre, $contenu);
        flash('CER mis à jour.', 'success');
        redirect('archive');
    }

    public function destroy(string $id): void
    {
        $this->modele->delete((int) $id);
        flash('CER supprimé.', 'success');
        redirect('archive');
    }

    private function notFound(): void
    {
        http_response_code(404);
        render('404', ['titre' => 'Introuvable']);
        exit;
    }
}
