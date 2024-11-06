<?php
require_once 'Models/RegisterModel.php';

class ChangePasswordController extends Controller{
    private $registermodel;

    public function __construct() { 
        session_start();
        parent::__construct();

        $conexion = new Conexion();
        $this->registermodel = new RegisterModel($conexion->getConnection(), $conexion->getConnection2());
    }
    public function index(){
        $this->views->getView($this,"chagepasswordrout");
    }
    
    public function validate() {
        try {
            if(empty($_POST['document'])) {
                $msg = "Documento no encontrado";
            } else {
                $user = $_POST['document'];
                $firstname = $_POST['firstname'];
                $password = $_POST['new_password'];
                $confirmPassword = $_POST['confirm_password'];
    
                if (empty($firstname)) {
                    $msg = "Primer apellido no encontrado";
                } else {
                    $data = $this->registermodel->getRegister($user);
    
                    if ($data && $data['Ape1Afil'] == $firstname) {
                        if($password != $confirmPassword){
                            $msg = "Contraseñas no coinciden";
                        } else {
                            $existingUser = $this->registermodel->verifyUsers($user);
                            if (!$existingUser) {
                                $msg = "El documento no está registrado en la base de datos";
                            } else {
                                $_SESSION['id_user'] = $data['Identificacion'];
                                $_SESSION['firstname'] = $data['Ape1Afil'];
                                $this->registermodel->changePassword($user, $password);
                                $msg = "Ok";
                            }
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