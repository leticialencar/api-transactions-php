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
}