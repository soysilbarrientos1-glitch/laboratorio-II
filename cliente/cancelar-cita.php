<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}
include '../includes/db.php';

$id_cita = $_GET['id'] ?? null;
$id_cliente = $_SESSION['user_id'];

if ($id_cita) {
    $stmt = $conn->prepare("UPDATE citas SET estado = 'cancelada' WHERE id_cita = ? AND id_cliente = ?");
    $stmt->bind_param("ii", $id_cita, $id_cliente);
    $stmt->execute();
}

header("Location: ver-citas.php");
exit();
