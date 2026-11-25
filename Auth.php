<?php
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/Logger.php';
class Auth {
    public static function usuario(): ?array { return $_SESSION['usuario'] ?? null; }
    public static function admin(): bool { return (self::usuario()['perfil'] ?? '') === 'administrador'; }
    public static function exigirAdmin(): void {
        if (!self::usuario()) {
            Logger::registrar('ACESSO_NEGADO', ['resultado'=>'sem_login']);
            header('Location: index.php?acao=login'); exit;
        }
        if (!self::admin()) {
            Logger::registrar('ACESSO_NEGADO', ['resultado'=>'sem_permissao']);
            http_response_code(403); exit('Acesso restrito a administradores.');
        }
    }
    public static function entrar(string $email, string $senha): bool {
        $stmt=Conexao::getConexao()->prepare('SELECT id,nome,email,senha_hash,perfil,foto FROM usuarios WHERE email=? LIMIT 1');
        $stmt->execute([$email]); $usuario=$stmt->fetch();
        if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
            Logger::registrar('LOGIN', ['resultado'=>'falha']); return false;
        }
        session_regenerate_id(true);
        $_SESSION['usuario']=[
            'id'=>(int)$usuario['id'], 'nome'=>$usuario['nome'],
            'email'=>$usuario['email'], 'perfil'=>$usuario['perfil'],
            'foto'=>$usuario['foto']
        ];
        Logger::registrar('LOGIN', ['resultado'=>'sucesso']);
        return true;
    }
    public static function cadastrar(string $nome, string $email, string $senha, ?string $foto): void {
        $stmt=Conexao::getConexao()->prepare("INSERT INTO usuarios (nome,email,senha_hash,perfil,foto) VALUES (?,?,?,'cliente',?)");
        $stmt->execute([$nome,$email,password_hash($senha,PASSWORD_DEFAULT),$foto]);
        Logger::registrar('CADASTRO_USUARIO', ['resultado'=>'sucesso']);
    }
    public static function sair(): void {
        Logger::registrar('LOGOUT', ['resultado'=>'sucesso']);
        $_SESSION=[];
        if (ini_get('session.use_cookies')) {
            $p=session_get_cookie_params();
            setcookie(session_name(),'',[
                'expires'=>time()-42000,'path'=>$p['path'],'domain'=>$p['domain'],
                'secure'=>$p['secure'],'httponly'=>$p['httponly'],'samesite'=>$p['samesite']??'Lax'
            ]);
        }
        session_destroy();
    }
}
