<?php
session_start();

// PROTEGER EL ACCESO
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: Inicio de Sesion.php");
    exit();
}

// CONTROLADORES
require_once __DIR__ . "/Global/configServer.php";
require_once __DIR__ . "/Global/connectionDB.php";
require_once __DIR__ . "/Global/producto_controller.php";
require_once __DIR__ . "/Global/categoria_controller.php";

$mensaje = "";

// ============================
// CREAR PRODUCTO
// ============================
if (isset($_POST["accion"]) && $_POST["accion"] === "crear") {
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $id_categoria = $_POST["categoria"];

    if (ProductoController::crearProducto($nombre, $descripcion, $precio, $stock, $id_categoria)) {
        $mensaje = "Producto creado correctamente ✔";
    } else {
        $mensaje = "Error al crear producto ✖";
    }
}

// ============================
// EDITAR PRODUCTO
// ============================
if (isset($_POST["accion"]) && $_POST["accion"] === "editar") {
    $id = $_POST["id_producto"];
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $id_categoria = $_POST["categoria"];

    if (ProductoController::editarProducto($id, $nombre, $descripcion, $precio, $stock, $id_categoria)) {
        $mensaje = "Producto actualizado correctamente ✔";
    } else {
        $mensaje = "Error al actualizar producto ✖";
    }
}

// ============================
// ELIMINAR
// ============================
if (isset($_GET["eliminar"])) {
    $id = $_GET["eliminar"];
    if (ProductoController::eliminarProducto($id)) {
        $mensaje = "Producto eliminado correctamente ✔";
    } else {
        $mensaje = "Error al eliminar producto ✖";
    }
}

// ============================
// ENTRADA INVENTARIO
// ============================
if (isset($_POST["accion"]) && $_POST["accion"] === "entrada") {
    $id = $_POST["id_producto"];
    $cantidad = $_POST["cantidad"];

    if ($cantidad <= 0) {
        $mensaje = "La cantidad debe ser mayor a 0 ✖";
    } else if (ProductoController::agregarEntrada($id, $cantidad)) {
        $mensaje = "Entrada registrada ✔";
    } else {
        $mensaje = "Error al registrar entrada ✖";
    }
}

// ============================
// SALIDA INVENTARIO
// ============================
if (isset($_POST["accion"]) && $_POST["accion"] === "salida") {
    $id = $_POST["id_producto"];
    $cantidad = $_POST["cantidad"];

    if ($cantidad <= 0) {
        $mensaje = "La cantidad debe ser mayor a 0 ✖";
    } else {
        $res = ProductoController::agregarSalida($id, $cantidad);

        if ($res === "NO_STOCK") {
            $mensaje = "ERROR: Stock insuficiente para realizar la salida ✖";
        } else {
            $mensaje = "Salida registrada ✔";
        }
    }
}

// TRAER LISTA
$productos = ProductoController::obtenerProductos();
$categorias = CategoriaController::obtenerCategorias();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Inventario</title>
<link rel="stylesheet" href="Inventario.css">


</head>
<body>

<header>
    SISTEMA DE INVENTARIO
    <div id="fecha" class="fecha"></div>
</header>

<nav class="sidebar">
    <a href="Inicio de Sesion.php">INICIO</a>
    <a href="Ventas.php">VENTAS</a>
    <a href="Inventario.php" class="active"><strong>INVENTARIO</strong></a>
    <a href="Categorias.php">CATEGORÍAS</a>
     <a href="Usuarios.php" class="active"><strong>USUARIOS</strong></a>
</nav>

<main>
    <h2>INVENTARIO</h2>

    <?php if ($mensaje !== ""): ?>
        <p style="background:#d9eaff;padding:10px;border-radius:8px;color:#0a3a8e;">
            <?= $mensaje ?>
        </p>
    <?php endif; ?>

    <div class="actions">
        <button class="btn" id="btnAgregar">AGREGAR PRODUCTO</button>
        <button class="btn" onclick="cerrarSesion()" style="float:right;">CERRAR SESIÓN</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>NOMBRE</th>
                <th>CATEGORÍA</th>
                <th>PRECIO</th>
                <th>STOCK</th>
                <th>ACCIONES</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($productos as $p): ?>
            <tr>
                <td><?= $p['id_producto'] ?></td>
                <td><?= $p['nombre'] ?></td>
                <td><?= $p['categoria'] ?></td>
                <td>$<?= number_format($p['precio'],2) ?></td>
                <td><?= $p['stock'] ?></td>
<td>
<button class="btn"
    onclick="editarProducto(
        <?= $p['id_producto'] ?>,
        '<?= $p['nombre'] ?>',
        '<?= $p['descripcion'] ?>',
        <?= $p['precio'] ?>,
        <?= $p['stock'] ?>,
        <?= $p['id_categoria'] ?>
    )">
    Editar
</button>

<a href="Inventario.php?eliminar=<?= $p['id_producto'] ?>"
    onclick="return confirm('¿Eliminar producto?')"
    class="btn" style="background:#d9534f;">Eliminar</a>

