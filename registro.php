<?php
require_once "Global/configServer.php";
require_once "Global/connectionDB.php";
require_once "Global/user_controller.php";

$mensaje = "";
$exito = false;

// ===============================
// Cargar perfiles de la BD
// ===============================
$sql = $connection->query("SELECT * FROM perfil_usuario");
$perfiles = $sql->fetchAll(PDO::FETCH_ASSOC);

// ===============================
// Procesar formulario
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $pass = $_POST['pass'];
    $pass2 = $_POST['pass2'];
    $edad = 18;

    // Tomar el rol elegido
    $id_perfil = $_POST['id_perfil'];

    if ($pass !== $pass2) {
        $mensaje = "Las contraseñas no coinciden";
    } else {
        if (UsuarioController::crearUsuario($nombre, $apellido, $edad, $correo, $pass, $id_perfil)) {
            $mensaje = "Usuario registrado exitosamente, redirigiendo...";
            $exito = true;
        } else {
            $mensaje = "Error al registrar usuario";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro</title>

  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: Arial, sans-serif;
      background: #e9f0ff;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .container {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 5px 18px rgba(0,0,0,0.15);
      width: 360px;
    }

    .container h2 { text-align: center; color: #1a3a6e; margin-bottom: 20px; }

    label { display: block; margin-bottom: 6px; color: #234a7a; font-size: 14px; }

    input {
      width: 100%; padding: 10px; margin-bottom: 15px;
      border-radius: 8px; border: 1px solid #bcccff; font-size: 15px;
    }

    .btn {
      width: 100%; background: #1e63e6; color: white;
      border: none; padding: 12px; font-size: 16px;
      border-radius: 10px; cursor: pointer;
    }

    .msg { text-align:center; margin-bottom:10px; color:green; font-weight:bold; }
    .msg-error { text-align:center; margin-bottom:10px; color:red; font-weight:bold; }
  </style>
</head>
<body>

  <div class="container">
    <h2>Registro</h2>

    <?php 
      if ($mensaje !== "") {
        if ($exito) {
            echo "<p class='msg'>$mensaje</p>";
        } else {
            echo "<p class='msg-error'>$mensaje</p>";
        }
      }
    ?>

    <!-- FORMULARIO -->
    <form action="" method="post">
      <label for="nombre">Nombre</label>
      <input type="text" id="nombre" name="nombre" required>

      <label for="apellido">Apellido</label>
      <input type="text" id="apellido" name="apellido" required>

      <label for="correo">Correo electrónico</label>
      <input type="email" id="correo" name="correo" required>

      <label for="pass">Contraseña</label>
      <input type="password" id="pass" name="pass" minlength="6" required>

      <label for="pass2">Confirmar contraseña</label>
      <input type="password" id="pass2" name="pass2" minlength="6" required>

       <!-- NUEVO SELECT DE ROL -->
  <label for="id_perfil">Rol del usuario</label>
  <select id="id_perfil" name="id_perfil" required>
      <?php foreach ($perfiles as $perfil): ?>
          <option value="<?= $perfil['id_perfil'] ?>">
              <?= $perfil['nombre'] ?>
          </option>
      <?php endforeach; ?>
  </select>

      <button class="btn" type="submit">Crear cuenta</button>
    </form>
  </div>

<?php if ($exito): ?>
<script>
// Redirigir después de 1 segundo
setTimeout(() => {
    window.location.href = "Inicio de Sesion.php"; // ← pon aquí tu archivo de login
}, 1000);
</script>
<?php endif; ?>

</body>
</html>
