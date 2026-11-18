<h1>Juegos subidos</h1>

<?php if (empty($juegos)): ?>
    <p>Todavía no hay juegos subidos</p>
<?php else: ?>
    <?php foreach ($juegos as $juego): ?>
        <article class="juego">
            <p>Título: <?= htmlspecialchars((string)($juego['titulo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
            <p>Autor: <?= htmlspecialchars((string)($juego['autor'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
            <p>Categoría: <?= htmlspecialchars((string)($juego['categoria'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
            <p>Descripción: <?= htmlspecialchars((string)($juego['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
            <p><a href="<?= htmlspecialchars((string)($juego['archivo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">Descargar ZIP</a></p>
        </article>
    <?php endforeach; ?>
<?php endif; ?>
