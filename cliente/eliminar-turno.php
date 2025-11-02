<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}

// Validar CSRF
if (empty($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
    $_SESSION['mensaje'] = "Acción no permitida (CSRF inválido).";
    header("Location: mis-turnos.php");
    exit();
}

// Validar turno_id
$id_turno = $_POST['turno_id'] ?? null;
if (!$id_turno) {
    $_SESSION['mensaje'] = "No se recibió el turno a cancelar.";
    header("Location: mis-turnos.php");
    exit();
}

// Verificar que el turno pertenece al cliente y está pendiente
$stmt = $conn->prepare("SELECT estado FROM cita WHERE id_cita = ? AND cliente_id = ?");
$stmt->bind_param("ii", $id_turno, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$turno = $result->fetch_assoc();

if (!$turno) {
    $_SESSION['mensaje'] = "Turno no encontrado o no pertenece a tu cuenta.";
    header("Location: mis-turnos.php");
    exit();
}

if ($turno['estado'] !== 'pendiente') {
    $_SESSION['mensaje'] = "Solo se pueden cancelar turnos pendientes.";
    header("Location: mis-turnos.php");
    exit();
}

// Cancelar el turno
$stmt = $conn->prepare("UPDATE cita SET estado = 'cancelado' WHERE id = ? AND id_alumno = ?");
$stmt->bind_param("ii", $id_cita, $_SESSION['user_id']);
$stmt->execute();

// Confirmación
$_SESSION['mensaje'] = "Tu turno fue cancelado con éxito.";
header("Location: mis-turnos.php");
exit();
?>
