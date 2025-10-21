<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
</head>
<body>
  <h2>Registro de Cliente</h2>

  <?php if (isset($_GET['error'])): ?>
    <p style="color:red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
  <?php endif; ?>

  <form method="POST" action="procesar-register.php">
    <input type="text" name="nombre" placeholder="Nombre completo" required><br><br>
    <input type="email" name="email" placeholder="Correo electrónico" required><br><br>
    <input type="tel" name="telefono" placeholder="Teléfono"><br><br>
    <input type="password" name="password" placeholder="Contraseña" required><br><br>
    <button type="submit">Registrarse</button>
  </form>

  <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</body>
</html>