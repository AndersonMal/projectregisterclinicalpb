<?php 

class UserModel extends Query{

    private $connect2;

    public function __construct($connect,$connect2)
    {
        parent::__construct($connect);
        $this->connect2= $connect2;
    }
    public function getUser(String $document)
    {
        $sql = "SELECT * FROM user_registered WHERE numeroDocumento = '$document'";
        $data = $this->select($sql);
        return $data;
    }

    public function getRegisters($identificacion){
        $strid = strval($identificacion);
        $sql = "SELECT R.Nombre, U.Descrip, C.FechaHora, RH.RegistroXML
        FROM Pacientes P
        INNER JOIN Casos C ON P.Id = C.Paciente
        INNER JOIN RegistrosHistoria RH ON C.Caso = RH.Caso
        INNER JOIN Registros R ON RH.CodigoRegistro = R.Codigo
        INNER JOIN Unidades U ON RH.UnidadFuncional = U.Codigo
        WHERE P.Identificacion = ?";
        $stmt = $this->connect2->prepare($sql);
        $stmt->bindParam(1, $strid, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}

?>