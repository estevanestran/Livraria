<?php
$tituloPagina = 'Recomendações com IA | Livraria Virtual';
$descricaoPagina = 'Receba sugestões de livros disponíveis no catálogo usando inteligência artificial.';
require __DIR__.'/head.php';
$manifesto = __DIR__.'/../react/dist/.vite/manifest.json';
$manifest = is_file($manifesto) ? json_decode(file_get_contents($manifesto), true) : null;
$entry = $manifest['index.html'] ?? $manifest['src/main.jsx'] ?? null;
?>
<header class="d-flex justify-content-between align-items-center mb-4">
  <h1>Recomendações literárias</h1>
  <a class="btn btn-outline-secondary" href="index.php">Voltar ao catálogo</a>
</header>
<?php if (!$entry): ?>
<div class="alert alert-warning">O React ainda não foi compilado. Entre na pasta <code>react</code>, execute <code>npm install</code> e depois <code>npm run build</code>.</div>
<?php else: ?>
<div id="recomendacoes-root" data-csrf="<?= $esc($_SESSION['csrf']) ?>"></div>
<?php foreach (($entry['css'] ?? []) as $css): ?>
<link rel="stylesheet" href="<?= $esc('react/dist/'.$css) ?>">
<?php endforeach; ?>
<script type="module" src="<?= $esc('react/dist/'.$entry['file']) ?>"></script>
<?php endif; ?>
<?php require __DIR__.'/footer.php'; ?>
