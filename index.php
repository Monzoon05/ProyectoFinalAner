<?php
    session_start();
    require'modelo/conexion.php';

    if(!isset($_SESSION['usuario'])){
        header("Location:vista/login.php?");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>isunKi - Gestión de Multas</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="contenedorPrincipal">
        <header class="cabezera">
            <div class="contenedorLogo">
                <div class="logo-circular"><a href="index.php"><img src="img/logoPequeño.png" width="56" height="50"></a></div>
                <div class="logo-escrito"><a href="index.php"><img src="img/logoTexto.png" width="130" height="40"></a></div>
            </div>
            
            <nav class="navegador">
                <ul>
                    <li><a href="index.php?page=ranking">Ranking</a></li>
                    <li><a href="index.php?page=mis-multas">Mis Multas</a></li>
                </ul>
            </nav>
        </header>

        <main class="content">
            <?php
                echo "<h1>Bienvenido ". $_SESSION['usuario'] ."</h1>";
            ?>

        </main>

        <footer class="main-footer">
            <p>&copy; 2026 isunKi - Proyecto Final de Grado</p>
            <p>Diseñado para la gestión deportiva eficiente.</p>
        </footer>

    </div>

</body>
</html>