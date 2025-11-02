<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    die("Acceso no autorizado.");
}

$cliente_id = $_SESSION['user_id'];

// Verificar si hay al menos una cita pendiente
$stmt = $conn->prepare("SELECT id FROM cita WHERE id_cita = ? AND estado = 'pendiente' LIMIT 1");
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Ya tenés al menos una cita pendiente.";
} else {
    // Insertar cita de prueba
    $servicio_id = 1;       // ID de servicio válido
    $especialista_id = 1;   // ID de especialista válido
    $fecha = date('Y-m-d', strtotime('+1 day'));
    $hora = '11:00:00';
    $costo = 1000.00;
    $estado = 'pendiente';

    $insert = $conn->prepare("
        INSERT INTO cita (id_cita, id_cliente, id_empleado, fecha, hora, total, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $insert->bind_param("iiissds", $cliente_id, $servicio_id, $especialista_id, $fecha, $hora, $costo, $estado);
    $insert->execute();

    echo "Se creó una cita de prueba en estado pendiente.";
}
?>
