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

    public function generatedPDF() {
        $dataPDF = $this->model->getRegisters($_SESSION['id_user']);
    
        // Decodificar el JSON recibido
        $dataArray = json_decode($dataPDF);
    
        if (empty($data)) {
            // Manejar error de recuperación de datos
            return "Error al recuperar los datos.";
        }

        // 2. Obtener el dato binario (firma) y guardarlo en un archivo temporal
        $firma = isset($data[0]['Firma']) ? $data[0]['Firma'] : null;

        if ($firma) {
            file_put_contents('temp_image.jpg', $firma);
        } else {
            // Manejar caso donde no hay firma
            return "No se encontró una firma.";
        }

        // 3. Crear el PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Añadir la imagen al PDF
        if (file_exists('temp_image.jpg')) {
            $pdf->Image('temp_image.jpg', 10, 10, 50, 50); // Ajusta la posición y tamaño según sea necesario
        } else {
            // Manejar caso donde la imagen no se ha guardado
            $pdf->Cell(0, 10, 'Imagen no disponible');
        }

        $pdf->Output();

        // 4. Eliminar el archivo temporal
        unlink('temp_image.jpg');
    
    }

    public function createPDF(){
            require('Libraries/FPDF/fpdf.php');
    
            $pdf = new FPDF('P','mm','A4');
            $pdf->AddPage();
            $pdf->Image('Assets/css/v9_58.png', 15,8,23);
            $pdf->setTitle(utf8_decode('Registro Clínico'));
            $pdf->SetFont('Courier','B',10);
            
            $pdf->Cell(65);
            $pdf->MultiCell(120,4 , utf8_decode("PERFECT BODY MEDICAL CENTER"), 0, 'l', false);
            $pdf->Cell(70);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(29,3, utf8_decode("Identificación Interna:"));
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

          
        
            // Obtener los datos de la consulta
            $data = $this->model->getDataPatients($_SESSION['id_user']);
            $decodedData = json_decode($data, true);

        
            if (!empty($decodedData)) {
                // Solo seleccionamos el primer registro, ya que puede haber varios duplicados
                $patient = $decodedData[2];   
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
                $pdf->Cell(75);
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

                $pdf->Ln(1);

                /*
                if($patient['NombreRegistro'] == 'Evolución de Urgencia'){                   
                   $this->evolucionUrgencia($pdf, $patient);
                }else */ if($patient['NombreRegistro'] == 'Registro Clínico de Urgencia'){                   
                    $this->RegistroUrgencia($pdf, $patient);
                 }
            }

            $pdf->Output();
        
    }

    public function incapacidad($pdf, $patient){
        
        $x = 10;  // Coordenada X del cuadro
        $y = 101;  // Coordenada Y del cuadro
        $width = 190;  // Ancho del cuadro
        $height = 5;  // Altura del cuadro

        $pdf->Rect($x, $y, $width, $height);
        $pdf->SetFont('Courier', 'B', 10);
        $texto = utf8_decode(strtoupper($patient['NombreRegistro']));
        $anchoTexto = $pdf->GetStringWidth($texto);
        $xCentrada = $x + ($width - $anchoTexto) / 2;
        $pdf->SetXY($xCentrada, $y );
        $pdf->Cell($anchoTexto, $height, $texto, 0, 0, 'C');
       
        $pdf->Line(20, 110, 190, 110); // x1, y1, x2, y2
        $pdf->SetXY(25, 108);

        try {
            $xml = new SimpleXMLElement($patient['RegistroXML']);
            
            // Iterar sobre el XML y buscar el dato específico
            foreach ($xml as $item) {
                // Verificar si el atributo 'NombreCampo' es 'TipoConsulta'
                if ((string) $item['NombreCampo'] === 'NumeroIngreso') {
                    // Mostrar el valor del campo 'ValorCampo'
                    $pdf->Ln(5);
                    $pdf->Cell(10);
                    $pdf->SetFont('Arial','B',8);
                    $pdf->Cell(30, 1, utf8_decode("Número-Ingreso:"), 0);
                    $pdf->Cell(10);
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->Cell(5,1 , utf8_decode(strtoupper((string) $item['ValorCampo'])), 0);
                }
                if ((string) $item['NombreCampo'] === 'Servicio') {
                    // Mostrar el valor del campo 'ValorCampo'
                   $pdf->Ln(3);             
                   $pdf->Cell(10);
                   $pdf->SetFont('Arial','B',8);
                   $pdf->Cell(30, 1, utf8_decode("Servicio:"), 0);
                   $pdf->Cell(10);
                   $pdf->SetFont('Arial', '', 8);
                   $pdf->Cell(95,1 , utf8_decode(strtoupper((string) $item['ValorCampo'])), 0);
                }
                if ((string) $item['NombreCampo'] === 'Responsable') {
                   // Mostrar el valor del campo 'ValorCampo'
                   $pdf->Ln(3);
                   $pdf->Cell(10);
                   $pdf->SetFont('Arial','B',8);
                   $pdf->Cell(30, 1, utf8_decode("Responsable:"), 0);
                   $pdf->Cell(10);
                   $pdf->SetFont('Arial', '', 8);
                   $pdf->Cell(95,1 , utf8_decode(strtoupper((string) $item['ValorCampo'])), 0);
                }
                if ((string) $item['NombreCampo'] === 'CodigoDiagnosticoPrincipal') {
                   // Mostrar el valor del campo 'ValorCampo'
                   $pdf->Ln(3);
                   $pdf->Cell(10);
                   $pdf->SetFont('Arial','B',8);
                   $pdf->Cell(30, 1, utf8_decode("Diagnóstico:"), 0);
                   $pdf->Cell(10);
                   $pdf->SetFont('Arial', '', 8);
                   $pdf->Cell(95,1 , utf8_decode(strtoupper((string) $item['ValorCampo'])), 0);
                }
                if ((string) $item['NombreCampo'] === 'DiagnosticoPrincipal') {
                    // Mostrar el valor del campo 'ValorCampo'
                    $pdf->Ln(3);
                    $pdf->Cell(10);
                    $pdf->SetFont('Arial','B',8);
                    $pdf->Cell(30, 1, utf8_decode("Diagnóstico:"), 0);
                    $pdf->Cell(10);
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->Cell(95,1 , utf8_decode(strtoupper((string) $item['ValorCampo'])), 0);
                }
                if ((string) $item['NombreCampo'] === 'IncapacidadPor') {
                    // Mostrar el valor del campo 'ValorCampo'
                    $pdf->Ln(6);
                    $pdf->Cell(10);
                    $pdf->SetFont('Arial','B',8);
                    $pdf->Cell(38, 1, utf8_decode("INCAPACIDAD POR:"), 0);
                    $pdf->Ln(3);
                    $pdf->Cell(10);
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->MultiCell(155,4 , utf8_decode( $item['ValorCampo']), 0);
                }
                if ((string) $item['NombreCampo'] === 'Observaciones') {
                   // Mostrar el valor del campo 'ValorCampo'
                   $pdf->Ln(6);
                   $pdf->Cell(10);
                   $pdf->SetFont('Arial','B',8);
                   $pdf->Cell(38, 1, utf8_decode("OBSERVACIONES:"), 0);
                   $pdf->Ln(3);
                   $pdf->Cell(10);
                   $pdf->SetFont('Arial', '', 8);
                   $pdf->Cell(95,1 , utf8_decode(strtoupper((string) $item['ValorCampo'])), 0);
                }
                
            }
                        
            $x_start = 150;
            $x_end = 190;
            $width = $x_end - $x_start;

            $currentY = $pdf->GetY();
            $pdf->Ln(30);
            if ($currentY > 10) {
                $currentY += 30;  // Ajustar según sea necesario
            }
            $pdf->SetY($currentY);

            $pdf->Line(195, $currentY, 145, $currentY);

            $pdf->SetFont('Times', 'B', 7);
            $nombreMedico = strtoupper((string) $patient['NombreMedico']);
            $text_width = $pdf->GetStringWidth($nombreMedico);
            $x_position = $x_start + (($width - $text_width) / 2);
            $pdf->SetXY($x_position, $currentY + 3); // Ajustar coordenada Y
            $pdf->Cell($text_width, 1, utf8_decode($nombreMedico), 0);

            $descripcion = strtoupper((string) $patient['Descrip']);
            $text_width = $pdf->GetStringWidth($descripcion);
            $x_position = $x_start + (($width - $text_width) / 2);
            $pdf->SetXY($x_position, $currentY + 7); 
            $pdf->Cell($text_width, 1, utf8_decode($descripcion), 0);

            $registroMedico = strtoupper((string) $patient['RegistroMedico']);
            $text_width = $pdf->GetStringWidth($registroMedico);
            $x_position = $x_start + (($width - $text_width) / 2);
            $pdf->SetXY($x_position, $currentY + 11);
            $pdf->Cell($text_width, 1, utf8_decode($registroMedico), 0);

            $tiDocMedico = (string) $patient['TiDocMedico'];
            $docMedico = (string) $patient['DocMedico']; 

            $text_width_tipo = $pdf->GetStringWidth($tiDocMedico);
            $text_width_doc = $pdf->GetStringWidth($docMedico);

            $x_position_tipo = $x_start + (($width - ($text_width_tipo + $text_width_doc + 5)) / 2);
            $pdf->SetXY($x_position_tipo, $currentY + 15);
            $pdf->Cell($text_width_tipo, 1, utf8_decode($tiDocMedico), 0);

            $x_position_doc = $x_position_tipo + $text_width_tipo + 5; 
            $pdf->SetXY($x_position_doc, $currentY + 15); 
            $pdf->Cell($text_width_doc, 1, utf8_decode($docMedico), 0);

            
        } catch (Exception $e) {
            // Mostrar el error si el XML es inválido
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

        // Parámetros para el rectángulo punteado
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
            $maxY = 270;  // Ajustar según el tamaño de la página
            $counter = 0; // Contador de columnas por fila
            $diagnosticoPrincipal = ''; // Variable para almacenar el valor del diagnóstico principal temporalmente
            $observacion = ''; // Variable para almacenar la observación temporalmente

            foreach ($xml as $item) {
                if ((string) $item['NombreCampo'] === 'DescripcionNota') {
                    $valorCampo = (string) $item['ValorCampo'];
                    if ($valorCampo != '') {
                        // Mostrar 'DescripcionNota'
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

                // Lista de campos a mostrar en pares, excepto DiagnosticoPrincipal
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
                        // Verificar si es la segunda columna, forzar salto de línea si es necesario
                        if ($counter > 0 && $counter % 2 == 0) {
                            $pdf->Ln();
                            $counter = 0; // Reiniciar el contador de columnas
                        }

                        // Mostrar el título y el valor
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

                        $counter++; // Incrementar el contador de columnas
                    }
                }

                // Guardar Diagnóstico Principal para mostrarlo al final en su propia línea
                if ((string) $item['NombreCampo'] === 'DiagnosticoPrincipal' && (string) $item['ValorCampo'] != '') {
                    $diagnosticoPrincipal = (string) $item['ValorCampo'];
                }

                // Guardar Observación para mostrarla después del Diagnóstico Principal
                if ((string) $item['NombreCampo'] == 'ObservacionP') {
                    $valorCampo = (string) $item['ValorCampo'];
                    if($valorCampo != ''){
                        $observacion = (string) $item['ValorCampo'];
                    }
                    
                }
            }

            // Mostrar Diagnóstico Principal al final
            if ($diagnosticoPrincipal != '') {
                $pdf->Ln(); // Salto de línea antes del Diagnóstico Principal
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(45, 5, utf8_decode('Diagnóstico Principal:'));
                $pdf->SetFont('Arial', '', 8);

                // Decodificar y limpiar el valor del Diagnóstico Principal
                $diagnosticoPrincipalDecodificado = html_entity_decode($diagnosticoPrincipal, ENT_QUOTES, 'UTF-8');
                $diagnosticoPrincipalDecodificado = str_replace(
                    ['<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;'],
                    PHP_EOL,
                    $diagnosticoPrincipalDecodificado
                );
                $textoPlanoDiagnostico = strip_tags($diagnosticoPrincipalDecodificado);
                $pdf->Cell(35, 5, utf8_decode($textoPlanoDiagnostico));
                $pdf->Ln(); // Salto de línea después del Diagnóstico Principal
            }

            // Mostrar Observación después del Diagnóstico Principal
            if ($observacion != '') {
                $pdf->Ln(3); // Agregar unos saltos de línea
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(45, 5, utf8_decode('Observación:'));
                $pdf->SetFont('Arial', '', 8);
                // Decodificar y limpiar el valor de la Observación
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

        // Dibujar las líneas y la información del médico
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

        // Parámetros para el rectángulo punteado
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
        
        $pdf->Ln(2);

        try {
            $xml = new SimpleXMLElement($patient['RegistroXML']);
            $maxY = 270;
            $planAdministradora = '';
            $counter = 0;
            foreach ($xml as $item) {
                
                $campos = [
                    'ORemitido' => 'Remitido:',
                    'Contrareferencia' => 'Contra referencia:',
                    'GlasW' => 'Glasgow:',
                    'Valoracion' => 'Valoracion:',
                    'Prioridad' => 'Triage:',
                    'TipoConsulta' => 'Tipo de consulta:',
                ];
            
                foreach ($campos as $campo => $titulo) {
                    if ((string) $item['NombreCampo'] === $campo) {
                        $valorCampo = $item['ValorCampo'];
            
                        if ($item['NombreCampo'] == 'ORemitido' && $item['NombreTabla'] == 'CamposBoolean') {
                            $valorCampo = ($valorCampo == 0) ? 'No' : 'Si';
                        } elseif ($item['NombreCampo'] == 'ORemitido' && $item['NombreTabla'] == 'CamposTexto' && $valorCampo == 0) {
                            $valorCampo = '';
                        }
            
                        if ($item['NombreCampo'] == 'Contrareferencia' && $item['ValorCampo'] == "false") {
                            $valorCampo = 'No';
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
            
                if ((string) $item['NombreCampo'] === 'PlanAdministradora') {
                    $planAdministradora = (string) $item['ValorCampo'];
                }
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

        // Alinear horizontalmente los textos (centrados entre $x_start y $x_end)
        $pdf->SetX($x_start);

        // Nombre del médico
        $nombreMedico = strtoupper($patient['NombreMedico']);
        $pdf->Cell($width, 5, utf8_decode($nombreMedico), 0, 1, 'C');  // Centrado con ancho controlado

        // Descripción
        $descripcion = strtoupper($patient['Descrip']);
        $pdf->SetX($x_start);
        $pdf->Cell($width, 5, utf8_decode($descripcion), 0, 1, 'C');

        // Registro médico
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
        // Línea superior
        for ($i = $x; $i < $x + $w; $i += ($dot_length + $space_length)) {
            $pdf->Line($i, $y, $i + $dot_length, $y);
        }
        
        // Línea inferior
        for ($i = $x; $i < $x + $w; $i += ($dot_length + $space_length)) {
            $pdf->Line($i, $y + $h, $i + $dot_length, $y + $h);
        }

        // Línea izquierda
        for ($i = $y; $i < $y + $h; $i += ($dot_length + $space_length)) {
            $pdf->Line($x, $i, $x, $i + $dot_length);
        }

        // Línea derecha
        for ($i = $y; $i < $y + $h; $i += ($dot_length + $space_length)) {
            $pdf->Line($x + $w, $i, $x + $w, $i + $dot_length);
        }
    }
        
}

?>