<?php
session_start();

// PROTEGER EL ACCESO
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: Inicio de Sesion.php");
    exit();
}

require_once __DIR__ . "/Global/configServer.php";
require_once __DIR__ . "/Global/connectionDB.php";
require_once __DIR__ . "/Global/user_controller.php";

$mensaje = "";

// =========================
// ELIMINAR USUARIO
// =========================
if (isset($_GET["eliminar"])) {
    $id = $_GET["eliminar"];
    if (UsuarioController::eliminarUsuario($id)) {
        $mensaje = "Usuario eliminado correctamente ✔";
    } else {
        $mensaje = "Error al eliminar usuario ✖";
    }
}

// =========================
// EDITAR USUARIO
// =========================
if (isset($_POST["accion"]) && $_POST["accion"] === "editar") {

    $id = $_POST["id_usuario"];
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $edad = $_POST["edad"];
    $correo = $_POST["correo"];
    $rol = $_POST["id_perfil"];

    // Contraseña especial para permitir cambio de rol
    $claveEspecial = "12345";  

    if ($_POST["claveRol"] === $claveEspecial) {
        UsuarioController::editarUsuario($id, $nombre, $apellido, $edad, $correo, $rol);
    } else {
        $mensaje = "Clave incorrecta para modificar el rol ❌";
    }

    if (!empty($_POST["nueva_contrasena"])) {
        UsuarioController::cambiarPassword($id, $_POST["nueva_contrasena"]);
    }

    if ($mensaje === "")
        $mensaje = "Usuario actualizado correctamente ✔";
}

// =========================
// OBTENER USUARIOS
// =========================
$usuarios = UsuarioController::obtenerUsuarios();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Sistema de Inventario</title>
    <link rel="stylesheet" href="Categorias.css">
</head>

<body>

<header>
    SISTEMA DE INVENTARIO
    <div id="fecha" class="fecha"></div>
</header>

<nav class="sidebar">
    <a href="Inicio de Sesion.php">INICIO</a>
    <a href="Ventas.php">VENTAS</a>
    <a href="Inventario.php">INVENTARIO</a>
    <a href="Categorias.php">CATEGORÍAS</a>
    <a href="Usuarios.php" class="active"><strong>USUARIOS</strong></a>

    <!-- BOTÓN DE CERRAR SESIÓN -->
    <a href="logout.php" style="background:#d9534f; color:white; margin-top:20px;">
        CERRAR SESIÓN
    </a>
</nav>

<main>
    <h2>GESTIÓN DE USUARIOS</h2>

    <?php if ($mensaje !== ""): ?>
        <p style="background:#d9eaff;padding:10px;border-radius:8px;color:#0a3a8e;">
            <?= $mensaje ?>
        </p>
    <?php endif; ?>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>NOMBRE</th>
            <th>APELLIDO</th>
            <th>EDAD</th>
            <th>CORREO</th>
            <th>ROL</th>
            <th>ACCIONES</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= $u["id_usuario"]; ?></td>
                <td><?= $u["nombre"]; ?></td>
                <td><?= $u["apellido"]; ?></td>
                <td><?= $u["edad"]; ?></td>
                <td><?= $u["correo"]; ?></td>
                <td><?= $u["perfil"]; ?></td>
                <td>
                    <button class="btn"
                            onclick='editarUsuario(
                                <?= $u["id_usuario"]; ?>,
                                "<?= $u["nombre"]; ?>",
                                "<?= $u["apellido"]; ?>",
                                <?= $u["edad"]; ?>,
                                "<?= $u["correo"]; ?>",
                                <?= $u["id_perfil"]; ?>
                            )'>
                        Editar
                    </button>

                    <a href="Usuarios.php?eliminar=<?= $u["id_usuario"]; ?>"
                       onclick="return confirm('¿Eliminar usuario?')"
                       class="btn"
                       style="background:#d9534f;">
                        Eliminar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>

<!-- MODAL EDITAR -->
<div id="modalUsuario" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>

        <h3>Editar Usuario</h3>

        <form method="POST" action="Usuarios.php">
            <input type="hidden" name="accion" value="editar">
            <input type="hidden" id="id_usuario" name="id_usuario">

            <label>Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label>Apellido:</label>
            <input type="text" id="apellido" name="apellido" required>

            <label>Edad:</label>
            <input type="number" id="edad" name="edad" required>

            <label>Correo:</label>
            <input type="email" id="correo" name="correo" required>

            <label>Nuevo Rol:</label>
            <select id="id_perfil" name="id_perfil">
                <option value="1">Jefe</option>
                <option value="2">Administrador</option>
                <option value="3">Empleado</option>
            </select>

            <label>Clave especial para cambiar rol:</label>
            <input type="password" name="claveRol" placeholder="Clave requerida">

            <label>Nueva Contraseña (opcional):</label>
            <input type="password" name="nueva_contrasena">

            <br><br>
            <button type="submit" class="btn">Guardar Cambios</button>
            <button type="button" class="btn" id="btnCerrarModal">Cancelar</button>
        </form>
    </div>
</div>

<script>
// Modal
const modal = document.getElementById("modalUsuario");
const cerrarModal = document.getElementById("btnCerrarModal");
const xClose = document.querySelector(".close");

function editarUsuario(id, nombre, apellido, edad, correo, rol) {
    document.getElementById("id_usuario").value = id;
    document.getElementById("nombre").value = nombre;
    document.getElementById("apellido").value = apellido;
    document.getElementById("edad").value = edad;
    document.getElementById("correo").value = correo;
    document.getElementById("id_perfil").value = rol;

    modal.style.display = "flex";
}

cerrarModal.onclick = () => modal.style.display = "none";
xClose.onclick = () => modal.style.display = "none";

window.onclick = function(e) {
    if (e.target === modal) modal.style.display = "none";
};
</script>

<script src="fecha.js"></script>
<footer>© 2025 Sistema de Inventario</footer>

</body>
</html>
