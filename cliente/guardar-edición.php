<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}

$id_cliente = $_SESSION['user_id'];
$id_cita = $_POST['id_cita'] ?? null;
$servicio_id = $_POST['servicio_id'] ?? null;
$especialista_id = $_POST['especialista_id'] ?? null;
$fecha = $_POST['fecha'] ?? '';
$hora = $_POST['hora'] ?? '';
$total = $_POST['total'] ?? 0;

// Validar que la cita pertenezca al cliente y esté confirmada
$stmt = $conn->prepare("SELECT id_cita FROM citas WHERE id_cita = ? AND id_cliente = ? AND estado = 'confirmada'");
$stmt->bind_param("ii", $id_cita, $id_cliente);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    echo "<p>No se puede modificar esta cita.</p>";
    exit();
}

// Verificar disponibilidad
$stmt = $conn->prepare("SELECT id_cita FROM citas WHERE id_empleado = ? AND fecha = ? AND hora = ? AND estado != 'cancelada' AND id_cita != ?");
$stmt->bind_param("issi", $especialista_id, $fecha, $hora, $id_cita);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo "<p>El especialista no está disponible en ese horario.</p>";
    exit();
}

// Actualizar cita
$stmt = $conn->prepare("UPDATE citas SET id_servicio = ?, id_empleado = ?, fecha = ?, hora = ?, total = ? WHERE id_cita = ?");
$stmt->bind_param("iissdi", $servicio_id, $especialista_id, $fecha, $hora, $total, $id_cita);
$stmt->execute();

header("Location: ver-citas.php");
exit();
