<?php
session_start();
require '../modelo/conexion.php';

if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'multero' && $_SESSION['rol'] !== 'admin')) {
    header("Location: ../vista/login.php");
    exit();
}

if (!isset($_POST['id_usuario_multado']) || !isset($_POST['motivo']) || !isset($_POST['monto']) || !isset($_POST['fecha_infraccion'])) {
    header("Location: ../vista/perfiles/multero.php?error=Error en el envio de los datos");
    exit();
}

$id_usuario_creador = $_SESSION['usuario_id'];
$id_usuario_multado = trim($_POST['id_usuario_multado']);
$motivo = trim($_POST['motivo']);
$monto = trim($_POST['monto']);
$fecha_infraccion = trim($_POST['fecha_infraccion']);

if (empty($id_usuario_multado) || empty($motivo) || empty($monto) || empty($fecha_infraccion)) {
    header("Location: ../vista/perfiles/multero.php?error=Debes rellenar todos los campos");
    exit();
}

// VERIFICAR QUE EL USUARIO MULTADO EXISTA
$sql_verificar_usuario = "SELECT id_usuario, rol FROM usuarios WHERE id_usuario = '$id_usuario_multado'";
$resultado_usuario = mysqli_query($conn, $sql_verificar_usuario);

if (mysqli_num_rows($resultado_usuario) == 0) {
    header("Location: ../vista/perfiles/multero.php?error=El usuario seleccionado no existe");
    exit();
}

$usuario = mysqli_fetch_assoc($resultado_usuario);

// VERIFICAR QUE EL USUARIO MULTADO SEA JUGADOR (no multero ni admin)
if ($usuario['rol'] !== 'jugador') {
    header("Location: ../vista/perfiles/multero.php?error=Solo se pueden multar a jugadores");
    exit();
}

// VERIFICAR QUE EL MULTERO NO SE ESTE MULTANDO A SI MISMO
if ($id_usuario_multado == $id_usuario_creador) {
    header("Location: ../vista/perfiles/multero.php?error=No puedes multarte a ti mismo");
    exit();
}

$id_multado_seguro = mysqli_real_escape_string($conn, $id_usuario_multado);
$motivo_seguro     = mysqli_real_escape_string($conn, $motivo);
$monto_seguro      = mysqli_real_escape_string($conn, $monto);
$fecha_segura      = mysqli_real_escape_string($conn, $fecha_infraccion);

$sql_insert = "INSERT INTO multas (id_usuario_multado, id_usuario_creador, motivo, monto_a_pagar, fecha_infraccion, fecha_registro, estado) 
               VALUES ('$id_multado_seguro', '$id_usuario_creador', '$motivo_seguro', '$monto_seguro', '$fecha_segura', NOW(), 'sin_pagar')";

if (mysqli_query($conn, $sql_insert)) {
    header("Location: ../vista/perfiles/multero.php?mensaje=Multa registrada con exito");
    exit();
} else {
    header("Location: ../vista/perfiles/multero.php?error=Error al guardar la multa: " . mysqli_error($conn));
    exit();
}
?>