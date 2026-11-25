<?php
$tituloPagina=($tipo==='termos'?'Termos de serviço':'Política de plágio').' | Livraria Virtual';
require __DIR__.'/head.php'; ?>
<a href="index.php" class="btn btn-link">Voltar ao catálogo</a>
<?php if ($tipo==='termos'): ?>
<h1>Termos de serviço e privacidade</h1>
<p>Esta livraria é um projeto acadêmico demonstrativo. Não são processados pagamentos reais.</p>
<h2 class="h4">Contas e dados</h2><p>Para criar uma conta, são coletados nome, e-mail, senha protegida por hash e, opcionalmente, foto de perfil. Esses dados são utilizados para autenticação e funcionamento da aplicação.</p>
<h2 class="h4">Sessões e registros</h2><p>O sistema utiliza cookies de sessão para manter o login e arquivos de log para registrar ações e auxiliar na segurança. Não registramos senhas nos logs.</p>
<h2 class="h4">Direitos dos usuários</h2><p>Solicitações relacionadas a acesso, correção ou exclusão de dados pessoais devem ser encaminhadas ao responsável pelo projeto. O canal de contato será disponibilizado na etapa de Fale Conosco.</p>
<h2 class="h4">Limitações</h2><p>Os livros e imagens exibidos são utilizados apenas para fins de demonstração. O sistema não constitui uma loja comercial em operação.</p>
<?php else: ?>
<h1>Política de plágio e direitos autorais</h1>
<p>É proibido publicar, reproduzir ou distribuir textos, imagens, descrições e outros materiais protegidos sem autorização ou fundamento legal.</p>
<p>As informações de livros devem respeitar a autoria original e, quando necessário, indicar a fonte ou o detentor dos direitos. O uso de capas e descrições deve observar as licenças e permissões aplicáveis.</p>
<p>Conteúdos que violem direitos autorais poderão ser removidos pelos administradores do sistema.</p>
<?php endif; ?>
<p class="small text-secondary">Texto inicial para fins acadêmicos; deve ser revisado e complementado antes da publicação real, inclusive quanto à identificação do controlador e ao procedimento de exercício de direitos LGPD.</p>
<?php require __DIR__.'/footer.php'; ?>