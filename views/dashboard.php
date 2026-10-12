<h1>Bienvenue, <?= e($_SESSION['user_nom'] ?? '') ?> !</h1>
<ul>
    <li><a href="<?= e(url('archive')) ?>"><?= (int) $nbCers ?> CER(s) archivé(s)</a></li>
    <li><a href="<?= e(url('corbeilles')) ?>"><?= (int) $nbCorbeilles ?> corbeille(s) d'exercices</a></li>
</ul>
