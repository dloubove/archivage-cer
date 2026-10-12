<h1>Connexion</h1>
<form action="<?= e(url('login')) ?>" method="post">
    <?= csrf_token_input() ?>
    <label for="email">Email :</label>
    <input type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" required>

    <label for="mot_de_passe">Mot de passe :</label>
    <input type="password" id="mot_de_passe" name="mot_de_passe" required>

    <p><button type="submit">Se connecter</button></p>
</form>
<p>Pas encore de compte ? <a href="<?= e(url('register')) ?>">Inscrivez-vous</a>.</p>
