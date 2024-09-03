<?php 
require_once 'Models/RegistersClinicalModel.php';

class RegisterController extends Controller {

    private $registermodel;

    public function __construct() { 
        session_start();
        parent::__construct();
        $conex = new Conexion();
        $this->registermodel = new RegistersClinicalModel($conex->getConnection());
    }
       public function view($view, $data = []) {
        require_once "../Views/$view.php";
    }

    public function index() {
       try {
            if (!isset($_SESSION['id_user'])) {
                header("Location: " . base_url);
                exit();
            }
            $identification = $_SESSION['id_user'];
            $data = $this->registermodel->getClinicalData($identification);   
            if (!empty($data) && is_array($data)) {
                $_SESSION['id_user'] = $data['Nombre'];
            }
            var_dump($data);
            $this->view('Templates/mainUser', ['data' => $data]);
            
       } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
       }
       die();
    }
}
?>