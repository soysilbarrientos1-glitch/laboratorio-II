<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}

$id_cliente = $_SESSION['user_id'];
$id_cita = $_GET['id'] ?? null;

if (!$id_cita || !is_numeric($id_cita)) {
    echo "<p>ID de cita inválido.</p>";
    exit();
}

// Validar que la cita sea futura y pertenezca al cliente
$stmt = $conn->prepare("
    SELECT * FROM citas 
    WHERE id_cita = ? 
      AND id_cliente = ? 
      AND CONCAT(fecha, ' ', hora) > NOW()
");
$stmt->bind_param("ii", $id_cita, $id_cliente);
$stmt->execute();
$cita = $stmt->get_result()->fetch_assoc();

if (!$cita) {
    echo "<p>No se puede editar esta cita. Puede que ya haya pasado o no te pertenezca.</p>";
    exit();
}

// Obtener servicios y especialistas
$servicios = $conn->query("SELECT id_servicio, nombre, precio FROM servicios ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$especialistas = $conn->query("SELECT id_usuario, nombre FROM usuarios WHERE id_rol IN (2,4)")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Cita</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/header-cliente.php'; ?>

<main class="main-content">
  <h2>Editar Cita</h2>
  <form method="POST" action="guardar-edicion.php">
    <input type="hidden" name="id_cita" value="<?= $id_cita ?>">

    <label>Servicio:</label>
    <select name="servicio_id" required onchange="actualizarTotal(this)">
      <?php foreach ($servicios as $s): ?>
        <option value="<?= $s['id_servicio'] ?>" data-precio="<?= $s['precio'] ?>"
          <?= $s['id_servicio'] == $cita['id_servicio'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($s['nombre']) ?> - $<?= number_format($s['precio'], 2) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Especialista:</label>
    <select name="especialista_id" required>
      <?php foreach ($especialistas as $e): ?>
        <option value="<?= $e['id_usuario'] ?>" <?= $e['id_usuario'] == $cita['id_empleado'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($e['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Fecha:</label>
    <input type="date" name="fecha" value="<?= $cita['fecha'] ?>" min="<?= date('Y-m-d') ?>" required>

    <label>Hora:</label>
    <input type="time" name="hora" value="<?= $cita['hora'] ?>" required>

    <p>Total: <strong id="totalDisplay">$<?= number_format($cita['total'], 2) ?></strong></p>
    <input type="hidden" name="total" id="totalInput" value="<?= $cita['total'] ?>">

    <button type="submit" class="btn-primary">Guardar cambios</button>
  </form>
</main>

<script>
function actualizarTotal(select) {
  const precio = select.options[select.selectedIndex].getAttribute('data-precio');
  document.getElementById('totalDisplay').textContent = '$' + parseFloat(precio).toFixed(2);
  document.getElementById('totalInput').value = precio;
}
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
