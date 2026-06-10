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

$id_multa = intval($_GET['id']);
$accion = $_GET['accion'];

if ($accion === 'confirmar') {
    $sql_update = "UPDATE multas SET estado = 'pagado' WHERE id_multa = $id_multa";
    $mensaje = "Pago confirmado correctamente.";
} elseif ($accion === 'rechazar') {
    $sql_update = "UPDATE multas SET estado = 'sin_pagar' WHERE id_multa = $id_multa";
    $mensaje = "Pago rechazado. La multa vuelve a estar pendiente.";
} else {
    header("Location: ../vista/perfiles/multero.php?error=Accion no valida");
    exit();
}

if (mysqli_query($conn, $sql_update)) {
    header("Location: ../vista/perfiles/multero.php?mensaje=" . urlencode($mensaje));
    exit();
} else {
    header("Location: ../vista/perfiles/multero.php?error=Error al procesar el pago");
    exit();
}
?>