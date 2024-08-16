<?php 
require_once 'Models/UserModel.php';

class Users extends Controller{
    private $model;

    public function __construct() {
        session_start();
        parent::__construct();
        $this->model = new UserModel(); 
    }

    public function index()
    {
        //print_r($this->model->getUser());
        $this->views->getView($this, "index");
    }

    public function validate(){
        if(empty($_POST['document']) || empty($_POST['password'])){
            $msg = "Los campos estan vacios";
        }else{
            $user = $_POST['document'];
            $data = $this->model->getUser($user);
            if($data){
                $_SESSION['id_user'] = $data['Identificacion'];
                $_SESSION['name'] = $data['Nombre'];
                $msg = "Ok";
            }else{
                $msg = "Usuario o contraseña incorrecta";
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

}

?>