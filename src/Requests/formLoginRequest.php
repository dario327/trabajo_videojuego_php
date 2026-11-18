<?php
declare(strict_types=1);

namespace Requests;

/**
 * Sanea y valida el formulario de inicio de sesión.
 *
 * Solo comprueba que los campos no estén vacíos y que el correo tenga
 * formato válido. Si las credenciales son correctas lo decide el modelo.
 */
final class formLoginRequest
{
    private string $email = '';
    private string $password = '';

    /** @var array<string, string> mensajes indexados por campo */
    private array $errores = [];

    /** Evita ejecutar las validaciones dos veces */
    private bool $validado = false;

    public function __construct(array $post, array $files = [])
    {
        // Correo: solo textos, trim() + FILTER_SANITIZE_EMAIL
        $correo = is_string($post['email'] ?? null) ? trim($post['email']) : '';
        $this->email = (string) filter_var($correo, FILTER_SANITIZE_EMAIL);

        // La contraseña NO se sanea
        $this->password = is_string($post['password'] ?? null) ? $post['password'] : '';
    }

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
     * Nunca incluye la contraseña.
     *
     * @return array<string, string>
     */
    public function datos(): array
    {
        return ['email' => $this->email];
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
        if ($this->email === '') {
            $this->errores['email'] = 'El correo electrónico es obligatorio.';
        } elseif (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errores['email'] = 'El correo electrónico no tiene un formato válido.';
        }

        if (trim($this->password) === '') {
            $this->errores['password'] = 'La contraseña es obligatoria.';
        }
    }
}
