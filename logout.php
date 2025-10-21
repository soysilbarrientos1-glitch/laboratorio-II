<?php
session_start();
session_destroy(); // Elimina todas las variables de sesión
header("Location: index.php"); // Redirige a la página principal
exit; // Asegura que el script se detenga aquí
?>