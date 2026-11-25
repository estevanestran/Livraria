<?php
$tituloPagina=$tituloPagina??'Livraria Virtual';
$descricaoPagina=$descricaoPagina??'Catálogo de livros da Livraria Virtual.';
$urlPagina='http'.((!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'s':'').'://'.($_SERVER['HTTP_HOST']??'localhost').($_SERVER['REQUEST_URI']??'/');
$esc=static fn($v)=>htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');
?><!doctype html><html lang="pt-BR"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $esc($tituloPagina) ?></title><meta name="description" content="<?= $esc($descricaoPagina) ?>">
<meta name="author" content="Livraria Virtual"><meta name="copyright" content="Conteúdo sujeito aos direitos de seus respectivos autores.">
<meta name="plagiarism" content="Proibida a reprodução não autorizada de conteúdo; consulte a política de plágio.">
<meta property="og:type" content="website"><meta property="og:title" content="<?= $esc($tituloPagina) ?>">
<meta property="og:description" content="<?= $esc($descricaoPagina) ?>">
<meta property="og:url" content="<?= $esc($urlPagina) ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head><body class="container py-4">
