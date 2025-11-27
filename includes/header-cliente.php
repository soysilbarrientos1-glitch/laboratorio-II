<?php
$nombre = $_SESSION['nombre'] ?? 'Usuario';
$rol = $_SESSION['rol'] ?? '';
?>

<header class="navbar-cliente">
  <div class="logo">
    <img src="../Imagenes/nuevologo.jpg" alt="Logo Semis by Marie" width="60" height="60">
  </div>

  <div class="titulo">
    <h1>Semipermanentes by Marie</h1>
    <p class="slogan">Esmaltado que dura. Belleza que brilla.</p>
  </div>

  <nav class="nav-cliente">
    <a href="dashboard.php">Inicio</a>
    <a href="gestion-servicios.php">Servicios</a>
    <a href="gestion-usuarios.php">Usuarios</a>
    <a href="gestion-turnos.php">Turnos</a>
    <a href="panel-secretaria.php">Panel de Secretaria</a>
    <a href="ver-logs.php">Historial de Logs</a>
    <span class="usuario">Hola, <?= htmlspecialchars($nombre) ?></span>
    <a href="../logout.php">Cerrar Sesión</a>
  </nav>
  
</header>
