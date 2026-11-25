<?php
require_once __DIR__.'/conexao.php';
require_once __DIR__.'/Logger.php';
require_once __DIR__.'/Email.php';
final class Recuperacao {
    public static function solicitar(string $email): void {
        $db = Conexao::getConexao();
        $st = $db->prepare('SELECT id FROM usuarios WHERE email=? LIMIT 1');
        $st->execute([$email]); $id = $st->fetchColumn();
        if (!$id) { Logger::registrar('RECUPERACAO_SOLICITADA', ['resultado'=>'processada']); return; }
        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);
        $st = $db->prepare('INSERT INTO recuperacao_senha (usuario_id,token_hash,expira_em) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))');
        $st->execute([$id,$hash]); $registroId = $db->lastInsertId();
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // Não aceitar Host arbitrário na criação de links enviados por e-mail.
        $permitido = preg_match('/^(localhost|127\.0\.0\.1)(:[0-9]{1,5})?$/i', $host);
        $base = getenv('LIVRARIA_URL_BASE');
        if (!$base && $permitido) $base = ($https ? 'https' : 'http').'://'.$host.rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        if (!$base || !filter_var($base, FILTER_VALIDATE_URL)) {
            $db->prepare('DELETE FROM recuperacao_senha WHERE id=?')->execute([$registroId]);
            throw new RuntimeException('Configure LIVRARIA_URL_BASE para enviar e-mails.');
        }
        $link = rtrim($base, '/') . '/index.php?acao=redefinir&token=' . urlencode($token);
        $assunto = 'Recuperacao de senha - Livraria';
        $mensagem = "Solicitou redefinir a senha? Abra este link (valido por 30 minutos):\n".$link."\n\nSe nao foi voce, ignore esta mensagem.";
        try {
            Email::enviar($email, $assunto, $mensagem);
        } catch (Throwable $e) {
            $db->prepare('DELETE FROM recuperacao_senha WHERE id=?')->execute([$registroId]);
            Logger::registrar('RECUPERACAO_SOLICITADA', ['resultado'=>'falha_envio']);
            throw $e;
        }
        Logger::registrar('RECUPERACAO_SOLICITADA', ['resultado'=>'processada']);
    }
    public static function redefinir(string $token, string $senha): bool {
        if (!preg_match('/^[a-f0-9]{64}$/', $token) || strlen($senha)<8) return false;
        $db=Conexao::getConexao();
        $db->beginTransaction();
        try {
            $st=$db->prepare('SELECT id, usuario_id FROM recuperacao_senha WHERE token_hash=? AND utilizado_em IS NULL AND expira_em>NOW() LIMIT 1 FOR UPDATE');
            $st->execute([hash('sha256',$token)]); $rec=$st->fetch();
            if (!$rec) { $db->rollBack(); Logger::registrar('SENHA_REDEFINIDA', ['resultado'=>'token_invalido']); return false; }
            $db->prepare('UPDATE usuarios SET senha_hash=? WHERE id=?')->execute([password_hash($senha,PASSWORD_DEFAULT),$rec['usuario_id']]);
            $db->prepare('UPDATE recuperacao_senha SET utilizado_em=NOW() WHERE usuario_id=? AND utilizado_em IS NULL')->execute([$rec['usuario_id']]);
            $db->commit();
            Logger::registrar('SENHA_REDEFINIDA', ['resultado'=>'sucesso']);
            return true;
        } catch (Throwable $e) { if ($db->inTransaction()) $db->rollBack(); throw $e; }
    }
}
