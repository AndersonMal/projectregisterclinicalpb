<?php 

class Views{
    public function getView($controller, $view){
        $controller = get_class($controller);
        if( $controller == "Home" ){
            $view = "Views/".$view.".php";
        }else{
            $controller = str_replace("Controller", "", $controller);
             $view = "Views/" . $controller . "/" . $view . ".php";
        }
        require $view;
    }
}

?>