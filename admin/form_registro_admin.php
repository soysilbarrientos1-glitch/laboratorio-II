<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar administrador</title>
</head>
<body>
  <h2>Formulario de registro de administrador</h2>
  <form action="registrar_admin.php" method="POST">
    <input type="text" name="nombre" placeholder="Nombre completo" required><br>
    <input type="email" name="email" placeholder="Correo electrónico" required><br>
    <input type="password" name="clave" placeholder="Contraseña" required><br>
    <button type="submit">Registrar administrador</button>
  </form>
</body>
</html>
