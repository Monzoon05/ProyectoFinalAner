<?php
session_start();
require '../modelo/conexion.php';

if (!isset($_SESSION['usuario_id']) || !isset($_POST['id_usuario_multado']) || !isset($_POST['motivo']) || !isset($_POST['monto']) || !isset($_POST['fecha_infraccion'])) {
    header("Location: ../vista/crearMulta.php?error=Error en el envío de los datos del formulario.");
    exit();
}

$id_usuario_creador = $_SESSION['usuario_id'];
$id_usuario_multado = trim($_POST['id_usuario_multado']);
$motivo = trim($_POST['motivo']);
$monto = trim($_POST['monto']);
$fecha_infraccion = trim($_POST['fecha_infraccion']);

if (empty($id_usuario_multado) || empty($motivo) || empty($monto) || empty($fecha_infraccion)) {
    header("Location: ../vista/crearMulta.php?error=Debes rellenar todos los campos seleccionando un usuario de la lista.");
    exit();
}

$id_multado_seguro = mysqli_real_escape_string($conn, $id_usuario_multado);
$motivo_seguro     = mysqli_real_escape_string($conn, $motivo);
$monto_seguro      = mysqli_real_escape_string($conn, $monto);
$fecha_segura      = mysqli_real_escape_string($conn, $fecha_infraccion);

$sql_insert = "INSERT INTO multas (id_usuario_multado, id_usuario_creador, motivo, monto_a_pagar, fecha_infraccion, fecha_registro) VALUES ('$id_multado_seguro', '$id_usuario_creador', '$motivo_seguro', '$monto_seguro', '$fecha_segura', NOW())";

if (mysqli_query($conn, $sql_insert)) {
    header("Location: ../vista/crearMulta.php?mensaje=Multa registrada con éxito en el sistema.");
    exit();
} else {
    header("Location: ../vista/crearMulta.php?error=Error crítico al guardar la multa en la base de datos.");
    exit();
}
?>