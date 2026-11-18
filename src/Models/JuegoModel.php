<?php
declare(strict_types=1);

namespace Models;

use RunTimeException;

class JuegoModel{
    private const RUTA_PUBLICA = "uploads/juegos/";

    public function __construct(private string $rutaJson, private string $rutaUploads){}

    /* Lista de juegos. JSON inexistente o vacío => []. */
    public function todos(): array{
        if (!is_file($this->rutaJson)) {
            return [];
        }

        $json = file_get_contents($this->rutaJson);
        if ($json === false || trim($json) === "") {
            return [];
        }

        $juegos = json_decode($json, true);
        if(!is_array($juegos)){
            throw new \RuntimeException("El fichero de juegos está corrupto");
        }

        return $juegos;
    }

    /* Guardo el ZIP con nombre único y registra el juego.
     * $datos: titulo, descripcion, autor, categoria, nombre_original
     */
    public function guardar(array $datos, string $rutaTemporal): void{
        // 1. Carpteas de destino
        foreach ([$this->rutaJson, dirname($this->rutaJson)] as $carpeta) {
            if (!is_dir($carpeta) && !mkdir($carpeta, 0755, true) && !is_dir($carpeta)) {
                throw new RuntimeException('No se puedo crear la carpeta ' . basename($carpeta));
            }
        }

        // 2. Nombre propio y único: nunca se usa el nombre del usuario
        $nombre = date('TmdHis') . '_' . bin2hex(random_bytes(8)) . '.zip';
        $destino = rtrim($this->rutaUploads, '/\\') . DIRECTORY_SEPARATOR . $nombre;

        // 3. Mover el temporal
        if (!move_uploaded_file($rutaTemporal, $destino)) {
            throw new RunTimeException("No se pudo guardar el archivo subido.");
        }

        // 4. Registrar en el JSON
        $juegos = $this->todos();
        $juegos[] = [
            'titulo'          => $datos['titulo'],
            'descripcion'     => $datos['descripcion'],
            'autor'           => $datos['autor'],
            'categoria'       => $datos['categoria'],
            'archivo'         => self::RUTA_PUBLICA . $nombre,
            'nombre_original' => $datos['nombre_original'],
        ];

        $json = json_encode($juegos, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($json === false || file_put_contents($this->rutaJson, $json, LOCK_EX) === false) {
            unlink($destino); //no fejamos un ZIP huérfano sin registro
            throw new RunTimeException("No se pudo registrar el juego.");
        }
    }
}