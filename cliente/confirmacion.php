<?php
session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'cliente') {
  header("Location: ../login.php");
  exit();
}
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: servicios.php");
  exit();
}

// Validación básica de entrada
$cliente_id = (int)$_SESSION['user_id'];
$servicio_id = isset($_POST['servicio_id']) ? (int)$_POST['servicio_id'] : 0;
$especialista_id = isset($_POST['especialista_id']) ? (int)$_POST['especialista_id'] : 0;
$fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
$hora = isset($_POST['hora']) ? trim($_POST['hora']) : '';
$total = isset($_POST['total']) ? str_replace([',', '$'], ['', ''], $_POST['total']) : '0';

// Validaciones mínimas
$errors = [];
if ($servicio_id <= 0) $errors[] = 'Servicio inválido.';
if ($especialista_id <= 0) $errors[] = 'Especialista inválido.';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) $errors[] = 'Formato de fecha inválido.';
if (!preg_match('/^\d{2}:\d{2}$/', $hora)) $errors[] = 'Formato de hora inválido.';
if (!is_numeric($total)) $errors[] = 'Total inválido.';

if (!empty($errors)) {
  // Guardar errores y valores enviados en sesión y redirigir al formulario
  $_SESSION['booking_errors'] = $errors;
  $_SESSION['booking_old'] = [
    'servicio_id' => $servicio_id,
    'especialista_id' => $especialista_id,
    'fecha' => $fecha,
    'hora' => $hora,
    'total' => $total,
  ];
  header('Location: agendar-cita.php');
  exit();
}

$total = (float)$total;

// CSRF validation
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
  $_SESSION['booking_errors'] = ['Token de seguridad inválido. Intenta de nuevo.'];
  $_SESSION['booking_old'] = [
    'servicio_id' => $servicio_id,
    'especialista_id' => $especialista_id,
    'fecha' => $fecha,
    'hora' => $hora,
    'total' => $total,
  ];
  header('Location: agendar-cita.php');
  exit();
}

// Verificar precio real del servicio (evitar manipulación del cliente)
$precioStmt = $conn->prepare("SELECT precio FROM servicios WHERE id_servicio = ? LIMIT 1");
$precioStmt->bind_param('i', $servicio_id);
$precioStmt->execute();
$resPrecio = $precioStmt->get_result();
if (!$resPrecio || $resPrecio->num_rows === 0) {
  $_SESSION['booking_errors'] = ['Servicio no encontrado.'];
  $_SESSION['booking_old'] = [
    'servicio_id' => $servicio_id,
    'especialista_id' => $especialista_id,
    'fecha' => $fecha,
    'hora' => $hora,
    'total' => $total,
  ];
  header('Location: agendar-cita.php');
  exit();
}
$precio_real = (float)$resPrecio->fetch_assoc()['precio'];
if (abs($precio_real - $total) > 0.01) {
  $_SESSION['booking_errors'] = ['El total enviado no coincide con el precio del servicio.'];
  $_SESSION['booking_old'] = [
    'servicio_id' => $servicio_id,
    'especialista_id' => $especialista_id,
    'fecha' => $fecha,
    'hora' => $hora,
    'total' => $total,
  ];
  header('Location: agendar-cita.php');
  exit();
}

// Comprobar disponibilidad del especialista en la misma fecha/hora
$check = $conn->prepare("SELECT COUNT(*) AS cnt FROM citas WHERE id_empleado = ? AND fecha = ? AND hora = ? AND estado IN ('confirmado','pendiente')");
$check->bind_param('iss', $especialista_id, $fecha, $hora);
$check->execute();
$cnt = (int)$check->get_result()->fetch_assoc()['cnt'];
if ($cnt > 0) {
  $_SESSION['booking_errors'] = ['El especialista ya tiene una cita en esa fecha y hora. Por favor elegí otro horario.'];
  $_SESSION['booking_old'] = [
    'servicio_id' => $servicio_id,
    'especialista_id' => $especialista_id,
    'fecha' => $fecha,
    'hora' => $hora,
    'total' => $total,
  ];
  header('Location: agendar-cita.php');
  exit();
}

