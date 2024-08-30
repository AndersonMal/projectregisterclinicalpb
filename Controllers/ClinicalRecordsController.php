<?php
require_once 'Models/RegisterModel.php';

class ClinicalRecordsController extends Controller {
    private $registermodel;

    public function __construct() { 
        parent::__construct();
        $conexion = new Conexion();
        var_dump($conexion->getConnection()); 
        $this->registermodel = new RegisterModel($conexion->getConnection(), $conexion->getConnection2());
    }


    public function index() {
        if (!isset($_SESSION['id_user'])) {
            header("Location: " . base_url);
            exit();
        }
        $identification = $_SESSION['id_user'];
        var_dump($identification); 
        $data = $this->registermodel->getClinicalData($identification);
        var_dump($data); 
        $this->views->getView($this, "mainUser", $data);
    }
    
    
    
}
