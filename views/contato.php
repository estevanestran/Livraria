<?php $tituloPagina='Fale Conosco | Livraria Virtual'; 
$descricaoPagina='Entre em contato com a Livraria Virtual.'; 
require __DIR__.'/head.php'; ?>
<main style="max-width:720px" class="mx-auto">
    <h1>Fale Conosco</h1>
    <p>Envie sua mensagem pelo formulário abaixo.</p>
    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger"><?= $esc($erro) ?></div>
    <?php endif; ?>
    <?php if (!empty($sucesso)): ?>
        <div class="alert alert-success"><?= $esc($sucesso) ?></div>
    <?php endif; ?>
    <form method="post" action="index.php?acao=enviar_contato">
        <input type="hidden" name="csrf" value="<?= $esc($_SESSION['csrf']) ?>">
        <?php $usuarioContato=Auth::usuario(); ?>
        <?php if ($usuarioContato): ?>
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input class="form-control" id="nome" value="<?= $esc($usuarioContato['nome']) ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input class="form-control" type="email" id="email" value="<?= $esc($usuarioContato['email']) ?>" readonly>
            </div>
        <?php else: ?>
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input class="form-control" id="nome" name="nome" maxlength="150" minlength="2" required value="<?= $esc($dados['nome']??'') ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input class="form-control" type="email" id="email" name="email" maxlength="190" required value="<?= $esc($dados['email']??'') ?>">
        </div>
        <?php endif; ?>
        <div class="mb-3">
            <label for="assunto" class="form-label">Assunto</label>
            <input class="form-control" id="assunto" name="assunto" maxlength="150" minlength="3" required value="<?= $esc($dados['assunto']??'') ?>">
        </div>
        <div class="mb-3">
            <label for="mensagem" class="form-label">Mensagem</label>
            <textarea class="form-control" id="mensagem" name="mensagem" rows="6" minlength="10" maxlength="5000" required><?= $esc($dados['mensagem']??'') ?></textarea>
        </div>
        <button class="btn btn-primary">Enviar mensagem</button>
    </form>
</main>
<?php require __DIR__.'/footer.php'; ?>
