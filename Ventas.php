
<?php
session_start();

// SI NO HAY SESIÓN → REDIRIGIR AL LOGIN REAL
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: Inicio de Sesion.php");
    exit();
}
?>
<?php
if (isset($_GET['login']) && $_GET['login'] === 'success') {
    echo "<script>alert('Inicio de sesión exitoso. Bienvenido, ".$_SESSION['usuario']."');</script>";
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inventario - Ventas</title>
    <link rel="stylesheet" href="ventas.css">
</head>
<body>



<!-- HEADER -->
<header>
    SISTEMA DE INVENTARIO
    <div id="fecha" class="fecha"></div>
</header>

<!-- SIDEBAR -->
<nav class="sidebar">
    <a href="Inicio de Sesion.php">INICIO</a>
    <a href="Ventas.php" class="active"><strong>VENTAS</strong></a>
    <a href="Inventario.php">INVENTARIO</a>
    <a href="Categorias.php">CATEGORÍAS</a>
     <a href="Usuarios.php" class="active"><strong>USUARIOS</strong></a>
</nav>

<!-- CONTENIDO PRINCIPAL -->
<main>
    <h2>VENTAS</h2>

    <div class="actions">
        <button class="btnAgregar" id="btnAgregar">AGREGAR VENTA</button>
        <button class="btn"style="float:right" onclick="cerrarSesion()">CERRAR SESIÓN</button>
    </div>

    <table class="ventas-table">
        <thead>
            <tr>
                <th>CÓDIGO DE BARRAS</th>
                <th>DESCRIPCIÓN DEL PRODUCTO</th>
                <th>PRECIO UNITARIO</th>
                <th>CANTIDAD</th>
                <th>PRECIO TOTAL</th>
            </tr>
        </thead>
        <tbody id="tablaBody">
            <!-- Filas dinámicas -->
        </tbody>
    </table>
</main>

<!-- MODAL AGREGAR VENTA -->
<div id="modalAgregar" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Agregar Venta</h3>
        <form id="formAgregar">
            <label for="codigo">Código de Barras:</label>
            <input type="text" id="codigo" name="codigo" required>

            <label for="descripcion">Descripción:</label>
            <input type="text" id="descripcion" name="descripcion" required>

            <label for="precio">Precio Unitario:</label>
            <input type="number" id="precio" name="precio" step="0.01" required>

            <label for="cantidad">Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad" min="1" required>

            <div class="modal-buttons">
                <button type="submit" id="btnGuardar">Guardar</button>
                <button type="button" id="btnCancelar">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT PRINCIPAL -->
<script>
    const modal = document.getElementById("modalAgregar");
    const btn = document.getElementById("btnAgregar");
    const span = document.getElementsByClassName("close")[0];
    const form = document.getElementById("formAgregar");
    const tablaBody = document.getElementById("tablaBody");
    const btnCancelar = document.getElementById("btnCancelar");

    // Modal oculto al cargar
    modal.style.display = "none";

    // Abrir modal
    btn.onclick = () => modal.style.display = "flex";

    // Cerrar modal
    span.onclick = () => modal.style.display = "none";
    btnCancelar.onclick = () => modal.style.display = "none";

    // Cerrar al hacer clic fuera
    window.onclick = (event) => {
        if (event.target === modal) modal.style.display = "none";
    };

    // Agregar venta
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        const codigo = form.codigo.value.trim();
        const descripcion = form.descripcion.value.trim();
        const precio = parseFloat(form.precio.value);
        const cantidad = parseInt(form.cantidad.value);
        const total = (precio * cantidad).toFixed(2);

        const nuevaFila = document.createElement("tr");
        nuevaFila.innerHTML = `
            <td>${codigo}</td>
            <td>${descripcion}</td>
            <td>$${precio.toFixed(2)}</td>
            <td>${cantidad}</td>
            <td>$${total}</td>
        `;
        tablaBody.appendChild(nuevaFila);

        form.reset();
        modal.style.display = "none";
    });

    function cerrarSesion() {
        localStorage.removeItem("logueado");
        localStorage.removeItem("usuario");
        window.location.href = "Inicio de Sesion.php";
    }
</script>

<script src="fecha.js"></script>

<footer>© 2025 Sistema de Inventario</footer>
</body>
</html>
