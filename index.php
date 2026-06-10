<?php
session_start();
require 'modelo/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: vista/login.php");
    exit();
}

// Redirigir según el rol
if ($_SESSION['rol'] === 'jugador') {
    header("Location: vista/perfiles/jugador.php");
    exit();
} elseif ($_SESSION['rol'] === 'multero' || $_SESSION['rol'] === 'admin') {
    // Multeros y admin comparten la misma interfaz
    header("Location: vista/perfiles/multero.php");
    exit();
} else {
    session_destroy();
    header("Location: vista/login.php?error=Rol no válido");
    exit();
}
?>