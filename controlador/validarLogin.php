<?php
session_start();
require '../modelo/conexion.php';

if (empty($_POST['correo']) || empty($_POST['contraseña'])) {
    header("Location:../vista/login.php?error=Rellena todos los campos");
    exit();
}

$correo = trim($_POST['correo']);
$contraseña = trim($_POST['contraseña']);

$correo_seguro = mysqli_real_escape_string($conn, $correo);

$sql = "SELECT id_usuario, nombre, password, rol 
        FROM usuarios 
        WHERE correo = '$correo_seguro'";

$consulta = mysqli_query($conn, $sql);

if ($consulta && $usuario = mysqli_fetch_assoc($consulta)) {

    if (password_verify($contraseña, $usuario['password'])) {

        $_SESSION['usuario_id'] = $usuario['id_usuario'];
        $_SESSION['usuario'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        // REDIRECCIONES ACTUALIZADAS
        if ($usuario['rol'] === 'jugador') {
            header("Location: ../index.php");
            exit();
        }

        if ($usuario['rol'] === 'multero') {
            header("Location: ../vista/perfiles/multero.php");
            exit();
        }

        if ($usuario['rol'] === 'admin') {
            header("Location: ../vista/perfiles/multero.php");
            exit();
        }

    } else {
        header("Location:../vista/login.php?error=Contraseña incorrecta.");
        exit();
    }

} else {
    header("Location:../vista/login.php?error=Correo incorrecto.");
    exit();
}
?>