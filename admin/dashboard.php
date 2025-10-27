<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireRole(['administrador']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administración - Semipermanentes by Marie</title>
  <link rel="stylesheet" href="../css/style.css">
  <a href="gestion-turnos.php">📅 Gestionar Turnos</a>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: #f5f5f5;
    }
    .container {
      max-width: 1000px;
      margin: 2rem auto;
      padding: 0 1rem;
    }
    h1, h2 {
      color: #e91e63;
    }
    .menu {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      margin: 1.5rem 0;
    }
    .menu a {
      display: block;
      padding: 0.8rem 1.2rem;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 6px;
      text-decoration: none;
      color: #333;
      font-weight: bold;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: background 0.2s;
    }
    .menu a:hover {
      background: #e91e63;
      color: white;
    }
    .welcome {
      background: white;
      padding: 1.5rem;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      margin-bottom: 1.5rem;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="welcome">
    <h1>Panel de Administración</h1>
    <p>Bienvenida, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong>!</p>
    <p>Rol: <?php echo htmlspecialchars($_SESSION['rol']); ?></p>
  </div>

  <div class="menu">
    <a href="gestion-turnos.php">📅 Gestionar Turnos</a>
    <a href="gestion-servicios.php">💅 Servicios</a>
    <a href="gestion-usuarios.php">👥 Usuarios</a>
    <a href="../logout.php">🚪 Cerrar Sesión</a>
  </div>

  <h2>Resumen Rápido</h2>
  <?php
  // Contar usuarios por rol
  $roles = [
      1 => 'Clientes',
      2 => 'Manicuristas',
      4 => 'Pedicuristas',
      5 => 'Secretarias',
      3 => 'Administradores'
  ];

  foreach ($roles as $id_rol => $nombre) {
      $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM usuarios WHERE id_rol = ?");
      $stmt->bind_param("i", $id_rol);
      $stmt->execute();
      $total = $stmt->get_result()->fetch_assoc()['total'];
      echo "<p><strong>{$nombre}:</strong> {$total}</p>";
  }

  // Contar citas hoy
  $hoy = date('Y-m-d');
  $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE fecha = ? AND estado = 'confirmada'");
  $stmt->bind_param("s", $hoy);
  $stmt->execute();
  $turnos_hoy = $stmt->get_result()->fetch_assoc()['total'];
  echo "<p><strong>Turnos confirmados hoy:</strong> {$turnos_hoy}</p>";
  ?>
</div>

</body>
</html>