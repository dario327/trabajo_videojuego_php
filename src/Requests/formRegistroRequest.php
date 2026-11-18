<?php
declare(strict_types=1);

namespace Requests;

/**
 * Sanea y valida el formulario de registro.
 *
 * No conoce los modelos ni imprime nada.
 * Las reglas que dependen del modelo (usuario o correo repetido)
 * las añade el controlador con agregarError().
 */
final class formRegistroRequest
{
    private string $usuario = '';
    private string $email = '';
    private string $password = '';
    private string $confirmar = '';

    /** @var array<string, string> mensajes indexados por campo */
    private array $errores = [];

    /** Evita ejecutar las validaciones dos veces */
    private bool $validado = false;

    /**
     * Recibe los datos en bruto y guarda los valores saneados.
     * $files no se usa en este formulario, pero se mantiene por el contrato común.
     */
    public function __construct(array $post, array $files = [])
    {
        // Todo dato del usuario es peligroso: solo aceptamos textos
        $texto = static fn(mixed $v): string => is_string($v) ? trim(strip_tags($v)) : '';

        $this->usuario = $texto($post['usuario'] ?? '');

        // Correo: trim() + FILTER_SANITIZE_EMAIL
        $correo = is_string($post['email'] ?? null) ? trim($post['email']) : '';
        $this->email = (string) filter_var($correo, FILTER_SANITIZE_EMAIL);

        // Las contraseñas NO se sanean (se usan tal cual, solo se guarda su hash)
        $this->password = is_string($post['password'] ?? null) ? $post['password'] : '';
        $this->confirmar = is_string($post['confirmar'] ?? null) ? $post['confirmar'] : '';
    }

    /** Ejecuta las validaciones y devuelve true si no hay errores. */
    public function esValido(): bool
    {
        if (!$this->validado) {
            $this->validar();
            $this->validado = true;
        }

        return $this->errores === [];
    }

    /** @return array<string, string> */
    public function errores(): array
    {
        return $this->errores;
    }

    /** Permite añadir errores de reglas que dependen del modelo. */
    public function agregarError(string $campo, string $mensaje): void
    {
        $this->errores[$campo] = $mensaje;
    }

    /**
     * Valores saneados para rellenar de nuevo el formulario.
     * Nunca incluye contraseñas.
     *
     * @return array<string, string>
     */
    public function datos(): array
    {
        return [
            'usuario' => $this->usuario,
            'email'   => $this->email,
        ];
    }

    public function username(): string
    {
        return $this->usuario;
    }

    public function email(): string
    {
        return $this->email;
    }

    /** Devuelve la contraseña SIN sanear: solo para pasársela al modelo. */
    public function password(): string
    {
        return $this->password;
    }

    private function validar(): void
    {
        // --- Usuario: obligatorio, entre 3 y 20 caracteres ---
        if ($this->usuario === '') {
            $this->errores['usuario'] = 'El nombre de usuario es obligatorio.';
        } elseif (mb_strlen($this->usuario) < 3 || mb_strlen($this->usuario) > 20) {
            $this->errores['usuario'] = 'El nombre de usuario debe tener entre 3 y 20 caracteres.';
        }

        // --- Correo: obligatorio y con formato válido ---
        if ($this->email === '') {
            $this->errores['email'] = 'El correo electrónico es obligatorio.';
        } elseif (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errores['email'] = 'El correo electrónico no tiene un formato válido.';
        }

        // --- Contraseña: obligatoria y mínimo 8 caracteres ---
        if (trim($this->password) === '') {
            $this->errores['password'] = 'La contraseña es obligatoria.';
        } elseif (mb_strlen($this->password) < 8) {
            $this->errores['password'] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        // --- Confirmación: obligatoria e idéntica a la contraseña ---
        if (trim($this->confirmar) === '') {
            $this->errores['confirmar'] = 'Debes confirmar la contraseña.';
        } elseif ($this->confirmar !== $this->password) {
            $this->errores['confirmar'] = 'Las contraseñas no coinciden.';
        }
    }
}
