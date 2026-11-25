<!doctype html
><html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title><?= $modo==='login'?'Entrar':'Criar conta' ?> - Livraria</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="container py-5">
        <main class="mx-auto" style="max-width:460px">
            <h1><?= $modo==='login'?'Entrar':'Criar conta' ?></h1>
            <p><a href="index.php">Voltar ao catálogo</a></p>
<?php if ($erro): ?>
    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($erro,ENT_QUOTES,'UTF-8') ?></div>
<?php endif; ?>
<?php if ($modo==='login' && isset($_GET['redefinida'])): ?>
    <div class="alert alert-success">Senha atualizada. Faça login.</div>
<?php endif; ?>
<?php if ($modo==='login' && isset($_GET['cadastrado'])): ?>
    <div class="alert alert-success">Conta criada. Faça login.</div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" action="index.php?acao=<?= $modo==='login'?'entrar':'registrar' ?>">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'],ENT_QUOTES,'UTF-8') ?>">
<?php if ($modo==='cadastro'): ?>
    <div class="mb-3">
        <label class="form-label" for="nome">Nome</label>
        <input class="form-control" id="nome" name="nome" maxlength="150" required value="<?= htmlspecialchars((string)($_POST['nome']??''),ENT_QUOTES,'UTF-8') ?>">
    </div>
<?php endif; ?>
<div class="mb-3">
    <label class="form-label" for="email">E-mail</label>
    <input type="email" class="form-control" id="email" name="email" maxlength="190" required value="<?= htmlspecialchars((string)($_POST['email']??''),ENT_QUOTES,'UTF-8') ?>">
</div>
<div class="mb-3">
    <label class="form-label" for="senha">Senha</label>
    <input type="password" class="form-control" id="senha" name="senha" <?= $modo==='cadastro'?'minlength="8"':'' ?> required>
</div>
<?php if ($modo==='cadastro'): ?>
    <div class="mb-3">
        <label class="form-label" for="foto">Foto de perfil (opcional, JPG/PNG/WebP, até 2 MB)</label>
        <input type="file" class="form-control" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
    </div>
    <div class="mb-3">
        <label class="form-label" for="confirmacao">Confirmar senha</label>
        <input type="password" class="form-control" id="confirmacao" name="confirmacao" minlength="8" required>
    </div>
    <?php endif; ?>
<button class="btn btn-primary" type="submit"><?= $modo==='login'?'Entrar':'Cadastrar' ?></button>
</form>
<?php if ($modo==='login'): ?>
    <p class="mt-3"><a href="index.php?acao=esqueci">Esqueci minha senha</a></p>
<?php endif; ?>
<p class="mt-3"><?php if ($modo==='login'): ?><a href="index.php?acao=cadastro">Não tem conta? Cadastre-se</a><?php else: ?><a href="index.php?acao=login">Já tem conta? Entre</a><?php endif; ?></p>
</main>
</body>
</html>
