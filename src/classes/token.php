<?php

/**
 *
 */
date_default_timezone_set('America/Argentina/San_Luis');

class token
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    private $secret = 'rCaDNN0VxJ0MLMYxA0BmGeBBlkPXneyN';
    private $timeOut = 10000000000; // Minutos de vida del token
    private $expire = null;

    /**
     * Datos del Usuario
     */
    private $idUsuario;
    private $isValidToken = false;
    /**
     * Undocumented function
     */
    public function __construct()
    {
        $now = strtotime('now');
        $this->expire = $now + ($this->timeOut * 60);
    }

    /**
     * Undocumented function
     *
     * @param [type] $data
     * @return void
     */
    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
    }

    /**
     * Recibe un arreglo y devuelve un token que contiene los datos del usuario
     */
    public function setToken($usuario)
    {
        //return "Token.de.prueba";

        //Define los Headers del token
        $header = self::base64url_encode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT',
        ]));
        //Define el payload y carga los datos del usuario
        $payload = self::base64url_encode(json_encode([
            'expire' => $this->expire, //esta linea dejarla asi, xq sino rompe
            'user' => $usuario,
        ]));
        $signature = self::base64url_encode(hash_hmac('sha256', $header . '.' . $payload, $this->secret, true));
        //Construye el token = header+paylad+signature
        $token = $header . '.' . $payload . '.' . $signature;

        return $token;
    }
    /**
     * Verifica si el token es Valito
     * @param  [] $token [token que envia el frontend]
     * @return [bool]        [Verdadero si es valido]
     */
    public function checkToken($token)
    {
        //FOR TESTIN ONLY
        //return true;

        //Divide el Token en 3 Partes
        if ($t = explode('.', $token)) {

            //asigna los valores a referencias
            $refHeader = $t[0];
            $refPayload = $t[1];
            $refSignature = $t[2];

            //Crea un nuevo signature con los datos del token
            $newSignature = self::base64url_encode(hash_hmac('sha256', $refHeader . '.' . $refPayload, $this->secret, true));

            //decodifica el payload
            $refPayload = json_decode(base64_decode($refPayload), true);

            //obtiene el momento en que expira
            $expire = is_array($refPayload) && array_key_exists('expire', $refPayload) ? $refPayload['expire'] : 000000;

            //Verifica si el token ha expirado
            if ($expire >= strtotime('now') && $refSignature === $newSignature) {
                // si aun es valido
                $this->isValidToken = true;
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * Renueva un token existente
     * @param  [] $token [token que envia el frontend]
     * @return [bool]        [Verdadero si es valido]
     */
    public function updateToken($token)
    {
        //return "Token.de.prueba";

        //Divide el Token en 3 Partes
        if ($t = explode('.', $token)) {
            $refHeader = $t[0];
            $refPayload = $t[1];

            //decodifica el payload
            $refPayload = json_decode(base64_decode($refPayload), true);
            //actualiza EXPIRE
            $refPayload['expire'] = $this->expire;
            $this->idUsuario = $refPayload['user']['Id'];
            //codificamos el nuevo Payload con
            $newPayload = self::base64url_encode(json_encode($refPayload));
            //Crea un nuevo signature con los datos del token
            $signature = self::base64url_encode(hash_hmac('sha256', $refHeader . '.' . $newPayload, $this->secret, true));
            //Construye el token = header+paylad+signature
            $newToken = $refHeader . '.' . $newPayload . '.' . $signature;
            return $newToken;
        }
        return false;
    }

    /**
     * Devuelve el ID del usuario actual
     * @param  [] $token [token que envia el frontend]
     * @return [bool]        [Verdadero si es valido]
     */
    public function getUserId()
    {
        if ($this->isValidToken) {
            return $this->idUsuario;
        }
        return false;
    }
}
