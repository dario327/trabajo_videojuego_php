<?php
declare(strict_types=1);

namespace Controllers;

use Controllers\ErrorController;
use Throwable;

/**
 * Controlador frontal: único punto de entrada de la aplicación.
 *
 * Lee ?controller=...&action=... de la URL, comprueba que existen
 * y llama a la acción. Si no es válido, muestra un error 404.
 *
 * Requiere las constantes CONTROLLER_DEFAULT y ACTION_DEFAULT (config/config.php).
 */
class FrontController
{
    private const ACTION_DEFAULT = 'home';
    private const RUTAS = [
        "home" => [HomeController::class, "index"],
        "registro" => [AuthController::class, "registro"],
        "login" => [AuthController::class, "login"],
        "logout" => [AuthController::class, "logout"],
        "subir" => [JuegoController::class, "subir"],
        "juegos" => [JuegoController::class, "listar"],
    ];
    public static function main(): void
    {
        // Acción: los de la URL o los predeterminados
        $action = $_GET['action'] ?? self::ACTION_DEFAULT;

        if (!is_string($action) || !array_key_exists($action, self::RUTAS)) {
            ErrorController::show_error404();
            return;
        }

        [$clase, $metodo] = self::RUTAS[$action];
        try {
            (new $clase()) -> $metodo();
        } catch (Throwable $e) {
            error_log($e->getMessage());
            ErrorController::show_error500();
        }
    }
}