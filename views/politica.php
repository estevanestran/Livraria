<?php
$tituloPagina=($tipo==='termos'?'Termos de serviço':'Política de plágio').' | Livraria Virtual';
require __DIR__.'/head.php'; ?>
<a href="index.php" class="btn btn-link">Voltar ao catálogo</a>
<?php if ($tipo==='termos'): ?>
    
<h1>Termos de Uso e Política de Privacidade</h1>
<p class="text-muted">Última atualização: 27 de novembro de 2026</p>

<section class="mb-4">
    <h2 class="h4">1. Apresentação e finalidade</h2>
    <p>
        A Livraria Virtual é uma aplicação web desenvolvida para fins
        acadêmicos, com o objetivo de simular o funcionamento de uma
        loja virtual de livros. O sistema oferece funcionalidades de
        consulta de obras literárias, cadastro de usuários, carrinho
        de compras, pedidos simulados e recomendações personalizadas
        por inteligência artificial.
    </p>
    <p>
        A utilização da plataforma implica ciência das condições
        apresentadas neste documento. Por se tratar de um projeto
        demonstrativo, não são realizados pagamentos, vendas ou
        entregas reais.
    </p>
</section>

<section class="mb-4">
    <h2 class="h4">2. Cadastro e responsabilidades dos usuários</h2>
    <p>
        Para acessar determinadas funcionalidades, como finalizar
        pedidos e consultar o histórico de compras, é necessário
        realizar um cadastro. O usuário deverá fornecer informações
        corretas e manter suas credenciais de acesso em segurança.
    </p>
    <p>
        É proibido utilizar a plataforma para atividades ilícitas,
        acessar contas de terceiros sem autorização, explorar
        vulnerabilidades ou comprometer o funcionamento do sistema.
        As senhas são armazenadas de forma protegida por meio de
        mecanismos de hash.
    </p>
</section>

<section class="mb-4">
    <h2 class="h4">3. Coleta e tratamento de dados pessoais</h2>
    <p>
        Para disponibilizar suas funcionalidades, a plataforma
        poderá coletar e armazenar nome, endereço de e-mail, senha
        protegida por hash, fotografia de perfil, histórico de
        pedidos e mensagens enviadas pelo formulário de contato.
        Também são mantidos registros técnicos de determinadas
        operações para fins de segurança e diagnóstico.
    </p>
    <p>
        O tratamento dessas informações busca observar os princípios
        de finalidade, necessidade e transparência estabelecidos
        pela Lei Geral de Proteção de Dados Pessoais (LGPD),
        Lei nº 13.709/2018.
    </p>
</section>

<section class="mb-4">
    <h2 class="h4">4. Segurança, sessões e serviços externos</h2>
    <p>
        A Livraria Virtual utiliza cookies de sessão para manter
        a autenticação e o funcionamento de recursos como o carrinho
        de compras. São adotadas medidas de segurança, incluindo
        validação de formulários, controle de permissões, proteção
        contra requisições não autorizadas e registros de operações.
    </p>
    <p>
        A aplicação também utiliza serviços externos para hospedagem,
        envio de e-mails e outras funcionalidades. As informações
        necessárias ao funcionamento desses serviços poderão ser
        transmitidas aos respectivos fornecedores. Apesar das
        medidas adotadas, não é possível garantir segurança ou
        disponibilidade absoluta.
    </p>
</section>

<section class="mb-4">
    <h2 class="h4">5. Recomendações com inteligência artificial</h2>
    <p>
        O sistema oferece recomendações literárias utilizando a
        API Gemini, do Google. Para gerar sugestões, são enviadas
        ao serviço as preferências informadas pelo usuário e
        informações dos livros cadastrados no catálogo.
    </p>
    <p>
        As recomendações são produzidas automaticamente e podem
        apresentar imprecisões ou indisponibilidade temporária.
        Recomenda-se que o usuário não informe dados pessoais
        sensíveis no campo destinado às preferências literárias.
    </p>
</section>

<section class="mb-4">
    <h2 class="h4">6. Direitos autorais e propriedade intelectual</h2>
    <p>
        Os títulos, capas, descrições e demais conteúdos relacionados
        às obras literárias estão sujeitos aos direitos de seus
        respectivos titulares. Sua utilização deverá respeitar
        a legislação aplicável e as autorizações necessárias.
    </p>
    <p>
        É proibida a reprodução, distribuição ou utilização
        indevida de conteúdos protegidos por direitos autorais.
        Informações adicionais estão disponíveis na
        <a href="index.php?acao=plagio">Política de Plágio</a>
        da plataforma.
    </p>
</section>

<section class="mb-4">
    <h2 class="h4">7. Direitos dos usuários, alterações e contato</h2>
    <p>
        Os usuários poderão solicitar informações sobre o
        tratamento de seus dados pessoais, sua correção ou
        exclusão, quando aplicável, conforme os direitos
        previstos na LGPD. As solicitações poderão ser
        encaminhadas pelo formulário
        <a href="index.php?acao=contato">Fale Conosco</a>.
    </p>
    <p>
        Estes termos poderão ser atualizados para acompanhar
        alterações nas funcionalidades do sistema ou nas
        disposições legais aplicáveis. A versão vigente
        será disponibilizada nesta página, acompanhada
        de sua data de atualização.
    </p>
    <p>
        <strong>Responsável pelo projeto:</strong>
        Estevan Estran Pinheiro.
    </p>
</section>

<?php else: ?>
    <h1>Política de plágio e direitos autorais</h1>
    <p>É proibido publicar, reproduzir ou distribuir textos, imagens, descrições e outros materiais protegidos sem autorização ou fundamento legal.</p>
    <p>As informações de livros devem respeitar a autoria original e, quando necessário, indicar a fonte ou o detentor dos direitos. O uso de capas e descrições deve observar as licenças e permissões aplicáveis.</p>
    <p>Conteúdos que violem direitos autorais poderão ser removidos pelos administradores do sistema.</p>
<?php endif; ?>
<?php require __DIR__.'/footer.php'; ?>