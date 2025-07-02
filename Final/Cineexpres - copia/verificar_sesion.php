<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit();
}
?>
