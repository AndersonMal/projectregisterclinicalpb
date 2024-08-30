<?php
    require_once "Config/Config.php";
    $ruta = !empty($_GET['url']) ? $_GET['url'] : "Home/index";
    $array = explode("/", $ruta);
    $controller = $array[0];
    $metodo = !empty($array[1]) ? $array[1] : "index";
    $parametro = !empty($array[2]) ? implode(",", array_slice($array, 2)) : "";
    
    require_once "Config/App/autoload.php";
    $dirControllers = "Controllers/".$controller.".php";
    
    if (file_exists($dirControllers)) {
        require_once $dirControllers;
        $controller = new $controller();
        if (method_exists($controller, $metodo)) {
            $controller->$metodo($parametro);
        } else {
            echo "No existe el método";
        }
    } else {
        echo "No existe el controlador";
    }
    
?>