<?php

class Persona
{
    private $conn;
    private $logger;

    public function __construct($monolog_OBJ)
    {
        $this->logger = $monolog_OBJ;
        $pdoMysql = new pdoMysql($this->logger);
        $this->conn = $pdoMysql->conectar();
    }

    public function listarPersonas($idUsuario)
    {
        $sql = "SELECT * FROM `persona` WHERE usuario_idusuario = :idUsuario;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idUsuario' =>  $idUsuario
            ));
            return $sth->fetchAll();
        } catch (Exception $e) {
            $this->logger->warning('listarPerdonas() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function insertarPersona($nombres, $apellido, $telefono, $dni, $idUsuario)
    {
        $sql = "INSERT
                    INTO `persona`
                    (`idpersona`, `persona_nombre`, `persona_apellido`, `persona_telefono`, `persona_dni`, `persona_fecha`, `usuario_idusuario`)
                VALUES
                    (NULL, :nombres, :apellidos, :telefono, :dni, :fecha, :usuario);";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':nombres' => $nombres,
                ':apellidos' => $apellido,
                ':telefono' => $telefono,
                ':dni' => $dni,
                ':fecha' => date("Y-m-d H:i:s"),
                ':usuario' => $idUsuario,

            ));
            return $this->listarPersonas($idUsuario);
        } catch (Exception $e) {
            $this->logger->warning('insertarPersona() - ', [$e->getMessage()]);
            return 500;
        }
    }

    // public function eliminarMarca($idMarca)
    // {
    //     $sql = "DELETE FROM vhMarca WHERE vhMarca.idvhMarca = :idMarca ;";
    //     try {
    //         $sth = $this->conn->prepare($sql);
    //         $sth->execute(array(
    //             ':idMarca' => $idMarca,
    //         ));
    //         return $this->listarMarcas();
    //     } catch (Exception $e) {
    //         $this->logger->warning('eliminar marca() - ', [$e->getMessage()]);
    //         return 500;
    //     }
    // }
}
