<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}

// Generar token CSRF si no existe
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(24));
}

// Obtener citas del cliente
$stmt = $conn->prepare("
    SELECT c.id_cita, s.nombre AS servicio, e.nombre AS especialista, c.fecha, c.hora, c.total, c.estado
    FROM citas c
    JOIN servicios s ON c.id_servicio = s.id_servicio
    JOIN usuarios e ON c.id_empleado = e.id_usuario
    WHERE c.id_cliente = ?
    ORDER BY c.fecha DESC, c.hora DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$citas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Citas - Semis by Marie</title>
  <link rel="stylesheet" href="../css/mis-citas.css">
</head>
<body>

<?php include '../includes/header-cliente.php'; ?>

<main class="main-content">
  <h2>Mis Citas</h2>

  <?php if (!empty($_SESSION['mensaje'])): ?>
    <div class="alert-exito"><?php echo htmlspecialchars($_SESSION['mensaje']); ?></div>
    <?php unset($_SESSION['mensaje']); ?>
  <?php endif; ?>

  <?php if (empty($citas)): ?>
    <p>No tenés citas agendadas aún.</p>
  <?php else: ?>
    <table class="tabla-citas">
      <thead>
        <tr>
          <th>Servicio</th>
          <th>Especialista</th>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Total</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($citas as $cita): ?>
          <tr>
            <td><?php echo htmlspecialchars($cita['servicio']); ?></td>
            <td><?php echo htmlspecialchars($cita['especialista']); ?></td>
            <td><?php echo date('d/m/Y', strtotime($cita['fecha'])); ?></td>
            <td><?php echo date('H:i', strtotime($cita['hora'])); ?></td>
            <td>$<?php echo number_format($cita['total'], 2); ?></td>
            <td><?php echo ucfirst($cita['estado']); ?></td>
            <td>
              <?php if ($cita['estado'] === 'pendiente'): ?>
                <form action="modificar-cita.php" method="POST" style="display:inline;">
                  <input type="hidden" name="cita_id" value="<?php echo $cita['id_cita']; ?>">
                  <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
                  <button type="submit" class="btn-modificar">Modificar</button>
                </form>

                <form action="cancelar-cita.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que querés cancelar esta cita?');">
                  <input type="hidden" name="cita_id" value="<?php echo $cita['id_cita']; ?>">
                  <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
                  <button type="submit" class="btn-cancelar">Cancelar</button>
                </form>
              <?php else: ?>
                <em>Sin acciones</em>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</main>

</body>
</html>
