<?php
declare(strict_types=1);

namespace Controllers;

use Core\Pages;
use Models\JugadorModel;
use Requests\formLoginRequest;
use Requests\formRegistroRequest;

/**
 * Registro, inicio y cierre de sesión.
 *
 * El controlador solo orquesta: crea la Request, llama al modelo y
 * muestra la vista o redirige. No valida ni escribe HTML.
 */
class AuthController
{
    private Pages $pages;

    public function __construct()
    {
        $this->pages = new Pages();
    }

    /** Fase 1: registro de jugador. */
    public function registro(): void
    {
        // GET: formulario vacío
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->pages->render('Registro/registro', ['errores' => [], 'datos' => []]);
            return;
        }

        // POST: sanear y validar
        $request = new formRegistroRequest($_POST);

        if ($request->esValido()) {
            // Reglas que dependen del modelo: el modelo responde, el controlador decide
            $modelo = new JugadorModel(RUTA_JUGADORES);

            if ($modelo->existeUsername($request->username())) {
                $request->agregarError('usuario', 'Ese nombre de usuario ya está en uso.');
            }
            if ($modelo->existeEmail($request->email())) {
                $request->agregarError('email', 'Ese correo ya está registrado.');
            }

            if ($request->errores() === []) {
                $modelo->crear($request->username(), $request->email(), $request->password());

                // POST/Redirect/GET: evita duplicar el jugador al recargar
                $this->redirigir('login', ['registrado' => 1]);
            }
        }

        // Hay errores: mostramos de nuevo el formulario con lo que escribió
        $this->pages->render('Registro/registro', [
            'errores' => $request->errores(),
            'datos'   => $request->datos(),
        ]);
    }

    /** Fase 2: inicio de sesión. */
    public function login(): void
    {
        // GET: formulario (con mensaje si venimos del registro)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->pages->render('Login/login', [
                'errores'    => [],
                'datos'      => [],
                'registrado' => ($_GET['registrado'] ?? '') === '1',
            ]);
            return;
        }

        // POST: sanear y validar
        $request = new formLoginRequest($_POST);

        if ($request->esValido()) {
            $modelo = new JugadorModel(RUTA_JUGADORES);
            $jugador = $modelo->verificarCredenciales($request->email(), $request->password());

            if ($jugador !== null) {
                // Sin sesiones todavía: el nombre viaja en la URL
                $this->redirigir('home', ['bienvenido' => $jugador['username']]);
            }

            // Siempre el mismo error genérico: no revelamos qué falló
            $request->agregarError('login', 'Email o contraseña incorrectos');
        }

        $this->pages->render('Login/login', [
            'errores'    => $request->errores(),
            'datos'      => $request->datos(),
            'registrado' => false,
        ]);
    }

    /** Fase 6: cierre de sesión (no usa vista). */
    public function logout(): void
    {
        session_start();
        $_SESSION = [];
        session_destroy();

        $this->redirigir('home');
    }

    /**
     * Redirige a otra acción y termina la ejecución.
     * Los parámetros se codifican con http_build_query().
     *
     * @param array<string, scalar> $params
     */
    private function redirigir(string $accion, array $params = []): never
    {
        header('Location: index.php?' . http_build_query(['accion' => $accion] + $params));
        exit;
    }
}
