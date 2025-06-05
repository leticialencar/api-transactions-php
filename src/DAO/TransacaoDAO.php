<?php
namespace Src\DAO;

use Src\Model\Transacao;
use Src\Config\Conexao;
use PDO;

class TransacaoDAO {
    private $db;

    public function __construct() {
        $this->db = Conexao::getConn();
    }

    public function inserir(Transacao $transacao) {
        $stmt = $this->db->prepare("INSERT INTO transacao (id, valor, dataHora) VALUES (?, ?, ?)");
        return $stmt->execute([
            $transacao->getId(),
            $transacao->getValor(),
            $transacao->getDataHora()
        ]);
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM transacao WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        $transacao = new Transacao();
        $transacao->setId($row['id']);
        $transacao->setValor($row['valor']);
        $transacao->setDataHora($row['dataHora']);
        return $transacao;
    }

    public function excluirPorId($id) {
        $stmt = $this->db->prepare("DELETE FROM transacao WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public function excluirTodas() {
        $stmt = $this->db->prepare("DELETE FROM transacao");
        return $stmt->execute();
    }
}