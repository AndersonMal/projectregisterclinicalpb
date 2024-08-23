<?php 

class UserModel extends Query{
    public function __construct($connection)
    {
        parent::__construct($connection);
    }
    public function getUser(String $document)
    {
        $sql = "SELECT * FROM user_registered WHERE numeroDocumento = '$document'";
        $data = $this->select($sql);
        return $data;
    }
}

?>