<button class="btn"
    onclick="abrirEntrada(
        <?= $p['id_producto'] ?>,
        '<?= $p['nombre'] ?>',
        <?= $p['stock'] ?>,
        <?= $p['precio'] ?>
    )">
    Entrada
</button>

<button class="btn"
    onclick="abrirSalida(
        <?= $p['id_producto'] ?>,
        '<?= $p['nombre'] ?>',
        <?= $p['stock'] ?>,
        <?= $p['precio'] ?>
    )">
    Salida
</button>
</td>

            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<!-- MODAL PRODUCTO -->
<div id="modalProducto" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>

    <h3 id="tituloModal">Agregar Producto</h3>

    <form method="POST" action="Inventario.php" id="formProducto">

      <input type="hidden" name="accion" id="accion" value="crear">
      <input type="hidden" name="id_producto" id="id_producto">

      <label>Nombre:</label>
      <input type="text" name="nombre" id="nombre" required>

      <label>Descripción:</label>
      <textarea name="descripcion" id="descripcion"></textarea>

      <label>Precio:</label>
      <input type="number" step="0.01" name="precio" id="precio" required>

      <label>Stock:</label>
      <input type="number" name="stock" id="stock" required>

      <label>Categoría:</label>
      <select name="categoria" id="categoria" required>
        <option value="">Seleccione...</option>
        <?php foreach($categorias as $c): ?>
        <option value="<?= $c['id_categoria'] ?>"><?= $c['nombre'] ?></option>
        <?php endforeach; ?>
      </select>

      <button type="submit" class="btn" style="margin-top:15px;">Guardar</button>

    </form>
  </div>
</div>

<script>
// Modal Producto
const modal = document.getElementById("modalProducto");
const btnAdd = document.getElementById("btnAgregar");
const close = document.querySelector(".close");

// Abrir modal para crear
btnAdd.onclick = () => {
    document.getElementById("tituloModal").innerHTML = "Agregar Producto";
    document.getElementById("accion").value = "crear";
    document.getElementById("formProducto").reset();
    modal.style.display = "flex";
};

// Editar producto
function editarProducto(id, nombre, descripcion, precio, stock, categoria){
    document.getElementById("tituloModal").innerHTML = "Editar Producto";
    document.getElementById("accion").value = "editar";
    document.getElementById("id_producto").value = id;
    document.getElementById("nombre").value = nombre;
    document.getElementById("descripcion").value = descripcion;
    document.getElementById("precio").value = precio;
    document.getElementById("stock").value = stock;
    document.getElementById("categoria").value = categoria;
    modal.style.display = "flex";
}

// Cerrar modal producto
close.onclick = () => modal.style.display = "none";
window.onclick = (e) => { if(e.target===modal) modal.style.display="none"; };

// Logout
function cerrarSesion(){ window.location.href = "logout.php"; }
</script>

<script src="fecha.js"></script>

<footer>© 2025 Sistema de Inventario</footer>


<!-- ============================
     MODAL ENTRADA DE INVENTARIO
=============================== -->
<div id="modalEntrada" class="modal">
  <div class="modal-content">
    <span class="closeEntrada">&times;</span>

    <h3>Entrada de Inventario</h3>

    <form method="POST" action="Inventario.php">

      <input type="hidden" name="accion" value="entrada">

      <label>ID Producto:</label>
      <input type="text" name="id_producto" id="entrada_id" readonly required>

      <label>Nombre:</label>
      <input type="text" id="entrada_nombre" readonly>

      <label>Stock Actual:</label>
      <input type="number" id="entrada_stock" readonly>

      <label>Precio:</label>
      <input type="number" id="entrada_precio" readonly>

      <label>Cantidad a ingresar:</label>
      <input type="number" name="cantidad" id="entrada_cantidad" required min="1">

      <label>Stock Final:</label>
      <input type="number" id="entrada_final" readonly>

      <label>Valor total de entrada ($):</label>
      <input type="number" id="entrada_valor" readonly>

      <button type="submit" class="btn" style="margin-top:15px;">Registrar Entrada</button>

    </form>
  </div>
</div>


<!-- ============================
     MODAL SALIDA DE INVENTARIO
=============================== -->
<div id="modalSalida" class="modal">
  <div class="modal-content">
    <span class="closeSalida">&times;</span>

    <h3>Salida de Inventario</h3>

    <form method="POST" action="Inventario.php">

      <input type="hidden" name="accion" value="salida">

      <label>ID Producto:</label>
      <input type="text" name="id_producto" id="salida_id" readonly required>

      <label>Nombre:</label>
      <input type="text" id="salida_nombre" readonly>

      <label>Stock Actual:</label>
      <input type="number" id="salida_stock" readonly>

      <label>Precio:</label>
      <input type="number" id="salida_precio" readonly>

      <label>Cantidad a retirar:</label>
      <input type="number" name="cantidad" id="salida_cantidad" required min="1">

      <label>Stock Final:</label>
      <input type="number" id="salida_final" readonly>

      <label>Valor total de salida ($):</label>
      <input type="number" id="salida_valor" readonly>

      <button type="submit" class="btn" style="margin-top:15px;">Registrar Salida</button>

    </form>
  </div>
