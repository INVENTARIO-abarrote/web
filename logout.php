<?php
session_start();

// Guardar el nombre del usuario ANTES de cerrar sesión
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : "usuario";

// Cerrar la sesión
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Sesión Cerrada</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #eef3ff;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .box {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        width: 350px;
    }
    h2 {
        color: #1a3a6e;
    }
    p {
        margin-top: 10px;
        font-size: 17px;
        color: #2a4a7a;
    }
    .btn {
        margin-top: 20px;
        background: #1e63e6;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-size: 16px;
    }
</style>
</head>
<body>

<div class="box">
    <h2>¡Sesión Cerrada!</h2>
    <p>Adiós <strong><?php echo htmlspecialchars($usuario); ?></strong>, hasta pronto 👋</p>

    <button class="btn" onclick="window.location.href='Inicio de Sesion.php'">Volver al Inicio de Sesión</button>
</div>

</body>
</html>
