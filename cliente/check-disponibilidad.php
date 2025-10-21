<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

$especialista_id = isset($_POST['especialista_id']) ? (int)$_POST['especialista_id'] : 0;
$fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
$hora = isset($_POST['hora']) ? trim($_POST['hora']) : '';

if ($especialista_id <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || !preg_match('/^\d{2}:\d{2}$/', $hora)) {
    echo json_encode(['error' => 'Parámetros inválidos']);
    exit();
}

$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM citas WHERE id_empleado = ? AND fecha = ? AND hora = ? AND estado IN ('confirmado','pendiente')");
$stmt->bind_param('iss', $especialista_id, $fecha, $hora);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$cnt = (int)$result['cnt'];

echo json_encode(['available' => $cnt === 0]);
