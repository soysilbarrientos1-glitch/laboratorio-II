<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = [];
session_unset();
session_destroy();

// Redirigir al login del administrador
header("Location: login-admin.php");
exit();
