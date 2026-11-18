<?php
declare(strict_types=1);

namespace Controllers;

use Core\Pages;

/**
 * Fase 5: página principal.
 * Sin sesiones, el nombre llega por la URL (?bienvenido=Nombre).
 * Cuando haya sesiones solo cambiará la línea que obtiene $usuario.
 */
class HomeController
{
    public function index(): void
    {
        $bienvenido = $_GET['bienvenido'] ?? '';
        $usuario = (is_string($bienvenido) && $bienvenido !== '') ? $bienvenido : null;

        (new Pages())->render('Home/home', ['usuario' => $usuario]);
    }
}
