<?php
namespace Src\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Src\DAO\TransacaoDAO;
use Src\Model\Transacao;

class TransacaoController {

    private function codigoValido(string $uuid): bool {
        return preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid
        ) === 1;
    }

    public function postTransacao(Request $request, Response $response) {
        $dao = new TransacaoDAO();

        $dados = json_decode($request->getBody()->getContents(), true);

        if (!$dados || !isset($dados['id'], $dados['valor'], $dados['dataHora'])) {
            return $response->withStatus(400); 
        }

        $id = $dados['id'];
        $valor = $dados['valor'];
        $dataHora = $dados['dataHora'];

        if (!is_string($id) || !$this->codigoValido($id)) {
            return $response->withStatus(422);
        }

        if (!is_numeric($valor) || $valor < 0) {
            return $response->withStatus(422);
        }

        $data = \DateTime::createFromFormat('Y-m-d H:i:s', $dataHora);
        if (!$data || $data->format('Y-m-d H:i:s') !== $dataHora) {
            return $response->withStatus(422); 
        }

        $agora = new \DateTime();
        if ($data > $agora) {
            return $response->withStatus(422);
        }

        if ($dao->buscarPorId($id)) {
            return $response->withStatus(422);
        }

        $transacao = new Transacao();
        $transacao->setId($id);
        $transacao->setValor($valor);
        $transacao->setDataHora($dataHora);

        $ok = $dao->inserir($transacao);

        if ($ok) {
            return $response->withStatus(201);
        } else {
            return $response->withStatus(404);
        }
    }
}
