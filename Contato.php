<?php
require_once __DIR__.'/conexao.php';
require_once __DIR__.'/Email.php';
final class Contato {
    public static function registrar(string $nome,string $email,string $assunto,string $mensagem): int {
        $st=Conexao::getConexao()->prepare('INSERT INTO contatos (usuario_id,nome,email,assunto,mensagem) VALUES (?,?,?,?,?)');
        $st->execute([$_SESSION['usuario']['id']??null,$nome,$email,$assunto,$mensagem]);
        Logger::registrar('CONTATO_RECEBIDO',['resultado'=>'gravado']);
        return (int)Conexao::getConexao()->lastInsertId();
    }
    public static function enviar(int $id,string $nome,string $email,string $assunto,string $mensagem): void {
        $corpo="Mensagem #{$id} recebida pelo Fale Conosco.\n\nNome: {$nome}\nE-mail: {$email}\nAssunto: {$assunto}\n\nMensagem:\n{$mensagem}";
        Email::enviar(Email::destinatarioContato(), 'Contato da livraria: '.$assunto, $corpo, $email);
        Logger::registrar('CONTATO_ENVIADO',['resultado'=>'sucesso']);
    }
}
