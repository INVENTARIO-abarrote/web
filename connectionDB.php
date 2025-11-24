<?php
// IMPORTANTE → Cargar las constantes de conexión
require_once "configServer.php";

$ServerInfo = "mysql:dbname=" . DB . ";host=" . SERVER;

try {
    $connection = new PDO($ServerInfo, USER, PASSWORD, array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ));

    // ✔ Conexión exitosa → NO mostrar nada
    // echo eliminado totalmente

} catch (PDOException $e) {
    // ❌ Mostrar error SOLO si falla
    echo "<script>alert('Error en la conexión: " . $e->getMessage() . "');</script>";
}
?>
