<h1><?= e($titre) ?></h1>
<form action="<?= e(url($action)) ?>" method="post">
    <?= csrf_token_input() ?>
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" value="<?= e($corbeille['nom'] ?? '') ?>" maxlength="255" required>

    <label for="description">Description (optionnelle) :</label>
    <textarea id="description" name="description" rows="5"><?= e($corbeille['description'] ?? '') ?></textarea>

    <p><button type="submit">Enregistrer</button> <a href="<?= e(url('corbeilles')) ?>">Annuler</a></p>
</form>
