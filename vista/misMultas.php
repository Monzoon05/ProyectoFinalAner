<?php
session_start();
require '../modelo/conexion.php';

// Control de acceso común
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_sesion = $_SESSION['usuario_id'];
$rol_sesion = $_SESSION['rol'];

// Multas del usuario logueado
$sql_multas = "SELECT m.*, u.nombre AS nombre_creador, u.apellido AS apellido_creador 
               FROM multas m
               JOIN usuarios u ON m.id_usuario_creador = u.id_usuario
               WHERE m.id_usuario_multado = '$id_sesion'
               ORDER BY m.fecha_infraccion DESC";

$consulta_multas = mysqli_query($conn, $sql_multas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>isunKi - Mis Multas</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>

<body>
<div class="contenedorPrincipal">

    <header class="cabezera">
        <div class="contenedorLogo">
            <div class="logo-circular">
                <a href="perfiles/<?php echo ($_SESSION['rol'] === 'jugador') ? 'jugador.php' : 'multero.php'; ?>">
                    <img src="../img/logoPequeño.png" width="56" height="50">
                </a>
            </div>
            <div class="logo-escrito">
                <a href="perfiles/<?php echo ($_SESSION['rol'] === 'jugador') ? 'jugador.php' : 'multero.php'; ?>">
                    <img src="../img/logoTexto.png" width="130" height="40">
                </a>
            </div>
        </div>

        <nav class="navegador">
            <ul>
                <li><a href="ranking.php">Ranking</a></li>
                <li><a href="misMultas.php" style="color:#F9C74F;">Mis Multas</a></li>
                
                <?php if ($_SESSION['rol'] === 'multero' || $_SESSION['rol'] === 'admin'): ?>
                    <li><a href="crearMulta.php">Crear Multa</a></li>
                <?php endif; ?>
                
                <li><a href="../controlador/cerrarSesion.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="content">

        <h2 class="titulo">Mis multas</h2>

        <div class="contenedorTarjetas">

            <?php if (mysqli_num_rows($consulta_multas) == 0): ?>
                <p style="text-align:center; color:#555;">
                    ¡Enhorabuena! No tienes ninguna multa registrada.
                </p>
            <?php endif; ?>

            <?php while($multa = mysqli_fetch_assoc($consulta_multas)):

                // Clases de color según estado
                $clase_estado = '';

                if ($multa['estado'] === 'sin_pagar') {
                    $clase_estado = 'estado-rojo';
                } elseif ($multa['estado'] === 'pendiente') {
                    $clase_estado = 'estado-amarillo';
                } elseif ($multa['estado'] === 'pagado') {
                    $clase_estado = 'estado-verde';
                }
            ?>

            <div class="tarjetaMulta <?php echo $clase_estado; ?>">
                
                <div class="cuerpoTarjeta">
                    <h3><?php echo htmlspecialchars($multa['motivo']); ?></h3>

                    <p class="monto">
                        <?php echo number_format($multa['monto_a_pagar'], 2); ?> €
                    </p>

                    <p>
                        <strong>Puesta por:</strong>
                        <?php echo htmlspecialchars($multa['nombre_creador'] . ' ' . $multa['apellido_creador']); ?>
                    </p>

                    <p>
                        <strong>Fecha Infracción:</strong>
                        <?php echo date("d/m/Y", strtotime($multa['fecha_infraccion'])); ?>
                    </p>

                    <p class="texto-estado">
                        Estado: <span><?php echo strtoupper($multa['estado']); ?></span>
                    </p>
                </div>

                <div class="accionesTarjeta">

                    <!-- BOTÓN HE PAGADO -->
                    <?php if ($multa['estado'] === 'sin_pagar'): ?>
                        <a href="../controlador/cambiarEstadoMulta.php?id=<?php echo $multa['id_multa']; ?>&nuevo_estado=pendiente"
                           class="btn-pagar">
                            He pagado
                        </a>
                    <?php endif; ?>

                    <!-- BOTÓN QUEJA -->
                    <a href="crearQueja.php?id_multa=<?php echo $multa['id_multa']; ?>"
                       class="btn-queja">
                        Enviar Queja
                    </a>

                </div>

            </div>

            <?php endwhile; ?>

        </div>

    </main>

    <footer class="main-footer">
        <p>&copy; 2026 isunKi - Proyecto Final de Grado</p>
    </footer>

</div>
</body>
</html>