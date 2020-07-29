<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * RUTA
 * PERSONAS
 */

$app->group('/personas', function () use ($app) {
    $app->map(['GET', 'POST'], '', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {

            if ($request->isGet()) {

                $persona = new Persona($this->logger);

                $res = $persona->listarPersonas($request->getAttribute('idUsuario'));
            } else
            if ($request->isPost()) {
                $bodyIn = [];

                $bodyIn = $request->getParsedBody();
                print_r($bodyIn);
                $res = 200;
                if (
                    is_array($bodyIn['data']) &&
                    array_key_exists('nombre', $bodyIn['data']) &&
                    array_key_exists('apellido', $bodyIn['data']) &&
                    array_key_exists('telefono', $bodyIn['data']) &&
                    array_key_exists('dni', $bodyIn['data'])
                ) {
                    $nombres = $bodyIn['data']['nombre'];
                    $apellido = $bodyIn['data']['apellido'];
                    $telefono = $bodyIn['data']['telefono'];
                    $dni = $bodyIn['data']['dni'];
                    $usuario = $request->getAttribute('idUsuario');

                    $persona = new Persona($this->logger);

                    $res = $persona->insertarPersona($nombres, $apellido, $telefono, $dni, $usuario);
                } else {
                    $res = 400;
                }
            } else {
                $res = 405;
            }

            //desde aca mandar al midleware
            $rp['token'] = $request->getAttribute('newToken');

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
        }
        return $response->withHeader('Content-type', 'application/json')
            ->withStatus(401)
            ->withJson(null);
    });
})->add($guardMiddleware);
