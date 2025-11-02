<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}

// Validar cita_id recibido por POST
$id_cita = $_POST['cita_id'] ?? null;
if (!$id_cita) {
    $_SESSION['mensaje'] = "No se recibió la cita a modificar.";
    header("Location: mis-citas.php");
    exit();
}

// Obtener datos de la cita
$stmt = $conn->prepare("
    SELECT id_cita, id_servicio, id_empleado, fecha, hora, total, estado
    FROM citas
    WHERE id_cita = ? AND id_cliente = ?
");
$stmt->bind_param("ii", $id_cita, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$cita = $result->fetch_assoc();

if (!$cita || $cita['estado'] !== 'pendiente') {
    $_SESSION['mensaje'] = "No se puede modificar esta cita.";
    header("Location: mis-citas.php");
    exit();
}

// Obtener servicios disponibles
$servicios = $conn->query("SELECT id_servicio, nombre FROM servicios")->fetch_all(MYSQLI_ASSOC);

// Obtener especialistas disponibles
$especialistas = $conn->query("SELECT id_usuario, nombre FROM usuarios WHERE rol = 'especialista'")->fetch_all(MYSQLI_ASSOC);

// Generar token CSRF si no existe
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(24));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Modificar Cita - Semis by Marie</title>
  <link rel="stylesheet" href="../css/modificar-cita.css">
</head>
<body>

<?php include '../includes/header-cliente.php'; ?>

<main class="main-content">
  <h2>Modificar Cita</h2>

  <form action="editar-cita.php" method="POST" class="form-cita">
    <input type="hidden" name="cita_id" value="<?php echo $cita['id_cita']; ?>">
    <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">

    <label for="servicio_id">Servicio:</label>
    <select name="servicio_id" required>
      <?php foreach ($servicios as $s): ?>
        <option value="<?php echo $s['id_servicio']; ?>" <?php if ($s['id_servicio'] == $cita['id_servicio']) echo 'selected'; ?>>
          <?php echo htmlspecialchars($s['nombre']); ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label for="especialista_id">Especialista:</label>
    <select name="especialista_id" required>
      <?php foreach ($especialistas as $e): ?>
        <option value="<?php echo $e['id_usuario']; ?>" <?php if ($e['id_usuario'] == $cita['id_empleado']) echo 'selected'; ?>>
          <?php echo htmlspecialchars($e['nombre']); ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label for="fecha">Fecha:</label>
    <input type="date" name="fecha" value="<?php echo $cita['fecha']; ?>" required>

    <label for="hora">Hora:</label>
    <input type="time" name="hora" value="<?php echo $cita['hora']; ?>" required>

    <label for="total">Total:</label>
    <input type="number" name="total" step="0.01" value="<?php echo $cita['total']; ?>" required>

    <button type="submit" class="btn-confirmar">Guardar Cambios</button>
    <a href="mis-citas.php" class="btn-cancelar">Cancelar</a>
  </form>
</main>

</body>
</html>
