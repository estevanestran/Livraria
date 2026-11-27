<?php $tituloPagina='Gêneros literários | Livraria Virtual'; 
require __DIR__.'/head.php'; ?>
<h1>Gêneros literários</h1>
<p>Escolha uma categoria para ver os livros relacionados.</p>
<a href="index.php" class="btn btn-outline-secondary mb-3">Voltar ao catálogo</a>
<div class="list-group">
    <?php foreach ($categorias as $c): ?>
        <a class="list-group-item list-group-item-action d-flex justify-content-between" href="index.php?categoria=<?= (int)$c['id'] ?>"><?= $esc($c['nome']) ?>
        <span class="badge text-bg-secondary"><?= (int)$c['total_livros'] ?> livro(s)</span>
        </a>
    <?php endforeach; ?>
</div><?php require __DIR__.'/footer.php'; ?>