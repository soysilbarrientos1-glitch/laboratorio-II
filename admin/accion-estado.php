<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireRole(['secretaria', 'admin']); // Solo secretaria o admin pueden cambiar estados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cita = $_POST['id_cita'] ?? null;
    $nuevo_estado = $_POST['nuevo_estado'] ?? null;
    $usuario = $_SESSION['nombre'] ?? 'Desconocido';

    // Validar entrada
    if (!$id_cita || !$nuevo_estado) {
        header('Location: panel-secretaria.php?error=datos_invalidos');
        exit;
    }

    // Actualizar estado en la tabla citas
    $stmt = $conn->prepare("UPDATE citas SET estado = ? WHERE id_cita = ?");
    $stmt->bind_param("si", $nuevo_estado, $id_cita);
    $stmt->execute();

    // Registrar log en logs_turnos
    $accion = "Estado cambiado a '$nuevo_estado'";
    $stmt_log = $conn->prepare("INSERT INTO logs_turnos (id_turno, usuario, accion, fecha) VALUES (?, ?, ?, NOW())");
    $stmt_log->bind_param("iss", $id_cita, $usuario, $accion);
    $stmt_log->execute();

    // Redirigir con éxito
    header('Location: panel-secretaria.php?exito=estado_actualizado');
    exit;
} else {
    header('Location: panel-secretaria.php');
    exit;
}
