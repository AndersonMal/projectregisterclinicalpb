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
        $currentDate = date('Y-m-d H:i:s');  // Obtener la fecha actual en PHP
        $sql = "INSERT INTO user_registered (numeroDocumento, primerApellido, fechanacimiento, contraseña, fecharegistro) 
                VALUES (:document, :firstname, :birthdate, :password, :fecharegistro)";
        $stmt = $this->con2->prepare($sql);
        $stmt->bindParam(':document', $data['document']);
        $stmt->bindParam(':firstname', $data['firstname']);
        $stmt->bindParam(':birthdate', $data['birthdate']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':fecharegistro', $currentDate);  // Pasar la fecha actual como parámetro
        $stmt->execute();
    }

    public function verifyUsers($document){
        $sql = "SELECT numeroDocumento FROM user_registered WHERE numeroDocumento = :document";
        $stmt = $this->con2->prepare($sql);
        $stmt->bindParam(':document', $document);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
    public function changePassword($identificacion, $newPassword) {
        $strid = strval($identificacion);
        $hashedPassword = strval($newPassword); 
        $this->con2->exec("SET SQL_SAFE_UPDATES = 0");
        $sql = "UPDATE user_registered
                SET contraseña = ?
                WHERE numeroDocumento = ?";
        $stmt = $this->con2->prepare($sql);
        $stmt->bindParam(1, $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(2, $strid, PDO::PARAM_STR);
        $result = $stmt->execute();
        $this->con2->exec("SET SQL_SAFE_UPDATES = 1");
        return $result;
    }
    
}

?>