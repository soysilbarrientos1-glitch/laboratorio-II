<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireRole(['administrador']);

$accion = $_GET['accion'] ?? '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id && in_array($accion, ['activar', 'desactivar']) && $id != $_SESSION['user_id']) {
    $activo = $accion === 'activar' ? 1 : 0;
    $stmt = $conn->prepare("UPDATE usuarios SET activo = ? WHERE id_usuario = ?");
    $stmt->bind_param("ii", $activo, $id);
    $stmt->execute();
}

header("Location: gestion-usuarios.php");
exit();
?>