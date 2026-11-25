<?php
final class Logger {
    public static function registrar(string $acao, array $dados = []): void {
        $permitidos = ['livro_id', 'usuario_id', 'resultado', 'rota', 'metodo'];
        $dados = array_intersect_key($dados, array_flip($permitidos));
        $dados['usuario_id'] = $_SESSION['usuario']['id'] ?? null;
        $registro = [
            'quando' => date('c'),
            'acao' => $acao,
            'dados' => $dados
        ];
        $arquivo = __DIR__ . '/logs/acoes.log';
        $linha = json_encode($registro, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) . PHP_EOL;
        if (@file_put_contents($arquivo, $linha, FILE_APPEND | LOCK_EX) === false) {
            error_log('Não foi possível gravar o log da livraria.');
        }
    }
}
