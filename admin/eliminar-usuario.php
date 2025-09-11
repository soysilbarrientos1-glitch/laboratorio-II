<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$id = $_GET['id'] ?? null;
if ($id && eliminar_usuario($id)) {
    $_SESSION['mensaje'] = 'Usuario eliminado.';
} else {
    $_SESSION['error'] = 'No se pudo eliminar.';
}
header('Location: usuarios.php');
exit;