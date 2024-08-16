<?php 

class UserModel extends Query{
    public function __construct()
    {
        parent::__construct();
    }
    public function getUser(String $document)
    {
        $sql = "SELECT * FROM Pacientes WHERE Identificacion = '$document'";
        $data = $this->select($sql);
        return $data;
    }
}

?>