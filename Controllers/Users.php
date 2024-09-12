<?php 
require_once 'Models/UserModel.php';

class Users extends Controller{
    private $model;

    public function __construct() {
        session_start();
        parent::__construct();
        $conexion = new Conexion();
        $this->model = new UserModel($conexion->getConnection2(),$conexion->getConnection()); 
    }

    public function index()
    {
        if (!isset($_SESSION['id_user'])) {
            header("Location: " . base_url );
            exit();
        }
        //print_r($this->model->getUser());
        $this->views->getView($this, "index");
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: " . base_url );
        exit();
    }

    public function validate(){
        try {
            if(isset($_POST['logout']))
                {
                    session_destroy();
                    unset($_SESSION['username']);
                }
            if(empty($_POST['document']) || empty($_POST['password'])){
                $msg = "Los campos estan vacios";
            } else {
                $user = $_POST['document'];
                $password = $_POST['password'];
                $data = $this->model->getUser($user);
                if($data && $data['contraseña']==$password){
                    $_SESSION['id_user'] = $data['numeroDocumento'];
                    $_SESSION['name'] = $data['primerApellido'];
                    $_SESSION['identification'] = $data['numeroDocumento'];
                    $msg = "Ok";
                } else {
                    $msg = "Usuario o contraseña incorrecta";
                }
            }
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode("Error: " . $e->getMessage());
        }
        die();
    }

    public function listRegistersClinical(){
        
        $data = $this->model->getRegisters($_SESSION['id_user']);
        $data = json_decode($data);
        $this->views->getView($this, "index", $data);


    }

    public function generatedPDF(){
        $dataPDF = $this->model->getRegisters($_SESSION['id_user']);
        $dataArray = json_decode($dataPDF);
        foreach ($dataArray as $item) {
            echo "RegistroXML: " . htmlspecialchars($item->RegistroXML) . "<br><br><br>";
        }        
        echo "\n";
        echo "\n";
        print_r($dataArray);
    }

