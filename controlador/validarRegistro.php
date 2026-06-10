<?php
session_start();
require '../modelo/conexion.php';

if (!isset($_POST['nombre']) || !isset($_POST['apellido']) || !isset($_POST['correo']) || !isset($_POST['contraseña']) || !isset($_POST['rol'])) {
    header("Location:../vista/registro.php?error=ERROR: Los datos no se han enviado correctamente.");
    exit();
} else {
    if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['correo']) || empty($_POST['contraseña']) || empty($_POST['rol'])) {
        header("Location:../vista/registro.php?error=Debes rellenar todos los campos.");
        exit();
    }

    $nombre     = trim($_POST['nombre']);
    $apellido   = trim($_POST['apellido']);
    $correo     = trim($_POST['correo']);
    $contraseña = trim($_POST['contraseña']);
    $rol        = trim($_POST['rol']); // Recogemos el rol

    $nombre_seguro   = mysqli_real_escape_string($conn, $nombre);
    $apellido_seguro = mysqli_real_escape_string($conn, $apellido);
    $correo_seguro   = mysqli_real_escape_string($conn, $correo);
    $rol_seguro      = mysqli_real_escape_string($conn, $rol); // Limpieza básica

    if ($rol_seguro === 'admin') {
        header("Location:../vista/registro.php?error=No tienes permisos para registrarte con ese rol.");
        exit();
    }

    $sql_check = "SELECT id_usuario FROM usuarios WHERE correo = '$correo_seguro'";
    $consulta_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($consulta_check) > 0) {
        header("Location:../vista/registro.php?error=El correo ya está registrado por otro usuario.");
        exit();
    }

    $password_encriptada = password_hash($contraseña, PASSWORD_DEFAULT);

    $sql_insert = "INSERT INTO usuarios (nombre, apellido, correo, password, rol, fecha_registro) VALUES ('$nombre_seguro', '$apellido_seguro', '$correo_seguro', '$password_encriptada', '$rol_seguro', NOW())";

    if (mysqli_query($conn, $sql_insert)) {
        header("Location:../vista/login.php?mensaje=Registro completado correctamente. Ya puedes iniciar sesión.");
        exit();
    } else {
        header("Location:../vista/registro.php?error=Error al registrar el usuario en el servidor.");
        exit();
    }
}
?>