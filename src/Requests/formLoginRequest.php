<?php
declare(strict_types=1);

namespace Requests;

final class FormLoginRequest{
    private array $datos = [];
    private array $errores = [];

    public function __construct(array $post, array $files = [])
    {
        $this->sanear($post);
        $this->sanear($files);
        $this->validar();
    }

    public function esValido(): bool
    {
        return $this->errores === [];
    }

    /** @return array<string, string> */
    public function errores(): array
    {
        return $this->errores;
    }

    /** @return array<string, mixed> los datos saneados */
    public function datos(): array
    {
        return $this->datos;
    }

    private function agregarError(string $campo, string $mensaje): void{

    }
    private function sanear(array $entrada): void
    {
        // El usuario puede manipular la petición: solo aceptamos textos
        $texto = static fn(mixed $v): string => is_string($v) ? trim(strip_tags($v)) : '';
        $this->datos = [
            /*
            'usuario' => $texto($entrada['usuario'] ?? ''),
            'contraseña' => $texto($entrada['contraseña'] ?? '')
            */
        ];

    }

    private function validar(): void
    {
        $d = $this->datos;

        /*

        // --- Usuario ---
        if ($d['usuario'] === ''){
            $this->errores['usuario'] = "El usuario es obligatorio.";
        } elseif (mb_strlen($d['usuario']) < 5) {
            $this->errores['usuario'] = "El usuario debe tener al menos 5 caracteres.";
        } elseif (!preg_match('/^[\pL]+$/u', $d['usuario'])) {
            $this->errores['usuario'] = "El usuario solo puede contener letras.";
        }
        // --- Contraseña ---
        if ($d['contraseña'] !== '') {
            $this->errores["contraseña"] = "La contraseña es obligatoria.";
            if ($d["contraseña"] === ""){
                $this->errores["contraseña"] = "La contraseña es obligatoria.";
            } elseif (mb_strlen($d['contraseña']) < 5) {
                $this->errores['contraseña'] = "La contraseña debe tener al menos 5 caracteres.";
            }
        }

        */
    }
}

