<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $clave = $_POST['clave'] ?? '';

    if ($email && $clave) {
        // Consulta segura con JOIN para obtener el rol
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
            $user = $resultado->fetch_assoc();

            // Verificación segura de contraseña
            if (password_verify($clave, $user['password'])) {
                $_SESSION['user_id'] = $user['id_usuario'];
                $_SESSION['nombre'] = $user['nombre'];
                $_SESSION['rol'] = strtolower($user['nombre_rol']); // Normaliza el rol

                // Redirección según rol
                switch ($_SESSION['rol']) {
                    case 'administrador':
                        header('Location: dashboard.php');
                        break;
                    case 'secretaria':
                        header('Location: panel-secretaria.php');
                        break;
                    case 'manicurista':
                    case 'pedicurista':
                        header('Location: panel-especialista.php');
                        break;
                    default:
                        header('Location: acceso-denegado.php');
                        break;
                }
                exit;
            }
        }
    }

    // Mensaje de error genérico
    $error = "Correo o clave incorrectos";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>
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
  <h2>Iniciar sesión</h2>
  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" autocomplete="off">
    <input type="text" id="email" name="email" placeholder="Correo electrónico" required maxlength="100">
    <input type="password" id="clave" name="clave" placeholder="Contraseña" required maxlength="50">
    <button type="submit">Ingresar</button>
  </form>
</div>

</body>
</html>
