<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $livro ? 'Editar' : 'Novo' ?> livro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h1 class="mb-4"><?= $livro ? 'Editar livro' : 'Cadastrar livro' ?></h1>
    <form method="post" action="index.php?acao=salvar">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
    <input type="hidden" name="id" value="<?= (int)($livro['id']??0) ?>">
    <?php foreach (['isbn'=>'ISBN','titulo'=>'Título','autor'=>'Autor','imagem_capa'=>'URL ou caminho da capa'] as $campo=>$rotulo): ?>
        <div class="mb-3">
            <label class="form-label" for="<?= $campo ?>"><?= $rotulo ?></label>
            <input class="form-control" id="<?= $campo ?>" name="<?= $campo ?>" value="<?= htmlspecialchars((string)($livro[$campo]??'')) ?>" <?= in_array($campo,['isbn','titulo'])?'required':'' ?>>
        </div>
    <?php endforeach; ?>
    <div class="mb-3">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" class="form-control" rows="4"><?= htmlspecialchars((string)($livro['descricao']??'')) ?></textarea>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Preço (R$)</label>
            <input name="preco" type="number" min="0" step="0.01" required class="form-control" value="<?= htmlspecialchars((string)($livro['preco']??'0.00')) ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Estoque</label>
            <input name="estoque" type="number" min="0" step="1" required class="form-control" value="<?= (int)($livro['estoque']??0) ?>">
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <?php foreach (['Disponível','Indisponível'] as $opcao): ?>
                <option value="<?= $opcao ?>" <?= ($livro['status']??'Disponível')===$opcao?'selected':'' ?>><?= $opcao ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <fieldset class="mb-4">
        <legend class="fs-5">Categorias</legend>
        <?php foreach ($categorias as $cat): ?>
            <label class="d-block">
                <input type="checkbox" name="categorias[]" value="<?= (int)$cat['id'] ?>" <?= in_array($cat['id'],$livro['categorias']??[]) ? 'checked':'' ?>> 
                <?= htmlspecialchars($cat['nome']) ?>
            </label>
        <?php endforeach; ?>
    </fieldset>
    <button class="btn btn-primary">Salvar livro</button> 
    <a class="btn btn-secondary" href="index.php">Cancelar</a>
</form>
</body>
</html>