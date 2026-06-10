<?php
session_start();
require '../modelo/conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'jugador') {
    header("Location: ../vista/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../vista/perfiles/jugador.php?error=Datos incorrectos");
    exit();
}

$id_multa = intval($_GET['id']);
$id_jugador = $_SESSION['usuario_id'];

// Verificar que la multa pertenece al jugador y está en estado "sin_pagar"
$sql_verificar = "SELECT id_multa FROM multas WHERE id_multa = $id_multa AND id_usuario_multado = $id_jugador AND estado = 'sin_pagar'";
$resultado = mysqli_query($conn, $sql_verificar);

if (mysqli_num_rows($resultado) == 0) {
    header("Location: ../vista/perfiles/jugador.php?error=No puedes marcar esta multa como pagada");
    exit();
}

// Cambiar estado a 'pendiente' (esperando confirmación del multero)
$sql_update = "UPDATE multas SET estado = 'pendiente' WHERE id_multa = $id_multa";

if (mysqli_query($conn, $sql_update)) {
    header("Location: ../vista/perfiles/jugador.php?mensaje=Has marcado la multa como pagada. Espera confirmación del administrador.");
    exit();
} else {
    header("Location: ../vista/perfiles/jugador.php?error=Error al actualizar el estado");
    exit();
}
?>