// Usar transacción para insertar cita y notificación
try {
  $conn->begin_transaction();

  $stmt = $conn->prepare("INSERT INTO citas (id_cliente, id_servicio, id_empleado, fecha, hora, estado, total) VALUES (?, ?, ?, ?, ?, ?, ?)");
  if (!$stmt) throw new Exception('Error preparando consulta: ' . $conn->error);
  $estado = 'confirmado';
  // tipos: i i i s s s d -> usar 'iiisssd' (d para double)
  $stmt->bind_param('iiisssd', $cliente_id, $servicio_id, $especialista_id, $fecha, $hora, $estado, $total);
  if (!$stmt->execute()) throw new Exception('Error insertando cita: ' . $stmt->error);
  $cita_id = $stmt->insert_id;

  // Notificación al especialista
  $mensaje = 'Nueva cita asignada: ' . ($_SESSION['nombre'] ?? 'Cliente') . ' el ' . $fecha . ' a las ' . $hora;
  // Atención: la columna en la tabla se llama `id_usuario` (no 'usuario_id') — usar el nombre correcto
  $stmtNotif = $conn->prepare("INSERT INTO notificaciones (id_usuario, mensaje) VALUES (?, ?)");
  if (!$stmtNotif) throw new Exception('Error preparando notificación: ' . $conn->error);
  $stmtNotif->bind_param('is', $especialista_id, $mensaje);
  if (!$stmtNotif->execute()) throw new Exception('Error insertando notificación: ' . $stmtNotif->error);

  // Obtener nombres para mostrar (consultas seguras)
  $stmtS = $conn->prepare("SELECT nombre FROM servicios WHERE id_servicio = ? LIMIT 1");
  $stmtS->bind_param('i', $servicio_id);
  $stmtS->execute();
  $resS = $stmtS->get_result();
  $servicio_nombre = $resS->num_rows ? $resS->fetch_assoc()['nombre'] : 'Servicio';

  $stmtE = $conn->prepare("SELECT nombre FROM usuarios WHERE id_usuario = ? LIMIT 1");
  $stmtE->bind_param('i', $especialista_id);
  $stmtE->execute();
  $resE = $stmtE->get_result();
  $especialista_nombre = $resE->num_rows ? $resE->fetch_assoc()['nombre'] : 'Especialista';

  // Nota: tus tablas muestran motor MyISAM (no soporta transacciones). Si quieres que commit/rollback funcionen
  // convierte las tablas a InnoDB: ALTER TABLE citas ENGINE=InnoDB; ALTER TABLE notificaciones ENGINE=InnoDB; etc.
  $conn->commit();
  // Hacer el token CSRF de un solo uso: invalidarlo tras éxito
  if (isset($_SESSION['csrf'])) {
    unset($_SESSION['csrf']);
  }
} catch (Exception $e) {
  $conn->rollback();
  // Registrar error en log y redirigir con mensaje genérico
  error_log('Error al confirmar la cita: ' . $e->getMessage());
  $_SESSION['booking_errors'] = ['No se pudo confirmar la cita. Intenta de nuevo más tarde.'];
  // Mantener valores ingresados para que el usuario no tenga que reescribirlos
  $_SESSION['booking_old'] = [
    'servicio_id' => $servicio_id,
    'especialista_id' => $especialista_id,
    'fecha' => $fecha,
    'hora' => $hora,
    'total' => $total,
  ];
  header('Location: agendar-cita.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cita Confirmada - Semis by Marie</title>
  <link rel="stylesheet" href="../css/confirmacion.css">
</head>
<body>

<?php include '../includes/header-cliente.php'; ?>

<main class="main-content">
  <div class="confirmacion-box">
    <h1>✅ ¡Cita Confirmada!</h1>
    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
    <p><strong>Servicio:</strong> <?php echo htmlspecialchars($servicio_nombre); ?></p>
    <p><strong>Especialista:</strong> <?php echo htmlspecialchars($especialista_nombre); ?></p>
    <p><strong>Fecha y Hora:</strong> <?php echo $fecha . ' ' . $hora; ?></p>
    <p><strong>Total pagado:</strong> $<?php echo number_format($total, 2); ?></p>
    <a href="servicios.php" class="btn-volver">Volver a Servicios</a>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>