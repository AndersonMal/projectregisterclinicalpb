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
                    if($data['numeroDocumento']==$user && $data['numeroDocumento'] == 'Administrador'){
                        $_SESSION['id_user'] = $data['numeroDocumento'];
                        $_SESSION['name'] = $data['primerApellido'];
                        $_SESSION['identification'] = $data['numeroDocumento'];
                        $msg="Admin";
                    }else{
                        $_SESSION['id_user'] = $data['numeroDocumento'];
                        $_SESSION['name'] = $data['primerApellido'];
                        $_SESSION['identification'] = $data['numeroDocumento'];
                        $msg = "Ok";
                    }
                }else{
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

    public function panelAdmin(){
        $data = $this->model->getRegisters($_SESSION['id_user']);
        $data = json_decode($data);
        $this->views->getView($this, "adminPanel", $data);

    }
    public function userList(){
        $data = $this->model->getUsers();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function countUsersRegisters(){
        $data = $this->model->countUsers();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function generaPDF($idRegistro){
        require('Libraries/FPDF/fpdf.php'); 
        // Obtener los datos de la consulta
        $data = $this->model->getIdRegistro($idRegistro);

        $decodedData = json_decode($data, true);
        if (!empty($decodedData)) {
            $patient = $decodedData[0]; 
                                
            $pdf = new FPDF('P','mm','A4');
            $pdf->AddPage();
            $pdf->Image('Assets/css/v9_58.png', 15,8,23);
            $pdf->setTitle(utf8_decode('Registro Clínico'));
            $pdf->SetFont('Courier','B',10);
            
            $pdf->Cell(65);
            $pdf->MultiCell(120,4 , utf8_decode("PERFECT BODY MEDICAL CENTER"), 0, 'l', false);
            $pdf->Cell(70);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(30,3, utf8_decode("Identificación interna:"));
            $pdf->SetFont('Arial','',8);
            $pdf->MultiCell(120,3, utf8_decode(" 900223667"));
            $pdf->Cell(70);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(24,3, utf8_decode("Cód. Habilitación:"));
            $pdf->SetFont('Arial','',8);
            $pdf->MultiCell(120,3, utf8_decode(" 470010087701"));
            $pdf->Cell(52);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(14,3, utf8_decode("Dirección:"));
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(50,3, utf8_decode(" Cra 20 No 15 - 110, Barrio El Jardín"));
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(12,3, utf8_decode("Teléfono:"));
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(10,3, utf8_decode(" 4237101"));


            $x = 10;
            $y = 27;
            $width = 190;
            $height = 73; 

            $pdf->Rect($x, $y, $width, $height);

            $pdf->SetXY(10, 30);
            $pdf->SetFont('Courier','B',10);
            $pdf->Cell(75);
            $pdf->MultiCell(120,1 , utf8_decode("INFORMACIÓN GENERAL"), 0, 'l', false);
            $pdf->Ln(2);

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(27, 1, utf8_decode("Centro de atención:"), 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(60, 1, utf8_decode("01 - SEDE PRINCIPAL"));

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(27, 1, utf8_decode("Fecha de Atención:"), 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(39, 1, utf8_decode($patient['FechaRegistro']));

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
            $pdf->Cell(8, 1, utf8_decode("Religion:"), 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(57, 1, utf8_decode($patient['Religion']));

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
            $pdf->Cell(15, 1, utf8_decode("Carnet:"), 0);
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
            $pdf->Cell(20, 1, utf8_decode("Acompañante:"), 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(60, 1, utf8_decode($patient['Acompañante']));
            $pdf->Ln(4);

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(25, 1, utf8_decode("Teléfono Acomp.:"), 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(63, 1, utf8_decode($patient['TelAcomp']));

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(28, 1, utf8_decode("Parentezco Acomp.:"), 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(60, 1, utf8_decode($patient['ParentescoAcompañante']));
            $pdf->Ln(4);

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(25, 1, utf8_decode("Direccion Acomp:"), 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(60, 1, utf8_decode($patient['DirAcompañante']));
            $pdf->Ln(4);

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(20, 1, utf8_decode("Responsable:"), 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(70, 1, utf8_decode($patient['Responsable']));
            $pdf->Ln(4);

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(25, 1, utf8_decode("Teléfono Resp.:"), 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(63, 1, utf8_decode($patient['TelResponsable']));

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(25, 1, utf8_decode("Parentesco Resp.:"), 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(60, 1, utf8_decode($patient['ParentescoResponsable']));
            $pdf->Ln(4);

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(25, 1, utf8_decode("Dirección Resp.:"), 0);
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
            $pdf->Cell(24, 1, utf8_decode("Tipo Vinculación:"), 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(65, 1, utf8_decode(""));   

            $pdf->Ln(1);

             if($patient['NombreRegistro'] == 'Evolución de Urgencia'){                   
                $this->evolucionUrgencia($pdf, $patient);
             }else if($patient['NombreRegistro'] == 'Registro Clínico de Urgencia'){                   
                $this->RegistroUrgencia($pdf, $patient);
             }else if($patient['NombreRegistro'] == 'Incapacidad'){                   
                $this->incapacidad($pdf, $patient);
             }else if($patient['NombreRegistro'] == 'Epicrisis'){                   
                $this->Epicrisis($pdf, $patient);
             }else if($patient['NombreRegistro'] == 'Historia Clínica Consulta Externa'){                   
                $this->consultaExterna($pdf, $patient);
             }else if($patient['NombreRegistro'] == 'Historia Clínica Hospitalización'){                   
                $this->RegistroHospitalizacion($pdf, $patient);
             }
        }

        $pdf->Output();
    
}

    public function incapacidad($pdf, $patient){
        $x = 10;  
        $y = 101; 
        $width = 190;
        $height = 5;  

        $pdf->Rect($x, $y, $width, $height);
        $pdf->SetFont('Courier', 'B', 10);
        $texto = utf8_decode(strtoupper($patient['NombreRegistro']));
        $anchoTexto = $pdf->GetStringWidth($texto);
        $xCentrada = $x + ($width - $anchoTexto) / 2;
        $pdf->SetXY($xCentrada, $y );
        $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');
        
        $pdf->Ln(6);

        try {
            $xml = new SimpleXMLElement($patient['RegistroXML']);
            $maxY = 310;
            
            foreach ($xml as $item) {
                // Definimos los campos y sus títulos
                $campos2 = [
                    'NumeroIngreso' => ['Número - Ingreso:'],
                    'Servicio' => ['Servicio:'],
                    'Responsable' => ['Responsable:'],
                    'CodigoDiagnosticoPrincipal' => ['Diagnóstico Principal:'],
                    'CodigoDiagnosticoRelacionado1' => ['Diagnóstico Relacionado1:'], 
                ];
        
                $diagnosticoPrincipal = '';
                $codigoDiagnosticoPrincipal = '';
                $diagnosticoRelacionado1 = '';
                $codigoDiagnosticoRelacionado1 = '';
            
                foreach ($xml as $item) {
                    $campo = (string)$item['NombreCampo'];
                    $valorCampo = (string)$item['ValorCampo'];

                    if ($campo === 'CodigoDiagnosticoPrincipal') {
                        $codigoDiagnosticoPrincipal = $valorCampo;
                    } elseif ($campo === 'DiagnosticoPrincipal') {
                        $diagnosticoPrincipal = $valorCampo;
                    } elseif ($campo === 'CodigoDiagnosticoRelacionado1') {
                        $codigoDiagnosticoRelacionado1 = $valorCampo;
                    } elseif ($campo === 'DiagnosticoRelacionado1') {
                        $diagnosticoRelacionado1 = $valorCampo;
                    } else {
                        if (isset($campos2[$campo])) {
                            $pdf->SetFont('Arial', 'B', 8);
                            $pdf->Cell(65, 3, utf8_decode($campos2[$campo][0]));
                            $pdf->SetFont('Arial', '', 8);
                            $textoPlano = strip_tags(html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8'));
                            $pdf->MultiCell(145, 3, utf8_decode($textoPlano));
                        }
                    }
                }
                if (!empty($codigoDiagnosticoPrincipal) && !empty($diagnosticoPrincipal)) {
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(65, 3, utf8_decode("Diagnóstico Relacionado:"));
                    $pdf->SetFont('Arial', '', 8);
                    $textoPrincipal = "$codigoDiagnosticoPrincipal - $diagnosticoPrincipal";
                    $pdf->MultiCell(145, 3, utf8_decode(strip_tags(html_entity_decode($textoPrincipal, ENT_QUOTES, 'UTF-8'))));
                }
                if (!empty($codigoDiagnosticoRelacionado1) && !empty($diagnosticoRelacionado1)) {
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(65, 3, utf8_decode("Diagnóstico:"));
                    $pdf->SetFont('Arial', '', 8);
                    $textoRelacionado = "$codigoDiagnosticoRelacionado1 - $diagnosticoRelacionado1";
                    $pdf->MultiCell(145, 3, utf8_decode(strip_tags(html_entity_decode($textoRelacionado, ENT_QUOTES, 'UTF-8'))));
                }
            }
            
            foreach ($xml as $item) {      
                $campos = [
                    'Grupo_servicios' => 'Grupo de servicios:',
                    'Modalidad' => 'Modalidad de la prestación del servicio:',
                    'Presunto' => 'Presunto origen de la incapacidad:',
                    'Prorroga' => 'Prórroga:',
                    'Retroactiva' => 'Incapacidad retroactiva:',
                    'Causas' => 'Causa que motiva la atención:',
                ];
            
                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] == $campo) {
                        $valorCampo = $item['ValorCampo'];
                        
                        if($valorCampo == 'undefined'){
                            $valorCampo = '';
                        }
                        $partes = explode('|', $valorCampo);
                        if (isset($partes[1])) {
                            $valorCampo = $partes[1];
                        }
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(65, 3, utf8_decode($titulo));
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->MultiCell(115, 3, utf8_decode($textoPlano));
                        $pdf->Ln(1);
                    }
                    
                }
            }
            $pdf->Ln();
            foreach ($xml as $item) {
                $campos = [
                    'Observaciones' => 'OBSERVACIONES:',
                    'IncapacidadPor' => 'INCAPACIDAD POR:',
                ];
            
                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] == $campo) {
                        $valorCampo = $item['ValorCampo'];
                        
                        if($valorCampo == 'undefined'){
                            $valorCampo = '';
                        }
                        $partes = explode('|', $valorCampo);
                        if (isset($partes[1])) {
                            $valorCampo = $partes[1];
                        }
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(65, 3, utf8_decode($titulo));
                        $pdf->Ln();
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->MultiCell(115, 3, utf8_decode($textoPlano));
                        $pdf->Ln(5);
                    }
                    
                }
            }
            
            $this->drawInformationDoctor($pdf, $patient, $maxY);

        } catch (Exception $e) {
            echo "Error al procesar el XML: " . $e->getMessage();
        }
    }

    public function evolucionUrgencia($pdf, $patient){
        
        $x = 10;  
        $y = 101;  
        $width = 190;  
        $height = 5; 

        $pdf->Rect($x, $y, $width, $height);
        $pdf->SetFont('Courier', 'B', 10);

        $texto = utf8_decode(strtoupper($patient['NombreRegistro']));
        $anchoTexto = $pdf->GetStringWidth($texto);
        $xCentrada = $x + ($width - $anchoTexto) / 2;

        $pdf->SetXY($xCentrada, $y );
        $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

        $x = 10;
        $y = 107; 
        $width = 190;
        $height = 5;

        $this->dottedRect($pdf, $x, $y, $width, $height);

        $pdf->SetFont('Courier', 'B', 10); 
        $text = "DESCRIPCIÓN DE LA NOTA";
        $text_width = $pdf->GetStringWidth($text);
        $text_x = $x + ($width - $text_width) / 2;
        $text_y = $y + ($height / 2) - 0.5; 

        $pdf->SetXY($text_x, $text_y);
        $pdf->Cell($text_width, 1, utf8_decode($text), 0, 1, 'C');

        try {
            $xml = new SimpleXMLElement($patient['RegistroXML']);
            $fechaIngreso = '';
            $horaIngreso = '';
            $minutosIngreso = '';
    
            foreach ($xml->row as $item) {
                if ((string) $item['NombreCampo'] == 'FechaIngreso') {
                    $fechaIngreso = (string) $item['ValorCampo'];
                } elseif ((string) $item['NombreCampo'] == 'Hora') {
                    $partesHora = explode('|', (string) $item['ValorCampo']);
                    $horaIngreso = $partesHora[0];
                } elseif ((string) $item['NombreCampo'] == 'HoraMinutos') {
                    $partesMinutos = explode('|', (string) $item['ValorCampo']);
                    $minutosIngreso = $partesMinutos[0];
                }
    
                if ($fechaIngreso && $horaIngreso && $minutosIngreso) {
                    break;
                }
            }
    
            if ($fechaIngreso && $horaIngreso && $minutosIngreso) {
                $fechaCompleta = $fechaIngreso . ' ' . $horaIngreso . ':' . $minutosIngreso;

                $pdf->SetXY(98, 33); 
                
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(27, 1, utf8_decode("Fecha de Atención:"), 0);
        
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(61, 1, utf8_decode($fechaCompleta));
                $pdf->Ln(4);
            }

            $pdf->SetXY($xCentrada, $y );
            $pdf->Ln(4);
            $maxY = 270;
            $counter = 0;
            $diagnosticoPrincipal = '';
            $observacion = '';

            foreach ($xml as $item) {
                if ((string) $item['NombreCampo'] === 'DescripcionNota') {
                    $valorCampo = (string) $item['ValorCampo'];
                    if ($valorCampo != '') {
                        $pdf->SetXY(25, $pdf->GetY());
                        $pdf->Ln(5);
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->MultiCell(160, 5, utf8_decode($textoPlano), 0);
                        $counter = 0; // Reiniciar contador de columnas para empezar una nueva fila
                        $pdf->Ln(6);
                    }
                }

               
                $campos = [
                    'FechaIngreso' => 'Fecha y Hora de Ingreso:',
                    'Autorizacion' => 'Autorización:',
                    'FechaEgreso' => 'Fecha y Hora de Egreso:',
                    'CausaExterna' => 'Causa Externa:',
                    'EstadoSalida' => 'Estado de Salida:',
                    'DestinoPaciente' => 'Destino del Usuario a la Salida:',
                ];

                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] === $campo && $item['ValorCampo'] != '') {
                        if ($counter > 0 && $counter % 2 == 0) {
                            $pdf->Ln();
                            $counter = 0; // Reiniciar el contador de columnas
                        }

                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(45, 5, utf8_decode($titulo));
                        $pdf->SetFont('Arial', '', 8);

                        $valorCampo = $item['ValorCampo'];
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->Cell(45, 5, utf8_decode($textoPlano));

                        $counter++;
                    }
                }

                if ((string) $item['NombreCampo'] === 'DiagnosticoPrincipal' && (string) $item['ValorCampo'] != '') {
                    $diagnosticoPrincipal = (string) $item['ValorCampo'];
                }

                if ((string) $item['NombreCampo'] == 'ObservacionP') {
                    $valorCampo = (string) $item['ValorCampo'];
                    if($valorCampo != ''){
                        $observacion = (string) $item['ValorCampo'];
                    }
                    
                }
            }

            if ($diagnosticoPrincipal != '') {
                $pdf->Ln();
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(45, 5, utf8_decode('Diagnóstico Principal:'));
                $pdf->SetFont('Arial', '', 8);

                $diagnosticoPrincipalDecodificado = html_entity_decode($diagnosticoPrincipal, ENT_QUOTES, 'UTF-8');
                $diagnosticoPrincipalDecodificado = str_replace(
                    ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                    PHP_EOL,
                    $diagnosticoPrincipalDecodificado
                );
                $textoPlanoDiagnostico = strip_tags($diagnosticoPrincipalDecodificado);
                $pdf->Cell(35, 5, utf8_decode($textoPlanoDiagnostico));
                $pdf->Ln();
            }

            if ($observacion != '') {
                $pdf->Ln(3);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(45, 5, utf8_decode('Observación:'));
                $pdf->SetFont('Arial', '', 8);
                $observacionDecodificada = html_entity_decode($observacion, ENT_QUOTES, 'UTF-8');
                $observacionDecodificada = str_replace(
                    ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                    PHP_EOL,
                    $observacionDecodificada
                );
                $textoPlanoObservacion = strip_tags($observacionDecodificada);
                $pdf->Ln();
                $pdf->MultiCell(160, 5, utf8_decode($textoPlanoObservacion), 0);
            }

        $this->drawInformationDoctor($pdf, $patient, $maxY);
    
        } catch (Exception $e) {
            echo "Error al procesar el XML: " . $e->getMessage();
        }

    }

    public function consultaExterna($pdf, $patient){
           
        $x = 10;  
        $y = 101;  
        $width = 190;  
        $height = 5; 

        $pdf->Rect($x, $y, $width, $height);
        $pdf->SetFont('Courier', 'B', 10);

        $texto = utf8_decode(strtoupper($patient['NombreRegistro']));
        $anchoTexto = $pdf->GetStringWidth($texto);
        $xCentrada = $x + ($width - $anchoTexto) / 2;

        $pdf->SetXY($xCentrada, $y );
        $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

        $x = 10;
        $y = 107; 
        $width = 190;
        $height = 5;

        $this->dottedRect($pdf, $x, $y, $width, $height);

        $pdf->SetFont('Courier', 'B', 10); 
        $text = "DATOS DE LA CONSULTA";
        $text_width = $pdf->GetStringWidth($text);
        $text_x = $x + ($width - $text_width) / 2;
        $text_y = $y + ($height / 2) - 0.5; 

        $pdf->SetXY($text_x, $text_y);
        $pdf->Cell($text_width, 1, utf8_decode($text), 0, 1, 'C');

        $pdf->Ln(3);

        try{
            $xml = new SimpleXMLElement($patient['RegistroXML']);
            $cont = 0;
            $maxY = 310;
    
            foreach ($xml as $item) {      
                $campos = [
                    'TipoConsulta' => 'Tipo de Consulta:',
                    'PlanAdministradora' => 'Plan / Administradora:',
                    'MotivoConsulta' => 'Motivo de Consulta:',
                    'EnfermedadActual' => 'Enfermedad Actual:',
                    'SistemaRespiratorio' => 'Sistema respiratorio:',
                ];
            
                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] == $campo) {
                        $valorCampo = $item['ValorCampo'];
                        
                        if($valorCampo == 'undefined'){
                            $valorCampo = '';
                        }
                        $partes = explode('|', $valorCampo);
                        if (isset($partes[1])) {
                            $valorCampo = $partes[1];
                        }
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(45, 3, utf8_decode($titulo));
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        if($cont == 2){
                            $pdf->Ln();
                        }
                        if($cont == 3){
                            $pdf->Ln();
                        }
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->MultiCell(145, 3, utf8_decode($textoPlano));
                        $pdf->Ln(1);
                        $cont++;
                    }
                    
                }
            }

            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 0.5;
            $width = 190;  
            $height = 5; 
    
            $pdf->Rect($x, $y, $width, $height);
            $pdf->SetFont('Courier', 'B', 10);
    
            $texto = utf8_decode(strtoupper("ANTECEDENTES"));
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
    
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

            $pdf->Ln(5);

            foreach ($xml as $item) {      
                $campos = [
                    'AntecedentesPersonales' => 'Personales:',
                    'AntecedentesFamiliares' => 'Familiares:',
                    'AntecedentesAlergicos' => 'Alérgicos:',
                    'AntecedentesQx' => 'Quirúrgicos:',
                    'aAlergiaMedicamento' => 'Alergia-toxicidad a medicamentos:', 
                ];
            
                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] == $campo) {
                        $valorCampo = $item['ValorCampo'];
                        
                        if($valorCampo == 'undefined'){
                            $valorCampo = '';
                        }

                        if($valorCampo == 'false'){
                            $valorCampo = 'No';
                        }

                        $partes = explode('|', $valorCampo);
                        if (isset($partes[1])) {
                            $valorCampo = $partes[1];
                        }
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(45, 3, utf8_decode($titulo));
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Ln();
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->MultiCell(145, 3, utf8_decode($textoPlano));
                        $pdf->Ln(1);
                        $cont++;
                    }
                    
                }
            }

            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 0.5;
            $width = 190;  
            $height = 5; 
            $pdf->Rect($x, $y, $width, $height);
            $pdf->SetFont('Courier', 'B', 10);
    
            $texto = utf8_decode(strtoupper("VARIABLES 4505"));
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');
            $pdf->Ln(4);
            
            $ultimaPosicionY = $pdf->GetY();
            $y = $ultimaPosicionY + 0.5;
            $pdf->SetFont('Arial', 'B', 8);
            $texto = utf8_decode("Variables 4505");
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0);
            
            $pdf->Ln(3); 
            $ultimaPosicionY = $pdf->GetY();
            $y = $ultimaPosicionY + 0.5;
            $pdf->SetFont('Arial', 'B', 8);
            $texto = utf8_decode("Riesgos");
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0);
            
            $pdf->Ln(4);
            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 0.5;
            $width = 190;  
            $height = 5; 
            $pdf->Rect($x, $y, $width, $height);
            $pdf->SetFont('Courier', 'B', 10);
            $texto = utf8_decode(strtoupper("REVISIÓN POR SISTEMAS"));
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

            $pdf->Ln(5);

            $width_total = 190;
            $width_cuadro = $width_total / 3;
            $ultimaPosicionY = $pdf->GetY(); 
            $y = $ultimaPosicionY + 2;
            $height = 4; 
            $width_cuadro1 = 50; 
            $width_cuadro2 = 30;  
            $width_cuadro3 = 110;  

            $x1 = 10;
            $x2 = $x1 + $width_cuadro1; 
            $x3 = $x2 + $width_cuadro2;

            $this->dottedRect($pdf, $x1, $y, $width_cuadro1, $height);
            $pdf->SetFont('Courier', 'B', 9);
            $text1 = "SISTEMA";
            $text_width1 = $pdf->GetStringWidth($text1);
            $text_x1 = $x1 + ($width_cuadro1 - $text_width1) / 2;
            $pdf->SetXY($text_x1, $y + ($height / 1.2) - 2);
            $pdf->Cell($text_width1, 1, utf8_decode($text1), 0, 1, 'C');

            $this->dottedRect($pdf, $x2, $y, $width_cuadro2, $height);
            $text2 = "ESTADO";
            $text_width2 = $pdf->GetStringWidth($text2);
            $text_x2 = $x2 + ($width_cuadro2 - $text_width2) / 2;
            $pdf->SetXY($text_x2, $y + ($height / 1.2) - 2);
            $pdf->Cell($text_width2, 1, utf8_decode($text2), 0, 1, 'C');

            $this->dottedRect($pdf, $x3, $y, $width_cuadro3, $height);
            $text3 = "OBSERVACIÓN";
            $text_width3 = $pdf->GetStringWidth($text3);
            $text_x3 = $x3 + ($width_cuadro3 - $text_width3) / 2;
            $pdf->SetXY($text_x3, $y + ($height / 1.2) - 2);
            $pdf->Cell($text_width3, 1, utf8_decode($text3), 0, 1, 'C');
            $pdf->Ln(3);

            
            foreach ($xml as $item) {
                
                $campos = [
                    'tipoConsulta1' => 'Estado de conciencia:',
                    'tipoConsulta2' => 'Piel y mucosa:',
                    'tipoConsulta3' => 'Cabeza,Cara y Cuero Cabelludo:',
                    'tipoConsulta4' => 'Cuello:',
                    'tipoConsulta5' => 'Órganos de los Sentidos:',
                    'tipoConsulta6' => 'Tórax:',
                    'tipoConsulta7' => 'Respiratorio:',
                    'tipoConsulta8' => 'Cardíaco:',
                    'tipoConsulta9' => 'Vascular Periféricos:',
                    'tipoConsulta10' => 'Abdomen:',
                    'tipoConsulta11' => 'Perianal:',
                    'tipoConsulta12' => 'Region Inguinal:',
                    'tipoConsulta13' => 'Genitales:',
                    'tipoConsulta14' => 'Extremidades:',
                    'tipoConsulta15' => 'Sist. Nerv. Central:',
                    'tipoConsulta16' => 'Sist. Nerv. Periférico:',
                    'tipoConsulta17' => 'Sistema Linfático:',
                    'tipoConsulta18' => 'Sist. Osteo-Articular:',

                ];

                $camposDescripcion = [
                    'tipoDescripcionConsulta1',
                    'tipoDescripcionConsulta2',
                    'tipoDescripcionConsulta3',
                    'tipoDescripcionConsulta4',
                    'tipoDescripcionConsulta5',
                    'tipoDescripcionConsulta6',
                    'tipoDescripcionConsulta7',
                    'tipoDescripcionConsulta8',
                    'tipoDescripcionConsulta9',
                    'tipoDescripcionConsulta10',
                    'tipoDescripcionConsulta11',
                    'tipoDescripcionConsulta12',
                    'tipoDescripcionConsulta13',
                    'tipoDescripcionConsulta14',
                    'tipoDescripcionConsulta15',
                    'tipoDescripcionConsulta16',
                    'tipoDescripcionConsulta17',
                    'tipoDescripcionConsulta18',
                ];
            
                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] == $campo) {
                        $valorCampo = $item['ValorCampo'];

                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(55, 3, utf8_decode($titulo));
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->Cell(25, 3, utf8_decode($textoPlano));

                        $campoDescripcion = str_replace('tipoConsulta', 'tipoDescripcionConsulta', $campo);
                        if (in_array($campoDescripcion, $camposDescripcion)) {
                            $valorDescripcion = ''; 
                            $valorDescripcionDecodificado = html_entity_decode($valorDescripcion, ENT_QUOTES, 'UTF-8');
                            $valorDescripcionDecodificado = str_replace(
                                ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                                PHP_EOL,
                                $valorDescripcionDecodificado
                            );
                            $textoDescripcionPlano = strip_tags($valorDescripcionDecodificado);
                            $pdf->MultiCell(75, 3, utf8_decode($textoDescripcionPlano));
                        }

                    }
                }
            }

            $pdf->Ln(3);
            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 0.5;
            $width = 190;  
            $height = 5; 
            $pdf->Rect($x, $y, $width, $height);
            $pdf->SetFont('Courier', 'B', 10);
            $texto = utf8_decode(strtoupper("SIGNOS VITALES"));
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

            $pdf->Ln(2);
            $cont = 0;
            foreach ($xml as $item) {
                $campos = [
                    'FrecuenciaCardiaca' => ['F.Cardiaca:', 'xMin'],
                    'Temperatura' => ['Temperatura:', '°C'],
                    'FrecuenciaRespiratoria' => ['F.Respiratoria:', 'xMin'],
                    'Peso' => ['Peso:', 'Kg'],
                    'Talla' => ['Talla:', 'm'],
                    'PresionArterialMedia' => ['Presión', '/mmHg'],
                    'IndiceMasaCorporal' => ['Indice de masa corporal:', 'Kg/m²'],
                    'SuperficieMasaCorporal' => ['Superficie masa corporal:', 'm²'],
                ];
            
                foreach ($campos as $campo => $tituloYUnidad) {
                    if ((string) $item['NombreCampo'] === $campo) {
                        $titulo = $tituloYUnidad[0]; 
                        $unidad = $tituloYUnidad[1]; 
                        $valorCampo = (string) $item['ValorCampo'];
                            if ($cont % 3 == 0) {
                                $pdf->Ln();
                            }
                            
                            $pdf->SetFont('Arial', 'B', 8);
                            $pdf->Cell(35, 3, utf8_decode($titulo)); 
                            $pdf->SetFont('Arial', '', 8);
                            
                            $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                            $valorCampoDecodificado = str_replace(
                                ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                                PHP_EOL,
                                $valorCampoDecodificado
                            );
                            $textoPlano = strip_tags($valorCampoDecodificado);
                            
                            $pdf->Cell(25, 3, utf8_decode($textoPlano . ' ' . $unidad));
                            $cont++;
                    }
                }
            }

            $pdf->Ln(3);
            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 0.5;
            $width = 190;  
            $height = 5; 
            $pdf->Rect($x, $y, $width, $height);
            $pdf->SetFont('Courier', 'B', 10);
            $texto = utf8_decode(strtoupper("EXAMEN FISICO"));
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

            $pdf->Ln(5);
            $cont = 0;

            foreach ($xml as $item) {
                $campos2 = [
                    'Apariencia' => ['Apariencia:'],
                    'CraneoCaraCuello' => ['Cráneo, cara y cuello:'],
                    'Torax' => ['Tórax:'],
                    'Abdomen' => ['Abdomen:'],
                    'PielYFaneras' => ['Piel y faneras:'],
                    'GenitoUrinario' => ['Genito-urinario:'],
                    'Extremidades' => ['Extremidades:'],
                    'SistemaNerviosoCentral' => ['Sistema nervioso central:'],
                    'Conciliacionmedicamentosa' => ['Conciliación medicamentosa:'],
                    'Analisis' => ['Análisis:'],
                ];

                foreach ($campos2 as $campo => $titulo2) {
                    if ((string) $item['NombreCampo'] === $campo) {
                        $valorCampo = $item['ValorCampo'];
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(42, 3, utf8_decode($titulo2[0]));
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Ln();
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                
                        $pdf->MultiCell(145, 3, utf8_decode($textoPlano));
                    }
                }             
                         
            }

            $pdf->Ln(3);
            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 0.5;
            $width = 190;  
            $height = 5; 
            $pdf->Rect($x, $y, $width, $height);
            $pdf->SetFont('Courier', 'B', 10);
            $texto = utf8_decode(strtoupper("OBSERVACIONES Y RESULTADOS DE PARACLÍNICOS"));
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

            $pdf->Ln(5);
            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 0.5;
            $width = 190;  
            $height = 5; 
            $pdf->Rect($x, $y, $width, $height);
            $pdf->SetFont('Courier', 'B', 10);
            $texto = utf8_decode(strtoupper("PREGUNTAS RED DE BUEN TRATO"));
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

            $pdf->Ln(4);
            $cont = 0;
            foreach ($xml as $item) {
                $campos = [
                    'ViolenciaCasa' => ['¿Ha sufrido algún tipo de violencia en su casa?: '],
                    'ViolenciaCasaFisico' => ['Fisico:'],
                    'ViolenciaCasaSexual' => ['Sexual:'],
                    'ViolenciaCasaEmocional' => ['Emocional:'],
                    'SienteRiesgo' => ['¿Usted se siente en riesgo?:'],
                    'HablarTema' => ['¿Quiere hablar del tema?:'],
                ];
            
                foreach ($campos as $campo => $titulo2) {
                    if ((string) $item['NombreCampo'] === $campo) {
                        $valorCampo = $item['ValorCampo'];
                        if($valorCampo == 'undefined'){
                            $valorCampo = '';
                        }
                        if ($cont % 2 == 0) {
                            $pdf->Ln();
                        }
                           
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(75, 3, utf8_decode($titulo2[0])); 
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                
                        $pdf->Cell(25, 3, utf8_decode($textoPlano));
                        $cont++;
                    }
                }   
            }

            $pdf->Ln(5);
            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 0.5;
            $width = 190;  
            $height = 5; 
            $pdf->Rect($x, $y, $width, $height);
            $pdf->SetFont('Courier', 'B', 10);
            $texto = utf8_decode(strtoupper("RIPS"));
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');
            $pdf->Ln(7);

            foreach ($xml as $item) {
                $campos2 = [
                    'FinalidadConsulta' => ['Finalidad Consulta:'],
                    'CausaExterna' => ['Causa Externa:'],
                    'TipoDiagnosticoPrincipal' => ['Tipo Diagnóstico Principal:'],
                    'DiagnosticoPrincipal' => ['Diagnóstico Principal:'],
                    'DiagnosticoRelacionado1' => ['Diagnóstico Relacionado 1:'],
                    'DiagnosticoRelacionado2' => ['Diagnóstico Relacionado 2:'],
                    'DiagnosticoRelacionado3' => ['Diagnóstico Relacionado 3:'],
                    'PlanTratamiento' => ['Plan de Tratamiento:']
                ];

                foreach ($campos2 as $campo => $titulo2) {
                    if ((string) $item['NombreCampo'] === $campo) {
                        $valorCampo = $item['ValorCampo'];
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(42, 3, utf8_decode($titulo2[0]));
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                
                        $pdf->MultiCell(145, 3, utf8_decode($textoPlano));  
                    }
                }             
                         
            }
            
            $this->drawInformationDoctor($pdf, $patient, $maxY);


        } catch (Exception $e) {
            echo "Error al procesar el XML: " . $e->getMessage();
        }

    }
    
    public function RegistroUrgencia($pdf, $patient){

        $x = 10;  
        $y = 101;  
        $width = 190;  
        $height = 5; 

        $pdf->Rect($x, $y, $width, $height);
        $pdf->SetFont('Courier', 'B', 10);

        $texto = utf8_decode(strtoupper($patient['NombreRegistro']));
        $anchoTexto = $pdf->GetStringWidth($texto);
        $xCentrada = $x + ($width - $anchoTexto) / 2;

        $pdf->SetXY($xCentrada, $y );
        $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

        $x = 10;
        $y = 107; 
        $width = 190;
        $height = 4;

        $this->dottedRect($pdf, $x, $y, $width, $height);

        $pdf->SetFont('Courier', 'B', 9); 
        $text = "DATOS DE LA CONSULTA";
        $text_width = $pdf->GetStringWidth($text);
        $text_x = $x + ($width - $text_width) / 2;
        $text_y = $y + ($height / 2) - 0.5; 

        $pdf->SetXY($text_x, $text_y);
        $pdf->Cell($text_width, 1, utf8_decode($text), 0, 1, 'C');
        
        $pdf->Ln(2);

        try {
            $xml = new SimpleXMLElement($patient['RegistroXML']);

            
            $maxY = 310;
            $planAdministradora = '';
            $tipoConsulta = '';
            $counter = 0;
            foreach ($xml as $item) {
                
                $campos = [
                    'ORemitido' => 'Remitido:',
                    'Contrareferencia' => 'Contra referencia:',
                    'GlasW' => 'Glasgow:',
                    'Valoracion' => 'Valoracion:',
                    'Prioridad' => 'Triage:',
                ];
            
                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] == $campo) {
                        $valorCampo = $item['ValorCampo'];
            
                        if ($item['NombreCampo'] == 'ORemitido' && $item['NombreTabla'] == 'CamposBoolean') {
                            $valorCampo = ($valorCampo == 0) ? 'No' : 'Si';
                        } elseif ($item['NombreCampo'] == 'ORemitido' && $item['NombreTabla'] == 'CamposTexto' && $valorCampo == 0) {
                            $valorCampo = '';
                        }
            
                        if ($item['NombreCampo'] == 'Contrareferencia' && $item['ValorCampo'] == "false") {
                            $valorCampo = 'No';
                        }

                        $partes = explode('|', $valorCampo);
                        if (isset($partes[1])) {
                            $valorCampo = $partes[1];
                        }

                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(45, 3, utf8_decode($titulo));
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->Cell(45, 3, utf8_decode($textoPlano));
                        $counter++;
            
                        if ($counter % 2 == 0) {
                            $pdf->Ln();
                        }
                    }
                }

                if ((string) $item['NombreCampo'] === 'TipoConsulta') {
                    $tipoConsulta = (string) $item['ValorCampo'];

                    $partes = explode('|', $tipoConsulta);
                    if (isset($partes[1])) {
                        $tipoConsulta = $partes[1];
                    }
                }
            
                if ((string) $item['NombreCampo'] === 'PlanAdministradora') {
                    $planAdministradora = (string) $item['ValorCampo'];
                    $partes = explode('|', $planAdministradora);
                    if (isset($partes[1])) {
                        $planAdministradora = $partes[1]; 
                    }
                }
            }
            
            if ($tipoConsulta != '') {
                $pdf->Ln(3);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(45, 3, utf8_decode('Tipo de consulta:'));
                $pdf->SetFont('Arial', '', 8);

                $diagnosticoPrincipalDecodificado = html_entity_decode($tipoConsulta, ENT_QUOTES, 'UTF-8');
                $diagnosticoPrincipalDecodificado = str_replace(
                    ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                    PHP_EOL,
                    $diagnosticoPrincipalDecodificado
                );
                $textoPlanoDiagnostico = strip_tags($diagnosticoPrincipalDecodificado);
                $pdf->Cell(35, 3, utf8_decode($textoPlanoDiagnostico));
                $pdf->Ln();
            }

            if ($planAdministradora != '') {
                $pdf->Ln(1);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(45, 3, utf8_decode('Plan / Administradora:'));
                $pdf->SetFont('Arial', '', 8);

                $diagnosticoPrincipalDecodificado = html_entity_decode($planAdministradora, ENT_QUOTES, 'UTF-8');
                $diagnosticoPrincipalDecodificado = str_replace(
                    ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                    PHP_EOL,
                    $diagnosticoPrincipalDecodificado
                );
                $textoPlanoDiagnostico = strip_tags($diagnosticoPrincipalDecodificado);
                $pdf->Cell(35, 3, utf8_decode($textoPlanoDiagnostico));
                $pdf->Ln();
            }
            
            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 2;
            $width = 190;
            $height = 4;

            $this->dottedRect($pdf, $x, $y, $width, $height);

            $pdf->SetFont('Courier', 'B', 9); 
            $text = "MOTIVO DE LA CONSULTA";
            $text_width = $pdf->GetStringWidth($text);
            $text_x = $x + ($width - $text_width) / 2;
            $text_y = $y + ($height / 2) - 0.5; 

            $pdf->SetXY($text_x, $text_y);
            $pdf->Cell($text_width, 1, utf8_decode($text), 0, 1, 'C');
            
            $pdf->Ln(3);

            foreach ($xml as $item) {
                if ((string)$item['NombreCampo'] === 'MotivoConsulta') {
                    $valorCampo = (string)$item['ValorCampo'];  
                    $pdf->SetFont('Arial', '', 8);
                    $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlano = strip_tags($valorCampoDecodificado);
                    $pdf->MultiCell(0, 2, utf8_decode($textoPlano)); // Usa MultiCell para manejar texto largo
                }
            }

            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 1;
            $width = 190;
            $height = 4;

            $this->dottedRect($pdf, $x, $y, $width, $height);

            $pdf->SetFont('Courier', 'B', 9); 
            $text = "ENFERMEDAD ACTUAL";
            $text_width = $pdf->GetStringWidth($text);
            $text_x = $x + ($width - $text_width) / 2;
            $text_y = $y + ($height / 2) - 0.5; 

            $pdf->SetXY($text_x, $text_y);
            $pdf->Cell($text_width, 1, utf8_decode($text), 0, 1, 'C');
            
            $pdf->Ln(3);

            foreach ($xml as $item) {
                if ((string)$item['NombreCampo'] === 'EnfermedadActual') {
                    $valorCampo = (string)$item['ValorCampo'];  
                    $pdf->SetFont('Arial', '', 8);
                    $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlano = strip_tags($valorCampoDecodificado);
                    $pdf->MultiCell(0, 3, utf8_decode($textoPlano)); 
                }
            }

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(45, 3.5, utf8_decode('Sistema respiratorio:'), 0, 1);

            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 0.5;
            $width = 190;  
            $height = 5; 
    
            $pdf->Rect($x, $y, $width, $height);
            $pdf->SetFont('Courier', 'B', 10);
    
            $texto = utf8_decode(strtoupper("ANTECEDENTES"));
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
    
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

            $pdf->Ln(5);

            $xInicial = 10;
            $yInicial = $pdf->GetY();
            $anchoCampo = 40;
            $anchoCuadro = 120;
            $alturaCampo = 27;
            $grosorLinea = -1;
            
            foreach ($xml as $item) {
                $xPosicion = $xInicial;
                $yPosicion = $yInicial;
            
                if ((string) $item['NombreCampo'] === 'AntecedentesPersonales') {
                    $valorAntecedentes = (string) $item['ValorCampo'];
            
                    $pdf->SetLineWidth($grosorLinea);
                    $xCuadro = $xPosicion + $anchoCampo + 30;
                    $pdf->Rect($xCuadro, $yPosicion, $anchoCuadro, $alturaCampo);
            
                    $pdf->SetXY($xCuadro + 2, $yPosicion + 2);
                    $pdf->SetFont('Arial', '', 8);

                    $valorCampoDecodificado = html_entity_decode($valorAntecedentes, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlano = strip_tags($valorCampoDecodificado);


                    $pdf->MultiCell($anchoCuadro - 4, 5, utf8_decode($textoPlano));
                    $yPosicion += $alturaCampo;
                    break;
                }
            }
            
            foreach ($xml as $item) {
                $campos = [
                    'aPatologicos' => '1.Patológicos (HTA, Diabetes):',
                    'aQuirurgicos' => '2.Quirúrgicos:',
                    'aHospitalarios' => '3.Hospitalarios:',
                    'aTranfusionales' => '4.Tranfusionales:',
                    'aToxico' => '5.Tóxico-Alérgicos:',
                    'aFarmacologicos' => '6.Farmacológicos:',
                    'aGinecoObstetrico' => '7.Gineco-Obstétrico:',
                    'aTraumaticos' => '8.Traumáticos:',
                    'aOtros' => '9.Otros:', 
                    'aAlergiaMedicamento' => '10.Alergia-toxicidad a medicamentos:',
                ];
            
                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] === $campo) {
                        $valorCampo = (string) $item['ValorCampo'];
            
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(55, 3, utf8_decode($titulo));
                        $pdf->Ln();
                    }
                }
            }
            
            $yPosicion += $alturaCampo;

            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 0.5;
            $width = 190;  
            $height = 5; 
            $pdf->Rect($x, $y, $width, $height);
            $pdf->SetFont('Courier', 'B', 10);
            $texto = utf8_decode(strtoupper("FAMILIARES"));
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
    
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

            $pdf->Ln(3);

            if($patient['Sexo'] == 'F'){
                $pdf->Ln(1);
                $ultimaPosicionY = $pdf->GetY();
                $x = 10;
                $y = $ultimaPosicionY + 0.5;
                $width = 190;  
                $height = 5; 
        
                $pdf->Rect($x, $y, $width, $height);
                $pdf->SetFont('Courier', 'B', 10);
        
                $texto = utf8_decode(strtoupper("GINECOLÓGICOS"));
                $anchoTexto = $pdf->GetStringWidth($texto);
                $xCentrada = $x + ($width - $anchoTexto) / 2;

                $pdf->SetXY($xCentrada, $y );
                $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

                $pdf->Ln(1);

                $cont = 0;
                foreach ($xml as $item) {
                
                    $campos = [
                        'Menarquia2' => 'Menarquia:',
                        'FechaUltimaMestruacion' => 'Última Menstruación:',
                        'UltimoParto2' => 'Ultimo Parto:',
                        'Citologia2' => 'Citologia:',
                        'Gestacion' => 'Gestacion:',
                        'Parto' => 'Paridad:',
                        'Aborto' => 'Aborto:',
                        'Cesarea' => 'Cesárea:',
                        'Vivos' => 'Vivos:', 
                        'Embarazada' => 'Esta Embarazada:',
                    ];
    
                    $xPosicion = $xInicial;
                    $yPosicion = $yInicial;
                
                    foreach ($campos as $campo => $titulo) {
                        if ((string) $item['NombreCampo'] === $campo) {
                            $valorCampo = $item['ValorCampo'];
                            if($valorCampo == 'off'){
                                $valorCampo = 'No';
                            }else if($valorCampo == 'On'){
                                $valorCampo == 'Si';
                            }
                            if($cont%2 == 0){
                                $pdf->Ln();
                            }
                            $pdf->SetFont('Arial', 'B', 8);
                            $pdf->Cell(45, 3, utf8_decode($titulo));
                            $pdf->SetFont('Arial', '', 8);
                            $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                            $valorCampoDecodificado = str_replace(
                                ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                                PHP_EOL,
                                $valorCampoDecodificado
                            );
                            $textoPlano = strip_tags($valorCampoDecodificado);
                            $cont++;
                            $pdf->Cell(45, 3, utf8_decode($textoPlano));
                        }
                    }        
                }
                $pdf->Ln(3);
            }

            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 0.5;
            $width = 190;  
            $height = 5; 
            $pdf->Rect($x, $y, $width, $height);
            $pdf->SetFont('Courier', 'B', 10);
    
            $texto = utf8_decode(strtoupper("VARIABLES 4505"));
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');
            $pdf->Ln(4);
            
            $ultimaPosicionY = $pdf->GetY();
            $y = $ultimaPosicionY + 0.5;
            $pdf->SetFont('Arial', 'B', 8);
            $texto = utf8_decode("Variables 4505");
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0);
            
            $pdf->Ln(3); 
            $ultimaPosicionY = $pdf->GetY();
            $y = $ultimaPosicionY + 0.5;
            $pdf->SetFont('Arial', 'B', 8);
            $texto = utf8_decode("Riesgos");
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0);
            
            $pdf->Ln(4);
            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 0.5;
            $width = 190;  
            $height = 5; 
            $pdf->Rect($x, $y, $width, $height);
            $pdf->SetFont('Courier', 'B', 10);
            $texto = utf8_decode(strtoupper("REVISIÓN POR SISTEMAS"));
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');
            
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(45, 3, utf8_decode('Sistema afectado:'));
            foreach ($xml as $item) {
                if ((string)$item['NombreCampo'] === 'SistemaAfectado') {
                    $valorCampo = (string)$item['ValorCampo'];  
                    $pdf->SetFont('Arial', 'B', 8);
                    $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlanoDiagnostico = strip_tags($valorCampoDecodificado);
                    $pdf->Cell(35, 3, utf8_decode($textoPlanoDiagnostico)); 
                }
            }

            $pdf->Ln(4);
            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 0.5;
            $width = 190;  
            $height = 5; 
            $pdf->Rect($x, $y, $width, $height);
            $pdf->SetFont('Courier', 'B', 10);
            $texto = utf8_decode(strtoupper("EXAMENES FISICOS"));
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');
            $pdf->Ln(3);

            $cont = 0;
            foreach ($xml as $item) {
                $campos = [
                    'FrecuenciaCardiaca' => ['F.Cardiaca:', 'xMin'],
                    'Temperatura' => ['Temperatura:', '°C'],
                    'FrecuenciaRespiratoria' => ['F.Respiratoria:', 'xMin'],
                    'Peso' => ['Peso:', 'kg'],
                    'Talla' => ['Talla:', 'm'],
                    'PresionSistole' => ['Presión Sistólica:', 'mmHg'],
                    'PresionDiastole' => ['Presión Diastólica:', 'mmHg'],
                    'PresionArterialMedia' => ['Presión Arterial Media:', 'mmHg'],
                    'IndiceMasaCorporal' => ['IMC:', 'Kg/m²'],
                    'SuperficieMasaCorporal' => ['SMC:', 'm²'],
                ];

                foreach ($campos as $campo => $tituloYUnidad) {
                    if ((string) $item['NombreCampo'] === $campo) {
                        $titulo = $tituloYUnidad[0]; 
                        $unidad = $tituloYUnidad[1]; 
                        $valorCampo = (string) $item['ValorCampo'];

                        if($cont%3 == 0){
                            $pdf->Ln();
                        }
                
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(35, 3, utf8_decode($titulo)); 
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->Cell(25, 3, utf8_decode($textoPlano . ' ' . $unidad));
                        $cont++;
                    }
                }
                         
            }

            $pdf->Ln(3);

            foreach ($xml as $item) {
                $campos2 = [
                    'Apariencia' => ['Apariencia:'],
                    'CraneoCaraCuello' => ['Cráneo, cara y cuello:'],
                    'Torax' => ['Tórax:'],
                    'Abdomen' => ['Abdomen:'],
                    'PielYFaneras' => ['Piel y faneras:'],
                    'GenitoUrinario' => ['Genito-urinario:'],
                    'Extremidades' => ['Extremidades:'],
                    'SistemaNerviosoCentral' => ['Sistema nervioso central:'],
                    'Conciliacionmedicamentosa' => ['Conciliación medicamentosa:'],
                ];

                foreach ($campos2 as $campo => $titulo2) {
                    if ((string) $item['NombreCampo'] === $campo) {
                        $valorCampo = $item['ValorCampo'];
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(42, 3, utf8_decode($titulo2[0]));
                        $pdf->SetFont('Arial', '', 8);
                
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                
                        $pdf->MultiCell(145, 3, utf8_decode($textoPlano));
                    }
                }   
                
                  
                         
            }

            $pdf->Ln(2);

            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 0.5;
            $width = 190;  
            $height = 5; 
            $pdf->Rect($x, $y, $width, $height);
            $pdf->SetFont('Courier', 'B', 10);
            $texto = utf8_decode(strtoupper("OBSERVACIONES Y RESULTADOS DE PARACLÍNICOS"));
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

            foreach ($xml as $item) {
                if ((string)$item['NombreCampo'] === 'ObservacionesParaclinicos') {
                    $valorCampo = (string)$item['ValorCampo'];  
                    if($valorCampo != ''){
                        
                    $pdf->SetFont('Arial', '', 8);
                    $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlano = strip_tags($valorCampoDecodificado);
                    $pdf->MultiCell(0, 3, utf8_decode($textoPlano));
                    }else{
                        $pdf->Ln();
                    }
                }
            }

            $pdf->Ln(2);

            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 0.5;
            $width = 190;  
            $height = 5; 
            $pdf->Rect($x, $y, $width, $height);
            $pdf->SetFont('Courier', 'B', 10);
            $texto = utf8_decode(strtoupper("ANÁLISIS"));
            $anchoTexto = $pdf->GetStringWidth($texto);
            $xCentrada = $x + ($width - $anchoTexto) / 2;
            $pdf->SetXY($xCentrada, $y );
            $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

            $pdf->Ln(6);

            $diagnosticoPrincipal = '';
            $diagnosticoRelacionado1 = '';
            $mostradoDiagnosticoPrincipal = false;
            $mostradoDiagnosticoRelacionado1 = false;

            foreach ($xml as $item) {
                $campos = [
                    'FinalidadConsulta' => ['Finalidad de la consulta:'],
                    'CausaExterna' => ['Causa externa:'],
                    'TipoDiagnosticoPrincipal' => ['Tipo de diagnóstico principal:'],
                    'PlanTratamiento' => ['Plan de Tratamiento:'],
                    'Analisis' => ['Análisis:'],
                    'Destino' => ['Destino del Paciente:'],
                ];

                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] === $campo) {
                        $valorCampo = $item['ValorCampo'];
                        $partes = explode('|', $valorCampo);
                        if (isset($partes[1])) {
                            $valorCampo = $partes[1];
                        }
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(42, 3, utf8_decode($titulo[0]));
                        $pdf->SetFont('Arial', '', 8);

                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);

                        $pdf->MultiCell(145, 3, utf8_decode($textoPlano));
                    }
                }

                if ((string) $item['NombreCampo'] === 'CodigoDiagnosticoPrincipal' || (string) $item['NombreCampo'] === 'DiagnosticoPrincipal') {
                    $valorCampo = $item['ValorCampo'];
                    $partes = explode('|', $valorCampo);
                    if (isset($partes[1])) {
                        $valorCampo = $partes[1];
                    }
                    $diagnosticoPrincipal .= strip_tags(html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8')) . ' ';
                }

                if ((string) $item['NombreCampo'] === 'CodigoDiagnosticoRelacionado1' || (string) $item['NombreCampo'] === 'DiagnosticoRelacionado1') {
                    $valorCampo = $item['ValorCampo'];
                    $partes = explode('|', $valorCampo);
                    if (isset($partes[1])) {
                        $valorCampo = $partes[1];
                    }
                    $diagnosticoRelacionado1 .= strip_tags(html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8')) . ' ';
                }
            }

            if (!empty($diagnosticoPrincipal) && !$mostradoDiagnosticoPrincipal) {
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(42, 3, utf8_decode('Diagnóstico principal:'));
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(145, 3, utf8_decode($diagnosticoPrincipal));
                $mostradoDiagnosticoPrincipal = true;
            }

            if (!empty($diagnosticoRelacionado1) && !$mostradoDiagnosticoRelacionado1) {
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(42, 3, utf8_decode('Diagnóstico relacionado 1:'));
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(145, 3, utf8_decode($diagnosticoRelacionado1));
                $mostradoDiagnosticoRelacionado1 = true;
            }

            $pdf->Ln(1);

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(45, 3, utf8_decode('Recomendaciones:'));
            
            $this->drawInformationDoctor($pdf, $patient, $maxY);
    
        } catch (Exception $e) {
            echo "Error al procesar el XML: " . $e->getMessage();
        }

    }

    public function Epicrisis($pdf, $patient){
        
        $x = 10;  
        $y = 101;  
        $width = 190;  
        $height = 5; 

        $pdf->Rect($x, $y, $width, $height);
        $pdf->SetFont('Courier', 'B', 10);

        $texto = utf8_decode(strtoupper($patient['NombreRegistro']));
        $anchoTexto = $pdf->GetStringWidth($texto);
        $xCentrada = $x + ($width - $anchoTexto) / 2;

        $pdf->SetXY($xCentrada, $y );
        $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

        $x = 10;
        $y = 107;
        $width = 190;
        $height = 4;

        $this->dottedRect($pdf, $x, $y, $width, $height);

        $pdf->SetFont('Courier', 'B', 9); 
        $text = "DATOS DE LA CONSULTA";
        $text_width = $pdf->GetStringWidth($text);
        $text_x = $x + ($width - $text_width) / 2;
        $text_y = $y + ($height / 2) - 0.5; 

        $pdf->SetXY($text_x, $text_y);
        $pdf->Cell($text_width, 1, utf8_decode($text), 0, 1, 'C');
        
        $pdf->Ln(2);

        try{
            $xml = new SimpleXMLElement($patient['RegistroXML']);
            $cont = 0;
            $maxY = 310;
    
            foreach ($xml as $item) {      
                $campos = [
                    'AsuntoHistoriaPadre' => 'Historia Clínica:',
                    'FechaHCPadre' => 'Fecha de Ingreso:',
                    'Cama' => 'Cama:',
                    'MotivoConsulta' => 'Motivo de la consulta:',
                    'AntecedentesHTML' => 'ANTECEDENTES:',
                ];
            
                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] == $campo) {
                        $valorCampo = $item['ValorCampo'];
                        
                        $partes = explode('|', $valorCampo);
                        if (isset($partes[1])) {
                            $valorCampo = $partes[1];
                        }

                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(45, 3, utf8_decode($titulo));
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        if($cont == 4){
                            $pdf->Ln();
                        }
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->MultiCell(145, 3, utf8_decode($textoPlano));
                        $pdf->Ln();
                        $cont++;
                    }
                    
                }
            }

            foreach ($xml as $item) {
                if ((string)$item['NombreCampo'] === 'SintesisEnfermedad') {
                    $valorCampo = (string)$item['ValorCampo'];  
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->MultiCell(42, 4, utf8_decode('Sintesis de la Enfermedad:'));
                    $pdf->SetFont('Arial', '', 8);
                    $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlano = strip_tags($valorCampoDecodificado);
                    $pdf->MultiCell(0, 3, utf8_decode($textoPlano)); 
                }
            }
                
            $pdf->Ln();
            $pdf->SetFont('Courier', 'B', 10);
            $pdf->Cell(30, 5,  utf8_decode("EXAMEN FISICO"), 0, 0, 'C');
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->Line($x - 30, $y + 5, $x, $y + 5);
            $pdf->Ln(7);

            foreach ($xml as $item) {      
             
                $campos = [
                    'Revisiónsistema' => 'Resivión Por Sistema:',
                    'AparienciaLlegada' => 'Apariencia a su llegada:',
                    'OrganosSentidos' => 'Cabeza, cara y órganos de los sentidos:',
                    'Torax' => 'Tórax:',
                    'Abdomen' => 'Abdomen:',
                    'PielFaneras' => 'Piel y faneras:',
                    'Torax' => 'Tórax:',
                    'GenitoUrinario' => 'Genito-Urinario:',
                    'Extremidades' => 'Extremidades:',
                    'Sistema nervioso central' => 'Sistema nervioso central:',
                    'Analisis' => 'Análisis:',
                ];
            
                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] == $campo) {
                        $valorCampo = $item['ValorCampo'];
                        
                        $partes = explode('|', $valorCampo);
                        if (isset($partes[1])) {
                            $valorCampo = $partes[1];
                        }

                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->MultiCell(65, 4, utf8_decode($titulo));
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        if($cont == 4){
                            $pdf->Ln();
                        }
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->MultiCell(175, 3, utf8_decode($textoPlano));
                        $pdf->Ln(1);
                        $cont++;
                    }
                    
                }
            }

            $pdf->Ln();
            $pdf->SetFont('Courier', 'B', 10);
            $pdf->Cell(53, 5,  utf8_decode("DIAGNÓSTICOS DE ENTRADA:"), 0, 0, 'C');
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->Line($x - 53, $y + 5, $x, $y + 5);
            $pdf->Ln(7);

            $diagnosticoPrincipal = '';
            $diagnosticoRelacionado1 = '';
            $mostradoDiagnosticoPrincipal = false;
            $mostradoDiagnosticoRelacionado1 = false;
            foreach ($xml as $item){
                if ((string) $item['NombreCampo'] === 'CodigoDiagnosticoPrincipal' || (string) $item['NombreCampo'] === 'DiagnosticoPrincipal') {
                    $valorCampo = $item['ValorCampo'];
                    $partes = explode('|', $valorCampo);
                    if (isset($partes[1])) {
                        $valorCampo = $partes[1];
                    }
                    $diagnosticoPrincipal .= strip_tags(html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8')) . ' ';
                }
    
                if ((string) $item['NombreCampo'] === 'CodigoDiagnosticoRelacionado1' || (string) $item['NombreCampo'] === 'DiagnosticoRelacionado1') {
                    $valorCampo = $item['ValorCampo'];
                    $partes = explode('|', $valorCampo);
                    if (isset($partes[1])) {
                        $valorCampo = $partes[1];
                    }
                    $diagnosticoRelacionado1 .= strip_tags(html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8')) . ' ';
                }
            }
            if (!empty($diagnosticoPrincipal) && !$mostradoDiagnosticoPrincipal) {
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(42, 3, utf8_decode('Diagnóstico principal:'));
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(145, 3, utf8_decode($diagnosticoPrincipal));
                $mostradoDiagnosticoPrincipal = true;
            }
            if (!empty($diagnosticoRelacionado1) && !$mostradoDiagnosticoRelacionado1) {
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(42, 3, utf8_decode('Diagnóstico relacionado 1:'));
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(145, 3, utf8_decode($diagnosticoRelacionado1));
                $mostradoDiagnosticoRelacionado1 = true;
            }

            $pdf->Ln();
            $pdf->SetFont('Courier', 'B', 10);
            $pdf->Cell(27, 5,  utf8_decode("EVOLUCIONES:"), 0, 0, 'C');
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->Line($x - 27, $y + 5, $x, $y + 5);
            $pdf->Ln(7);

            foreach ($xml as $item){
                if ((string)$item['NombreCampo'] === 'EvolucionesHTML') {
                    $valorCampo = (string)$item['ValorCampo'];  
                    $pdf->SetFont('Arial', '', 8);
                    $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlano = strip_tags($valorCampoDecodificado);
                    $pdf->MultiCell(0, 3, utf8_decode($textoPlano)); 
                }
                if ((string)$item['NombreCampo'] === 'ProcedimientosAsociadosHTML') {
                    $valorCampo = (string)$item['ValorCampo'];  
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->MultiCell(70, 4, utf8_decode('Procedimientos Realizados y Ordenados:'));
                    $pdf->SetFont('Arial', '', 8);
                    $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlano = strip_tags($valorCampoDecodificado);
                    $pdf->MultiCell(170, 3, utf8_decode($textoPlano)); 
                }
                if ((string)$item['NombreCampo'] === 'MedicamentosAdministradoHTML') {
                    $valorCampo = (string)$item['ValorCampo'];  
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->MultiCell(72, 4, utf8_decode('Medicamentos Ordenados y Administrados:')); 
                    $pdf->SetFont('Arial', '', 8);
                    $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlano = strip_tags($valorCampoDecodificado);
                    $pdf->MultiCell(0, 3, utf8_decode($textoPlano)); 
                }
                if ((string)$item['NombreCampo'] === 'MedidasGeneralesHTML') {
                    $valorCampo = (string)$item['ValorCampo'];  
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->MultiCell(72, 4, utf8_decode('Medidas Generales Ordenadas:')); 
                    $pdf->SetFont('Arial', '', 8);
                    $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlano = strip_tags($valorCampoDecodificado);
                    $pdf->MultiCell(0, 3, utf8_decode($textoPlano)); 
                }
                if ((string)$item['NombreCampo'] === 'JustificacionTerapeuticas') {
                    $valorCampo = (string)$item['ValorCampo'];  
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->MultiCell(72, 4, utf8_decode('Justificación de indicaciones Terapéuticas:')); 
                    $pdf->SetFont('Arial', '', 8);
                    $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlano = strip_tags($valorCampoDecodificado);
                    $pdf->MultiCell(0, 3, utf8_decode($textoPlano)); 
                }
                if ((string)$item['NombreCampo'] === 'Complicaciones') {
                    $valorCampo = (string)$item['ValorCampo'];  
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->MultiCell(72, 4, utf8_decode('Complicaciones:')); 
                    $pdf->SetFont('Arial', '', 8);
                    $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlano = strip_tags($valorCampoDecodificado);
                    $pdf->MultiCell(0, 3, utf8_decode($textoPlano)); 
                }
                if ((string)$item['NombreCampo'] === 'ServicioEgreso') {
                    $valorCampo = (string)$item['ValorCampo'];  
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->MultiCell(72, 4, utf8_decode('Servicio de Egreso:')); 
                    $pdf->SetFont('Arial', '', 8);
                    $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlano = strip_tags($valorCampoDecodificado);
                    $pdf->MultiCell(0, 3, utf8_decode($textoPlano)); 
                }
                if ((string)$item['NombreCampo'] === 'FechaEgreso') {
                    $valorCampo = (string)$item['ValorCampo'];  
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->MultiCell(72, 4, utf8_decode('Fecha de Egreso:')); 
                    $pdf->SetFont('Arial', '', 8);
                    $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlano = strip_tags($valorCampoDecodificado);
                    $pdf->MultiCell(0, 3, utf8_decode($textoPlano)); 
                }
                if ((string)$item['NombreCampo'] === 'MotivoSalida') {
                    $parteCampo = explode('|', (string) $item['ValorCampo']);
                    $valorCampo = $parteCampo[0];  
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(27, 4, utf8_decode('Motivo de Salida:')); 
                    $pdf->SetFont('Arial', '', 8);
                    $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlano = strip_tags($valorCampoDecodificado);
                    $pdf->Cell(30, 4, utf8_decode($textoPlano)); 
                }
                if ((string)$item['NombreCampo'] === 'EstadoSalida') {
                    $parteCampo = explode('|', (string) $item['ValorCampo']);
                    $valorCampo = $parteCampo[0];
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(28, 4, utf8_decode('Estado de Salida:')); 
                    $pdf->SetFont('Arial', '', 8);
                    $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                    $valorCampoDecodificado = str_replace(
                        ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                        PHP_EOL,
                        $valorCampoDecodificado
                    );
                    $textoPlano = strip_tags($valorCampoDecodificado);
                    $pdf->Cell(0, 4, utf8_decode($textoPlano)); 
                }

            }

            $pdf->Ln();
            $pdf->SetFont('Courier', 'B', 10);
            $pdf->Cell(51, 5,  utf8_decode("DIAGNÓSTICOS DE SALIDA:"), 0, 0, 'C');
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->Line($x - 50, $y + 5, $x, $y + 5);
            $pdf->Ln(7);

            $diagnosticoPrincipal = '';
            $diagnosticoRelacionado1 = '';
            $mostradoDiagnosticoPrincipal = false;
            $mostradoDiagnosticoRelacionado1 = false;
            foreach ($xml as $item){
                if ((string) $item['NombreCampo'] === 'CodigoDiagnosticoPrincipalSalida' || (string) $item['NombreCampo'] === 'DiagnosticoPrincipalSalida') {
                    $valorCampo = $item['ValorCampo'];
                    $partes = explode('|', $valorCampo);
                    if (isset($partes[1])) {
                        $valorCampo = $partes[1];
                    }
                    $diagnosticoPrincipal .= strip_tags(html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8')) . ' ';
                }
    
                if ((string) $item['NombreCampo'] === 'CodigoDiagnosticoRelacionado1Salida' || (string) $item['NombreCampo'] === 'DiagnosticoRelacionado1Salida') {
                    $valorCampo = $item['ValorCampo'];
                    $partes = explode('|', $valorCampo);
                    if (isset($partes[1])) {
                        $valorCampo = $partes[1];
                    }
                    $diagnosticoRelacionado1 .= strip_tags(html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8')) . ' ';
                }
            }
            if (!empty($diagnosticoPrincipal) && !$mostradoDiagnosticoPrincipal) {
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(42, 3, utf8_decode('Diagnóstico principal:'));
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(145, 3, utf8_decode($diagnosticoPrincipal));
                $mostradoDiagnosticoPrincipal = true;
            }
            if (!empty($diagnosticoRelacionado1) && !$mostradoDiagnosticoRelacionado1) {
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(42, 3, utf8_decode('Diagnóstico relacionado 1:'));
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(145, 3, utf8_decode($diagnosticoRelacionado1));
                $mostradoDiagnosticoRelacionado1 = true;
            }

            $pdf->Ln();
            $pdf->SetFont('Courier', 'B', 10);
            $pdf->Cell(77, 5,  utf8_decode("PLAN ATENCIÓN INTEGRAL POR MEDICINA:"), 0, 0, 'C');
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->Line($x - 76, $y + 5, $x, $y + 5);
            $pdf->Ln(7);

            foreach ($xml as $item) {      
             
                $campos = [
                    'TratamientoFamacologico' => 'Tratamiento Farmacologico:',
                    'RecomentacionesAdicionales' => 'Recomentaciones Adicionales:',
                    'AplicaCuidadoEnfermeria' => 'Aplica Cuidados de Enfermeria:',
                ];
            
                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] == $campo) {
                        $valorCampo = $item['ValorCampo'];
                        if($valorCampo == 'false'){
                            $valorCampo = 'No';
                        }else if($valorCampo == 'true'){
                            $valorCampo = 'Yes';
                        }
                        $partes = explode('|', $valorCampo);
                        if (isset($partes[1])) {
                            $valorCampo = $partes[1];
                        }

                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->MultiCell(65, 4, utf8_decode($titulo));
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        if($cont == 4){
                            $pdf->Ln();
                        }
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->MultiCell(175, 3, utf8_decode($textoPlano));
                        $pdf->Ln(1);
                        $cont++;
                    }
                    
                }
            }

            $this->drawInformationDoctor($pdf, $patient, $maxY);
        }catch (Exception $e) {
            echo "Error al procesar el XML: " . $e->getMessage();
        }

    }

    public function RegistroHospitalizacion($pdf,$patient){
    
      
        $x = 10;  
        $y = 101;  
        $width = 190;  
        $height = 5; 

        $pdf->Rect($x, $y, $width, $height);
        $pdf->SetFont('Courier', 'B', 10);

        $texto = utf8_decode(strtoupper($patient['NombreRegistro']));
        $anchoTexto = $pdf->GetStringWidth($texto);
        $xCentrada = $x + ($width - $anchoTexto) / 2;

        $pdf->SetXY($xCentrada, $y );
        $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');

        $x = 10;
        $y = 107; 
        $width = 190;
        $height = 4;

        $this->dottedRect($pdf, $x, $y, $width, $height);

        $pdf->SetFont('Courier', 'B', 9); 
        $text = "DATOS DE LA CONSULTA";
        $text_width = $pdf->GetStringWidth($text);
        $text_x = $x + ($width - $text_width) / 2;
        $text_y = $y + ($height / 2) - 0.5; 

        $pdf->SetXY($text_x, $text_y);
        $pdf->Cell($text_width, 1, utf8_decode($text), 0, 1, 'C');
        
        $pdf->Ln(2);

        try {
            $xml = new SimpleXMLElement($patient['RegistroXML']);

            

            $maxY = 310;
    
            foreach ($xml as $item) {
                
                $campos = [
                    'MotivoConsulta' => 'Motivo de consulta:',
                    'EnfermedadActual' => 'Enferemedad actual:',
                ];
            
                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] == $campo) {
                        $valorCampo = $item['ValorCampo'];
                        if((string) $item['NombreCampo'] == 'EnfermedadActual'){
                            $partes = explode('|', $valorCampo);
                            if (isset($partes[1])) {
                                $valorCampo = $partes[1];
                            }
    
                            $pdf->SetFont('Arial', 'B', 8);
                            $pdf->Cell(45, 3, utf8_decode($titulo));
                            $pdf->SetFont('Arial', '', 8);
                            $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                            $valorCampoDecodificado = str_replace(
                                ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                                PHP_EOL,
                                $valorCampoDecodificado
                            );
                            $textoPlano = strip_tags($valorCampoDecodificado);
                            $pdf->MultiCell(140, 3, utf8_decode($textoPlano));
                            break;
                        }
                        $partes = explode('|', $valorCampo);
                        if (isset($partes[1])) {
                            $valorCampo = $partes[1];
                        }

                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(45, 3, utf8_decode($titulo));
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->Cell(45, 3, utf8_decode($textoPlano));
                        $pdf->Ln();
                    }
                }
            }

            $ultimaPosicionY = $pdf->GetY(); 
            $x = 10;
            $y = $ultimaPosicionY + 2;
            $width = 190;
            $height = 4;

            $this->dottedRect($pdf, $x, $y, $width, $height);

            $pdf->SetFont('Courier', 'B', 9); 
            $text = "ANTECEDENTES";
            $text_width = $pdf->GetStringWidth($text);
            $text_x = $x + ($width - $text_width) / 2;
            $text_y = $y + ($height / 2) - 0.5; 
            $pdf->SetXY($text_x, $text_y);
            $pdf->Cell($text_width, 1, utf8_decode($text), 0, 1, 'C');
            
            $pdf->Ln(3);

            foreach ($xml as $item) {
                
                $campos = [
                    'AntecedentesPersonales' => 'Personales:',
                    'AntecedentesFamiliares' => 'Familiares:',
                    'AntecedentesAlergicos' => 'Alérgicos:',
                    'AntecedentesQx' => 'Quirúrgicos:',
                ];
            
                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] == $campo) {
                        $valorCampo = $item['ValorCampo'];
                        $partes = explode('|', $valorCampo);
                        if (isset($partes[1])) {
                            $valorCampo = $partes[1];
                        }

                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(45, 3, utf8_decode($titulo));
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->MultiCell(100, 3, utf8_decode($textoPlano));
                        $pdf->Ln();
                    }
                }
            }

            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 2;
            $width = 190;
            $height = 4;

            $this->dottedRect($pdf, $x, $y, $width, $height);

            $pdf->SetFont('Courier', 'B', 9); 
            $text = "RIVISIÓN POR SISTEMA";
            $text_width = $pdf->GetStringWidth($text);
            $text_x = $x + ($width - $text_width) / 2;
            $text_y = $y + ($height / 2) - 0.5; 
            $pdf->SetXY($text_x, $text_y);
            $pdf->Cell($text_width, 1, utf8_decode($text), 0, 1, 'C');
            $pdf->Ln(2);

            $width_total = 190;
            $width_cuadro = $width_total / 3;
            $ultimaPosicionY = $pdf->GetY(); 
            $y = $ultimaPosicionY + 2;
            $height = 4; 
            $width_cuadro1 = 50; 
            $width_cuadro2 = 30;  
            $width_cuadro3 = 110;  

            $x1 = 10;
            $x2 = $x1 + $width_cuadro1; 
            $x3 = $x2 + $width_cuadro2;

            $this->dottedRect($pdf, $x1, $y, $width_cuadro1, $height);
            $pdf->SetFont('Courier', 'B', 9);
            $text1 = "SISTEMA";
            $text_width1 = $pdf->GetStringWidth($text1);
            $text_x1 = $x1 + ($width_cuadro1 - $text_width1) / 2;
            $pdf->SetXY($text_x1, $y + ($height / 1.2) - 2);
            $pdf->Cell($text_width1, 1, utf8_decode($text1), 0, 1, 'C');

            $this->dottedRect($pdf, $x2, $y, $width_cuadro2, $height);
            $text2 = "ESTADO";
            $text_width2 = $pdf->GetStringWidth($text2);
            $text_x2 = $x2 + ($width_cuadro2 - $text_width2) / 2;
            $pdf->SetXY($text_x2, $y + ($height / 1.2) - 2);
            $pdf->Cell($text_width2, 1, utf8_decode($text2), 0, 1, 'C');

            $this->dottedRect($pdf, $x3, $y, $width_cuadro3, $height);
            $text3 = "OBSERVACIÓN";
            $text_width3 = $pdf->GetStringWidth($text3);
            $text_x3 = $x3 + ($width_cuadro3 - $text_width3) / 2;
            $pdf->SetXY($text_x3, $y + ($height / 1.2) - 2);
            $pdf->Cell($text_width3, 1, utf8_decode($text3), 0, 1, 'C');
            $pdf->Ln(3);

            
            foreach ($xml as $item) {
                
                $campos = [
                    'tipoConsulta1' => 'Estado de conciencia',
                    'tipoConsulta2' => 'Piel y mucosa',
                    'tipoConsulta3' => 'Cabeza,cara y cuero cabelludo',
                    'tipoConsulta4' => 'Cuello',
                    'tipoConsulta5' => 'Órganos de los sentidos',
                    'tipoConsulta6' => 'Tórax',
                    'tipoConsulta7' => 'Respiratorio',
                    'tipoConsulta8' => 'Cardíaco',
                    'tipoConsulta9' => 'Vascular periféricos',
                    'tipoConsulta10' => 'Abdomen',
                    'tipoConsulta11' => 'Perianal',
                    'tipoConsulta12' => 'Region inguinal',
                    'tipoConsulta13' => 'Genitales',
                    'tipoConsulta14' => 'Extremidades',
                    'tipoConsulta15' => 'Sist. nerv. central',
                    'tipoConsulta16' => 'Sist. nerv. periférico',
                    'tipoConsulta17' => 'Sistema linfático',
                    'tipoConsulta18' => 'Sist. osteo-articular',

                ];

                $camposDescripcion = [
                    'tipoDescripcionConsulta1',
                    'tipoDescripcionConsulta2',
                    'tipoDescripcionConsulta3',
                    'tipoDescripcionConsulta4',
                    'tipoDescripcionConsulta5',
                    'tipoDescripcionConsulta6',
                    'tipoDescripcionConsulta7',
                    'tipoDescripcionConsulta8',
                    'tipoDescripcionConsulta9',
                    'tipoDescripcionConsulta10',
                    'tipoDescripcionConsulta11',
                    'tipoDescripcionConsulta12',
                    'tipoDescripcionConsulta13',
                    'tipoDescripcionConsulta14',
                    'tipoDescripcionConsulta15',
                    'tipoDescripcionConsulta16',
                    'tipoDescripcionConsulta17',
                    'tipoDescripcionConsulta18',
                ];
            
                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] == $campo) {
                        $valorCampo = $item['ValorCampo'];

                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(55, 3, utf8_decode($titulo));
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->Cell(25, 3, utf8_decode($textoPlano));

                        $campoDescripcion = str_replace('tipoConsulta', 'tipoDescripcionConsulta', $campo);
                        if (in_array($campoDescripcion, $camposDescripcion)) {
                            $valorDescripcion = ''; 
                            $valorDescripcionDecodificado = html_entity_decode($valorDescripcion, ENT_QUOTES, 'UTF-8');
                            $valorDescripcionDecodificado = str_replace(
                                ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                                PHP_EOL,
                                $valorDescripcionDecodificado
                            );
                            $textoDescripcionPlano = strip_tags($valorDescripcionDecodificado);
                            $pdf->MultiCell(75, 3, utf8_decode($textoDescripcionPlano));
                        }

                    }
                }
            }

            $ultimaPosicionY = $pdf->GetY();
            $x = 10;
            $y = $ultimaPosicionY + 2;
            $width = 190;
            $height = 4;

            $this->dottedRect($pdf, $x, $y, $width, $height);

            $pdf->SetFont('Courier', 'B', 9); 
            $text = "EXAMEN FÍSICO";
            $text_width = $pdf->GetStringWidth($text);
            $text_x = $x + ($width - $text_width) / 2;
            $text_y = $y + ($height / 2) - 0.5; 
            $pdf->SetXY($text_x, $text_y);
            $pdf->Cell($text_width, 1, utf8_decode($text), 0, 1, 'C');
            $pdf->Ln(2);

            $cont = 0;
            $presion1 = '';
            $presion2 = '';
            
            foreach ($xml as $item) {
                $campos = [
                    'FrecuenciaCardiaca' => ['F.Cardiaca:', 'xMin'],
                    'Temperatura' => ['Temperatura:', '°C'],
                    'FrecuenciaRespiratoria' => ['F.Respiratoria:', 'xMin'],
                    'Peso' => ['Peso:', 'kg'],
                    'Talla' => ['Talla:', 'm'],
                    'Presion1' => ['Presión', 'mmHg'],
                    'Presion2' => ['Presión', 'mmHg'],
                    'IndiceMasaCorporal' => ['Indice de masa corporal:', 'Kg/m²'],
                    'SuperficieMasaCorporal' => ['Superficie masa corporal:', 'm²'],
                ];
            
                foreach ($campos as $campo => $tituloYUnidad) {
                    if ((string) $item['NombreCampo'] === $campo) {
                        $titulo = $tituloYUnidad[0]; 
                        $unidad = $tituloYUnidad[1]; 
                        $valorCampo = (string) $item['ValorCampo'];
            
                        if ($campo == 'Presion1') {
                            $presion1 = $valorCampo;
                        } elseif ($campo == 'Presion2') {
                            $presion2 = $valorCampo;
                        } else {
                            if ($cont % 3 == 0) {
                                $pdf->Ln();
                            }
                            
                            $pdf->SetFont('Arial', 'B', 8);
                            $pdf->Cell(35, 3, utf8_decode($titulo)); 
                            $pdf->SetFont('Arial', '', 8);
                            
                            $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                            $valorCampoDecodificado = str_replace(
                                ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                                PHP_EOL,
                                $valorCampoDecodificado
                            );
                            $textoPlano = strip_tags($valorCampoDecodificado);
                            
                            $pdf->Cell(25, 3, utf8_decode($textoPlano . ' ' . $unidad));
                            $cont++;
                        }
                    }
                }
            
                if ($presion1 !== '' && $presion2 !== '') {
                    if ($cont % 3 == 0) {
                        $pdf->Ln();
                    }
            
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(35, 3, utf8_decode('Presión:'));
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->Cell(25, 3, utf8_decode($presion1 . '/' . $presion2 . ' mmHg')); // Mostrar los valores combinados
                    $cont++;
                    $presion1 = '';
                    $presion2 = '';
                }
            }
            $pdf->Ln(3);

            $codigo = '';
            $diagnostico = '';
            foreach ($xml as $item) {
    
                $campos = [
                    'Apariencia' => 'Apariencia:',
                    'CraneoCaraCuello' => 'Craneo, cara y cuello:',
                    'Torax' => '',
                    'Abdomen' => 'Abdomen:',
                    'PielYFaneras' => 'Piel y faneras:',
                    'GenitoUrinario' => 'Génito-Urinario:',
                    'Extremidades' => 'Extremidades:',
                    'SistemaNerviosoCentral' => 'Sistema nervioso central:',
                    'FinalidadConsulta' => 'Finalidad consulta:',
                    'CausaExterna' => 'Causa externa:',
                    'TipoDiagnostico' => 'Tipo diagnóstico principal:',
                    'DiagnosticoPrincipal' => 'Diagnóstico principal:',
                    'CodigoDiagnosticoPrincipal' => '', // Lo dejamos vacío porque se mostrará junto al Diagnóstico Principal
                    'DiagnosticoRelacionado1' => 'Diagnostico relacionado 1:',
                    'DiagnosticoRelacionado2' => 'Diagnostico relacionado 2:',
                    'DiagnosticoRelacionado3' => 'Diagnostico relacionado 3:',
                    'PlanTratamiento' => 'Plan de tratamiento:',
                    'Analisis' => 'Análisis:',
                ];
            
            
                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] == $campo) {
                        $valorCampo = $item['ValorCampo'];

                        if ($campo == 'CodigoDiagnosticoPrincipal') {
                            $codigo = $valorCampo;
                        } elseif ($campo == 'DiagnosticoPrincipal') {
                            $diagnostico = $valorCampo;
                        }else{
                            $partes = explode('|', $valorCampo);
                        if (isset($partes[1])) {
                            $valorCampo = $partes[1]; // Esto obtiene la parte que está después de "|"
                        }
            
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(45, 3, utf8_decode($titulo));
                        $pdf->SetFont('Arial', '', 8);
                        $valorCampoDecodificado = html_entity_decode($valorCampo, ENT_QUOTES, 'UTF-8');
                        $valorCampoDecodificado = str_replace(
                            ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                            PHP_EOL,
                            $valorCampoDecodificado
                        );
                        $textoPlano = strip_tags($valorCampoDecodificado);
                        $pdf->MultiCell(120, 3, utf8_decode($textoPlano));
                        $pdf->Ln(1);
                        }
                    }
                }

                if ($codigo !== '' && $diagnostico !== '') {
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(45, 3, utf8_decode('Diagnóstico principal:'));
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->Cell(25, 3, utf8_decode($codigo . ':' . $diagnostico )); // Mostrar los valores combinados
                    $codigo = '';
                    $diagnostico = '';
                    $pdf->Ln();
                }

            }
            

            
            $this->drawInformationDoctor($pdf, $patient, $maxY);
    
        } catch (Exception $e) {
            echo "Error al procesar el XML: " . $e->getMessage();
        }


    }

    public function drawInformationDoctor($pdf, $patient, $maxY) {
        $pdf->Ln(15);
        $x_start = 150;
        $x_end = 190;
        $width = $x_end - $x_start;
        $currentY = $pdf->GetY();
        $required_space = 30;

        if ($currentY + $required_space > $maxY) {
            $pdf->AddPage();
            $pdf->SetY(10); 
        }

        $currentY = $pdf->GetY();
        $pdf->Line(145, $currentY, 195, $currentY);
        $pdf->SetY($currentY + 2);
        $pdf->SetFont('Times', 'B', 7);
        $pdf->SetX($x_start);

        $nombreMedico = strtoupper($patient['NombreMedico']);
        $pdf->Cell($width, 5, utf8_decode($nombreMedico), 0, 1, 'C');
        $descripcion = strtoupper($patient['Descrip']);
        $pdf->SetX($x_start);
        $pdf->Cell($width, 5, utf8_decode($descripcion), 0, 1, 'C');
        $registroMedico = strtoupper($patient['RegistroMedico']);
        $pdf->SetX($x_start);
        $pdf->Cell($width, 5, utf8_decode($registroMedico), 0, 1, 'C');

        $tiDocMedico = (string) $patient['TiDocMedico'];
        $docMedico = (string) $patient['DocMedico'];
        $pdf->SetX($x_start);
        $pdf->Cell($width, 5, utf8_decode("$tiDocMedico $docMedico"), 0, 1, 'C');
    }

    public function dottedRect($pdf, $x, $y, $w, $h){
        $dot_length = 1; $space_length = 1;
        for ($i = $x; $i < $x + $w; $i += ($dot_length + $space_length)) {
            $pdf->Line($i, $y, $i + $dot_length, $y);
        }
        
        for ($i = $x; $i < $x + $w; $i += ($dot_length + $space_length)) {
            $pdf->Line($i, $y + $h, $i + $dot_length, $y + $h);
        }
        for ($i = $y; $i < $y + $h; $i += ($dot_length + $space_length)) {
            $pdf->Line($x, $i, $x, $i + $dot_length);
        }
        for ($i = $y; $i < $y + $h; $i += ($dot_length + $space_length)) {
            $pdf->Line($x + $w, $i, $x + $w, $i + $dot_length);
        }
    }
        
}

?>