<?php

class Conexion {
    private $conect;
    private $conect2;

    public function __construct() {
        global $host, $port, $dbname, $username, $password, $host2, $dbname2,$username2, $password2, $port2;
        
        //Connection to the database of the clinical
        $dsn = "sqlsrv:Server=$host,$port;Database=$dbname";
        try {
            $this->conect = new PDO($dsn, $username, $password);
            $this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exp) {
            echo "No se logró conectar a la BD: " . $exp->getMessage();
        }

        //Connection to the bd for save the users
        $ds2 = "mysql:host=$host2;dbname=$dbname2;charset=utf8";
        try{
            $this->conect2 = new PDO($ds2, $username2, $password2);
            $this->conect2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $exp){
            echo "No se logró conectar a la segunda BD: " . $exp->getMessage();
        }


    }

    public function getConnection() {
        return $this->conect;
    }

    public function getConnection2() {
        return $this->conect2;
    }

}

?>
