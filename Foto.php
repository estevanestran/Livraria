<?php
final class Foto {
    public static function receber(array $arquivo): string {
        if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Selecione uma imagem válida.');
        }
        if (($arquivo['size'] ?? 0) > 2 * 1024 * 1024 || ($arquivo['size'] ?? 0) <= 0) {
            throw new RuntimeException('A foto deve ter até 2 MB.');
        }
        if (!is_uploaded_file($arquivo['tmp_name'])) {
            throw new RuntimeException('Arquivo de upload inválido.');
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
        $extensoes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($extensoes[$mime]) || @getimagesize($arquivo['tmp_name']) === false) {
            throw new RuntimeException('Use uma imagem JPG, PNG ou WebP.');
        }
        $nome = bin2hex(random_bytes(16)) . '.' . $extensoes[$mime];
        $destino = __DIR__ . '/uploads/usuarios/' . $nome;
        if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
            throw new RuntimeException('Não foi possível salvar a foto.');
        }
        return 'uploads/usuarios/' . $nome;
    }
}
