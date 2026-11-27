<?php $tituloPagina='Meus pedidos | Livraria Virtual'; 
require __DIR__.'/head.php'; ?>
<h1>Meus pedidos</h1>
<a href="index.php" class="btn btn-outline-primary mb-3">Voltar ao catálogo</a>
<?php if (!$pedidos): ?>
    <p>Você ainda não realizou pedidos.</p>
<?php else: ?>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Número</th>
                <th>Data</th>
                <th>Status</th>
                <th>Unidades</th>
                <th>Total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($pedidos as $p): ?>
                <tr>
                    <td>#<?= (int)$p['id'] ?></td>
                    <td><?= $esc($p['data_pedido']) ?></td>
                    <td><?= $esc($p['status']) ?></td>
                    <td><?= (int)$p['unidades'] ?></td>
                    <td>R$ <?= number_format((float)$p['valor_total'],2,',','.') ?></td>
                    <td><a href="index.php?acao=pedido&id=<?= (int)$p['id'] ?>">Detalhes</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; require __DIR__.'/footer.php'; ?>
