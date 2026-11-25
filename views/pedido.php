<?php $tituloPagina='Pedido #'.(int)$pedido['id'].' | Livraria Virtual'; require __DIR__.'/head.php'; ?>
<h1>Pedido #<?= (int)$pedido['id'] ?></h1><p><strong>Data:</strong> <?= $esc($pedido['data_pedido']) ?> · <strong>Status:</strong> <?= $esc($pedido['status']) ?></p>
<a class="btn btn-outline-primary mb-3" href="index.php?acao=meus_pedidos">Voltar aos pedidos</a>
<div class="table-responsive"><table class="table"><thead><tr><th>Livro</th><th>Quantidade</th><th>Valor unitário (na compra)</th><th>Subtotal</th></tr></thead><tbody>
<?php foreach ($pedido['itens'] as $item): ?><tr><td><?= $esc($item['titulo']) ?></td><td><?= (int)$item['quantidade'] ?></td><td>R$ <?= number_format((float)$item['preco_unitario'],2,',','.') ?></td><td>R$ <?= number_format((float)$item['preco_unitario']*$item['quantidade'],2,',','.') ?></td></tr><?php endforeach; ?>
</tbody></table></div><p class="fs-4 text-end"><strong>Total: R$ <?= number_format((float)$pedido['valor_total'],2,',','.') ?></strong></p>
<p class="text-secondary">Pedido de demonstração; nenhum pagamento foi processado.</p>
<?php require __DIR__.'/footer.php'; ?>
