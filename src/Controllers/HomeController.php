<?php
declare(strict_types=1);

namespace Controllers;

class HomeController{
    public function index(){
        $bienvenido = $_GET['bienvenido'] ?? '';
        $usuario = (is_string($bienvenido) && $bienvenido !== '') ? $bienvenido : null;

        (new Pages())->render('Home/home', ['usuario' => $usuario]);
    }

}
