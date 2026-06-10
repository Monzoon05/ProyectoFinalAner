<?php
session_start();
require '../../modelo/conexion.php';

if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'multero' && $_SESSION['rol'] !== 'admin')) {
    header("Location: ../../index.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

if ($rol === 'admin') {
    $sql_stats = "SELECT 
                    (SELECT COUNT(*) FROM multas) as total_multas,
                    (SELECT COUNT(*) FROM multas WHERE estado = 'sin_pagar') as pendientes,
                    (SELECT SUM(monto_a_pagar) FROM multas WHERE estado = 'pagado') as total_recaudado,
                    (SELECT COUNT(*) FROM usuarios WHERE rol = 'jugador') as total_jugadores";
} else {
    $sql_stats = "SELECT 
                    COUNT(*) as total_multas,
                    SUM(CASE WHEN estado = 'sin_pagar' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'pagado' THEN monto_a_pagar ELSE 0 END) as total_recaudado
                  FROM multas 
                  WHERE id_usuario_creador = $id_usuario";
}
$stats = mysqli_fetch_assoc(mysqli_query($conn, $sql_stats));

$sql_usuarios = "SELECT id_usuario, nombre, apellido FROM usuarios WHERE id_usuario != 1 AND rol = 'jugador' ORDER BY nombre ASC";
$consulta_usuarios = mysqli_query($conn, $sql_usuarios);

$sql_multas_recientes = "SELECT m.*, u.nombre, u.apellido 
                         FROM multas m
                         JOIN usuarios u ON m.id_usuario_multado = u.id_usuario
                         ORDER BY m.fecha_registro DESC 
                         LIMIT 10";
$consulta_multas = mysqli_query($conn, $sql_multas_recientes);

$titulo = ($rol === 'admin') ? 'Panel de Administrador' : 'Panel de Multero';
$descripcion = ($rol === 'admin') 
    ? 'Desde aquí puedes gestionar todas las multas del sistema, ver estadísticas globales y administrar jugadores.'
    : 'Desde aquí puedes crear nuevas multas, hacer seguimiento de las sanciones que has impuesto y ver el estado de los pagos.';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>isunKi - <?php echo $titulo; ?></title>
    <link rel="stylesheet" href="../../css/estilos.css">
</head>
<body>
<div class="contenedorPrincipal">
    <header class="cabezera">
        <div class="contenedorLogo">
            <div class="logo-circular"><a href="multero.php"><img src="../../img/logoPequeño.png" width="56" height="50"></a></div>
            <div class="logo-escrito"><a href="multero.php"><img src="../../img/logoTexto.png" width="130" height="40"></a></div>
        </div>
        <nav class="navegador">
            <ul>
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#estadisticas">Estadísticas</a></li>
                <li><a href="#crearMulta">Crear Multa</a></li>
                <li><a href="#multasRecientes">Multas Recientes</a></li>
                <li><a href="../../controlador/cerrarSesion.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="content">
        <!-- SECCIÓN INICIO -->
        <section id="inicio" class="seccion">
            <div class="tarjeta-bienvenida">
                <div class="tarjeta-bienvenida-contenido">
                    <h1><?php echo $titulo; ?> - <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
                    <p><?php echo $descripcion; ?></p>
                    <p>Utiliza el menú superior o el menú del pie de página para navegar rápidamente entre las diferentes secciones.</p>
                    <?php if ($rol === 'admin'): ?>
                        <p>Permisos de administrador activos. Puedes ver estadísticas globales del sistema.</p>
                    <?php else: ?>
                        <p>Recuerda que las multas deben ser justificadas y transparentes para mantener un buen ambiente en el equipo.</p>
                    <?php endif; ?>
                </div>
                <div class="tarjeta-bienvenida-logo">
                    <img src="../../img/logoPequeño.png" width="90" height="80" alt="isunKi Logo">
                </div>
            </div>
        </section>

        <!-- SECCIÓN ESTADÍSTICAS -->
        <section id="estadisticas" class="seccion">
            <h2>Estadísticas</h2>
            <div class="flex-center">
                <div class="tarjeta-estadistica">
                    <h3><?php echo ($rol === 'admin') ? 'Total Multas' : 'Multas Creadas'; ?></h3>
                    <p class="numero"><?php echo $stats['total_multas'] ?? 0; ?></p>
                </div>
                <div class="tarjeta-estadistica">
                    <h3>Pendientes</h3>
                    <p class="numero amarillo"><?php echo $stats['pendientes'] ?? 0; ?></p>
                </div>
                <div class="tarjeta-estadistica">
                    <h3>Recaudado</h3>
                    <p class="numero verde"><?php echo number_format($stats['total_recaudado'] ?? 0, 2); ?> €</p>
                </div>
                <?php if ($rol === 'admin'): ?>
                <div class="tarjeta-estadistica">
                    <h3>Jugadores</h3>
                    <p class="numero azul"><?php echo $stats['total_jugadores'] ?? 0; ?></p>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- SECCIÓN CREAR MULTAS -->
        <section id="crearMulta" class="seccion">
            <h2>Crear Nueva Multa</h2>
            <div class="form-multa">
                <form action="../../controlador/validarMulta.php" method="POST">
                    <?php
                        if(isset($_GET['error'])){
                            echo "<p style='color: #E63946; text-align: center; margin-bottom: 15px;'>" . htmlspecialchars($_GET['error']) . "</p>";
                        }
                        if(isset($_GET['mensaje'])){
                            echo "<p style='color: #2A9D8F; text-align: center; margin-bottom: 15px;'>" . htmlspecialchars($_GET['mensaje']) . "</p>";
                        }
                    ?>
                    
                    <label><strong>Multero:</strong> <?php echo htmlspecialchars($_SESSION['usuario']); ?></label>
                    
                    <select name="id_usuario_multado" required>
                        <option value="">- Selecciona un jugador -</option>
                        <?php while($user = mysqli_fetch_assoc($consulta_usuarios)): ?>
                            <option value="<?php echo $user['id_usuario']; ?>">
                                <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    
                    <input type="text" name="motivo" placeholder="Motivo de la infracción" required>
                    <input type="number" name="monto" step="0.01" placeholder="Monto a pagar (euros)" required>
                    <input type="date" name="fecha_infraccion" required>
                    
                    <button type="submit">Registrar Multa</button>
                </form>
            </div>
        </section>

        <!-- SECCIÓN MULTAS RECIENTES -->
        <section id="multasRecientes" class="seccion">
            <h2>Últimas Multas Registradas</h2>
            <div style="overflow-x: auto;">
                <table class="tabla-multas">
                    <thead>
                        <tr>
                            <th>Jugador</th>
                            <th>Motivo</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($multa = mysqli_fetch_assoc($consulta_multas)): 
                            $clase_texto = '';
                            if($multa['estado'] === 'sin_pagar') $clase_texto = 'style="color:#E63946; font-weight:bold;"';
                            elseif($multa['estado'] === 'pendiente') $clase_texto = 'style="color:#FFB703; font-weight:bold;"';
                            else $clase_texto = 'style="color:#2A9D8F; font-weight:bold;"';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($multa['nombre'] . ' ' . $multa['apellido']); ?></td>
                            <td><?php echo htmlspecialchars($multa['motivo']); ?></td>
                            <td><?php echo number_format($multa['monto_a_pagar'], 2); ?> €</td>
                            <td <?php echo $clase_texto; ?>><?php echo strtoupper($multa['estado']); ?></td>
                            <td><?php echo date("d/m/Y", strtotime($multa['fecha_infraccion'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- FOOTER CON MENÚ DUPLICADO -->
    <footer class="main-footer">
        <div class="footer-menu">
            <div class="footer-logo">
                <img src="../../img/logoPequeño.png" width="35" height="32" alt="isunKi">
                <img src="../../img/logoTexto.png" width="90" height="27" alt="isunKi" style="margin-left: 8px;">
            </div>
            <ul>
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#estadisticas">Estadísticas</a></li>
                <li><a href="#crearMulta">Crear Multa</a></li>
                <li><a href="#multasRecientes">Multas Recientes</a></li>
                <li><a href="../../controlador/cerrarSesion.php">Cerrar Sesión</a></li>
            </ul>
            <div class="footer-copy">
                <p>&copy; 2026 isunKi - Proyecto Final de Grado</p>
                <p>Gestión deportiva eficiente y transparente</p>
            </div>
        </div>
    </footer>
</div>
</body>
</html>