
<?php 

class RegistersClinicalModel extends Query{

    private $con1;

    public function __construct($con1)
    {
        parent::__construct($con1);
    }
    public function getClinicalData($identification) {
        $sql = "SELECT R.Nombre, U.Descrip, C.FechaHora 
                FROM Pacientes P
                INNER JOIN Casos C ON P.Id = C.Paciente
                INNER JOIN RegistrosHistoria RH ON C.Caso = RH.Caso
                INNER JOIN Registros R ON RH.CodigoRegistro = R.Codigo
                INNER JOIN Unidades U ON RH.UnidadFuncional = U.Codigo
                WHERE P.Identificacion = '$identification'";
        
        $stmt = $this->con1->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

}

?>