<?php
session_start();
$error = '';

if (isset($_GET['error'])) {
    $error = $_GET['error'];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mindkind - Iniciar sesión</title>

    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>

    <!-- CONTENEDOR LOGIN -->
    <div class="login-wrapper-nobox">

        <h2 class="login-title">Inicia sesión</h2>

        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger text-center" style="max-width: 420px; width: 100%; margin: 0 auto;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <form action="../lib/procesar_login.php" method="POST" class="login-form-simple">
            
            <label for="mail">Correo electrónico</label>
            <input type="email" id="mail" name="mail" class="form-control" placeholder="ejemplo@correo.com" required>

            <label for="pass">Contraseña</label>
            <input type="password" id="pass" name="pass" class="form-control" placeholder="ingresa una contraseña segura" required>

            <button type="submit" class="btn-login">Iniciar sesión</button>

            <p class="login-footer-text">
                ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
            </p>

        </form>

    </div>

</body>
</html>

