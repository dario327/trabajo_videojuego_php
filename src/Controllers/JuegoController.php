<?php
declare(strict_types=1);

namespace Controllers;

use Core\Pages;
use Models\JuegoModel;
use Requests\formSubirJuegoRequest;

class JuegoController{
    public function subir(): void{
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->mostrarFormulario([], []);
            return;
        }

        $request = new FormSubirJuegoRequest($_POST, $_FILES);

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

        header('Location: index.php?accion=jueegos');
        exit;
    }

    public function listar(): void{
        $juegos = (new JuegoModel(RUTA_JUEGOS, RUTA_UPLOADS))->todos();
        (new Pages)->render('Juego/juegos', ['juegos' => $juegos]);
    }

    private function mostrarFormulario(array $errores, array $datos): void{
        (new Pages)->render('Juego/subir_juego', [
            'errores' => $errores,
            'datos' => $datos,
            'categorias' => CATEGORIAS,
        ]);
    }
}