<?php
require_once "configServer.php";
require_once "connectionDB.php";

class CategoriaController {

    // Crear categoría
    public static function crearCategoria($nombre, $descripcion) {
        global $connection;
        $sql = "INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)";
        $stmt = $connection->prepare($sql);
        return $stmt->execute([$nombre, $descripcion]);
    }

    // Obtener todas las categorías
    public static function obtenerCategorias() {
        global $connection;
        $sql = "SELECT * FROM categorias";
        $stmt = $connection->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener categoría por id
    public static function obtenerCategoria($id) {
        global $connection;
        $sql = "SELECT * FROM categorias WHERE id_categoria = ?";
        $stmt = $connection->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Editar categoría
    public static function editarCategoria($id, $nombre, $descripcion) {
        global $connection;
        $sql = "UPDATE categorias SET nombre=?, descripcion=? WHERE id_categoria=?";
        $stmt = $connection->prepare($sql);
        return $stmt->execute([$nombre, $descripcion, $id]);
    }

    // Eliminar categoría
    public static function eliminarCategoria($id) {
        global $connection;
        $sql = "DELETE FROM categorias WHERE id_categoria=?";
        $stmt = $connection->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
