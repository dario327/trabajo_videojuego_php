<h1>Plataforma de minijuegos</h1>

<div class="contadores">
    <p><strong><?= (int) $totalJuegos ?></strong> juegos</p>
    <p><strong><?= (int) $totalJugadores ?></strong> jugadores</p>
</div>

<?php if ($usuario !== null): ?>
    <p>Bienvenido, <?= htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') ?></p>
    <ul>
        <li><a href="index.php?accion=subir">Subir un nuevo juego</a></li>
        <li><a href="index.php?accion=juegos">Ver juegos subidos</a></li>
        <li><a href="index.php?accion=logout">Cerrar sesión</a></li>
    </ul>
<?php else: ?>
    <ul>
        <li><a href="index.php?accion=registro">Registrarse</a></li>
        <li><a href="index.php?accion=login">Iniciar sesión</a></li>
    </ul>
<?php endif; ?>
