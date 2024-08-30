<?php 

class Views {
    public function getView($controller, $view, $data = []) {
        $controllerName = get_class($controller);
        if ($controllerName == "Home") {
            $viewPath = "Views/" . $view . ".php";
        } else {
            $controllerName = str_replace("Controller", "", $controllerName);
            $viewPath = "Views/" . $controllerName . "/" . $view . ".php";
        }
        
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Vista no encontrada: " . $viewPath;
        }
    }
    
}


?>