</div>


<script>
// ============================
// INICIALIZACIÓN - ESPERAR A QUE EL DOM ESTÉ LISTO
// ============================
document.addEventListener('DOMContentLoaded', function() {
    // Asegurar que todos los modales estén ocultos al cargar
    ocultarTodosLosModales();
    
    // Inicializar event listeners
    inicializarEventListeners();
});

function ocultarTodosLosModales() {
    const modales = document.querySelectorAll('.modal');
    modales.forEach(modal => {
        modal.style.display = 'none';
    });
}

function inicializarEventListeners() {
    // ============================
    // MODAL PRODUCTO
    // ============================
    const modalProducto = document.getElementById("modalProducto");
    const btnAdd = document.getElementById("btnAgregar");
    const closeProducto = document.querySelector(".close");

    if (btnAdd) {
        btnAdd.onclick = () => {
            document.getElementById("tituloModal").innerHTML = "Agregar Producto";
            document.getElementById("accion").value = "crear";
            document.getElementById("formProducto").reset();
            modalProducto.style.display = "flex";
        };
    }

    if (closeProducto) {
        closeProducto.onclick = () => modalProducto.style.display = "none";
    }

    // ============================
    // MODAL ENTRADA
    // ============================
    const modalEntrada = document.getElementById("modalEntrada");
    const closeEntrada = document.querySelector(".closeEntrada");
    const entradaCantidad = document.getElementById("entrada_cantidad");

    if (closeEntrada) {
        closeEntrada.onclick = () => modalEntrada.style.display = "none";
    }

    if (entradaCantidad) {
        entradaCantidad.oninput = function () {
            let cant = Number(this.value);
            let stock = Number(document.getElementById("entrada_stock").value);
            let precio = Number(document.getElementById("entrada_precio").value);

            if (cant < 1) { 
                this.value = 1; 
                cant = 1; 
            }

            document.getElementById("entrada_final").value = stock + cant;
            document.getElementById("entrada_valor").value = cant * precio;
        };
    }

    // ============================
    // MODAL SALIDA
    // ============================
    const modalSalida = document.getElementById("modalSalida");
    const closeSalida = document.querySelector(".closeSalida");
    const salidaCantidad = document.getElementById("salida_cantidad");

    if (closeSalida) {
        closeSalida.onclick = () => modalSalida.style.display = "none";
    }

    if (salidaCantidad) {
        salidaCantidad.oninput = function () {
            let cant = Number(this.value);
            let stock = Number(document.getElementById("salida_stock").value);
            let precio = Number(document.getElementById("salida_precio").value);

            if (cant > stock) {
                this.value = stock;
                cant = stock;
            }
            if (cant < 1) {
                this.value = 1;
                cant = 1;
            }

            document.getElementById("salida_final").value = stock - cant;
            document.getElementById("salida_valor").value = cant * precio;
        };
    }

    // ============================
    // CERRAR MODALES AL HACER CLIC FUERA
    // ============================
    window.onclick = function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    }
}

// ============================
// FUNCIONES GLOBALES PARA LOS BOTONES
// ============================
function abrirEntrada(id, nombre, stock, precio){
    const modalEntrada = document.getElementById("modalEntrada");
    if (modalEntrada) {
        document.getElementById("entrada_id").value = id;
        document.getElementById("entrada_nombre").value = nombre;
        document.getElementById("entrada_stock").value = stock;
        document.getElementById("entrada_precio").value = precio;

        document.getElementById("entrada_cantidad").value = "";
        document.getElementById("entrada_final").value = "";
        document.getElementById("entrada_valor").value = "";

        modalEntrada.style.display = "flex";
    }
}

function abrirSalida(id, nombre, stock, precio){
    const modalSalida = document.getElementById("modalSalida");
    if (modalSalida) {
        document.getElementById("salida_id").value = id;
        document.getElementById("salida_nombre").value = nombre;
        document.getElementById("salida_stock").value = stock;
        document.getElementById("salida_precio").value = precio;

        document.getElementById("salida_cantidad").value = "";
        document.getElementById("salida_final").value = "";
        document.getElementById("salida_valor").value = "";

        modalSalida.style.display = "flex";
    }
}

function editarProducto(id, nombre, descripcion, precio, stock, categoria){
    const modalProducto = document.getElementById("modalProducto");
    if (modalProducto) {
        document.getElementById("tituloModal").innerHTML = "Editar Producto";
        document.getElementById("accion").value = "editar";
        document.getElementById("id_producto").value = id;
        document.getElementById("nombre").value = nombre;
        document.getElementById("descripcion").value = descripcion;
        document.getElementById("precio").value = precio;
        document.getElementById("stock").value = stock;
        document.getElementById("categoria").value = categoria;
        modalProducto.style.display = "flex";
    }
}

function cerrarSesion(){ 
    window.location.href = "logout.php"; 
}
</script>

</body>
</html>
