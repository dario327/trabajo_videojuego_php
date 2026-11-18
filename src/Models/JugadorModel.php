<?php
declare(strict_types=1);

namespace Models;

use JsonException;
use RuntimeException;

/**
 * Lee y guarda los jugadores en data/jugadores.json.
 *
 * Es la única clase que toca ese fichero. No usa superglobales,
 * ni imprime nada ni redirige.
 */
final class JugadorModel
{
    public function __construct(private string $ruta)
    {
    }

    /**
     * Devuelve todos los jugadores.
     * Si el fichero no existe o está vacío, devuelve [].
     * Si el JSON está corrupto lanza una excepción (el FrontController
     * la captura y muestra el error 500).
     *
     * @return array<int, array{username: string, email: string, password: string}>
     * @throws JsonException
     */
    public function todos(): array
    {
        if (!is_file($this->ruta)) {
            return [];
        }

        $json = file_get_contents($this->ruta);
        if ($json === false || trim($json) === '') {
            return [];
        }

        $jugadores = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return is_array($jugadores) ? $jugadores : [];
    }

    /** ¿Existe ya ese no   mbre de usuario? (sin distinguir mayúsculas) */

    /** Número total de jugadores registrados. */
    public function total(): int
    {
        return count($this->todos());
    }
    public function existeUsername(string $username): bool
    {
        $buscado = mb_strtolower($username);

        foreach ($this->todos() as $jugador) {
            if (mb_strtolower((string) ($jugador['username'] ?? '')) === $buscado) {
                return true;
            }
        }

        return false;
    }

    /** ¿Existe ya ese correo? (sin distinguir mayúsculas) */
    public function existeEmail(string $email): bool
    {
        $buscado = mb_strtolower($email);

        foreach ($this->todos() as $jugador) {
            if (mb_strtolower((string) ($jugador['email'] ?? '')) === $buscado) {
                return true;
            }
        }

        return false;
    }

    /**
     * Guarda un jugador nuevo con la contraseña cifrada.
     *
     * @throws RuntimeException si no se puede escribir el fichero
     */
    public function crear(string $username, string $email, string $password): void
    {
        // 1. La carpeta data/ se crea desde el código si no existe
        $carpeta = dirname($this->ruta);
        if (!is_dir($carpeta) && !mkdir($carpeta, 0755, true) && !is_dir($carpeta)) {
            throw new RuntimeException('No se pudo crear la carpeta de datos.');
        }

        // 2. Lista actual
        $jugadores = $this->todos();

        // 3. Hash de la contraseña (nunca se guarda en claro)
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // 4. Añadimos el nuevo jugador
        $jugadores[] = [
            'username' => $username,
            'email'    => $email,
            'password' => $hash,
        ];

        // 5. Guardamos la lista completa con bloqueo exclusivo
        $guardado = file_put_contents(
            $this->ruta,
            json_encode($jugadores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );

        if ($guardado === false) {
            throw new RuntimeException('No se pudo guardar el jugador.');
        }
    }

    /**
     * Busca al jugador por correo (sin distinguir mayúsculas) y comprueba la contraseña.
     * Devuelve sus datos (sin el hash) o null si algo falla.
     * No distingue entre "correo inexistente" y "contraseña incorrecta".
     *
     * @return array{username: string, email: string}|null
     */
    public function verificarCredenciales(string $email, string $password): ?array
    {
        $buscado = mb_strtolower($email);

        foreach ($this->todos() as $jugador) {
            if (mb_strtolower((string) ($jugador['email'] ?? '')) !== $buscado) {
                continue;
            }

            if (password_verify($password, (string) ($jugador['password'] ?? ''))) {
                return [
                    'username' => (string) $jugador['username'],
                    'email'    => (string) $jugador['email'],
                ];
            }

            return null;
        }

        return null;
    }
}
