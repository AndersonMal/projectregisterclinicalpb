<?php 

class UserModel extends Query{
    private $connect;
    private $connect2;

    public function __construct($connect,$connect2)
    {
        parent::__construct($connect);
        $this->connect = $connect;
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
        $sql = "SELECT R.Nombre, U.Descrip, C.FechaHora, RH.RegistroXML, RH.Id
        FROM Pacientes P
        INNER JOIN Casos C ON P.Id = C.Paciente
        INNER JOIN RegistrosHistoria RH ON C.Caso = RH.Caso
        INNER JOIN Registros R ON RH.CodigoRegistro = R.Codigo
        INNER JOIN Unidades U ON RH.UnidadFuncional = U.Codigo
        WHERE P.Identificacion = ?  AND RH.Caso = (SELECT MAX(Caso) FROM Casos WHERE Paciente = P.Id)
        ORDER BY RH.Caso DESC;";
        $stmt = $this->connect2->prepare($sql);
        $stmt->bindParam(1, $strid, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getIdRegistro($idRegistro){
        $sql = "SELECT RH.Id AS IdRegistro, P.Identificacion, P.TipoID, P.Nombre AS NombrePaciente, 
                FORMAT(CAST(P.FechaNac AS DATE), 'dd/MM/yyyy') AS FechaNacimiento, 
                RE.Nombre, P.Etnia, P.Regimen, P.DirAfil, P.TelRes, OC.Descrip AS Profesion, 
                P.GrupoPoblacional, P.Acompañante, P.TelAcomp, P.DirAcompañante, P.Responsable, 
                P.TelResponsable, P.ParentescoAcompañante, P.DirResponsable, PR.Nombre AS NombreMedico, 
                PR.Registro AS RegistroMedico, PR.TipoDoc AS TiDocMedico, PR.Documento AS DocMedico, 
                AD.Nombre AS NombreEntidad, SUBSTRING(CONVERT(VARCHAR, RH.FechaAsignacionRegistro, 103), 1, 10) + ' ' + 
                SUBSTRING(CONVERT(VARCHAR, RH.FechaAsignacionRegistro, 108), 1, 5) AS FechaRegistro, RH.Caso, 
                P.Creencia, RE.Nombre AS Religion, P.Nivel, P.Raza, P.ParentescoAcompañante, 
                P.ParentescoResponsable, ES.Descrip, P.Sexo, P.EstadoCivil, P.Creencia, P.Carnet, 
                DATEDIFF(YEAR, FechaNac, GETDATE()) - CASE WHEN (MONTH(FechaNac) > MONTH(GETDATE())) 
                OR (MONTH(FechaNac) = MONTH(GETDATE()) AND DAY(FechaNac) > DAY(GETDATE())) THEN 1 ELSE 0 END AS Edad, 
                RG.Nombre AS NombreRegistro, RH.RegistroXML 
                FROM RegistrosHistoria RH
                INNER JOIN Registros RG ON RH.CodigoRegistro = RG.Codigo
                INNER JOIN Prestadores PR ON RH.Usuario = PR.Usuario
                INNER JOIN PlanAdm PL ON RH.AdmPlan = PL.Id
                INNER JOIN Administradoras AD ON PL.Administradora = AD.CodAdminis
                INNER JOIN Especialidades ES ON PR.Especialidad = ES.Codigo
                INNER JOIN Casos C ON RH.Caso = C.Caso
                INNER JOIN Pacientes P ON C.Paciente = P.Id
                INNER JOIN Ocupaciones OC ON P.Profesion = OC.Codigo
                LEFT JOIN Religiones RE ON P.idReligion = RE.Codigo
                WHERE RH.Id = ?";
            // Ejecutar la consulta con el idRegistro
            $stmt = $this->connect2->prepare($sql);
            $stmt->execute([$idRegistro]);
            return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getUsers()
    {
        $sql = "SELECT Id,numeroDocumento, primerApellido, fecharegistro FROM user_registered WHERE numeroDocumento != 'Administrador'";
        $stmt = $this->connect->prepare($sql);  
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    public function countUsers(){
        $sql = "SELECT 
                    YEAR(fecharegistro) AS anio,
                    MONTH(fecharegistro) AS mes,
                    COUNT(*) AS cantidad_usuarios
                FROM 
                    user_registered
                WHERE Id != 1
                GROUP BY 
                    YEAR(fecharegistro), 
                    MONTH(fecharegistro)
                ORDER BY 
                    anio, mes;";
        $stmt = $this->connect->prepare($sql);  
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
    

}

?>