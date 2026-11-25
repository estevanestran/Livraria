<?php $tituloPagina='Carrinho de compras | Livraria Virtual'; require __DIR__.'/head.php'; ?>
<div class="d-flex justify-content-between align-items-center gap-3 flex-wrap"><h1>Carrinho de compras</h1><a href="index.php" class="btn btn-outline-primary">Continuar comprando</a></div>
<?php if ($aviso): ?><div class="alert alert-warning mt-3"><?= $esc($aviso) ?></div><?php endif; ?>
<?php if ($incompleto): ?><div class="alert alert-warning">Um dos livros do carrinho não está mais disponível no catálogo e foi removido automaticamente. Confira seu carrinho antes de finalizar.</div><?php endif; ?>
<?php if (!$itens): ?><p>Seu carrinho está vazio.</p><?php else: ?>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Livro</th><th>Preço unitário</th><th>Quantidade</th><th>Subtotal</th></tr></thead><tbody>
<?php foreach($itens as $i): ?><tr><td><a href="index.php?acao=livro&id=<?= (int)$i['id'] ?>"><?= $esc($i['titulo']) ?></a>
<?php if (!$i['disponivel']): ?><div class="text-danger small">Indisponível ou estoque insuficiente (<?= (int)$i['estoque'] ?> disponível)</div><?php endif; ?></td>
<td>R$ <?= number_format((float)$i['preco'],2,',','.') ?></td><td><form class="d-flex gap-2 align-items-center" method="post" action="index.php?acao=atualizar_carrinho">
<input type="hidden" name="csrf" value="<?= $esc($_SESSION['csrf']) ?>"><input type="hidden" name="id" value="<?= (int)$i['id'] ?>">
<input type="number" name="quantidade" class="form-control" style="width:90px" min="0" max="99" value="<?= (int)$i['quantidade'] ?>" required aria-label="Quantidade de <?= $esc($i['titulo']) ?>">
<button class="btn btn-sm btn-outline-primary">Atualizar</button><button type="submit" name="remover" value="1" class="btn btn-sm btn-outline-danger" formnovalidate>Remover</button>
</form></td><td>R$ <?= number_format($i['subtotal_centavos']/100,2,',','.') ?></td></tr><?php endforeach; ?></tbody></table></div>
<p class="fs-4 text-end"><strong>Total: R$ <?= number_format($totalCentavos/100,2,',','.') ?></strong></p>
<?php if ($disponivel): ?>
<?php if (Auth::usuario()): ?><form method="post" action="index.php?acao=finalizar_pedido" class="text-end"><input type="hidden" name="csrf" value="<?= $esc($_SESSION['csrf']) ?>"><button class="btn btn-success btn-lg">Confirmar pedido (sem pagamento real)</button></form>
<?php else: ?><p class="text-end">Você precisa <a href="index.php?acao=login">entrar na sua conta</a> para finalizar o pedido.</p><?php endif; ?>
<?php else: ?><div class="alert alert-secondary">Ajuste os itens para continuar.</div><?php endif; ?>
<?php endif; ?>
<?php require __DIR__.'/footer.php'; ?>
