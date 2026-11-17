<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro</title>
</head>
<body>
    <form action="index.php?">
        <label for="usuario">Nombre de usuario:</label>
        <input type="text" id="usuario" name="jugador[usuario]" value="">

        <label for="correo">Correo electrónico:</label>
        <input type="email" id="correo" name="jugador[correo]">

        <label for="contrasena">Contrasña:</label>
        <input type="password" id="contrasena" name="jugador[contrasena]">

        <label for="confirmarContrasena">Confirmar contraseña:</label>
        <input type="password" id="confirmarContrasena">

        <input type="submit" value="Enviar">
    </form>
</body>
</html>