    public function createPDF(){
            require('Libraries/FPDF/fpdf.php');
    
            $pdf = new FPDF('P','mm','A4');
            $pdf->AddPage();
            $pdf->Image('Assets/css/v9_58.png', 15,8,23);
            $pdf->setTitle(utf8_decode('Registro Clínico'));
            $pdf->SetFont('Courier','B',10);
            
            $pdf->Cell(60);
            $pdf->MultiCell(120,4 , utf8_decode("PERFECT BODY MEDICAL CENTER"), 0, 'l', false);
            $pdf->Cell(65);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(29,3, utf8_decode("Identificación Interna:"));
            $pdf->SetFont('Arial','',8);
            $pdf->MultiCell(120,3, utf8_decode(" 900223667"));
            $pdf->Cell(65);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(24,3, utf8_decode("Cód. Habilitación:"));
            $pdf->SetFont('Arial','',8);
            $pdf->MultiCell(120,3, utf8_decode(" 470010087701"));
            $pdf->Cell(47);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(14,3, utf8_decode("Dirección:"));
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(50,3, utf8_decode(" Cra 20 No 15 - 110, Barrio El Jardín"));
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(12,3, utf8_decode("Teléfono:"));
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(10,3, utf8_decode(" 4237101"));

          
        
            // Obtener los datos de la consulta
            $data = $this->model->getDataPatients($_SESSION['id_user']);
            $decodedData = json_decode($data, true);
            
            if (!empty($decodedData)) {
                // Solo seleccionamos el primer registro, ya que puede haber varios duplicados
                $patient = $decodedData[0];

                // Agregar fecha de impresión y otros datos generales
                // Definir las coordenadas y el tamaño del cuadro
                $x = 10;  // Coordenada X del cuadro
                $y = 27;  // Coordenada Y del cuadro
                $width = 190;  // Ancho del cuadro
                $height = 73;  // Altura del cuadro

                // Dibujar el cuadro
                $pdf->Rect($x, $y, $width, $height);

                // Posicionar el cursor dentro del cuadro para el contenido
                $pdf->SetXY(10, 30);  // Ajustar la posición inicial del contenido

                // Añadir los datos dentro del cuadro
                // Definir el ancho de cada columna
                $pdf->SetFont('Courier','B',10);
                $pdf->Cell(70);
                $pdf->MultiCell(120,1 , utf8_decode("INFORMACIÓN GENERAL"), 0, 'l', false);
                $pdf->Ln(2);

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(27, 1, utf8_decode("Centro de atención:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(61, 1, utf8_decode("01 - SEDE PRINCIPAL"));

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(14, 1, utf8_decode("Admision:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(60, 1, utf8_decode($patient['Caso']));
                $pdf->Ln(4);


                // Datos del paciente
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(13, 1, utf8_decode("Paciente:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(140, 1, utf8_decode($patient['TipoID'] . " " . $patient['Identificacion'] . " - " . $patient['NombrePaciente']));


                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(8, 1, utf8_decode("Sexo:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(50, 1, utf8_decode($patient['Sexo']));
                $pdf->Ln(4);

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(30, 1, utf8_decode("Fecha de Nacimiento:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(58, 1, utf8_decode($patient['FechaNacimiento']));

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(9, 1, utf8_decode("Edad:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(56, 1, utf8_decode($patient['Edad'] . " años"));
                
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(18, 1, utf8_decode("Estado Civil:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(40, 1, utf8_decode($patient['EstadoCivil']));
                $pdf->Ln(4);

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(8, 1, utf8_decode("Etnia:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(80, 1, utf8_decode($patient['Etnia']));

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(30, 1, utf8_decode("Creencia:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(50, 1, utf8_decode($patient['Creencia']));
                $pdf->Ln(4);

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(14, 1, utf8_decode("Regimen:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(74, 1, utf8_decode($patient['Regimen']));

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(8, 1, utf8_decode("Nivel:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(57, 1, utf8_decode($patient['Nivel']));
                
                
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(30, 1, utf8_decode("Carnet:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(50, 1, utf8_decode($patient['Carnet']));
                $pdf->Ln(0);

                // Datos de contacto
                $pdf->Ln(4);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(15, 1, utf8_decode("Dirección:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(65, 1, utf8_decode($patient['DirAfil']));
                $pdf->Ln(4);

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(13, 1, utf8_decode("Teléfono:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(75, 1, utf8_decode($patient['TelRes']));
                
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(10, 1, utf8_decode("Lugar:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(60, 1, utf8_decode("Santa Marta Magdalena"));
                $pdf->Ln(4);
                
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(16, 1, utf8_decode("Ocupación:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(72, 1, utf8_decode($patient['Profesion']));
                $pdf->Ln(4);

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(10, 1, utf8_decode("Grupo poblacional:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(78, 1, utf8_decode($patient['GrupoPoblacional']));

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(10, 1, utf8_decode("Raza:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(60, 1, utf8_decode($patient['Raza']));
                $pdf->Ln(4);

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(10, 1, utf8_decode("Acompañante:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(60, 1, utf8_decode($patient['Acompañante']));
                $pdf->Ln(4);

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(10, 1, utf8_decode("Teléfono Acomp.:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(78, 1, utf8_decode($patient['TelAcomp']));

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(10, 1, utf8_decode("Parentezco Acomp.:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(60, 1, utf8_decode($patient['ParentescoAcompañante']));
                $pdf->Ln(4);

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(10, 1, utf8_decode("Direccion Acomp:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(60, 1, utf8_decode($patient['DirAcompañante']));
                $pdf->Ln(4);

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(10, 1, utf8_decode("Responsable:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(70, 1, utf8_decode($patient['Responsable']));
                $pdf->Ln(4);

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(10, 1, utf8_decode("Teléfono Resp.:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(78, 1, utf8_decode($patient['TelResponsable']));

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(10, 1, utf8_decode("Parentesco Resp.:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(60, 1, utf8_decode($patient['ParentescoResponsable']));
                $pdf->Ln(4);

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(10, 1, utf8_decode("Dirección Resp.:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(60, 1, utf8_decode($patient['DirResponsable']));
                $pdf->Ln(4);


                // Información adicional

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(23, 1, utf8_decode("Médico Tratante:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(65, 1, utf8_decode($patient['NombreMedico']));

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(19, 1, utf8_decode("Especialidad:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(50, 1, utf8_decode($patient['Descrip']));
                $pdf->Ln(4);

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(23, 1, utf8_decode("Administradora:"), 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(65, 1, utf8_decode($patient['NombreEntidad']));
                

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(10, 1, utf8_decode("Tipo Vinculación:"), 0);


                $x = 10;  // Coordenada X del cuadro
                $y = 101;  // Coordenada Y del cuadro
                $width = 190;  // Ancho del cuadro
                $height = 5;  // Altura del cuadro

                // Dibujar el cuadro
                $pdf->Rect($x, $y, $width, $height);
                $pdf->SetXY(10, 103);

                $pdf->SetFont('Courier','B',10);
                $pdf->Cell(70);
                $pdf->Cell(120,1 , utf8_decode(strtoupper($patient['NombreRegistro'])), 0);

                

            }


            $pdf->Output();
        
    }
 

}

?>