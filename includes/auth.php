<?php
// Inicia sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica que el usuario tenga uno de los roles permitidos.
 * @param array $allowedRoles Ej: ['administrador', 'manicurista']
 */
function requireRole(array $allowedRoles) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['rol'], $allowedRoles)) {
        header("Location: ../login-admin.php"); // o login.php según contexto
        exit();
    }
}

/**
 * Verifica que el usuario esté logueado, sin importar el rol.
 */
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login-admin.php");
        exit();
    }
}

/**
 * Cierra la sesión actual de forma segura.
 */
function logout() {
    $_SESSION = [];
    session_unset();
    session_destroy();
    header("Location: ../login-admin.php");
    exit();
}
