<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Iniciar Sesión - Semipermanentes by Marie</title>
  <link rel="stylesheet" href="css/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="login-page">
  <header class="navbar">
    <div class="logo">
      <img src="Imagenes/nuevologo.jpg" alt="Logo Semis by Marie" width="60" height="60">
    </div>
    <h1>Semipermanentes by Marie</h1>
  </header>

  <div class="form-container">
    <h2>Iniciar Sesión</h2>

    <?php if (isset($_GET['error'])): ?>
      <div class="alert error">
        <?php if ($_GET['error'] === 'invalid'): ?>
          Email o contraseña incorrectos.
        <?php elseif ($_GET['error'] === 'required'): ?>
          Por favor, completa todos los campos.
        <?php endif; ?>
      </div>
    <?php endif; ?>

<!--formulario de login-->
    <form method="POST" action="procesar-login.php">

      <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" name="email" required placeholder="tu@email.com">
      </div>

      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>
      </div>
      
      <div class="form-group">
        <button type="submit" class="btn-primary">Iniciar Sesión</button>
      </div>
    </form>
    
    <p class="text-center mt-3">
      ¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>
    </p>
  </div>

  <!-- Opcional: si usas JS personalizado -->
  <!-- <script src="js/login.js"></script> -->
</body>
</html>