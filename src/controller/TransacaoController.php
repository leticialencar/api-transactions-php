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

    public function getTransacao(Request $request, Response $response, $args) {
        $dao = new TransacaoDAO();
        $transacao = $dao->buscarPorId($args['id']);

        if (!$transacao) {
            return $response->withStatus(404);
        }

        $data = [
            'id' => $transacao->getId(),
            'valor' => (float) $transacao->getValor(),
            'dataHora' => $transacao->getDataHora(),
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteTransacaoId(Request $request, Response $response, $args) {
        $dao = new TransacaoDAO();

        $excluido = $dao->excluirPorId($args['id']);
        if ($excluido) {
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404);
        }
    }

    public function deleteTransacoes(Request $request, Response $response) {
        $dao = new TransacaoDAO();
        $dao->excluirTodas();
        return $response->withStatus(200);
    }

    public function getEstatistica(Request $request, Response $response) {
        $dao = new TransacaoDAO();
        $transacoes = $dao->buscarUltimos60s();

        $count = count($transacoes);
        $sum = 0;
        $min = null;
        $max = null;

        foreach ($transacoes as $t) {
            $v = floatval($t['valor']);
            $sum += $v;
            if ($min === null || $v < $min) $min = $v;
            if ($max === null || $v > $max) $max = $v;
        }

        $avg = $count > 0 ? $sum / $count : 0;
        $min = $min ?? 0;
        $max = $max ?? 0;

        $stats = [
            'count' => $count,
            'sum' => $sum,
            'avg' => $avg,
            'min' => $min,
            'max' => $max
        ];

        $response->getBody()->write(json_encode($stats));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
