<?php
$tituloPagina='Catálogo de livros | Livraria Virtual';
$descricaoPagina='Pesquise livros por título, autor e categoria.';
require __DIR__.'/head.php';
?>
<header class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4">
    <h1>Livraria Virtual</h1>
    <nav class="d-flex flex-wrap gap-2 align-items-center">
        <a href="index.php" class="btn btn-outline-primary">Todos os livros</a>
        <a href="index.php?acao=recomendacoes" class="btn btn-outline-primary">Recomendações com IA</a>
        <a href="index.php?acao=contato" class="btn btn-outline-primary">Fale Conosco</a>
        <a href="index.php?acao=categorias" class="btn btn-outline-primary">Por categoria</a>
        <a href="index.php?acao=carrinho" class="btn btn-outline-primary">Carrinho (<?= array_sum(CarrinhoController::itens()) ?>)</a>
        <?php if (Auth::usuario()): ?>
            <a href="index.php?acao=meus_pedidos" class="btn btn-outline-primary">Meus pedidos</a>
        <?php endif; ?>
        <?php if (Auth::admin()): ?>
            <a href="index.php?acao=novo" class="btn btn-success">Novo livro</a>
            <a href="index.php?acao=gerenciar_categorias" class="btn btn-outline-dark">Gerenciar categorias</a>
        <?php endif; ?>
        <?php if (Auth::usuario()): ?>
            <span>Olá, <?= $esc(Auth::usuario()['nome']) ?></span>
            <form method="post" action="index.php?acao=sair">
                <input type="hidden" name="csrf" value="<?= $esc($_SESSION['csrf']) ?>">
                <button class="btn btn-outline-secondary">Sair</button>
            </form>
        <?php else: ?>
            <a href="index.php?acao=login" class="btn btn-outline-secondary">Entrar</a>
            <a href="index.php?acao=cadastro" class="btn btn-primary">Cadastrar</a>
        <?php endif; ?>
    </nav>
</header>
<form class="row g-2 mb-4" method="get" action="index.php">
    <div class="col-md-5">
        <label for="busca" class="form-label">Pesquisar título ou autor</label>
        <input class="form-control" id="busca" name="busca" maxlength="100" value="<?= $esc($busca) ?>" placeholder="Digite sua pesquisa">
    </div>
    <div class="col-md-5">
        <label for="categoria" class="form-label">Gênero literário</label>
        <select class="form-select" id="categoria" name="categoria">
            <option value="0">Todos os gêneros</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>" <?= $categoriaId===(int)$cat['id']?'selected':'' ?>><?= $esc($cat['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100">Buscar</button>
    </div>
</form>
<p><?= count($livros) ?> livro(s) encontrado(s).</p>
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
    <?php foreach ($livros as $l): ?>
        <div class="col">
            <article class="card h-100">
                <?php if (!empty($l['imagem_capa'])): ?>
                    <img class="card-img-top" style="height:220px;object-fit:contain" src="<?= $esc($l['imagem_capa']) ?>" alt="Capa de <?= $esc($l['titulo']) ?>">
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                    <h2 class="h5"><?= $esc($l['titulo']) ?></h2>
                    <p class="text-secondary mb-1"><?= $esc($l['autor']??'Autor não informado') ?></p>
                    <p class="small"><?= $esc($l['categorias_nomes']??'Sem categoria') ?></p>
                    <p class="fw-bold">R$ <?= number_format((float)$l['preco'],2,',','.') ?></p>
                    <a class="btn btn-primary mt-auto" href="index.php?acao=livro&id=<?= (int)$l['id'] ?>">Ver detalhes</a>
                    <?php if ($l['status']==='Disponível' && (int)$l['estoque']>0): ?>
                        <form method="post" action="index.php?acao=adicionar_carrinho" class="mt-2">
                            <input type="hidden" name="csrf" value="<?= $esc($_SESSION['csrf']) ?>">
                            <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
                            <button class="btn btn-outline-success w-100">Adicionar ao carrinho</button>
                        </form>
                    <?php endif; ?>
                    <?php if (Auth::admin()): ?>
                        <a class="btn btn-outline-secondary mt-2" href="index.php?acao=editar&id=<?= (int)$l['id'] ?>">Editar</a>
                        <form class="mt-2" method="post" action="index.php?acao=deletar" onsubmit="return confirm('Excluir livro?')">
                            <input type="hidden" name="csrf" value="<?= $esc($_SESSION['csrf']) ?>">
                            <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
                            <button class="btn btn-outline-danger w-100">Excluir</button>
                        </form>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
</div>
<?php require __DIR__.'/footer.php'; ?>
