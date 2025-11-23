<?php
function contarUsuariosPorRol($conn, $id_rol) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM usuarios WHERE id_rol = ?");
    $stmt->bind_param("i", $id_rol);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}

function contarTurnosHoy($conn) {
    $hoy = date('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE fecha = ? AND estado = 'confirmada'");
    $stmt->bind_param("s", $hoy);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}
