<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}
include '../includes/db.php';

// CSRF token para el formulario de reserva
if (empty($_SESSION['csrf'])) {
  // usar random_bytes para mayor seguridad
  $_SESSION['csrf'] = bin2hex(random_bytes(24));
}

// Obtener servicios
$stmt = $conn->prepare("SELECT id_servicio, nombre, precio FROM servicios ORDER BY nombre");
$stmt->execute();
$servicios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Obtener especialistas (manicuristas y pedicuristas)
$stmt = $conn->prepare("SELECT id_usuario, nombre FROM usuarios WHERE id_rol IN (3, 4)");
$stmt->execute();
$especialistas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agendar Cita - Semis by Marie</title>
  <link rel="stylesheet" href="../css/agendar.css">
</head>
<body>

<?php include '../includes/header-cliente.php'; ?>

<main class="main-content">
  <h2>Agendar Cita</h2>

  <?php if (!empty($_SESSION['booking_errors'])): ?>
    <div class="errors">
      <ul>
        <?php foreach ($_SESSION['booking_errors'] as $err): ?>
          <li><?php echo htmlspecialchars($err); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php unset($_SESSION['booking_errors']); endif; ?>

  <?php
    $old = $_SESSION['booking_old'] ?? null;
    if ($old) {
      // Values might be strings or ints; keep as-is for repopulation
    }
  ?>

  <form id="bookingForm" action="confirmacion.php" method="POST">
    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf']); ?>">
    <!-- Servicio -->
    <label for="servicio">Servicio:</label>
    <select name="servicio_id" id="servicio" required>
      <option value="">-- Selecciona un servicio --</option>
      <?php foreach ($servicios as $s): ?>
        <option value="<?php echo (int)$s['id_servicio']; ?>" data-precio="<?php echo $s['precio']; ?>"
          <?php if (!empty($old) && (int)$old['servicio_id'] === (int)$s['id_servicio']) echo 'selected'; ?>>
          <?php echo htmlspecialchars($s['nombre']); ?> - $<?php echo number_format($s['precio'], 2); ?>
        </option>
      <?php endforeach; ?>
    </select>

    <!-- Especialista -->
    <label for="especialista">Especialista:</label>
    <select name="especialista_id" id="especialista" required>
      <option value="">-- Selecciona un especialista --</option>
      <?php foreach ($especialistas as $e): ?>
        <option value="<?php echo (int)$e['id_usuario']; ?>" <?php if (!empty($old) && (int)$old['especialista_id'] === (int)$e['id_usuario']) echo 'selected'; ?>>
          <?php echo htmlspecialchars($e['nombre']); ?>
        </option>
      <?php endforeach; ?>
    </select>

    <!-- Fecha -->
    <label for="fecha">Fecha:</label>
  <input type="date" name="fecha" id="fecha" min="<?php echo date('Y-m-d'); ?>" value="<?php echo !empty($old['fecha']) ? htmlspecialchars($old['fecha']) : ''; ?>" required>

    <!-- Hora -->
    <label for="hora">Hora:</label>
    <select name="hora" id="hora" required>
      <option value="">-- Selecciona hora --</option>
      <?php
        $horas = ['10:00'=>'10:00 AM','11:00'=>'11:00 AM','12:00'=>'12:00 PM','14:00'=>'2:00 PM','15:00'=>'3:00 PM','16:00'=>'4:00 PM','17:00'=>'5:00 PM'];
        foreach ($horas as $val => $label) {
          $sel = (!empty($old) && $old['hora'] === $val) ? 'selected' : '';
          echo "<option value=\"$val\" $sel>$label</option>";
        }
      ?>
    </select>

  <div id="availabilityMessage" style="margin-top:10px; display:none;" aria-live="polite"></div>

    <!-- Total -->
  <p class="total">Total: <strong id="total"><?php echo !empty($old['total']) ? ('$' . number_format((float)$old['total'], 2)) : '$0.00'; ?></strong></p>
  <input type="hidden" name="total" id="totalInput" value="<?php echo !empty($old['total']) ? htmlspecialchars($old['total']) : '0'; ?>">

    <button type="submit" class="btn-confirmar">Confirmar y Pagar</button>
  </form>
</main>

<?php include '../includes/footer.php'; ?>
<script src="../JavaScrip/booking.js"></script>
</body>
</html>

<?php if (!empty($_SESSION['booking_old'])) unset($_SESSION['booking_old']); ?>