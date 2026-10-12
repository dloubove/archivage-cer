<h1>Archives des CERs</h1>
<p><a class="btn" href="<?= e(url('archive/create')) ?>">+ Nouveau CER</a></p>
<?php if (!$cers): ?>
    <p>Aucun CER archivé pour le moment.</p>
<?php else: ?>
<table>
    <thead><tr><th>Titre</th><th>Contenu</th><th>Créé le</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($cers as $cer): ?>
        <tr>
            <td><?= e($cer['titre']) ?></td>
            <td><?= e(mb_strimwidth($cer['contenu'], 0, 120, '…')) ?></td>
            <td><?= e($cer['date_creation']) ?></td>
            <td>
                <a href="<?= e(url('archive/' . (int) $cer['id'] . '/edit')) ?>">Modifier</a>
                <form class="inline" action="<?= e(url('archive/' . (int) $cer['id'] . '/delete')) ?>" method="post"
                      onsubmit="return confirm('Supprimer ce CER ?');">
                    <?= csrf_token_input() ?>
                    <button class="danger" type="submit">Supprimer</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
