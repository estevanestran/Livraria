<?php
require_once __DIR__.'/Logger.php';
final class Email {
    public static function enviar(string $destinatario, string $assunto, string $texto, ?string $responderPara=null): void {
        $autoload=__DIR__.'/vendor/autoload.php';
        if (!is_file($autoload)) throw new RuntimeException('PHPMailer não instalado. Execute composer install.');
        require_once $autoload;
        $config=__DIR__.'/config/email.local.php';
        if (!is_file($config)) throw new RuntimeException('Configuração SMTP ausente.');
        $cfg=require $config;
        foreach (['usuario','senha_app','destinatario_contato'] as $campo) {
            if (empty($cfg[$campo])) throw new RuntimeException('Configuração SMTP incompleta.');
        }
        $mail=new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host='smtp.gmail.com';
            $mail->SMTPAuth=true;
            $mail->Username=$cfg['usuario'];
            $mail->Password=$cfg['senha_app'];
            $mail->SMTPSecure=\PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port=587;
            $mail->CharSet='UTF-8';
            $mail->Timeout=15;
            $mail->setFrom($cfg['usuario'], 'Livraria Virtual');
            $mail->addAddress($destinatario);
            if ($responderPara && filter_var($responderPara,FILTER_VALIDATE_EMAIL)) $mail->addReplyTo($responderPara);
            $mail->Subject=$assunto;
            $mail->Body=$texto;
            $mail->isHTML(false);
            $mail->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            Logger::registrar('EMAIL', ['resultado'=>'falha']);
            throw new RuntimeException('Falha ao enviar e-mail pelo servidor SMTP.',0,$e);
        }
        Logger::registrar('EMAIL', ['resultado'=>'sucesso']);
    }
    public static function destinatarioContato(): string {
        $cfg=__DIR__.'/config/email.local.php';
        if (!is_file($cfg)) throw new RuntimeException('Configuração SMTP ausente.');
        $dados=require $cfg;
        $email=$dados['destinatario_contato']??'';
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Destinatário de contato inválido.');
        return $email;
    }
}
