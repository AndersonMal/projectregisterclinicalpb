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

    public function validate(){
        $document = $_POST['document'];
        $lastname = $_POST['lastname'];
        $birthdate = $_POST['birthdate'];
        $user = $this->registermodel->getRegister($document);

        if ($user) {
            if ($user['Apellido'] == $lastname && $user['FechaNacimiento'] == $birthdate) {
                echo json_encode("Ok");
            } else {
                echo json_encode("Datos incorrectos");
            }
        } else {
            echo json_encode("El usuario no existe");
        }

    }


}

?>