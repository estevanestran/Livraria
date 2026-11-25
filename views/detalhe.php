<?php
$tituloPagina=$livro['titulo'].' | Livraria Virtual';
$descricaoPagina=mb_substr(strip_tags($livro['descricao']?:'Conheça este livro em nosso catálogo.'),0,155);
require __DIR__.'/head.php'; ?>
<a href="index.php" class="btn btn-link mb-3">Voltar ao catálogo</a>
<main class="row g-4">
<div class="col-md-4"><?php if (!empty($livro['imagem_capa'])): ?><img class="img-fluid" src="<?= $esc($livro['imagem_capa']) ?>" alt="Capa de <?= $esc($livro['titulo']) ?>"><?php endif; ?></div>
<div class="col-md-8"><h1><?= $esc($livro['titulo']) ?></h1><p><strong>Autor:</strong> <?= $esc($livro['autor']??'Não informado') ?></p>
<p><strong>ISBN:</strong> <?= $esc($livro['isbn']) ?></p>
<p><strong>Gêneros:</strong> <?= $esc(implode(', ',array_column($livro['categorias_detalhe'],'nome'))) ?></p>
<p><strong>Preço:</strong> R$ <?= number_format((float)$livro['preco'],2,',','.') ?></p>
<p><strong>Estoque:</strong> <?= (int)$livro['estoque'] ?></p>
<p><strong>Status:</strong> <?= $esc($livro['status']) ?></p>
<h2 class="h4">Descrição</h2><p><?= nl2br($esc($livro['descricao']??'')) ?></p>
<?php if ($livro['status']==='Disponível' && (int)$livro['estoque']>0): ?>
<form method="post" action="index.php?acao=adicionar_carrinho" class="d-flex gap-2 align-items-end flex-wrap">
<input type="hidden" name="csrf" value="<?= $esc($_SESSION['csrf']) ?>"><input type="hidden" name="id" value="<?= (int)$livro['id'] ?>">
<div><label class="form-label" for="quantidade">Quantidade</label><input class="form-control" id="quantidade" type="number" name="quantidade" value="1" min="1" max="<?= min(99,(int)$livro['estoque']) ?>" required></div>
<button class="btn btn-success">Adicionar ao carrinho</button></form>
<?php else: ?><p class="text-danger">Livro indisponível para compra.</p><?php endif; ?>
<a class="btn btn-outline-primary mt-3" href="index.php?acao=carrinho">Ver carrinho</a>
</div></main>
<?php require __DIR__.'/footer.php'; ?>