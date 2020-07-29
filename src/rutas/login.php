<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * RUTA
 * LOGIN
 */

$app->post('/login', function (Request $request, Response $response, array $args) {

    $bodyIn = [];

    $bodyIn = $request->getParsedBody();
    if (array_key_exists('user_username', $bodyIn['data']) && array_key_exists('user_password', $bodyIn['data'])) {

        $userName = $bodyIn['data']['user_username'];
        $userPassword = $bodyIn['data']['user_password'];

        $usuario = new Usuario($this->logger);
        if ($usuario->login($userName, $userPassword) === true) {

            $usuario = $usuario->getUsuario();

            $token = new token;
            $res['token'] = $token->setToken($usuario);

            $res['data'] = $usuario;
        } else {
            $res = $usuario->login($userName, $userPassword);
        }

    } else {
        $res = 400;
    }
    if (is_numeric($res)) {
        return $response->withHeader('Content-type', 'application/json')
            ->withStatus($res)
            ->withJson(null);
    } else {
        $rp['data'] = $res;
        return $response->withHeader('Content-type', 'application/json')
            ->withStatus(200)
            ->withJson($rp);
    }

});
