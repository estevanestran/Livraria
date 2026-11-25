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
<p class="text-secondary">O carrinho de compras será implementado na próxima etapa.</p>
</div></main>
<?php require __DIR__.'/footer.php'; ?>