<?php
session_start();

function requireRole($allowedRoles) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['rol'], $allowedRoles)) {
        header("Location: ../login.php");
        exit();
    }
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}
?>