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
        $cont = 0;
        foreach ($dataArray as $item) {
            $xmlString = $item->RegistroXML;
            $xml = simplexml_load_string($xmlString);
            foreach ($xml->row as $row) {
                print_r($row) . '\n' ;
                $cont++;
                
            }
            echo $cont;
            echo   '<br>';
        }        
    }

    public function createPDF(){
        $dataPDF = $this->model->getRegisters($_SESSION['id_user']);

            require('Libraries/FPDF/fpdf.php');
         
            $pdf = new FPDF('P','mm','A4');
            $pdf->AddPage();
            $pdf->Image('Assets/css/v9_58.png', 15,8,23);
            $pdf->setTitle(utf8_decode('Registro Clínico'));
            $pdf->SetFont('Courier','B',10);
            
            $pdf->Cell(60);
            $pdf->MultiCell(120,4, utf8_decode("PERFECT BODY MEDICAL CENTER"), 0, 'l', false);
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
            $pdf->Cell(120,3, utf8_decode(" 4237101"));
            $pdf->Output();
        
    }


}

?>