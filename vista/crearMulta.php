<?php
session_start();
require '../modelo/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['rol'] !== 'multero' && $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$sql_usuarios = "SELECT id_usuario, nombre, apellido FROM usuarios WHERE id_usuario != 1 ORDER BY nombre ASC";
$consulta_usuarios = mysqli_query($conn, $sql_usuarios);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>isunKi - Crear Multa</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <div class="contenedorPrincipal">
        <header class="cabezera">
            <div class="contenedorLogo">
                <div class="logo-circular"><a href="perfiles/multero.php"><img src="../img/logoPequeño.png" width="56" height="50"></a></div>
                <div class="logo-escrito"><a href="perfiles/multero.php"><img src="../img/logoTexto.png" width="130" height="40"></a></div>
            </div>
            <nav class="navegador">
                <ul>
                    <li><a href="ranking.php">Ranking</a></li>
                    <li><a href="misMultas.php">Mis Multas</a></li>
                    <li><a href="crearMulta.php" style="color:#F9C74F;">Crear Multa</a></li>
                    <li><a href="../controlador/cerrarSesion.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </header>

        <main class="content">
            <h2 class="titulo">Registrar Nueva Multa</h2>
            <div class="contenedorForm">
                
                <form id="formMulta" action="../controlador/validarMulta.php" method="POST">
                    
                    <?php
                        if(isset($_GET['error'])){
                            echo "<p style='color: red; margin-bottom: 15px; font-weight: bold; text-align: center;'>" . htmlspecialchars($_GET['error']) . "</p>";
                        }
                        if(isset($_GET['mensaje'])){
                            echo "<p style='color: green; margin-bottom: 15px; font-weight: bold; text-align: center;'>" . htmlspecialchars($_GET['mensaje']) . "</p>";
                        }
                    ?>

                    <label>Multero Creador</label>
                    <input type="text" value="<?php echo htmlspecialchars($_SESSION['usuario']); ?>" disabled style="background-color: #e9ecef; cursor: not-allowed;">

                    <label for="id_usuario_multado">Usuario Multado</label>
                    <select name="id_usuario_multado" id="id_usuario_multado">
                        <option value="">- Selecciona un usuario -</option>
                        <?php while($user = mysqli_fetch_assoc($consulta_usuarios)): ?>
                            <option value="<?php echo $user['id_usuario']; ?>">
                                <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <label for="motivo">Motivo de la infracción</label>
                    <input type="text" name="motivo" id="motivo" placeholder="Ej: Llegar tarde">

                    <label for="monto">Monto a pagar (€)</label>
                    <input type="number" name="monto" id="monto" step="0.01" placeholder="0.00">

                    <label for="fecha_infraccion">Fecha de la infracción</label>
                    <input type="date" name="fecha_infraccion" id="fecha_infraccion">

                    <button type="submit">Sancionar</button>
                </form>
            </div>
        </main>

        <footer class="main-footer">
            <p>&copy; 2026 isunKi - Proyecto Final de Grado</p>
        </footer>
    </div>

    <script>
        document.getElementById('formMulta').addEventListener('submit', function(evento) {
            var usuario = document.getElementById('id_usuario_multado').value;
            var motivo = document.getElementById('motivo').value.trim();
            var monto = document.getElementById('monto').value;
            var fecha = document.getElementById('fecha_infraccion').value;

            if (usuario === "") {
                evento.preventDefault();
                alert("Por favor, selecciona un usuario.");
                return;
            }

            if (motivo === "") {
                evento.preventDefault();
                alert("El campo motivo no puede estar vacío.");
                return;
            }

            if (monto === "" || parseFloat(monto) <= 0) {
                evento.preventDefault();
                alert("El monto debe ser un número mayor que 0.");
                return;
            }

            if (fecha === "") {
                evento.preventDefault();
                alert("Debes seleccionar la fecha de la infracción.");
                return;
            }
        });
    </script>
</body>
</html>