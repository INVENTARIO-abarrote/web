<?php
require_once "configServer.php";
require_once "connectionDB.php";

class ProductoController {

    // Crear producto
    public static function crearProducto($nombre, $descripcion, $precio, $stock, $id_categoria) {
        global $connection;
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, id_categoria)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        return $stmt->execute([$nombre, $descripcion, $precio, $stock, $id_categoria]);
    }

    // Obtener todos los productos con info de categoría
    public static function obtenerProductos() {
        global $connection;
        $sql = "SELECT productos.*, categorias.nombre AS categoria
                FROM productos
                JOIN categorias ON productos.id_categoria = categorias.id_categoria";
        $stmt = $connection->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener producto por id
    public static function obtenerProducto($id) {
        global $connection;
        $sql = "SELECT * FROM productos WHERE id_producto = ?";
        $stmt = $connection->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Editar producto
    public static function editarProducto($id, $nombre, $descripcion, $precio, $stock, $id_categoria) {
        global $connection;
        $sql = "UPDATE productos
                SET nombre=?, descripcion=?, precio=?, stock=?, id_categoria=?
                WHERE id_producto=?";
        $stmt = $connection->prepare($sql);
        return $stmt->execute([$nombre, $descripcion, $precio, $stock, $id_categoria, $id]);
    }

    // Eliminar producto
    public static function eliminarProducto($id) {
        global $connection;
        $sql = "DELETE FROM productos WHERE id_producto=?";
        $stmt = $connection->prepare($sql);
        return $stmt->execute([$id]);
    }
    // Agregar entrada al inventario
public static function agregarEntrada($id_producto, $cantidad) {
    global $connection;

    // Obtener stock actual
    $sql = "SELECT stock FROM productos WHERE id_producto=?";
    $stmt = $connection->prepare($sql);
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) return false;

    $stock_anterior = $producto['stock'];
    $stock_nuevo = $stock_anterior + $cantidad;

    // Actualizar stock
    $sql = "UPDATE productos SET stock=? WHERE id_producto=?";
    $stmt = $connection->prepare($sql);
    $stmt->execute([$stock_nuevo, $id_producto]);

    // Crear registro Kardex
    $sql = "INSERT INTO kardex (id_producto, tipo, cantidad, stock_anterior, stock_nuevo)
            VALUES (?, 'entrada', ?, ?, ?)";
    $stmt = $connection->prepare($sql);
    return $stmt->execute([$id_producto, $cantidad, $stock_anterior, $stock_nuevo]);
}


// Quitar stock (salida)
public static function agregarSalida($id_producto, $cantidad) {
    global $connection;

    // Obtener stock actual
    $sql = "SELECT stock FROM productos WHERE id_producto=?";
    $stmt = $connection->prepare($sql);
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) return false;

    $stock_anterior = $producto['stock'];

    // Validar que NO se vuelva negativo
    if ($cantidad > $stock_anterior) {
        return "NO_STOCK"; // Puedes usarlo para mostrar un mensaje en pantalla
    }

    $stock_nuevo = $stock_anterior - $cantidad;

    // Actualizar stock
    $sql = "UPDATE productos SET stock=? WHERE id_producto=?";
    $stmt = $connection->prepare($sql);
    $stmt->execute([$stock_nuevo, $id_producto]);

    // Crear registro Kardex
    $sql = "INSERT INTO kardex (id_producto, tipo, cantidad, stock_anterior, stock_nuevo)
            VALUES (?, 'salida', ?, ?, ?)";
    $stmt = $connection->prepare($sql);
    return $stmt->execute([$id_producto, $cantidad, $stock_anterior, $stock_nuevo]);
}

}
?>
