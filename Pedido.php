<?php
require_once __DIR__.'/conexao.php';

final class Pedido {
    private PDO $db;
    public function __construct() { $this->db=Conexao::getConexao(); }

    public function produtos(array $carrinho): array {
        if (!$carrinho) return [];
        $ids=array_keys($carrinho);
        $placeholders=implode(',',array_fill(0,count($ids),'?'));
        $stmt=$this->db->prepare("SELECT id,titulo,autor,preco,estoque,status,imagem_capa FROM livros WHERE id IN ($placeholders) ORDER BY titulo");
        $stmt->execute($ids);
        $itens=[];
        foreach ($stmt->fetchAll() as $l) {
            $id=(int)$l['id'];
            $q=(int)$carrinho[$id];
            $l['quantidade']=$q;
            $l['subtotal_centavos']=(int)round((float)$l['preco']*100)*$q;
            $l['disponivel']=$l['status']==='Disponível' && (int)$l['estoque'] >= $q;
            $itens[]=$l;
        }
        return $itens;
    }

    /** Retorna o ID do pedido. Valores e estoque são recalculados sob bloqueio transacional. */
    public function finalizar(int $usuarioId, array $carrinho): int {
        if (!$carrinho) throw new DomainException('Seu carrinho está vazio.');
        $ids=array_keys($carrinho);
        sort($ids,SORT_NUMERIC);
        $db=$this->db;
        $db->beginTransaction();
        try {
            $placeholders=implode(',',array_fill(0,count($ids),'?'));
            $stmt=$db->prepare("SELECT id,preco,estoque,status FROM livros WHERE id IN ($placeholders) ORDER BY id FOR UPDATE");
            $stmt->execute($ids);
            $livros=[];
            foreach ($stmt->fetchAll() as $row) $livros[(int)$row['id']]=$row;
            if (count($livros)!==count($carrinho)) throw new DomainException('Há livros que não estão mais disponíveis. Atualize seu carrinho.');
            $totalCentavos=0;
            $itens=[];
            foreach ($ids as $id) {
                $q=$carrinho[$id];
                $l=$livros[$id];
                if ($l['status']!=='Disponível' || (int)$l['estoque']<$q) throw new DomainException('Estoque insuficiente para um dos livros. Atualize seu carrinho.');
                $centavos=(int)round((float)$l['preco']*100);
                if ($centavos<0) throw new DomainException('Preço inválido.');
                $totalCentavos+=$centavos*$q;
                if ($totalCentavos>999999999) throw new DomainException('Valor do pedido acima do limite permitido.');
                $itens[]=['id'=>$id,'quantidade'=>$q,'preco'=>number_format($centavos/100,2,'.','')];
            }
            $ins=$db->prepare("INSERT INTO pedidos (usuario_id,status,valor_total) VALUES (?,'confirmado',?)");
            $ins->execute([$usuarioId,number_format($totalCentavos/100,2,'.','')]);
            $pedidoId=(int)$db->lastInsertId();
            $insItem=$db->prepare('INSERT INTO pedido_itens (pedido_id,livro_id,quantidade,preco_unitario) VALUES (?,?,?,?)');
            $baixa=$db->prepare('UPDATE livros SET estoque=estoque-? WHERE id=? AND estoque>=?');
            foreach ($itens as $i) {
                $insItem->execute([$pedidoId,$i['id'],$i['quantidade'],$i['preco']]);
                $baixa->execute([$i['quantidade'],$i['id'],$i['quantidade']]);
                if ($baixa->rowCount()!==1) throw new DomainException('O estoque mudou durante a compra. Tente novamente.');
            }
            $db->commit();
            return $pedidoId;
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }

    public function listarUsuario(int $usuarioId): array {
        $stmt=$this->db->prepare('SELECT p.*, COUNT(i.id) AS quantidade_tipos, COALESCE(SUM(i.quantidade),0) AS unidades FROM pedidos p LEFT JOIN pedido_itens i ON i.pedido_id=p.id WHERE p.usuario_id=? GROUP BY p.id ORDER BY p.data_pedido DESC,p.id DESC');
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }
    public function buscarUsuario(int $pedidoId,int $usuarioId): ?array {
        $stmt=$this->db->prepare('SELECT * FROM pedidos WHERE id=? AND usuario_id=?');
        $stmt->execute([$pedidoId,$usuarioId]);
        $pedido=$stmt->fetch();
        if (!$pedido) return null;
        $stmt=$this->db->prepare('SELECT i.*,l.titulo,l.autor FROM pedido_itens i INNER JOIN livros l ON l.id=i.livro_id WHERE i.pedido_id=? ORDER BY i.id');
        $stmt->execute([$pedidoId]);
        $pedido['itens']=$stmt->fetchAll();
        return $pedido;
    }
}
