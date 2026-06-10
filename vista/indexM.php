<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>isunKi - Gestión de Multas</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <div class="contenedorPrincipal">
        <header class="cabezera">
            <div class="contenedorLogo">
                <div class="logo-circular"><a href="../index.php"><img src="../img/logoPequeño.png" width="56" height="50"></a></div>
                <div class="logo-escrito"><a href="../index.php"><img src="../img/logoTexto.png" width="130" height="40"></a></div>
            </div>
            
            <nav class="navegador">
                <ul>
                    <li><a href="registro.php">Registrarse</a></li>
                    <li><a href="login.php" class="login" style="color:#F9C74F;">Iniciar sesion</a></li>
                </ul>
            </nav>
        </header>

        <main class="content">
            <div class="contenedorImagen">
                <img src="../img/perfil.png" width="120">
            </div>
            <h2 class="titulo">Iniciar sesion</h2>
            <div class="contenedorForm">
                <form action="../controlador/validarLogin.php" method="POST">
                    <?php
                        if(isset($_GET['error'])){
                            echo "<p style='color: red;'>" . htmlspecialchars($_GET['error']) . "</p>";
                        }
                    ?>
                    <label for="correo">Correo electrónico</label>                        
                    <input type="text" name="correo" id="correo" placeholder="ejemplo@isunki.com">
                
                    <label for="contraseña">Contraseña</label>
                    <input type="password" name="contraseña" id="contraseña" placeholder="••••••••">
                
                    <button type="submit">Entrar a IsunKi</button>
                </form>
            </div>
        </main>

        <footer class="main-footer">
            <p>&copy; 2026 isunKi - Proyecto Final de Grado</p>
            <p>Diseñado para la gestión deportiva eficiente.</p>
        </footer>

    </div>

</body>
</html>