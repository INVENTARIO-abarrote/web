<?php
session_start();

// PROTEGER EL ACCESO
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: Inicio de Sesion.php");
    exit();
}

require_once __DIR__ . "/Global/configServer.php";
require_once __DIR__ . "/Global/connectionDB.php";
require_once __DIR__ . "/Global/categoria_controller.php";

// MENSAJE PARA MOSTRAR EN LA PANTALLA
$mensaje = "";

// =========================
// CREAR CATEGORÍA
// =========================
if (isset($_POST["accion"]) && $_POST["accion"] === "crear") {
    $nombre = $_POST["nombreCategoria"];
    $descripcion = $_POST["descripcionCategoria"];

    if (CategoriaController::crearCategoria($nombre, $descripcion)) {
        $mensaje = "Categoría creada correctamente ✔";
    } else {
        $mensaje = "Error al crear la categoría ✖";
    }
}

// =========================
// EDITAR CATEGORÍA
// =========================
if (isset($_POST["accion"]) && $_POST["accion"] === "editar") {
    $id = $_POST["idCategoria"];
    $nombre = $_POST["nombreCategoria"];
    $descripcion = $_POST["descripcionCategoria"];

    if (CategoriaController::editarCategoria($id, $nombre, $descripcion)) {
        $mensaje = "Categoría actualizada correctamente ✔";
    } else {
        $mensaje = "Error al actualizar la categoría ✖";
    }
}

// =========================
// ELIMINAR CATEGORÍA
// =========================
if (isset($_GET["eliminar"])) {
    $id = $_GET["eliminar"];
    if (CategoriaController::eliminarCategoria($id)) {
        $mensaje = "Categoría eliminada correctamente ✔";
    } else {
        $mensaje = "Error al eliminar la categoría ✖";
    }
}

// =========================
// OBTENER TODAS LAS CATEGORÍAS
// =========================
$categorias = CategoriaController::obtenerCategorias();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistema de Inventario - Categorías</title>
  <link rel="stylesheet" href="Categorias.css" />
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
  <a href="Categorias.php" class="active"><strong>CATEGORÍAS</strong></a>
   <a href="Usuarios.php" class="active"><strong>USUARIOS</strong></a>
</nav>

<main>
  <h2>CATEGORÍAS</h2>

  <!-- MENSAJE DE EXITO/ERROR -->
  <?php if ($mensaje !== ""): ?>
      <p style="background:#d9eaff; padding:10px; border-radius:8px; color:#0a3a8e;">
          <?php echo $mensaje; ?>
      </p>
  <?php endif; ?>

  <div class="actions">
    <button class="btn" id="btnAgregarCategoria">AGREGAR CATEGORÍA</button>
    <button class="btn" onclick="cerrarSesion()" style="float:right;">CERRAR SESIÓN</button>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>NOMBRE DE LA CATEGORÍA</th>
        <th>DESCRIPCIÓN</th>
        <th>ACCIONES</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($categorias as $cat): ?>
      <tr>
        <td><?= $cat['id_categoria']; ?></td>
        <td><?= $cat['nombre']; ?></td>
        <td><?= $cat['descripcion']; ?></td>
        <td>
          <button class="btn" onclick="editarCategoria(<?= $cat['id_categoria']; ?>, '<?= $cat['nombre']; ?>', '<?= $cat['descripcion']; ?>')">Editar</button>
          <a href="Categorias.php?eliminar=<?= $cat['id_categoria']; ?>"
             onclick="return confirm('¿Eliminar categoría?')"
             class="btn" style="background:#d9534f;">Eliminar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>

<!-- MODAL -->
<div id="modalCategoria" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3 id="tituloModal">Agregar Categoría</h3>

    <form id="formCategoria" action="Categorias.php" method="POST">
      <input type="hidden" name="accion" id="accion" value="crear">

      <label>ID</label>
      <input type="text" id="idCategoria" name="idCategoria" readonly style="background:#eee;">

      <label>Nombre de la categoría</label>
      <input type="text" id="nombreCategoria" name="nombreCategoria" required />

      <label>Descripción</label>
      <textarea id="descripcionCategoria" name="descripcionCategoria" rows="3"></textarea>

      <div style="margin-top:10px;">
        <button type="button" id="btnCancelarCategoria">Cancelar</button>
        <button type="submit" id="btnGuardarCategoria">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- JAVASCRIPT -->
<script>
const modal = document.getElementById("modalCategoria");
const btnAdd = document.getElementById("btnAgregarCategoria");
const btnCancelar = document.getElementById("btnCancelarCategoria");
const spanClose = document.querySelector(".close");

// Mostrar modal para agregar
btnAdd.addEventListener("click", () => {
    document.getElementById("tituloModal").innerText = "Agregar Categoría";
    document.getElementById("accion").value = "crear";
    document.getElementById("idCategoria").value = "";
    document.getElementById("formCategoria").reset();
    modal.style.display = "flex";
});

// Función editar
function editarCategoria(id, nombre, descripcion) {
    document.getElementById("tituloModal").innerText = "Editar Categoría";
    document.getElementById("accion").value = "editar";
    document.getElementById("idCategoria").value = id;
    document.getElementById("nombreCategoria").value = nombre;
    document.getElementById("descripcionCategoria").value = descripcion;
    modal.style.display = "flex";
}

// Cerrar modal
btnCancelar.addEventListener("click", () => modal.style.display = "none");
spanClose.addEventListener("click", () => modal.style.display = "none");
window.addEventListener("click", (e) => { if (e.target === modal) modal.style.display = "none"; });

// Logout
function cerrarSesion() {
  window.location.href = "logout.php";
}
</script>

<script src="fecha.js"></script>
<footer>© 2025 Sistema de Inventario</footer>

</body>
</html>
