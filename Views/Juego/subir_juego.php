<h1>Subir un nuevo juego</h1>

<?php if (!empty($errores)): ?>
    <ul class="errores">
        <?php foreach ($errores as $mensaje): ?>
            <li><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" action="index.php?accion=subir" enctype="multipart/form-data">
    <label for="titulo">Título del juego</label>
    <input type="text" id="titulo" name="titulo"
           value="<?= htmlspecialchars($datos['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <label for="descripcion">Descripción</label>
    <textarea id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($datos['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

    <label for="categoria">Categoría</label>
    <select id="categoria" name="categoria">
        <option value="">-- Elige una categoría --</option>
        <?php foreach ($categorias as $categoria): ?>
            <option value="<?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?>"
                <?= (($datos['categoria'] ?? '') === $categoria) ? 'selected' : '' ?>>
                <?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="archivo">Archivo ZIP (máx. 10 MB)</label>
    <input type="file" id="archivo" name="archivo" accept=".zip">

    <button type="submit">Subir juego</button>
</form>
