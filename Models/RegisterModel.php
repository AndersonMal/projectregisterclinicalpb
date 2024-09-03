<?php 

class RegisterModel extends Query{

    private $con2;
    private $con1;

    public function __construct($con1, $con2)
    {
        parent::__construct($con1);
        $this->con2 = $con2;
    }
    public function getRegister(String $document)
    {
        $sql = "SELECT Identificacion , Ape1Afil , FechaNac FROM Pacientes WHERE Identificacion = '$document'";
        $data = $this->select($sql);
        return $data;
    }

    public function saveRegister($data) {
        $sql = "INSERT INTO user_registered (numeroDocumento, primerApellido, fechanacimiento, contraseña) VALUES (:document, :firstname, :birthdate, :password)";
        $stmt = $this->con2->prepare($sql);
        $stmt->bindParam(':document', $data['document']);
        $stmt->bindParam(':firstname', $data['firstname']);
        $stmt->bindParam(':birthdate', $data['birthdate']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->execute();
    }

    public function getClinicalData($identification) {
        $sql = "SELECT R.Nombre, U.Descrip, C.FechaHora 
                FROM Pacientes P
                INNER JOIN Casos C ON P.Id = C.Paciente
                INNER JOIN RegistrosHistoria RH ON C.Caso = RH.Caso
                INNER JOIN Registros R ON RH.CodigoRegistro = R.Codigo
                INNER JOIN Unidades U ON RH.UnidadFuncional = U.Codigo
                WHERE P.Identificacion = :identification";
        
        $stmt = $this->con1->prepare($sql);
        $stmt->bindParam(':identification', $identification, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        return $data;;
    }

}

?>