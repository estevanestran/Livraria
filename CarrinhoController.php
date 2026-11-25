<?php
require_once __DIR__.'/Pedido.php';
require_once __DIR__.'/Logger.php';

final class CarrinhoController {
    private Pedido $modelo;
    private const MAX_ITENS_DIFERENTES=30;
    private const MAX_UNIDADES=99;
    public function __construct() { $this->modelo=new Pedido(); }

    /** Normaliza sessões antigas/manipuladas sem confiar em valores arbitrários. */
    public static function itens(): array {
        $carrinho=$_SESSION['carrinho']??[];
        if (!is_array($carrinho)) return [];
        $limpo=[];
        foreach ($carrinho as $id=>$q) {
            if (count($limpo)>=self::MAX_ITENS_DIFERENTES) break;
            if (!ctype_digit((string)$id) || (int)$id<1 || (int)$id>2147483647 || !is_int($q) || $q<1 || $q>self::MAX_UNIDADES) continue;
            $limpo[(int)$id]=$q;
        }
        $_SESSION['carrinho']=$limpo;
        return $limpo;
    }
    private static function inteiro(mixed $valor): int {
        if (!is_string($valor) && !is_int($valor)) return 0;
        if (!preg_match('/^[1-9][0-9]{0,9}$/',(string)$valor)) return 0;
        $n=(int)$valor;
        return $n>0 && $n<=2147483647?$n:0;
    }
    private static function voltar(string $destino='carrinho'): never {
        header('Location: index.php?acao='.$destino); exit;
    }
    private static function aviso(string $msg): void { $_SESSION['aviso_carrinho']=$msg; }

    public function adicionar(): void {
        $id=self::inteiro($_POST['id']??null);
        $q=self::inteiro($_POST['quantidade']??'1');
        if (!$id || !$q || $q>self::MAX_UNIDADES) {self::aviso('Quantidade ou livro inválido.'); self::voltar();}
        $p=$this->modelo->produtos([$id=>1]);
        if (!$p || $p[0]['status']!=='Disponível') {self::aviso('Este livro não está disponível.'); self::voltar();}
        $cart=self::itens();
        if (!isset($cart[$id]) && count($cart)>=self::MAX_ITENS_DIFERENTES) {self::aviso('Limite de livros diferentes no carrinho atingido.'); self::voltar();}
        $novo=($cart[$id]??0)+$q;
        if ($novo>self::MAX_UNIDADES || $novo>(int)$p[0]['estoque']) {self::aviso('Quantidade acima do estoque ou do limite de 99 unidades por livro.'); self::voltar();}
        $cart[$id]=$novo; $_SESSION['carrinho']=$cart;
        Logger::registrar('CARRINHO_ADICIONAR',['livro_id'=>$id,'resultado'=>'sucesso']);
        self::voltar();
    }
    public function atualizar(): void {
        $id=self::inteiro($_POST['id']??null);
        $raw=$_POST['quantidade']??null;
        $q=isset($_POST['remover']) || $raw==='0' || $raw===0 ? 0 : self::inteiro($raw);
        $cart=self::itens();
        if (!$id || !isset($cart[$id]) || ($q===0 && $raw!=='0' && $raw!==0 && !isset($_POST['remover'])) || $q>self::MAX_UNIDADES) {
            self::aviso('Quantidade inválida.'); self::voltar();
        }
        if ($q===0) unset($cart[$id]);
        else {
            $p=$this->modelo->produtos([$id=>1]);
            if (!$p || $p[0]['status']!=='Disponível' || (int)$p[0]['estoque']<$q) {
                self::aviso('Quantidade indisponível no estoque.'); self::voltar();
            }
            $cart[$id]=$q;
        }
        $_SESSION['carrinho']=$cart;
        Logger::registrar('CARRINHO_ATUALIZAR',['livro_id'=>$id,'resultado'=>'sucesso']);
        self::voltar();
    }
    public function mostrar(): void {
        $carrinho=self::itens();
        $itens=$this->modelo->produtos($carrinho);
        $totalCentavos=array_sum(array_column($itens,'subtotal_centavos'));
        $incompleto=count($itens)!==count($carrinho);
        if ($incompleto) {
            $existentes=array_map(static fn($item)=>(int)$item['id'],$itens);
            $_SESSION['carrinho']=array_intersect_key($carrinho,array_flip($existentes));
        }
        $disponivel=!$incompleto && count($itens)>0;
        foreach ($itens as $i) if (!$i['disponivel']) $disponivel=false;
        $aviso=$_SESSION['aviso_carrinho']??'';
        unset($_SESSION['aviso_carrinho']);
        require __DIR__.'/views/carrinho.php';
    }
    public function finalizar(): void {
        if (!Auth::usuario()) {
            self::aviso('Entre na sua conta para finalizar o pedido.');
            header('Location: index.php?acao=login'); exit;
        }
        try {
            $pedido=$this->modelo->finalizar((int)Auth::usuario()['id'],self::itens());
            $_SESSION['carrinho']=[];
            Logger::registrar('PEDIDO_FINALIZADO',['resultado'=>'sucesso']);
            header('Location: index.php?acao=pedido&id='.$pedido); exit;
        } catch (DomainException $e) {
            Logger::registrar('PEDIDO_RECUSADO',['resultado'=>'estoque_ou_dados']);
            self::aviso($e->getMessage()); self::voltar();
        } catch (Throwable $e) {
            Logger::registrar('PEDIDO_FALHA',['resultado'=>'erro_servidor']);
            error_log('Erro ao finalizar pedido: '.$e->getMessage());
            self::aviso('Não foi possível finalizar o pedido. Tente novamente.'); self::voltar();
        }
    }
    public function historico(): void {
        $pedidos=$this->modelo->listarUsuario((int)Auth::usuario()['id']);
        require __DIR__.'/views/pedidos.php';
    }
    public function pedido(int $id): void {
        $pedido=$this->modelo->buscarUsuario($id,(int)Auth::usuario()['id']);
        if (!$pedido) {http_response_code(404); echo 'Pedido não encontrado.'; return;}
        require __DIR__.'/views/pedido.php';
    }
}
