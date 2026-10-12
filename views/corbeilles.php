<h1>Corbeilles d'exercices</h1>
<p><a class="btn" href="<?= e(url('corbeilles/create')) ?>">+ Nouvelle corbeille</a></p>
<?php if (!$corbeilles): ?>
    <p>Aucune corbeille pour le moment.</p>
<?php else: ?>
<table>
    <thead><tr><th>Nom</th><th>Description</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($corbeilles as $c): ?>
        <tr>
            <td><?= e($c['nom']) ?></td>
            <td><?= e($c['description'] ?? '') ?></td>
            <td>
                <a href="<?= e(url('corbeilles/' . (int) $c['id'] . '/edit')) ?>">Modifier</a>
                <form class="inline" action="<?= e(url('corbeilles/' . (int) $c['id'] . '/delete')) ?>" method="post"
                      onsubmit="return confirm('Supprimer cette corbeille ?');">
                    <?= csrf_token_input() ?>
                    <button class="danger" type="submit">Supprimer</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
