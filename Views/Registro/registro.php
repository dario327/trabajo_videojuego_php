<?php
/**
 * Vista del registro.
 * Recibe: $errores (array por campo) y $datos (usuario y correo anteriores).
 * Las contraseñas nunca se rellenan.
 */
?>
<h1>Registro</h1>

<?php if ($errores !== []): ?>
    <ul class="errores">
        <?php foreach ($errores as $mensaje): ?>
            <li><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" action="index.php?accion=registro" novalidate>
    <label for="usuario">Nombre de usuario</label>
    <input type="text" id="usuario" name="usuario"
           value="<?= htmlspecialchars($datos['usuario'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <label for="email">Correo electrónico</label>
    <input type="email" id="email" name="email"
           value="<?= htmlspecialchars($datos['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <label for="password">Contraseña</label>
    <input type="password" id="password" name="password">

    <label for="confirmar">Confirmar contraseña</label>
    <input type="password" id="confirmar" name="confirmar">

    <button type="submit">Registrarse</button>
</form>

<p class="enlace">¿Ya tienes cuenta? <a href="index.php?accion=login">Inicia sesión</a></p>
