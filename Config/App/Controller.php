<?php 

class Controller{

    public $views;
    public function __construct()
    {
        $this->views = new Views();
        $this->loadModel();
    }

    public function loadModel(){
        $model = get_class($this)."Model";
        $rout = "Models/".$model.".php";
        if(file_exists($rout)){
            require_once $rout;
            $this->$model = new $model();
        }
    }

}


?>