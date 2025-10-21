<?php
$host = 'localhost';
$dbname = 'semis_marie';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Datos del usuario
    $nombre = "María";
    $apellido = "González";
    $email = "maria@example.com";
    $telefono = "123456789";
    $password_plano = "123456"; // Contraseña en texto plano
    $id_rol = 2; // cliente (según tu tabla roles)

    // Hashear la contraseña
    $hashed_password = password_hash($password_plano, PASSWORD_DEFAULT);

    // Insertar usuario
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, telefono, password, id_rol) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $apellido, $email, $telefono, $hashed_password, $id_rol]);

    echo "✅ Usuario creado con éxito.<br>";
    echo "📧 Email: maria@example.com<br>";
    echo "🔑 Contraseña: 123456<br>";
    echo "<a href='login.php'>Ir a login</a>";

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>