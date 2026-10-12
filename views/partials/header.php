<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titre ?? 'Archivage CER') ?> — Archivage CER</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 860px; margin: 0 auto; padding: 1rem; color: #222; }
        nav { display: flex; gap: 1rem; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: .6rem; margin-bottom: 1rem; }
        nav form { margin-left: auto; }
        label { display: block; margin-top: .8rem; font-weight: 600; }
        input[type=text], input[type=email], input[type=password], textarea { width: 100%; padding: .5rem; box-sizing: border-box; }
        button, .btn { padding: .45rem .9rem; border: 0; background: #1d4ed8; color: #fff; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        button.danger { background: #b91c1c; }
        .flash { padding: .6rem .9rem; border-radius: 4px; margin-bottom: 1rem; }
        .flash.error { background: #fee2e2; } .flash.success { background: #dcfce7; } .flash.info { background: #e0f2fe; }
        table { width: 100%; border-collapse: collapse; } th, td { text-align: left; padding: .5rem; border-bottom: 1px solid #eee; vertical-align: top; }
        form.inline { display: inline; }
    </style>
</head>
<body>
<nav>
    <strong>Archivage CER</strong>
    <?php if (is_logged_in()): ?>
        <a href="<?= e(url('dashboard')) ?>">Tableau de bord</a>
        <a href="<?= e(url('archive')) ?>">CERs</a>
        <a href="<?= e(url('corbeilles')) ?>">Corbeilles</a>
        <form action="<?= e(url('logout')) ?>" method="post">
            <?= csrf_token_input() ?>
            <span><?= e($_SESSION['user_nom'] ?? '') ?></span>
            <button type="submit">Déconnexion</button>
        </form>
    <?php else: ?>
        <a href="<?= e(url('login')) ?>">Connexion</a>
        <a href="<?= e(url('register')) ?>">Inscription</a>
    <?php endif; ?>
</nav>
<?php if ($flash = flash()): ?>
    <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>
