<?php
declare(strict_types=1);

namespace Controllers;

use Core\Pages;
use Models\JugadorModel;

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

        // Contadores: los calculan los modelos, el controlador solo los pasa a la vista
        $totalJugadores = (new JugadorModel(RUTA_JUGADORES))->total();
        $totalJuegos = 0; // TODO: (new JuegoModel(RUTA_JUEGOS, RUTA_UPLOADS))->total() cuando Darío tenga su modelo

        (new Pages())->render('Home/home', [
            'usuario'        => $usuario,
            'totalJugadores' => $totalJugadores,
            'totalJuegos'    => $totalJuegos,
        ]);
    }
}