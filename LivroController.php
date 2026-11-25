<?php
require_once __DIR__ . '/Livro.php';
class LivroController {
    private Livro $modelo;
    public function __construct() { $this->modelo=new Livro(); }
    public function index(): void { $livros=$this->modelo->listarTodos(); require __DIR__.'/views/Listar.php'; }
    public function formCriar(): void { $livro=null; $categorias=$this->modelo->listarCategorias(); require __DIR__.'/views/form.php'; }
    public function formEditar(int $id): void {
        $livro=$this->modelo->buscarPorId($id); if (!$livro) {http_response_code(404); echo 'Livro não encontrado'; return;}
        $categorias=$this->modelo->listarCategorias(); require __DIR__.'/views/form.php';
    }
    public function salvar(): void {
        $dados=[
            'isbn'=>trim($_POST['isbn']??''), 'titulo'=>trim($_POST['titulo']??''),
            'autor'=>trim($_POST['autor']??''), 'descricao'=>trim($_POST['descricao']??''),
            'preco'=>str_replace(',','.',trim($_POST['preco']??'')),
            'estoque'=>filter_var($_POST['estoque']??null,FILTER_VALIDATE_INT),
            'imagem_capa'=>trim($_POST['imagem_capa']??''), 'status'=>$_POST['status']??'Disponível'
        ];
        if ($dados['isbn']==='' || $dados['titulo']==='' || !is_numeric($dados['preco']) || (float)$dados['preco']<0 || $dados['estoque']===false || $dados['estoque']<0 || !in_array($dados['status'],['Disponível','Indisponível'],true)) {
            http_response_code(422); echo 'Dados inválidos. Volte e confira os campos.'; return;
        }
        $id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT) ?: null;
        try { $this->modelo->salvar($dados,(array)($_POST['categorias']??[]),$id); Logger::registrar($id ? 'LIVRO_EDITADO' : 'LIVRO_CRIADO', ['livro_id'=>$id,'resultado'=>'sucesso']); header('Location: index.php'); }
        catch (PDOException $e) { http_response_code(400); echo 'Não foi possível salvar. Confira se o ISBN já existe e se as categorias são válidas.'; }
    }
    public function deletar(): void {
        $id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);
        if (!$id) {http_response_code(400); return;}
        try { $this->modelo->deletar($id); Logger::registrar('LIVRO_EXCLUIDO',['livro_id'=>$id,'resultado'=>'sucesso']); header('Location: index.php'); }
        catch (PDOException $e) {http_response_code(409); echo 'Não é possível excluir um livro associado a pedidos.';}
    }
}
