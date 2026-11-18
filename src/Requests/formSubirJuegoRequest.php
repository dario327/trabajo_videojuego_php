<?php
declare(strict_types=1);

namespace Requests;

class formSubirJuegoRequest{
    private string $titulo;
    private string $descripcion;
    private string $categoria;
    private array $archivo;
    private array $errores = [];

    private function limpiarTexto(mixed $valor): string{
        return is_string($valor) ? trim(strip_tags($valor)) : '';
    }

    // Recibe los datos en bruto y guarda los valores saneados
    public function __construct(array $post, array $files = []){
        $this->titulo = $this->limpiarTexto($post['titulo'] ?? '');
        $this->descripcion = $this->limpiarTexto($post['descripcion'] ?? '');
        $this->categoria = is_string($post['categoria'] ?? null) ? trim($post['categoria']) : '';
        $this->archivo = is_array($files['archivo'] ?? null) ? $files['archivo'] : [];
    }

    // Ejecuta las validaciones y devuelve true si no hay errores
    public function esValido(): bool{
        $this->errores = [];

        if ($this->titulo === ''){
            $this->errores['titulo'] = 'El título es obligatorio.';
        } elseif (mb_strlen($this->titulo) > 100){
            $this->errores['titulo'] = 'El título no puede superar los 100 caracteres.';
        }

        if ($this->descripcion === ''){
            $this->errores['descripcion'] = 'El descripcion es obligatorio.';
        } elseif (mb_strlen($this->descripcion) > 500){
            $this->errores['descripcion'] = 'La descripcion no puede superar los 500 caracteres.';
        }


        if (!in_array($this->categoria, CATEGORIAS, true)){
            $this->errores['categoria'] = 'La categoría seleccionada no es válida.';
        }

        $this->ValidarArchivo();

        return $this->errores === [];
    }

    public function rutaTemporal(): string{
        return (string)($this->archivo['tmp_name'] ?? '');
    }

    private function ValidarArchivo(): void{
        $error = $this->archivo['error'] ?? UPLOAD_ERR_OK;

        // Que se haya enviado y sin errores
        if ($error !== UPLOAD_ERR_OK){
            $this->errores['archivo'] = match($error) {
                UPLOAD_ERR_NO_FILE => 'El archivo ZIP es obligatorio.',
                UPLOAD_ERR_INI_SIZE => 'El archivo supera el tamaño máximo permitido (10 MB)',
                default => 'No se pudo subir el archivo. Inténtalo de nuevo.',
            };
            return;
        }

        $tmp = $this->rutaTemporal();

        // Que venga de una subida HTTP real
        if (!is_uploaded_file($tmp)){
            $this->errores['archivo'] = "El archivo no es válido.";
            return;
        }

        // Extensión .zip (en minúsculas)
        $extension = strtoLower(pathInfo((string)($this->archivo['name'] ?? '')), PATHINFO_EXTENSION);
        if ($extension !== '.zip'){
            $this->errores['archivo'] = "El archivo debe tener extensión .zip.";
            return;
        }

        // Tipo MIME real (no el que dice el navegador en $_FILES['archivo']['type'])
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo !== false ? finfo_file($finfo, $tmp) : false;
        if ($mime !== 'application/zip'){
            $this->errores['archivo'] = "El archivo no es un ZIP válido.";
            return;
        }

        // Tamaño máximo
        $tamano = filesize($tmp);
        if ($tamano === false || $tamano > MAX_ZIP){
            $this->errores['archivo'] = "El archivo supera el tamaño máximo permitido (10 MB).";
        }
    }

    public function nombreOriginal(): string{
        $nombre = basename((string)($this->archivo['name'] ?? ''));
        return preg_replace(('/[^A-Za-z0-9._-]/') ?? '');
    }

    public function errores(): array
    {
        return $this->errores;
    }

    public function agregarError(string $campo, string $mensaje): void
    {
        $this->errores[$campo] = $mensaje;
    }

    public function datos(): array
    {
        return [
            'titulo'      => $this->titulo,
            'descripcion' => $this->descripcion,
            'categoria'   => $this->categoria,
        ];
    }

    public function titulo(): string
    {
        return $this->titulo;
    }

    public function descripcion(): string
    {
        return $this->descripcion;
    }

    public function categoria(): string
    {
        return $this->categoria;
    }
}
