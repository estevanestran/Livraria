# Livraria Virtual

Aplicação web de livraria desenvolvida como projeto acadêmico para a disciplina de Desenvolvimento Web. O sistema permite consultar livros, organizar categorias, gerenciar contas, adicionar produtos ao carrinho, registrar pedidos e receber recomendações literárias com inteligência artificial.

## Acesso à aplicação

- **Sistema publicado:** https://livrariadevwebpa1.websiteseguro.com
- **E-mail administrador:** emailadmin@livraria.host
- **Senha administrador:** 1Senha3Admin*

## Funcionalidades

- **Catálogo público:** listagem de livros, pesquisa por título ou autor, filtros por categorias e página dinâmica de detalhes de cada livro.
- **Administração:** cadastro, edição e exclusão de livros e categorias, com acesso restrito a administradores.
- **Usuários:** cadastro com validações no frontend e backend, foto de perfil enviada como arquivo, login, logout e recuperação de senha por e-mail.
- **Carrinho e pedidos:** adição e remoção de produtos, alteração de quantidades, validação de estoque, finalização sem pagamento real e histórico de pedidos do usuário.
- **Fale Conosco:** formulário com validações, registro no banco e envio por e-mail.
- **Inteligência artificial:** recomendações de livros existentes no catálogo com base nas preferências informadas pelo usuário, utilizando a API Gemini.
- **Páginas institucionais:** termos de serviço e política de plágio.
- **Segurança e auditoria:** controle de permissões, tokens CSRF, consultas preparadas com PDO, hash de senhas, proteção de arquivos privados e logs de rotas e ações.

## Tecnologias utilizadas

| Camada | Tecnologias |
| --- | --- |
| Backend | PHP, PDO, arquitetura MVC simplificada |
| Banco de dados | MySQL / MariaDB |
| Interface web | HTML, CSS, JavaScript, Bootstrap |
| Interface de recomendações | React, React Router, Vite |
| Comunicação assíncrona | AJAX com `fetch()` e respostas JSON |
| Envio de e-mails | PHPMailer, SMTP do Gmail |
| Inteligência artificial | API Gemini |
| Gerenciamento de dependências | Composer, npm |
| Hospedagem | Locaweb (Linux, PHP e MySQL) |

## Banco de dados

O projeto utiliza oito tabelas:

| Tabela | Finalidade |
| --- | --- |
| `usuarios` | Contas e perfis de acesso |
| `livros` | Informações dos livros, preços e estoque |
| `categorias` | Gêneros e categorias de livros |
| `livro_categoria` | Associação N:N entre livros e categorias |
| `pedidos` | Pedidos realizados pelos usuários |
| `pedido_itens` | Itens de pedidos; associação N:N entre pedidos e livros |
| `recuperacao_senha` | Solicitações e tokens temporários de redefinição de senha |
| `contatos` | Mensagens recebidas pelo formulário Fale Conosco |

Os relacionamentos incluem **usuários → pedidos (1:N)**, **livros ↔ categorias (N:N)** e **pedidos ↔ livros (N:N, via `pedido_itens`)**.

## Organização do código

```text
livraria/
├── index.php                 # Roteamento das ações PHP
├── conexao.php               # Conexão PDO com o banco de dados
├── Auth.php                  # Autenticação e permissões
├── Livro.php                 # Modelo de livros e categorias
├── LivroController.php       # Controle do catálogo e cadastros
├── CarrinhoController.php    # Carrinho e finalização de pedidos
├── Pedido.php                # Persistência dos pedidos
├── Contato.php               # Registro e envio de contatos
├── Email.php                 # Integração com PHPMailer
├── Recuperacao.php           # Recuperação de senha
├── Recomendacoes.php         # Endpoint PHP e integração com Gemini
├── Logger.php                # Registro de logs
├── Foto.php                  # Upload e validação de fotos
├── config/                   # Modelos e configurações locais protegidas
├── views/                    # Páginas PHP
├── react/
│   ├── src/main.jsx          # React, React Router e chamadas fetch
│   └── dist/                 # Arquivos compilados pelo Vite
├── uploads/usuarios/         # Fotos de perfil (não versionar fotos reais)
├── logs/                     # Logs da aplicação (não versionar logs reais)
├── composer.json
└── README.md
```

## Executar localmente (Windows / XAMPP)

**Pré-requisitos:** XAMPP com PHP 8.1 ou superior e MySQL/MariaDB, Composer, Node.js e npm.

1. Coloque os arquivos do projeto em `C:\xampp\htdocs\livraria` e inicie **Apache** e **MySQL** no XAMPP.
2. Crie um banco MySQL e importe o arquivo SQL de entrega, disponibilizado junto ao projeto.
3. Configure `conexao.php` para utilizar os dados do banco local.
4. Na raiz do projeto, execute:

   ```bash
   composer install
   ```

5. Para compilar a funcionalidade em React, execute:
   ```bash
   cd react
   npm ci
   npm run build
   ```

6. Configure `config/email.local.php` seguindo `config/email.exemplo.php`, com a conta Gmail e uma **senha de aplicativo** válida.
7. Configure `config/ia.local.php` seguindo `config/ia.exemplo.php`, com uma chave da API Gemini e um modelo disponível para sua conta.
8. Acesse `http://localhost/livraria/`.

> A aplicação pode abrir sem configurar imediatamente os serviços de e-mail e IA, mas essas funcionalidades específicas exigem credenciais válidas e acesso à internet. A recuperação de senha também exige uma URL base correta para gerar o link enviado por e-mail.

## React, SPA e AJAX

A funcionalidade de recomendações utiliza **React Router** para navegar entre as telas de recomendações e explicação, sem recarregar o documento HTML, caracterizando uma **SPA nessa parte do sistema**. O restante da livraria usa páginas PHP tradicionais.

No arquivo `react/src/main.jsx`, o `fetch()` realiza a chamada AJAX ao endpoint `index.php?acao=api_recomendacoes`. O backend, implementado em `Recomendacoes.php`, consulta os livros no MySQL e se comunica com a API Gemini.

## Segurança e boas práticas

- Senhas de usuários são armazenadas por meio de hash; o projeto não deve guardar senhas em texto puro no banco.
- Requisições que modificam dados utilizam validação de token CSRF.
- Operações de banco usam PDO e consultas preparadas.
- O envio de arquivos de foto valida formato e tamanho.
- Acesso a páginas administrativas é restrito ao perfil de administrador.
- **Não versionar** `config/*.local.php`, `.env`, logs, fotos de usuários ou dados pessoais de teste.
- Não publicar chaves do Gemini, senhas de aplicativo Gmail, senhas MySQL nem um SQL contendo dados pessoais reais ou tokens de recuperação.
- A integração com a API Gemini depende da disponibilidade e das cotas de uso do modelo selecionado; falhas temporárias de serviço podem ocorrer.