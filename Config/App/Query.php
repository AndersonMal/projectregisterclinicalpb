<?php

class Query extends Conexion
{
    private $con;

    public function __construct($connection) {
        $this->con = $connection;
    }

    public function select(string $sql, array $params = []) {
        $stmt = $this->con->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>