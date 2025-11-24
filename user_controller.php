<?php
// ==========================
// archivo: user_controller.php
// CRUD completo para usuarios
// ==========================
require_once "configServer.php";
require_once "connectionDB.php";

class UsuarioController {

    // Crear usuario
    public static function crearUsuario($nombre, $apellido, $edad, $correo, $password, $id_perfil) {
        global $connection;
        $sql = "INSERT INTO usuario (nombre, apellido, edad, correo, contraseña, id_perfil) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        return $stmt->execute([$nombre, $apellido, $edad, $correo, $passwordHash, $id_perfil]);
    }

    // Leer todos los usuarios
    public static function obtenerUsuarios() {
        global $connection;
        $sql = "SELECT usuario.*, perfil_usuario.nombre AS perfil FROM usuario JOIN perfil_usuario ON usuario.id_perfil = perfil_usuario.id_perfil";
        $stmt = $connection->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un usuario por id
    public static function obtenerUsuario($id) {
        global $connection;
        $sql = "SELECT * FROM usuario WHERE id_usuario = ?";
        $stmt = $connection->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Editar usuario
    public static function editarUsuario($id, $nombre, $apellido, $edad, $correo, $id_perfil) {
        global $connection;
        $sql = "UPDATE usuario SET nombre=?, apellido=?, edad=?, correo=?, id_perfil=? WHERE id_usuario=?";
        $stmt = $connection->prepare($sql);
        return $stmt->execute([$nombre, $apellido, $edad, $correo, $id_perfil, $id]);
    }

    // Cambiar contraseña
    public static function cambiarPassword($id, $newPassword) {
        global $connection;
        $sql = "UPDATE usuario SET contraseña=? WHERE id_usuario=?";
        $stmt = $connection->prepare($sql);
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
        return $stmt->execute([$passwordHash, $id]);
    }

    // Eliminar usuario
    public static function eliminarUsuario($id) {
        global $connection;
        $sql = "DELETE FROM usuario WHERE id_usuario=?";
        $stmt = $connection->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Login
    public static function login($correo, $password) {
        global $connection;
        $sql = "SELECT * FROM usuario WHERE correo=?";
        $stmt = $connection->prepare($sql);
        $stmt->execute([$correo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['contraseña'])) {
            return $user;
        }
        return false;
    }
}
?>
