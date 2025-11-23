<?php
session_start();
require_once '../includes/db.php';      // conexión segura
require_once '../includes/auth.php';    // funciones de sesión y rol

// Redirigir si ya está logueado como administrador
if (isset($_SESSION['user_id']) && $_SESSION['rol'] === 'administrador') {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $clave = $_POST['clave'] ?? '';

    if ($email && $clave) {
        // Consulta segura con JOIN para obtener rol
        $stmt = $conn->prepare("
            SELECT u.id_usuario, u.nombre, u.password, r.nombre_rol 
            FROM usuarios u 
            JOIN rol r ON u.id_rol = r.id_rol 
            WHERE u.email = ? AND u.activo = 1
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows === 1) {
            $admin = $resultado->fetch_assoc();

            // Validación de rol y contraseña
            if (
                strtolower($admin['nombre_rol']) === 'administrador' &&
                password_verify($clave, $admin['password'])
            ) {
                $_SESSION['user_id'] = $admin['id_usuario'];
                $_SESSION['nombre'] = $admin['nombre'];
                $_SESSION['rol'] = $admin['nombre_rol'];
                header("Location: dashboard.php");
                exit;
            }
        }
    }

    // Si algo falla, mostrar mensaje genérico
    $error = "Correo o clave incorrectos";
}
?>


           
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión - Admin</title>
  <link rel="stylesheet" href="../css/login_admin.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .login-box {
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      width: 300px;
    }
    h2 {
      color: #e91e63;
      margin-bottom: 1rem;
      text-align: center;
    }
    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 0.5rem;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    button {
      width: 100%;
      padding: 0.6rem;
      background: #e91e63;
      color: white;
      border: none;
      border-radius: 4px;
      font-weight: bold;
      cursor: pointer;
    }
    .error {
      background: #ffe0e0;
      color: #d32f2f;
      padding: 0.5rem;
      border-radius: 4px;
      margin-bottom: 1rem;
      text-align: center;
    }
  </style>
</head>
<body>

<div class="login-box">
  <h2>Admin - Iniciar sesión</h2>
  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

<form method="POST" autocomplete="off">
  <label for="email" style="display:none;">Correo electrónico</label>
  <input type="text" id="email" name="email" placeholder="Correo electrónico" required maxlength="100">

  <label for="clave" style="display:none;">Contraseña</label>
  <input type="password" id="clave" name="clave" placeholder="Contraseña" required maxlength="50">

  <button type="submit">Ingresar</button>
</form>

  
</div>

</body>
</html>
