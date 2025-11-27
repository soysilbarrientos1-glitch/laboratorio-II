<?php
session_start();
require_once '../includes/db.php';

// Validar sesión y rol
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}

$id_cliente = $_SESSION['user_id'];

// Capturar datos del formulario
$id_cita         = $_POST['id_cita'] ?? null;
$servicio_id     = $_POST['servicio_id'] ?? null;
$especialista_id = $_POST['especialista_id'] ?? null;
$fecha           = $_POST['fecha'] ?? '';
$hora            = $_POST['hora'] ?? '';
$total           = $_POST['total'] ?? 0;

// Validar datos recibidos
if (!$id_cita || !$servicio_id || !$especialista_id || !$fecha || !$hora || !$total) {
    echo "<p>Faltan datos para modificar la cita.</p>";
    $conn->close();
    exit();
}

// Validar que la cita pertenezca al cliente y sea futura
$stmt = $conn->prepare("
    SELECT id_cita FROM citas 
    WHERE id_cita = ? 
      AND id_cliente = ? 
      AND CONCAT(fecha, ' ', hora) > NOW()
");
$stmt->bind_param("ii", $id_cita, $id_cliente);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No se puede modificar esta cita. Puede que ya haya pasado o no te pertenezca.</p>";
    $conn->close();
    exit();
}

// Verificar disponibilidad del especialista en ese horario
$stmt = $conn->prepare("
    SELECT id_cita FROM citas 
    WHERE id_empleado = ? 
      AND fecha = ? 
      AND hora = ? 
      AND estado != 'cancelada' 
      AND id_cita != ?
");
$stmt->bind_param("issi", $especialista_id, $fecha, $hora, $id_cita);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<p>El especialista no está disponible en ese horario.</p>";
    $conn->close();
    exit();
}

// Actualizar la cita
$stmt = $conn->prepare("
    UPDATE citas 
    SET id_servicio = ?, id_empleado = ?, fecha = ?, hora = ?, total = ? 
    WHERE id_cita = ?
");
$stmt->bind_param("iissdi", $servicio_id, $especialista_id, $fecha, $hora, $total, $id_cita);
$stmt->execute();

// Cerrar conexión y redirigir
$conn->close();
header("Location: ver-citas.php");
exit();
