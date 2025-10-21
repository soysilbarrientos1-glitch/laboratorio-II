<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireRole(['manicurista','pedicurista']);

// Marcar notificaciones como leídas
$pdo->prepare("UPDATE notificaciones SET leido = 1 WHERE usuario_id = ?")->execute([$_SESSION['user_id']]);

// Cargar turnos asignados
$turnos = $pdo->prepare("
    SELECT t.*, u.nombre AS cliente, s.nombre AS servicio
    FROM turnos t
    JOIN usuarios u ON t.cliente_id = u.id
    JOIN servicios s ON t.servicio_id = s.id
    WHERE t.especialista_id = ?
    ORDER BY t.fecha, t.hora
");
$turnos->execute([$_SESSION['user_id']]);
$lista = $turnos->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Mis Turnos</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <h2>Hola, <?php echo $_SESSION['nombre']; ?></h2>
  <h3>Mis próximas citas</h3>
  <table border="1">
    <tr><th>Cliente</th><th>Servicio</th><th>Fecha</th><th>Hora</th><th>Estado</th></tr>
    <?php foreach ($lista as $t): ?>
      <tr>
        <td><?php echo $t['cliente']; ?></td>
        <td><?php echo $t['servicio']; ?></td>
        <td><?php echo $t['fecha']; ?></td>
        <td><?php echo $t['hora']; ?></td>
        <td><?php echo $t['estado']; ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <a href="../logout.php">Cerrar Sesión</a>
</body>
</html>