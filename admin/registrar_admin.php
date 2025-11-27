<?php
require_once '../includes/db.php';

//captura del campo del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $clave = $_POST['clave'] ?? '';

//incluis el campo en la validacion
    if ($nombre && $email && $clave) {
        $clave_cifrada = password_hash($clave, PASSWORD_DEFAULT);
        $id_rol = 3; // Asegurate que este ID corresponde a 'administrador'
        $activo = 1;

//consulta sql aca tambien agregas el campo, modificas el insert
        $stmt = $conn->prepare("
            INSERT INTO usuarios (nombre, email, password, id_rol, activo)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssii", $nombre, $email, $clave_cifrada, $id_rol, $activo);

        if ($stmt->execute()) {
            echo "✅ Administrador registrado correctamente.";
        } else {
            echo "❌ Error al registrar: " . $stmt->error;
        }
    } else {
        echo "⚠️ Faltan datos obligatorios.";
    }
}
?>
