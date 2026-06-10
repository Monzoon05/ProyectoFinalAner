<?php
session_start();
require '../modelo/conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'jugador') {
    header("Location: ../vista/login.php");
    exit();
}

if (!isset($_GET['id_multa']) || !isset($_GET['motivo'])) {
    header("Location: ../vista/perfiles/jugador.php?error=Datos incorrectos");
    exit();
}

$id_multa = intval($_GET['id_multa']);
$id_jugador = $_SESSION['usuario_id'];
$motivo_queja = trim($_GET['motivo']);

if (empty($motivo_queja)) {
    header("Location: ../vista/perfiles/jugador.php?error=Debes escribir un motivo");
    exit();
}

if (strlen($motivo_queja) < 10) {
    header("Location: ../vista/perfiles/jugador.php?error=El motivo debe tener al menos 10 caracteres");
    exit();
}

// Verificar que la multa existe y pertenece al jugador
$sql_verificar = "SELECT id_multa FROM multas WHERE id_multa = $id_multa AND id_usuario_multado = $id_jugador";
$resultado = mysqli_query($conn, $sql_verificar);

if (mysqli_num_rows($resultado) == 0) {
    header("Location: ../vista/perfiles/jugador.php?error=No puedes enviar queja sobre esta multa");
    exit();
}

// Verificar que no existe una queja pendiente para esta multa
$sql_check = "SELECT id_queja FROM quejas WHERE id_multia_referencia = $id_multa AND estado = 'pendiente'";
$check_result = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($check_result) > 0) {
    header("Location: ../vista/perfiles/jugador.php?error=Ya tienes una queja pendiente para esta multa");
    exit();
}

// Guardar la queja
$motivo_seguro = mysqli_real_escape_string($conn, $motivo_queja);
$sql_insert = "INSERT INTO quejas (id_usuario_emisor, id_multia_referencia, motivo_queja, estado, fecha_queja) 
               VALUES ($id_jugador, $id_multa, '$motivo_seguro', 'pendiente', NOW())";

if (mysqli_query($conn, $sql_insert)) {
    header("Location: ../vista/perfiles/jugador.php?mensaje=Queja enviada correctamente. El administrador la revisará.");
    exit();
} else {
    header("Location: ../vista/perfiles/jugador.php?error=Error al guardar la queja");
    exit();
}
?>