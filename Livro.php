<?php
require_once __DIR__ . '/conexao.php';
class Livro {
    private PDO $conn;
    public function __construct() { $this->conn = Conexao::getConexao(); }
    public function listarTodos(): array {
        return $this->conn->query("SELECT l.*, GROUP_CONCAT(c.nome ORDER BY c.nome SEPARATOR ', ') AS categorias_nomes FROM livros l LEFT JOIN livro_categoria lc ON lc.livro_id=l.id LEFT JOIN categorias c ON c.id=lc.categoria_id GROUP BY l.id ORDER BY l.id DESC")->fetchAll();
    }
    public function buscarPorId(int $id): ?array {
        $stmt=$this->conn->prepare('SELECT * FROM livros WHERE id=?'); $stmt->execute([$id]);
        $livro=$stmt->fetch(); if (!$livro) return null;
        $stmt=$this->conn->prepare('SELECT categoria_id FROM livro_categoria WHERE livro_id=?'); $stmt->execute([$id]);
        $livro['categorias']=$stmt->fetchAll(PDO::FETCH_COLUMN); return $livro;
    }
    public function listarCategorias(): array { return $this->conn->query('SELECT * FROM categorias ORDER BY nome')->fetchAll(); }
    public function salvar(array $dados, array $categorias, ?int $id=null): void {
        $this->conn->beginTransaction();
        try {
            if ($id) {
                $sql='UPDATE livros SET isbn=?,titulo=?,autor=?,descricao=?,preco=?,estoque=?,imagem_capa=?,status=? WHERE id=?';
                $valores=[$dados['isbn'],$dados['titulo'],$dados['autor'],$dados['descricao'],$dados['preco'],$dados['estoque'],$dados['imagem_capa'],$dados['status'],$id];
            } else {
                $sql='INSERT INTO livros (isbn,titulo,autor,descricao,preco,estoque,imagem_capa,status) VALUES (?,?,?,?,?,?,?,?)';
                $valores=[$dados['isbn'],$dados['titulo'],$dados['autor'],$dados['descricao'],$dados['preco'],$dados['estoque'],$dados['imagem_capa'],$dados['status']];
            }
            $stmt=$this->conn->prepare($sql); $stmt->execute($valores);
            if (!$id) $id=(int)$this->conn->lastInsertId();
            $stmt=$this->conn->prepare('DELETE FROM livro_categoria WHERE livro_id=?'); $stmt->execute([$id]);
            $stmt=$this->conn->prepare('INSERT INTO livro_categoria (livro_id,categoria_id) VALUES (?,?)');
            foreach (array_unique(array_map('intval',$categorias)) as $categoria) if ($categoria>0) $stmt->execute([$id,$categoria]);
            $this->conn->commit();
        } catch (Throwable $e) { $this->conn->rollBack(); throw $e; }
    }
    public function deletar(int $id): void {
        $stmt=$this->conn->prepare('DELETE FROM livros WHERE id=?'); $stmt->execute([$id]);
    }
}
