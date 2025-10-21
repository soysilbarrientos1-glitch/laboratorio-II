<?php
$host = 'localhost';
$dbname = 'semis_marie';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener todos los usuarios
    $stmt = $pdo->query("SELECT id_usuario, password FROM usuarios");
    while ($user = $stmt->fetch()) {
        // Si la contraseña NO empieza con $2y$, hashearla
        if (!str_starts_with($user['password'], '$2y$')) {
            $hashed = password_hash($user['password'], PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id_usuario = ?");
            $update->execute([$hashed, $user['id_usuario']]);
            echo "Actualizado usuario ID: " . $user['id_usuario'] . "\n";
        }
    }

    echo "✅ Todas las contraseñas actualizadas.";
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>