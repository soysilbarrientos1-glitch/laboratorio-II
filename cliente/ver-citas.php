<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}
include '../includes/db.php';

$id_cliente = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT c.id_cita, s.nombre AS servicio, u.nombre AS especialista, c.fecha, c.hora, c.total, c.estado
    FROM citas c
    JOIN servicios s ON c.id_servicio = s.id_servicio
    JOIN usuarios u ON c.id_empleado = u.id_usuario
    WHERE c.id_cliente = ?
    ORDER BY c.fecha, c.hora
");
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$citas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Citas - Semis by Marie</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <?php include '../includes/header-cliente.php'; ?>
  <main class="main-content">
    <h2>Mis Citas</h2>

    <?php if (count($citas) > 0): ?>
      <table class="tabla-citas">
        <tr>
          <th>Servicio</th><th>Especialista</th><th>Fecha</th><th>Hora</th><th>Total</th><th>Estado</th><th>Acción</th>
        </tr>
        <?php foreach ($citas as $cita): ?>
          <tr>
            <td><?= htmlspecialchars($cita['servicio']) ?></td>
            <td><?= htmlspecialchars($cita['especialista']) ?></td>
            <td><?= $cita['fecha'] ?></td>
            <td><?= $cita['hora'] ?></td>
            <td>$<?= number_format($cita['total'], 2) ?></td>
            <td><?= ucfirst($cita['estado']) ?></td>
            <td>
              <?php if ($cita['estado'] === 'confirmada'): ?>
                <a href="editar-cita.php?id=<?= $cita['id_cita'] ?>" class="btn-editar">Editar</a>
                <a href="cancelar-cita.php?id=<?= $cita['id_cita'] ?>" class="btn-cancelar">Cancelar</a>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <p>No tenés citas agendadas.</p>
    <?php endif; ?>

    <a href="agendar-cita.php" class="btn-agendar">Agendar nueva cita</a>
  </main>
  <?php include '../includes/footer.php'; ?>
</body>
</html>
