<?php
session_start();
require '../../modelo/conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'jugador') {
    header("Location: ../../index.php");
    exit();
}

$id_jugador = $_SESSION['usuario_id'];

$sql_stats = "SELECT 
                COUNT(*) as total_multas,
                SUM(CASE WHEN estado = 'pagado' THEN 1 ELSE 0 END) as pagadas,
                SUM(CASE WHEN estado IN ('sin_pagar', 'pendiente') THEN 1 ELSE 0 END) as sin_pagar,
                SUM(CASE WHEN estado = 'pagado' THEN monto_a_pagar ELSE 0 END) as total_pagado,
                SUM(CASE WHEN estado IN ('sin_pagar', 'pendiente') THEN monto_a_pagar ELSE 0 END) as total_a_deber
              FROM multas 
              WHERE id_usuario_multado = $id_jugador";
$stats = mysqli_fetch_assoc(mysqli_query($conn, $sql_stats));

$sql_ranking = "SELECT 
                    u.nombre,
                    u.apellido,
                    COUNT(m.id_multa) as total_multas,
                    SUM(m.monto_a_pagar) as monto_total
                FROM usuarios u
                LEFT JOIN multas m ON u.id_usuario = m.id_usuario_multado
                WHERE u.rol = 'jugador'
                GROUP BY u.id_usuario
                ORDER BY monto_total DESC, total_multas DESC";
$consulta_ranking = mysqli_query($conn, $sql_ranking);

$sql_multas = "SELECT m.*, u.nombre AS nombre_creador, u.apellido AS apellido_creador 
               FROM multas m
               JOIN usuarios u ON m.id_usuario_creador = u.id_usuario
               WHERE m.id_usuario_multado = '$id_jugador'
               ORDER BY m.fecha_infraccion DESC";
