<?php
session_start();

// Redirigir si no es cliente
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}

// Validar CSRF
if (!isset($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf'] ?? '')) {
    $_SESSION['booking_errors'] = ['Token de seguridad inválido.'];
    header("Location: agendar-cita.php");
    exit();
}

include '../includes/db.php';

// Limpiar datos
$servicio_id = filter_input(INPUT_POST, 'servicio_id', FILTER_VALIDATE_INT);
$especialista_id = filter_input(INPUT_POST, 'especialista_id', FILTER_VALIDATE_INT);
$fecha = trim($_POST['fecha'] ?? '');
$hora = trim($_POST['hora'] ?? '');
$total = filter_input(INPUT_POST, 'total', FILTER_VALIDATE_FLOAT);

// Validar campos
$errors = [];

if (!$servicio_id) $errors[] = 'Servicio inválido.';
if (!$especialista_id) $errors[] = 'Especialista inválido.';
if (empty($fecha) || !strtotime($fecha)) $errors[] = 'Fecha inválida.';
if (empty($hora) || !strtotime($hora)) $errors[] = 'Hora inválida.';
if ($total === false || $total <= 0) $errors[] = 'Total inválido.';

// Verificar que la fecha no sea pasada
if (strtotime($fecha) < strtotime(date('Y-m-d'))) {
    $errors[] = 'No puedes agendar en una fecha pasada.';
}

// Verificar disponibilidad del especialista
if (empty($errors)) {
    $stmt = $conn->prepare("SELECT id_cita FROM citas WHERE id_empleado = ? AND fecha = ? AND hora = ? AND estado != 'cancelada'");
    $stmt->bind_param("iss", $especialista_id, $fecha, $hora);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = 'El especialista no está disponible en esa fecha y hora.';
    }
}

// Si hay errores, volver al formulario
if (!empty($errors)) {
    $_SESSION['booking_errors'] = $errors;
    $_SESSION['booking_old'] = $_POST;
    header("Location: agendar-cita.php");
    exit();
}
// Guardar la cita
// Guardar la cita
$stmt = $conn->prepare("
    INSERT INTO citas (id_cliente, id_servicio, id_empleado, fecha, hora, total, estado)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$estado = 'confirmada';
$stmt->bind_param("iiisssd", $_SESSION['user_id'], $servicio_id, $especialista_id, $fecha, $hora, $total, $estado);

if ($stmt->execute()) {
    // Éxito: mostrar confirmación
    $cita_id = $conn->insert_id;

    // Obtener nombres para mostrar
    $stmt = $conn->prepare("SELECT nombre FROM servicios WHERE id_servicio = ?");
    $stmt->bind_param("i", $servicio_id);
    $stmt->execute();
    $servicio_nombre = $stmt->get_result()->fetch_assoc()['nombre'] ?? 'Servicio';

    $stmt = $conn->prepare("SELECT nombre FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $especialista_id);
    $stmt->execute();
    $especialista_nombre = $stmt->get_result()->fetch_assoc()['nombre'] ?? 'Especialista';

 
   
    // Limpiar sesión
    unset($_SESSION['booking_old']);
    unset($_SESSION['booking_errors']);
    unset($_SESSION['csrf']);

} else {
    $_SESSION['booking_errors'] = ['Error al guardar la cita. Inténtalo más tarde.'];
    header("Location: agendar-cita.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>Confirmación - Semis by Marie</title>

    <link rel="stylesheet" href="../css/confirmacion.css">

    <link rel="stylesheet" href="css/header-footer.css">

</head>
<body>
    
    <?php include '../includes/header-cliente.php'; ?>

<main class="confirmacion-main">
  <aside class="confirmacion-aside">
    <img src="../Imagenes/confirmado.jpg" alt="Cita confirmada">
    <div class="aside-overlay">
      <h3>¡Gracias por elegirnos!</h3>
      <p>Tu cita está agendada. Te esperamos con alegría.</p>
    </div>
  </aside>

  <section class="confirmacion-contenido">
    <h2>✅ ¡Cita Agendada!</h2>
    <p>Tu turno ha sido confirmado con éxito.</p>

    <div class="detalle-cita">
      <p><strong>Servicio:</strong> <?= htmlspecialchars($servicio_nombre) ?></p>
      <p><strong>Especialista:</strong> <?= htmlspecialchars($especialista_nombre) ?></p>
      <p><strong>Fecha:</strong> <?= htmlspecialchars($fecha) ?></p>
      <p><strong>Hora:</strong> <?= htmlspecialchars($hora) ?></p>
      <p><strong>Total:</strong> $<?= number_format($total, 2) ?></p>
    </div>

    <div class="acciones-cita">
      <a href="editar-cita.php?id=<?= $cita_id ?>" class="btn-editar">✏️ Modificar Cita</a>
      <a href="cancelar-cita.php?id=<?= $cita_id ?>" class="btn-cancelar" onclick="return confirm('¿Estás seguro de cancelar esta cita?')">❌ Cancelar Cita</a>
    </div>

    <a href="../index.php" class="btn-primary">Volver al Inicio</a>
  </section>
</main>



      
     

    <?php include '../includes/footer.php'; ?>
</body>
</html>