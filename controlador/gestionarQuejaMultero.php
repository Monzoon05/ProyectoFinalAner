<?php
session_start();
require '../modelo/conexion.php';

if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'multero' && $_SESSION['rol'] !== 'admin')) {
    header("Location: ../vista/login.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['accion'])) {
    header("Location: ../vista/perfiles/multero.php?error=Datos incorrectos");
    exit();
}

$id_queja = intval($_GET['id']);
$accion = $_GET['accion'];

$sql_queja = "SELECT id_multa_referencia FROM quejas WHERE id_queja = $id_queja";
$resultado = mysqli_query($conn, $sql_queja);
$queja = mysqli_fetch_assoc($resultado);
$id_multa = $queja['id_multa_referencia'];

if ($accion === 'aceptar') {
    // ELIMINAR la multa por completo
    $sql_delete_multa = "DELETE FROM multas WHERE id_multa = $id_multa";
    mysqli_query($conn, $sql_delete_multa);
    
    // Actualizar la queja
    $sql_update = "UPDATE quejas SET estado = 'aceptada' WHERE id_queja = $id_queja";
    $mensaje = "Queja aceptada. La multa ha sido eliminada por completo.";
    
} elseif ($accion === 'denegar') {
    // Solo actualizar la queja, la multa sigue igual
    $sql_update = "UPDATE quejas SET estado = 'denegada' WHERE id_queja = $id_queja";
    $mensaje = "Queja denegada. La multa sigue vigente.";
    
} else {
    header("Location: ../vista/perfiles/multero.php?error=Accion no valida");
    exit();
}

if (mysqli_query($conn, $sql_update)) {
    header("Location: ../vista/perfiles/multero.php?mensaje=" . urlencode($mensaje));
    exit();
} else {
    header("Location: ../vista/perfiles/multero.php?error=Error al gestionar la queja");
    exit();
}
?>