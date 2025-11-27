<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/stats.php';

requireRole(['administrador']);
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administración</title>
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

<!-- header.php -->
<header class="encabezado">
  <div class="logo">
    <img src="Imagenes/nuevologo.jpg" alt="Logo Semis by Marie" />
  </div>
  <div class="titulo">
    <h1>Semipermanentes by Marie</h1>
    <p>"Enamórate de un brillo que brille"</p>
  </div>
</header>


<nav>
  <a href="dashboard.php">Inicio</a>
  <a href="gestion-servicios.php">Servicios</a>
  <a href="gestion-usuarios.php">Usuarios</a>
  <a href="gestion-turnos.php">Turnos</a>
  <a href="ver-logs.php">Historial de Logs</a>
  <a href="panel-secretaria.php">Panel de Secretaria</a>
  <a href="../logout.php">Cerrar Sesión</a>
</nav>

<div class="welcome">
  <p>Bienvenida, <strong><?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?></strong>!</p>
  <p>Rol: <?= htmlspecialchars($_SESSION['rol'] ?? 'Desconocido') ?></p>
</div>

<h2 style="text-align:center;">Resumen Rápido</h2>
<div class="resumen">
  <?php
  $roles = [
      1 => 'Clientes',
      2 => 'Manicuristas',
      4 => 'Pedicuristas',
      5 => 'Secretarias',
      3 => 'Administradores'
  ];

  foreach ($roles as $id_rol => $nombre) {
      $total = contarUsuariosPorRol($conn, $id_rol);
      echo "<div class='card'><h3>{$total}</h3><p>{$nombre}</p></div>";
  }

  $turnos_hoy = contarTurnosHoy($conn);
  echo "<div class='card'><h3>{$turnos_hoy}</h3><p>Turnos confirmados hoy</p></div>";
  ?>
</div>

<footer>
  <p>Todos los derechos reservados</p>
  <p>&copy; Marie Semipermanentes 2015</p>
</footer>

</body>
</html>
