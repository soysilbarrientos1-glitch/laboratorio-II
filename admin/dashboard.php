<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireRole(['administrador']);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Panel Admin</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <h1>Panel de Administración</h1>
  <p>Bienvenido, <?php echo $_SESSION['nombre']; ?></p>

  <nav>
    <a href="gestion-turnos.php">Gestionar Turnos</a> |
    <a href="gestion-servicios.php">Servicios</a> |
    <a href="gestion-especialistas.php">Especialistas</a> |
    <a href="../logout.php">Cerrar Sesión</a>
  </nav>
</body>
</html>