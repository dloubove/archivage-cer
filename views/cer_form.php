<h1><?= e($titre) ?></h1>
<form action="<?= e(url($action)) ?>" method="post">
    <?= csrf_token_input() ?>
    <label for="titre">Titre :</label>
    <input type="text" id="titre" name="titre" value="<?= e($cer['titre'] ?? '') ?>" maxlength="255" required>

    <label for="contenu">Contenu :</label>
    <textarea id="contenu" name="contenu" rows="10" required><?= e($cer['contenu'] ?? '') ?></textarea>

    <p><button type="submit">Enregistrer</button> <a href="<?= e(url('archive')) ?>">Annuler</a></p>
</form>
