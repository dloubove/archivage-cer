<?php
require_once __DIR__ . '/../models/Corbeille.php';

class CorbeilleController
{
    private Corbeille $modele;

    public function __construct()
    {
        $this->modele = new Corbeille();
    }

    /** Valide les champs du formulaire ; retourne [nom, description, erreurs]. */
    private function valider(): array
    {
        $nom = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $erreurs = [];
        if ($nom === '' || mb_strlen($nom) > 255) {
            $erreurs[] = 'Le nom est obligatoire (255 caractères maximum).';
        }
        return [$nom, $description === '' ? null : $description, $erreurs];
    }

    public function index(): void
    {
        render('corbeilles', ['titre' => "Corbeilles d'exercices", 'corbeilles' => $this->modele->readAll()]);
    }

    public function create(): void
    {
        render('corbeille_form', ['titre' => 'Nouvelle corbeille', 'corbeille' => [], 'action' => 'corbeilles']);
    }

    public function store(): void
    {
        [$nom, $description, $erreurs] = $this->valider();
        if ($erreurs) {
            flash(implode(' ', $erreurs), 'error');
            render('corbeille_form', [
                'titre' => 'Nouvelle corbeille', 'action' => 'corbeilles',
                'corbeille' => ['nom' => $nom, 'description' => $description],
            ]);
            return;
        }
        $this->modele->create($nom, $description);
        flash('Corbeille créée.', 'success');
        redirect('corbeilles');
    }

    public function edit(string $id): void
    {
        $corbeille = $this->modele->read((int) $id);
        if (!$corbeille) {
            $this->notFound();
        }
        render('corbeille_form', [
            'titre' => 'Modifier la corbeille', 'corbeille' => $corbeille,
            'action' => "corbeilles/{$corbeille['id']}/update",
        ]);
    }

    public function update(string $id): void
    {
        if (!$this->modele->read((int) $id)) {
            $this->notFound();
        }
        [$nom, $description, $erreurs] = $this->valider();
        if ($erreurs) {
            flash(implode(' ', $erreurs), 'error');
            render('corbeille_form', [
                'titre' => 'Modifier la corbeille', 'action' => 'corbeilles/' . (int) $id . '/update',
                'corbeille' => ['id' => (int) $id, 'nom' => $nom, 'description' => $description],
            ]);
            return;
        }
        $this->modele->update((int) $id, $nom, $description);
        flash('Corbeille mise à jour.', 'success');
        redirect('corbeilles');
    }

    public function destroy(string $id): void
    {
        $this->modele->delete((int) $id);
        flash('Corbeille supprimée.', 'success');
        redirect('corbeilles');
    }

    private function notFound(): void
    {
        http_response_code(404);
        render('404', ['titre' => 'Introuvable']);
        exit;
    }
}
