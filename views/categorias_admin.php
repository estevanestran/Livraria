<?php $tituloPagina='Gerenciar categorias | Livraria Virtual'; 
require __DIR__.'/head.php'; ?>
<h1>Gerenciar categorias</h1>
<a href="index.php" class="btn btn-outline-secondary mb-3">Voltar ao catálogo</a>
<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?= $esc($erro) ?></div>
<?php endif; ?>
<form method="post" action="index.php?acao=salvar_categoria" class="row g-2 mb-4">
    <input type="hidden" name="csrf" value="<?= $esc($_SESSION['csrf']) ?>">
    <input type="hidden" name="id" value="<?= (int)($editando['id']??0) ?>">
    <div class="col-md-8">
        <label for="nome" class="form-label">Nome da categoria</label>
        <input id="nome" class="form-control" name="nome" maxlength="50" required value="<?= $esc($editando['nome']??'') ?>">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <button class="btn btn-primary"><?= $editando?'Salvar alteração':'Adicionar categoria' ?></button>
    </div>
</form>
<table class="table">
    <thead>
        <tr>
            <th>Categoria</th>
            <th>Livros</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categorias as $c): ?>
            <tr>
                <td><?= $esc($c['nome']) ?></td>
                <td><?= (int)$c['total_livros'] ?></td>
                <td><a class="btn btn-sm btn-outline-primary" href="index.php?acao=gerenciar_categorias&id=<?= (int)$c['id'] ?>">Editar</a>
                    <form method="post" action="index.php?acao=excluir_categoria" class="d-inline" onsubmit="return confirm('Excluir esta categoria?')">
                        <input type="hidden" name="csrf" value="<?= $esc($_SESSION['csrf']) ?>">
                        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<p class="small text-secondary">A exclusão de uma categoria remove suas associações com livros. Os livros não são excluídos.</p>
<?php require __DIR__.'/footer.php'; ?>