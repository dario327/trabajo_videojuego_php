<?php
declare(strict_types=1);

namespace Controllers;

use Core\Pages;
use Models\JuegoModel;
use Requests\formSubirJuegoRequest;

/**
 * Subida y listado de juegos. Orquesta: sin validaciones ni HTML.
 */
class JuegoController
{
    /** Fase 3: GET muestra el formulario, POST lo procesa. */
    public function subir(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->mostrarFormulario([], []);
            return;
        }

        $request = new formSubirJuegoRequest($_POST, $_FILES);

        if (!$request->esValido()) {
            $this->mostrarFormulario($request->errores(), $request->datos());
            return;
        }

        $modelo = new JuegoModel(RUTA_JUEGOS, RUTA_UPLOADS);
        $modelo->guardar([
            'titulo'          => $request->titulo(),
            'descripcion'     => $request->descripcion(),
            'categoria'       => $request->categoria(),
            'autor'           => 'invitado', // provisional hasta tener sesiones
            'nombre_original' => $request->nombreOriginal(),
        ], $request->rutaTemporal());

        // POST/Redirect/GET
        header('Location: index.php?accion=juegos');
        exit;
    }

    /** Fase 4: listado de juegos. */
    public function listar(): void
    {
        $juegos = (new JuegoModel(RUTA_JUEGOS, RUTA_UPLOADS))->todos();
        (new Pages())->render('Juego/juegos', ['juegos' => $juegos]);
    }

    private function mostrarFormulario(array $errores, array $datos): void
    {
        (new Pages())->render('Juego/subir_juego', [
            'errores'    => $errores,
            'datos'      => $datos,
            'categorias' => CATEGORIAS,
        ]);
    }
}
