<?php
require_once __DIR__ . '/Livro.php';
class LivroController {
    private Livro $modelo;
    public function __construct() { $this->modelo=new Livro(); }
    public function index(): void {
        $busca=mb_substr(trim((string)($_GET['busca']??'')),0,100);
        $categoriaId=filter_var($_GET['categoria']??0,FILTER_VALIDATE_INT);
        $categoriaId=$categoriaId!==false&&$categoriaId>0?$categoriaId:0;
        $categorias=$this->modelo->listarCategorias();
        $livros=$this->modelo->pesquisar($busca,$categoriaId);
        require __DIR__.'/views/catalogo.php';
    }
    public function detalhe(int $id): void {
        $livro=$this->modelo->detalhes($id);
        if (!$livro) {http_response_code(404); echo 'Livro não encontrado.'; return;}
        require __DIR__.'/views/detalhe.php';
    }
    public function categoriasPublicas(): void {
        $categorias=$this->modelo->categoriasComContagem();
        require __DIR__.'/views/categorias_publicas.php';
    }
    public function gerenciarCategorias(): void {
        $categorias=$this->modelo->categoriasComContagem();
        $id=filter_var($_GET['id']??0,FILTER_VALIDATE_INT);
        $editando=$id>0?$this->modelo->buscarCategoria($id):null;
        $erro='';
        require __DIR__.'/views/categorias_admin.php';
    }
    public function salvarCategoria(): void {
        $nome=trim((string)($_POST['nome']??''));
        $id=filter_var($_POST['id']??0,FILTER_VALIDATE_INT);
        if (mb_strlen($nome)<2 || mb_strlen($nome)>50) {
            http_response_code(422); echo 'Nome da categoria deve ter de 2 a 50 caracteres.'; return;
        }
        try {
            $this->modelo->salvarCategoria($nome,$id>0?$id:null);
            Logger::registrar('CATEGORIA_SALVA',['resultado'=>'sucesso']);
            header('Location: index.php?acao=gerenciar_categorias'); exit;
        } catch (PDOException $e) {
            http_response_code(409); echo 'Não foi possível salvar a categoria. Confira se o nome já existe.'; 
        }
    }
    public function excluirCategoria(): void {
        $id=filter_var($_POST['id']??0,FILTER_VALIDATE_INT);
        if (!$id || $id<1) {http_response_code(400); echo 'Categoria inválida.'; return;}
        $this->modelo->excluirCategoria($id);
        Logger::registrar('CATEGORIA_EXCLUIDA',['resultado'=>'sucesso']);
        header('Location: index.php?acao=gerenciar_categorias'); exit;
    }
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
