<?php 
require_once 'Models/RegisterModel.php';

class RegisterController extends Controller {
    private $registermodel;

    public function __construct() { 
        session_start();
        parent::__construct();

        $conexion = new Conexion();
        $this->registermodel = new RegisterModel($conexion->getConnection(), $conexion->getConnection2());
    }

    public function index()
    {
        $this->views->getView($this, "registersrout");
    }

    public function validate() {
        try {
            if(empty($_POST['document'])) {
                $msg = "Documento no encontrado";
            } else {
                $user = $_POST['document'];
                $firstname = $_POST['firstname'];
                $birthdate = $_POST['birthdate'];
                $password = $_POST['password'];
                $confirmPassword = $_POST['confirm_password'];

                if (empty($firstname)) {
                    $msg = "Primer apellido no encontrado";
                } else {
                    $data = $this->registermodel->getRegister($user);

                    if ($data && $data['Ape1Afil'] == $firstname) {
                        if($password != $confirmPassword){
                            $msg = "Contraseñas no coinciden";
                        }else{
                                
                            $_SESSION['id_user'] = $data['Identificacion'];
                            $_SESSION['firstname'] = $data['Ape1Afil'];

                            // Guardar datos en la otra base de datos
                            $this->registermodel->saveRegister([
                                'document' => $user,
                                'firstname' => $firstname,
                                'birthdate' => $birthdate,
                                'password' => $password
                            ]);

                            $msg = "Ok";
                        }
                    } else {
                        $msg = "Documento incorrecto o apellido no coincide";
                    }
                }
            }

            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode("Error: " . $e->getMessage());
        }
        die();
    }
}
?>