$consulta_multas = mysqli_query($conn, $sql_multas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>isunKi - Panel de Jugador</title>
    <link rel="stylesheet" href="../../css/estilos.css">
</head>
<body>
<div class="contenedorPrincipal">
    <header class="cabezera">
        <div class="contenedorLogo">
            <div class="logo-circular"><a href="jugador.php"><img src="../../img/logoPequeño.png" width="56" height="50" alt="Logo"></a></div>
            <div class="logo-escrito"><a href="jugador.php"><img src="../../img/logoTexto.png" width="130" height="40" alt="isunKi"></a></div>
        </div>
        <nav class="navegador">
            <ul>
                <li><a href="#inicio" class="menu-link" data-seccion="inicio">Inicio</a></li>
                <li><a href="#estadisticas" class="menu-link" data-seccion="estadisticas">Estadísticas</a></li>
                <li><a href="#ranking" class="menu-link" data-seccion="ranking">Ranking</a></li>
                <li><a href="#multas" class="menu-link" data-seccion="multas">Mis Multas</a></li>
                <li><a href="../../controlador/cerrarSesion.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="content">
        <?php if(isset($_GET['mensaje'])): ?>
            <div style="background-color: #2A9D8F; color: white; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <?php echo htmlspecialchars($_GET['mensaje']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div style="background-color: #E63946; color: white; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <section id="inicio" class="seccion">
            <div class="tarjeta-bienvenida">
                <div class="tarjeta-bienvenida-contenido">
                    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
                    <p><strong>isunKi</strong> es la plataforma diseñada para ayudarte a gestionar tus multas de forma fácil y rápida.</p>
                    <p>Desde aquí podrás consultar el ranking del equipo, tus estadísticas personales, todas tus multas y realizar pagos.</p>
                    <p>Mantén tu historial al día y evita acumulaciones. La transparencia y el orden son nuestra prioridad.</p>
                </div>
                <div class="tarjeta-bienvenida-logo">
                    <img src="../../img/logoPequeño.png" width="90" height="80" alt="isunKi Logo">
                </div>
            </div>
        </section>

        <section id="estadisticas" class="seccion">
            <h2>Mis Estadísticas</h2>
            <div class="flex-center">
                <div class="tarjeta-estadistica">
                    <h3>Total Multas</h3>
                    <p class="numero"><?php echo $stats['total_multas'] ?? 0; ?></p>
                </div>
                <div class="tarjeta-estadistica">
                    <h3>Pagadas</h3>
                    <p class="numero verde"><?php echo $stats['pagadas'] ?? 0; ?></p>
                </div>
                <div class="tarjeta-estadistica">
                    <h3>Sin Pagar</h3>
                    <p class="numero rojo"><?php echo $stats['sin_pagar'] ?? 0; ?></p>
                </div>
                <div class="tarjeta-estadistica">
                    <h3>Dinero Aportado</h3>
                    <p class="numero verde"><?php echo number_format($stats['total_pagado'] ?? 0, 2); ?> €</p>
                </div>
                <div class="tarjeta-estadistica">
                    <h3>Dinero a Deber</h3>
                    <p class="numero rojo"><?php echo number_format($stats['total_a_deber'] ?? 0, 2); ?> €</p>
                </div>
            </div>
        </section>

        <section id="ranking" class="seccion">
            <h2>Ranking del Equipo</h2>
            <div style="overflow-x: auto;">
                <table class="tabla-ranking">
                    <thead>
                        <tr><th>Posición</th><th>Jugador</th><th>Total Multas</th><th>Monto Total</th></tr>
                    </thead>
                    <tbody>
                        <?php $posicion = 0; while($jugador = mysqli_fetch_assoc($consulta_ranking)): $posicion++; ?>
                        <tr><td><?php echo $posicion; ?></td><td><?php echo htmlspecialchars($jugador['nombre'] . ' ' . $jugador['apellido']); ?></td><td><?php echo $jugador['total_multas'] ?? 0; ?></td><td><?php echo number_format($jugador['monto_total'] ?? 0, 2); ?> €</td></tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="multas" class="seccion">
            <h2>Mis Multas</h2>
            <div class="contenedorTarjetas">
                <?php if (mysqli_num_rows($consulta_multas) == 0): ?>
                    <p class="text-center" style="color:#555; padding: 40px;">Enhorabuena. No tienes ninguna multa registrada.</p>
                <?php endif; ?>
                <?php while($multa = mysqli_fetch_assoc($consulta_multas)):
                    $clase_estado = '';
                    $texto_estado = '';
                    if ($multa['estado'] === 'sin_pagar') {
                        $clase_estado = 'estado-rojo';
                        $texto_estado = '<span class="badge-estado badge-sin-pagar">Sin pagar</span>';
                    } elseif ($multa['estado'] === 'pendiente') {
                        $clase_estado = 'estado-amarillo';
                        $texto_estado = '<span class="badge-estado badge-pendiente">Pendiente de confirmación</span>';
                    } elseif ($multa['estado'] === 'pagado') {
                        $clase_estado = 'estado-verde';
                        $texto_estado = '<span class="badge-estado badge-pagado">Pagado</span>';
                    }
                ?>
                <div class="tarjetaMulta <?php echo $clase_estado; ?>">
                    <div class="cuerpoTarjeta">
                        <h3><?php echo htmlspecialchars($multa['motivo']); ?></h3>
                        <p class="monto"><?php echo number_format($multa['monto_a_pagar'], 2); ?> €</p>
                        <p><strong>Puesta por:</strong> <?php echo htmlspecialchars($multa['nombre_creador'] . ' ' . $multa['apellido_creador']); ?></p>
                        <p><strong>Fecha infracción:</strong> <?php echo date("d/m/Y", strtotime($multa['fecha_infraccion'])); ?></p>
                        <p><strong>Estado:</strong> <?php echo $texto_estado; ?></p>
                    </div>
                    <div class="accionesTarjeta">
                        <?php if ($multa['estado'] === 'sin_pagar'): ?>
                            <a href="../../controlador/cambiarEstadoMulta.php?id=<?php echo $multa['id_multa']; ?>" class="btn-tarjeta btn-pagar">Marcar como pagado</a>
                        <?php endif; ?>
                        <?php if ($multa['estado'] !== 'pagado'): ?>
                            <a href="#" onclick="enviarQueja(<?php echo $multa['id_multa']; ?>, '<?php echo htmlspecialchars($multa['motivo']); ?>')" class="btn-tarjeta btn-queja">Enviar queja</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
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
                <li><a href="#estadisticas">Estadísticas</a></li>
                <li><a href="#ranking">Ranking</a></li>
                <li><a href="#multas">Mis Multas</a></li>
                <li><a href="../../controlador/cerrarSesion.php">Cerrar Sesión</a></li>
            </ul>
            <div class="footer-copy">
                <p>&copy; 2026 isunKi - Proyecto Final de Grado</p>
                <p>Gestión deportiva eficiente y transparente</p>
            </div>
        </div>
    </footer>
</div>

<script>
    function enviarQueja(idMulta, motivoMulta) {
        let motivo = prompt("Escribe el motivo de tu queja para la multa:\n\n" + motivoMulta);
        if (motivo === null) return;
        if (motivo.trim() === "") { alert("Debes escribir un motivo para la queja"); return; }
        if (motivo.trim().length < 10) { alert("El motivo debe tener al menos 10 caracteres"); return; }
        window.location.href = "../../controlador/gestionarQueja.php?id_multa=" + idMulta + "&motivo=" + encodeURIComponent(motivo);
    }
    
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