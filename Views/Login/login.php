<?php
/**
 * Vista del inicio de sesión.
 * Recibe: $errores, $datos (correo anterior) y $registrado (true si venimos del registro).
 */
?>
<h1>Iniciar sesión</h1>

<?php if ($registrado): ?>
    <p class="exito">Registro correcto, ya puedes iniciar sesión</p>
<?php endif; ?>

<?php if ($errores !== []): ?>
    <ul class="errores">
        <?php foreach ($errores as $mensaje): ?>
            <li><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" action="index.php?accion=login" novalidate>
    <label for="email">Correo electrónico</label>
    <input type="email" id="email" name="email"
           value="<?= htmlspecialchars($datos['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <label for="password">Contraseña</label>
    <input type="password" id="password" name="password">

    <button type="submit">Entrar</button>
</form>

<p class="enlace">¿No tienes cuenta? <a href="index.php?accion=registro">Regístrate</a></p>