<?php

class Conexion {
    private $conect;

    public function __construct() {
        global $host, $port, $dbname, $username, $password;
        
        $dsn = "sqlsrv:Server=$host,$port;Database=$dbname";
        try {
            $this->conect = new PDO($dsn, $username, $password);
            $this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exp) {
            echo "No se logró conectar a la BD: " . $exp->getMessage();
        }
    }

    public function getConnection() {
        return $this->conect;
    }
}

?>
