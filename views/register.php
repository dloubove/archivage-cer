<h1>Inscription</h1>
<form action="<?= e(url('register')) ?>" method="post">
    <?= csrf_token_input() ?>
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" value="<?= e($old['nom'] ?? '') ?>" maxlength="100" required>

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" maxlength="100" required>

    <label for="mot_de_passe">Mot de passe (8 caractères minimum) :</label>
    <input type="password" id="mot_de_passe" name="mot_de_passe" minlength="8" required>

    <p><button type="submit">Créer mon compte</button></p>
</form>
