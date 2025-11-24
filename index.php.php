<?php
session_start();
require_once __DIR__ . "/Global/configServer.php";
require_once __DIR__ . "/Global/connectionDB.php";
require_once __DIR__ . "/Global/user_controller.php";

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $password = trim($_POST['contrasena']);

    $usuario = UsuarioController::login($correo, $password);

    if ($usuario) {

        // Guardar la sesión del usuario
        $_SESSION['logueado'] = true;
        $_SESSION['usuario'] = $usuario['nombre'] . " " . $usuario['apellido'];
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['id_perfil'] = $usuario['id_perfil'];

        // Redirige con mensaje de inicio de sesión exitoso
        header("Location: Ventas.php?login=success");
        exit();
    } else {
        $mensaje = "Correo o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inventario - Inicio de Sesión</title>
    <link rel="stylesheet" href="Inicio de Sesion.css">
</head>
<body>

<header>
    SISTEMA DE INVENTARIO
    <div id="fecha" class="fecha"></div>
</header>

<nav>
    <a href="<?php echo rawurlencode('Inicio de Sesion.php'); ?>" class="active">INICIO</a>
    <a href="Ventas.php">VENTAS</a>
    <a href="Inventario.php">INVENTARIO</a>
    <a href="Categorias.php">CATEGORÍAS</a>
    <a href="Usuarios.php"><strong>USUARIOS</strong></a>
</nav>


<main class="login-main">
    <div class="login-container">
        <h2>Iniciar Sesión</h2>

        <!-- Mensaje de error -->
        <?php if ($mensaje !== ""): ?>
            <p style="color:red;text-align:center;"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="correo">Correo electrónico:</label>
            <input type="email" id="correo" name="correo" placeholder="Ejemplo: usuario@gmail.com" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" placeholder="Ingresa tu contraseña" required>

            <button type="submit">Entrar</button>
            <div style="text-align:center; margin-top:15px;">
    <a href="Registro.php"
       style="
           display:inline-block;
           padding:10px 15px;
           background:#1e63e6;
           color:white;
           border-radius:8px;
           text-decoration:none;
           font-size:14px;
       ">
       ¿No eres usuario? Regístrate aquí
    </a>
</div>
        </form>
    </div>
</main>

<footer>
    © 2025 Sistema de Inventario
</footer>

<script src="fecha.js"></script>

</body>
</html>
