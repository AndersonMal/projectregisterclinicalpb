<?php 
require_once 'Models/RegisterModel.php';

class RegisterController extends Controller {
    private $registermodel;

    public function __construct() { 
        session_start();
        parent::__construct();
        $this->registermodel = new RegisterModel(); 
    }

    public function index()
    {
        //print_r($this->model->getUser())
        $this->views->getView($this, "registersrout");
    }
    public function validate() {
        try {
            if(empty($_POST['document'])) {
                $msg = "Documento no encontrado";
            } else {
                $user = $_POST['document'];
                $firstname = $_POST['firstname'];

                if ($firstname === null) {
                    $msg = "Primer apellido no encontrado";
                } else {
                    $data = $this->registermodel->getRegister($user);

                    if ($data && $data['Ape1Afil'] === $firstname) {
                        $_SESSION['id_user'] = $data['Identificacion'];
                        $_SESSION['firstname'] = $data['Ape1Afil'];
                        $msg = "Ok";
                    } else {
                        $msg = "Usuario incorrecto o apellido no coincide";
                    }
                }
            }

            // Envía la respuesta en formato JSON
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode("Error: " . $e->getMessage());
        }
        die();
    }

    
    
    


}

?>