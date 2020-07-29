<?php

require_once './../src/config/db.php';

class Usuario
{

    /** MONOLOG */
    protected $logger;

    /* DEL USUARIO */
    private $usr_id;
    private $usr_nombre;
    private $usr_apellido;

    /** CONEXION CON LA BASE DE DATOS */
    private $conn;

    // metodos
    public function __construct($monolog_OBJ)
    {

        $this->logger = $monolog_OBJ;
        $pdoMysql = new pdoMysql($this->logger);
        $this->conn = $pdoMysql->conectar();

    }

    public function login($username, $password)
    {

        $sql = 'SELECT
                idusuario AS id,
                usuario_nombre AS nombre,
                usuario_apellido AS apellido,
                usuario_password AS clave
                FROM usuario
                WHERE usuario_username = :usuario
                LIMIT 1;';
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':usuario' => $username,
            ));
            $res = $sth->fetchAll();

        } catch (Exception $e) {
            $this->logger->warning('usuario->login() - ', [$e->getMessage()]);
            return 500;
        }
        if (count($res) > 0) {
            if (
                array_key_exists('clave', $res[0]) &&
                password_verify($password, $res[0]['clave'])
            ) {
                $this->usr_id = $res[0]['id'];
                $this->usr_nombre = $res[0]['nombre'];
                $this->usr_apellido = $res[0]['apellido'];
                return true;
            }
        }
        return 401;
    }

    public function getUsuario()
    {
        $datosUsuario = [
            'Id' => $this->usr_id,
            'Nombre' => $this->usr_nombre,
            'Apellido' => $this->usr_apellido,
        ];
        return $datosUsuario;
    }
    public function __destruct()
    {
        unset($this->conn);
    }

}
