<?php
declare(strict_types=1);

namespace Controllers;
use Core\Pages;

class AuthController{
    private Pages $pages;

    public function __construct(){
        $this->pages = new Pages();
    }

    public function registro(){
        $this->pages->render("/Registro/registro");
    }
}

