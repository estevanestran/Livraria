<?php
ini_set('session.use_strict_mode','1');
ini_set('session.use_only_cookies','1');
ini_set('session.cookie_httponly','1');
ini_set('session.cookie_samesite','Lax');
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ini_set('session.cookie_secure','1');
session_start();
require_once __DIR__.'/Auth.php';
require_once __DIR__.'/Foto.php';
require_once __DIR__.'/Recuperacao.php';
require_once __DIR__.'/LivroController.php';
if (empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32));
function validarPost(): void {
    if ($_SERVER['REQUEST_METHOD']!=='POST' || !hash_equals($_SESSION['csrf'],(string)($_POST['csrf']??''))) {
        Logger::registrar('CSRF_BLOQUEADO', ['resultado'=>'negado']);
        http_response_code(403); exit('Requisição inválida.');
    }
}
$acao=(string)($_GET['acao']??'index');
// Log de TODAS as requisições ao roteador, sem registrar e-mails, senhas ou tokens.
Logger::registrar('ROTA', ['rota'=>substr($acao,0,60), 'metodo'=>$_SERVER['REQUEST_METHOD']??'GET']);
if (in_array($acao,['salvar','deletar','entrar','registrar','sair','enviar_recuperacao','salvar_senha'],true)) validarPost();
if (in_array($acao,['novo','editar','salvar','deletar'],true)) Auth::exigirAdmin();
$controller=new LivroController();
switch ($acao) {
    case 'login':
    case 'cadastro':
        if (Auth::usuario()) {header('Location: index.php'); exit;}
        $modo=$acao==='login'?'login':'cadastro'; $erro=''; require __DIR__.'/views/auth.php'; break;
    case 'entrar':
        $email=trim((string)($_POST['email']??'')); $senha=(string)($_POST['senha']??'');
        if (Auth::entrar($email,$senha)) {header('Location: index.php'); exit;}
        $modo='login'; $erro='E-mail ou senha incorretos.'; require __DIR__.'/views/auth.php'; break;
    case 'registrar':
        $nome=trim((string)($_POST['nome']??'')); $email=trim((string)($_POST['email']??''));
        $senha=(string)($_POST['senha']??''); $confirmacao=(string)($_POST['confirmacao']??'');
        $modo='cadastro'; $erro=''; $foto=null;
        if (mb_strlen($nome)<2 || mb_strlen($nome)>150 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>190 || strlen($senha)<8 || $senha!==$confirmacao) {
            $erro='Confira nome, e-mail e senha (mínimo de 8 caracteres; confirmação igual).';
        } else {
            try {
                if (!empty($_FILES['foto']['name'])) $foto=Foto::receber($_FILES['foto']);
                Auth::cadastrar($nome,$email,$senha,$foto);
                header('Location: index.php?acao=login&cadastrado=1'); exit;
            } catch (Throwable $e) {
                if ($foto && is_file(__DIR__.'/'.$foto)) unlink(__DIR__.'/'.$foto);
                $erro=$e instanceof PDOException ? 'Não foi possível cadastrar. Verifique se o e-mail já está em uso.' : $e->getMessage();
                Logger::registrar('CADASTRO_USUARIO', ['resultado'=>'falha']);
            }
        }
        require __DIR__.'/views/auth.php'; break;
    case 'esqueci':
        $modo='solicitar'; $mensagem=''; require __DIR__.'/views/recuperacao.php'; break;
    case 'enviar_recuperacao':
        $modo='solicitar'; $email=trim((string)($_POST['email']??''));
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)) { $mensagem='Informe um e-mail válido.'; }
        else {
            try { Recuperacao::solicitar($email); $mensagem='Se o e-mail estiver cadastrado, você receberá as instruções de recuperação.'; }
            catch (Throwable $e) { $mensagem='O envio de e-mail não está disponível neste servidor. Verifique a configuração de e-mail.'; Logger::registrar('RECUPERACAO_SOLICITADA',['resultado'=>'erro_servidor']); }
        }
        require __DIR__.'/views/recuperacao.php'; break;
    case 'redefinir':
        $modo='redefinir'; $token=(string)($_GET['token']??'');
        $mensagem=preg_match('/^[a-f0-9]{64}$/',$token)?'':'Link inválido.';
        header('Referrer-Policy: no-referrer');
        require __DIR__.'/views/recuperacao.php'; break;
    case 'salvar_senha':
        $modo='redefinir'; $token=(string)($_POST['token']??''); $senha=(string)($_POST['senha']??'');
        $mensagem='';
        if (strlen($senha)<8 || $senha!==(string)($_POST['confirmacao']??'')) $mensagem='A senha deve ter ao menos 8 caracteres e a confirmação deve coincidir.';
        elseif (Recuperacao::redefinir($token,$senha)) { header('Location: index.php?acao=login&redefinida=1'); exit; }
        else $mensagem='Link inválido, expirado ou já utilizado.';
        header('Referrer-Policy: no-referrer');
        require __DIR__.'/views/recuperacao.php'; break;
    case 'sair': Auth::sair(); header('Location: index.php'); exit;
    case 'novo': $controller->formCriar(); break;
    case 'editar': $controller->formEditar((int)($_GET['id']??0)); break;
    case 'salvar': $controller->salvar(); break;
    case 'deletar': $controller->deletar(); break;
    case 'index': $controller->index(); break;
    default: http_response_code(404); echo 'Página não encontrada.';
}
