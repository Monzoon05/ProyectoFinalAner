<?php
session_start();
require '../../modelo/conexion.php';

if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'multero' && $_SESSION['rol'] !== 'admin')) {
    header("Location: ../../index.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

// ESTADÍSTICAS GENERALES
if ($rol === 'admin') {
    $sql_stats = "SELECT 
                    (SELECT COUNT(*) FROM multas) as total_multas,
                    (SELECT COUNT(*) FROM multas WHERE estado = 'sin_pagar') as sin_pagar,
                    (SELECT COUNT(*) FROM multas WHERE estado = 'pendiente') as pendientes,
                    (SELECT COUNT(*) FROM multas WHERE estado = 'pagado') as pagadas,
                    (SELECT SUM(monto_a_pagar) FROM multas WHERE estado = 'pagado') as total_recaudado,
                    (SELECT SUM(monto_a_pagar) FROM multas WHERE estado IN ('sin_pagar', 'pendiente')) as total_pendiente,
                    (SELECT COUNT(*) FROM usuarios WHERE rol = 'jugador') as total_jugadores";
} else {
    $sql_stats = "SELECT 
                    COUNT(*) as total_multas,
                    SUM(CASE WHEN estado = 'sin_pagar' THEN 1 ELSE 0 END) as sin_pagar,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'pagado' THEN 1 ELSE 0 END) as pagadas,
                    SUM(CASE WHEN estado = 'pagado' THEN monto_a_pagar ELSE 0 END) as total_recaudado,
                    SUM(CASE WHEN estado IN ('sin_pagar', 'pendiente') THEN monto_a_pagar ELSE 0 END) as total_pendiente
                  FROM multas 
                  WHERE id_usuario_creador = $id_usuario";
}
$stats = mysqli_fetch_assoc(mysqli_query($conn, $sql_stats));

// LISTA DE JUGADORES
$sql_usuarios = "SELECT id_usuario, nombre, apellido FROM usuarios WHERE id_usuario != 1 AND rol = 'jugador' ORDER BY nombre ASC";
$consulta_usuarios = mysqli_query($conn, $sql_usuarios);

// CONFIRMAR PAGOS - MULTAS PENDIENTES
$sql_pagos_pendientes = "SELECT m.*, u.nombre, u.apellido 
                         FROM multas m
                         JOIN usuarios u ON m.id_usuario_multado = u.id_usuario
                         WHERE m.estado = 'pendiente'
                         ORDER BY m.fecha_registro DESC";
$consulta_pagos = mysqli_query($conn, $sql_pagos_pendientes);

// QUEJAS PENDIENTES
$sql_quejas = "SELECT q.*, u.nombre, u.apellido, m.motivo as motivo_multa, m.monto_a_pagar
               FROM quejas q
               JOIN usuarios u ON q.id_usuario_emisor = u.id_usuario
               JOIN multas m ON q.id_multa_referencia = m.id_multa
               WHERE q.estado = 'pendiente'
               ORDER BY q.fecha_queja DESC";
$consulta_quejas = mysqli_query($conn, $sql_quejas);

// TABLA DE MULTAS COMPLETA
$sql_todas_multas = "SELECT m.*, u.nombre, u.apellido 
                     FROM multas m
                     JOIN usuarios u ON m.id_usuario_multado = u.id_usuario
                     ORDER BY m.fecha_infraccion DESC";
$consulta_todas_multas = mysqli_query($conn, $sql_todas_multas);

$sql_totales = "SELECT 
                    SUM(CASE WHEN estado = 'pagado' THEN monto_a_pagar ELSE 0 END) as total_pagado,
                    SUM(CASE WHEN estado IN ('sin_pagar', 'pendiente') THEN monto_a_pagar ELSE 0 END) as total_pendiente
                FROM multas";
$totales = mysqli_fetch_assoc(mysqli_query($conn, $sql_totales));

$titulo = ($rol === 'admin') ? 'Panel de Administrador' : 'Panel de Multero';
$descripcion = ($rol === 'admin') 
    ? 'Desde aqui puedes gestionar todas las multas del sistema, ver estadisticas globales y administrar jugadores.'
    : 'Desde aqui puedes crear nuevas multas, confirmar pagos, gestionar quejas y ver el estado de todas las multas.';
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
            <div class="logo-circular"><a href="multero.php"><img src="../../img/logoPequeño.png" width="56" height="50" alt="Logo"></a></div>
            <div class="logo-escrito"><a href="multero.php"><img src="../../img/logoTexto.png" width="130" height="40" alt="isunKi"></a></div>
        </div>
        <nav class="navegador">
            <ul>
                <li><a href="#inicio" class="menu-link" data-seccion="inicio">Inicio</a></li>
                <li><a href="#crearMulta" class="menu-link" data-seccion="crearMulta">Crear Multa</a></li>
                <li><a href="#confirmarPagos" class="menu-link" data-seccion="confirmarPagos">Confirmar Pagos</a></li>
                <li><a href="#quejas" class="menu-link" data-seccion="quejas">Quejas</a></li>
                <li><a href="#tablaMultas" class="menu-link" data-seccion="tablaMultas">Tabla de Multas</a></li>
                <li><a href="../../controlador/cerrarSesion.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="content">
        <?php if(isset($_GET['mensaje'])): ?>
            <div style="background-color: #2A9D8F; color: white; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; text-align: center;"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div style="background-color: #E63946; color: white; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; text-align: center;"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <!-- INICIO -->
        <section id="inicio" class="seccion">
            <div class="tarjeta-bienvenida">
                <div class="tarjeta-bienvenida-contenido">
                    <h1><?php echo $titulo; ?> - <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
                    <p><?php echo $descripcion; ?></p>
                    <p>Utiliza el menu superior para navegar rapidamente entre las diferentes secciones.</p>
                </div>
                <div class="tarjeta-bienvenida-logo">
                    <img src="../../img/logoPequeño.png" width="90" height="80" alt="isunKi Logo">
                </div>
            </div>
            <div class="flex-center" style="margin-top: 40px;">
                <div class="tarjeta-estadistica"><h3>Total Multas</h3><p class="numero"><?php echo $stats['total_multas'] ?? 0; ?></p></div>
                <div class="tarjeta-estadistica"><h3>Sin Pagar</h3><p class="numero rojo"><?php echo $stats['sin_pagar'] ?? 0; ?></p></div>
                <div class="tarjeta-estadistica"><h3>Pendientes</h3><p class="numero amarillo"><?php echo $stats['pendientes'] ?? 0; ?></p></div>
                <div class="tarjeta-estadistica"><h3>Pagadas</h3><p class="numero verde"><?php echo $stats['pagadas'] ?? 0; ?></p></div>
                <div class="tarjeta-estadistica"><h3>Recaudado</h3><p class="numero verde"><?php echo number_format($stats['total_recaudado'] ?? 0, 2); ?> €</p></div>
                <div class="tarjeta-estadistica"><h3>Pendiente Cobrar</h3><p class="numero rojo"><?php echo number_format($stats['total_pendiente'] ?? 0, 2); ?> €</p></div>
                <?php if ($rol === 'admin'): ?>
                <div class="tarjeta-estadistica"><h3>Jugadores</h3><p class="numero azul"><?php echo $stats['total_jugadores'] ?? 0; ?></p></div>
                <?php endif; ?>
            </div>
        </section>

        <!-- CREAR MULTAS -->
        <section id="crearMulta" class="seccion">
            <h2>Crear Nueva Multa</h2>
            <div class="form-multa">
                <form action="../../controlador/validarMulta.php" method="POST">
                    <label><strong>Multero:</strong> <?php echo htmlspecialchars($_SESSION['usuario']); ?></label>
                    <select name="id_usuario_multado" required>
                        <option value="">- Selecciona un jugador -</option>
                        <?php while($user = mysqli_fetch_assoc($consulta_usuarios)): ?>
                            <option value="<?php echo $user['id_usuario']; ?>"><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <input type="text" name="motivo" placeholder="Motivo de la infraccion" required>
                    <input type="number" name="monto" step="0.01" placeholder="Monto a pagar (euros)" required>
                    <input type="date" name="fecha_infraccion" required>
                    <button type="submit">Registrar Multa</button>
                </form>
            </div>
        </section>

        <!-- CONFIRMAR PAGOS -->
        <section id="confirmarPagos" class="seccion">
            <h2>Confirmar Pagos</h2>
            <div style="overflow-x: auto;">
                <table class="tabla-pagos">
                    <thead><tr><th>Jugador</th><th>Motivo</th><th>Monto</th><th>Fecha Infraccion</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php if (mysqli_num_rows($consulta_pagos) == 0): ?>
                            <tr><td colspan="5" style="text-align: center;">No hay pagos pendientes de confirmar</td></tr>
                        <?php endif; ?>
                        <?php while($pago = mysqli_fetch_assoc($consulta_pagos)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pago['nombre'] . ' ' . $pago['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($pago['motivo']); ?></td>
                                <td><?php echo number_format($pago['monto_a_pagar'], 2); ?> €</td>
                                <td><?php echo date("d/m/Y", strtotime($pago['fecha_infraccion'])); ?></td>
                                <td>
                                    <a href="../../controlador/confirmarPago.php?id=<?php echo $pago['id_multa']; ?>&accion=confirmar" class="btn-confirmar" onclick="return confirm('¿Confirmar que este jugador ha pagado la multa?')">Confirmar</a>
                                    <a href="../../controlador/confirmarPago.php?id=<?php echo $pago['id_multa']; ?>&accion=rechazar" class="btn-rechazar" onclick="return confirm('¿Rechazar este pago?')">Rechazar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- QUEJAS -->
        <section id="quejas" class="seccion">
            <h2>Quejas de Jugadores</h2>
            <div style="overflow-x: auto;">
                <table class="tabla-quejas">
                    <thead><tr><th>Jugador</th><th>Multa</th><th>Monto</th><th>Motivo de la Queja</th><th>Fecha Queja</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php if (mysqli_num_rows($consulta_quejas) == 0): ?>
                            <tr><td colspan="6" style="text-align: center;">No hay quejas pendientes</td></tr>
                        <?php endif; ?>
                        <?php while($queja = mysqli_fetch_assoc($consulta_quejas)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($queja['nombre'] . ' ' . $queja['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($queja['motivo_multa']); ?></td>
                                <td><?php echo number_format($queja['monto_a_pagar'], 2); ?> €</td>
                                <td><?php echo htmlspecialchars($queja['motivo_queja']); ?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($queja['fecha_queja'])); ?></td>
                                <td>
                                    <a href="../../controlador/gestionarQuejaMultero.php?id=<?php echo $queja['id_queja']; ?>&accion=aceptar" class="btn-aceptar-queja" onclick="return confirm('¿Aceptar la queja? La multa sera anulada.')">Aceptar</a>
                                    <a href="../../controlador/gestionarQuejaMultero.php?id=<?php echo $queja['id_queja']; ?>&accion=denegar" class="btn-denegar-queja" onclick="return confirm('¿Denegar la queja? La multa seguira vigente.')">Denegar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- TABLA DE MULTAS -->
        <section id="tablaMultas" class="seccion">
            <h2>Todas las Multas</h2>
            <div class="resumen-totales">
                <div>Total Pagado: <span class="total-pagado"><?php echo number_format($totales['total_pagado'] ?? 0, 2); ?> €</span></div>
                <div>Pendiente de Cobro: <span class="total-pendiente"><?php echo number_format($totales['total_pendiente'] ?? 0, 2); ?> €</span></div>
                <div>Estimacion Total: <span><?php echo number_format(($totales['total_pagado'] ?? 0) + ($totales['total_pendiente'] ?? 0), 2); ?> €</span></div>
            </div>
            <div style="overflow-x: auto;">
                <table class="tabla-multas-completa">
                    <thead><tr><th>Jugador</th><th>Motivo</th><th>Monto</th><th>Estado</th><th>Fecha Infraccion</th><th>Multero ID</th></tr></thead>
                    <tbody>
                        <?php while($multa = mysqli_fetch_assoc($consulta_todas_multas)):
                            $badge_estado = '';
                            if($multa['estado'] === 'sin_pagar') $badge_estado = '<span class="badge-estado badge-sin-pagar">Sin pagar</span>';
                            elseif($multa['estado'] === 'pendiente') $badge_estado = '<span class="badge-estado badge-pendiente">Pendiente</span>';
                            else $badge_estado = '<span class="badge-estado badge-pagado">Pagado</span>';
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($multa['nombre'] . ' ' . $multa['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($multa['motivo']); ?></td>
                                <td><?php echo number_format($multa['monto_a_pagar'], 2); ?> €</td>
                                <td><?php echo $badge_estado; ?></td>
                                <td><?php echo date("d/m/Y", strtotime($multa['fecha_infraccion'])); ?></td>
                                <td><?php echo $multa['id_usuario_creador']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="footer-menu">
            <div class="footer-logo">
                <img src="../../img/logoPequeño.png" width="35" height="32" alt="isunKi">
                <img src="../../img/logoTexto.png" width="90" height="27" alt="isunKi" style="margin-left: 8px;">
            </div>
            <ul>
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#crearMulta">Crear Multa</a></li>
                <li><a href="#confirmarPagos">Confirmar Pagos</a></li>
                <li><a href="#quejas">Quejas</a></li>
                <li><a href="#tablaMultas">Tabla de Multas</a></li>
                <li><a href="../../controlador/cerrarSesion.php">Cerrar Sesión</a></li>
            </ul>
            <div class="footer-copy">
                <p>&copy; 2026 isunKi - Proyecto Final de Grado</p>
                <p>Gestion deportiva eficiente y transparente</p>
            </div>
        </div>
    </footer>
</div>

<script>
    const secciones = document.querySelectorAll('.seccion');
    const enlacesMenu = document.querySelectorAll('.menu-link');
    
    function activarEnlaceSegunScroll() {
        let indiceActual = -1;
        const scrollPos = window.scrollY + 100;
        secciones.forEach((seccion, index) => {
            const offsetTop = seccion.offsetTop;
            const offsetBottom = offsetTop + seccion.offsetHeight;
            if (scrollPos >= offsetTop && scrollPos < offsetBottom) indiceActual = index;
        });
        enlacesMenu.forEach(enlace => enlace.classList.remove('activo'));
        if (indiceActual >= 0 && enlacesMenu[indiceActual]) enlacesMenu[indiceActual].classList.add('activo');
    }
    
    window.addEventListener('scroll', activarEnlaceSegunScroll);
    activarEnlaceSegunScroll();
    
    document.querySelectorAll('.menu-link, .footer-menu a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId && targetId !== '#') {
                e.preventDefault();
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    const headerHeight = document.querySelector('.cabezera').offsetHeight;
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                    window.scrollTo({ top: targetPosition, behavior: 'smooth' });
                }
            }
        });
    });
</script>
</body>
</html>