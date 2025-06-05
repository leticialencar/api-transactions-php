<?php

use Slim\App;
use Src\Controller\TransacaoController;

return function (App $app) {
    $app->post('/transacao', [TransacaoController::class, 'postTransacao']);
    $app->get('/transacao/{id}', [TransacaoController::class, 'getTransacao']);
    $app->delete('/transacao', [TransacaoController::class, 'deleteTransacoes']);
    $app->delete('/transacao/{id}', [TransacaoController::class, 'deleteTransacaoId']);
    $app->get('/estatistica', [TransacaoController::class, 'getEstatistica']);

    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
        return $response->withStatus(404);
    });
};