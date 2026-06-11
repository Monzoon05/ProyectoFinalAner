<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>isunKi - Registro de Usuarios</title>
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
                    <li><a href="registro.php" class="registro" style="color:#F9C74F;">Registrarse</a></li>
                    <li><a href="login.php">Iniciar sesion</a></li>
                </ul>
            </nav>
        </header>

        <main class="content">
            <section id="inicio" class="seccion">
            <div class="tarjeta-bienvenida">
                <div class="tarjeta-bienvenida-contenido">
                    <h1>¡Bienvenida/o a isunki!</h1>
                    <p>Esta es app te permitira gestionar las multas de tu equipo y todo en una plataforma unica. Olvidate de excels complicados y mal gestionados.</p>
                    <p>Utiliza el menu superior para seleccionar Login o Registrarse.</p>
                </div>
                <div class="tarjeta-bienvenida-logo">
                    <img src="../img/logoPequeño.png" width="90" height="80" alt="isunKi Logo">
                </div>
            </div>
            </section>
            <h2 class="titulo">Crear Cuenta</h2>
            <div class="contenedorForm">
                <form action="../controlador/validarRegistro.php" method="POST">
                    
                    <?php
                        if(isset($_GET['error'])){
                            echo "<p style='color: red; margin-bottom: 15px; font-weight: bold; text-align: center;'>" . htmlspecialchars($_GET['error']) . "</p>";
                        }
                    ?>

                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" placeholder="Tu nombre">

                    <label for="apellido">Apellido</label>
                    <input type="text" name="apellido" id="apellido" placeholder="Tu apellido">

                    <label for="correo">Correo electrónico</label>                        
                    <input type="text" name="correo" id="correo" placeholder="ejemplo@isunki.com">
                
                    <label for="contraseña">Contraseña</label>
                    <input type="password" name="contraseña" id="contraseña" placeholder="••••••••">

                    <label for="rol">Rol</label>
                    <select name="rol" id="rol">
                        <option value="jugador">Jugador</option>
                        <option value="multero">Multero</option>
                    </select>
                
                    <button type="submit">Registrarse en IsunKi</button>
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