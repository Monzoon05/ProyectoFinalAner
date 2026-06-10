<?php
session_start();
require '../modelo/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Consultar ranking de multas
$sql_ranking = "SELECT u.nombre, u.apellido, COUNT(m.id_multa) as total_multas, SUM(m.monto_a_pagar) as total_monto
                FROM usuarios u
                LEFT JOIN multas m ON u.id_usuario = m.id_usuario_multado
                WHERE u.rol = 'jugador'
                GROUP BY u.id_usuario
                ORDER BY total_multas DESC";
$ranking = mysqli_query($conn, $sql_ranking);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>isunKi - Ranking de Multas</title>
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
                <li><a href="ranking.php" style="color:#F9C74F;">Ranking</a></li>
                <li><a href="misMultas.php">Mis Multas</a></li>
                <?php if ($_SESSION['rol'] === 'multero' || $_SESSION['rol'] === 'admin'): ?>
                    <li><a href="crearMulta.php">Crear Multa</a></li>
                <?php endif; ?>
                <li><a href="../controlador/cerrarSesion.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="content">
        <h2>🏆 Ranking de Multas</h2>
        <div class="contenedorForm" style="max-width: 800px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #252525; color: #F9C74F;">
                        <th style="padding: 12px;">Posición</th>
                        <th style="padding: 12px;">Jugador</th>
                        <th style="padding: 12px;">Total Multas</th>
                        <th style="padding: 12px;">Monto Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $posicion = 1;
                    while($row = mysqli_fetch_assoc($ranking)): 
                    ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px; text-align: center;"><?php echo $posicion++; ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></td>
                        <td style="padding: 10px; text-align: center;"><?php echo $row['total_multas']; ?></td>
                        <td style="padding: 10px; text-align: right;"><?php echo number_format($row['total_monto'], 2); ?> €</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="main-footer">
        <p>&copy; 2026 isunKi - Proyecto Final de Grado</p>
    </footer>
</div>
</body>
</html>