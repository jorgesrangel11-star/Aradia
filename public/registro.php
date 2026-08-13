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
    <title>Mindkind - Registro</title>

    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>

    <!-- CONTENEDOR REGISTRO -->
    <div class="login-wrapper-nobox">

        <h2 class="login-title">Regístrate</h2>

        <!-- Mostrar errores en caso de haber -->
        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger text-center"
                style="max-width: 420px; width: 100%; margin: 0 auto 20px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <form action="../lib/procesar_registro.php" method="POST" class="login-form-simple">

            <label for="nom_usr">Nombre completo</label>
            <input
                type="text"
                id="nom_usr"
                name="nom_usr"
                class="form-control"
                placeholder="Escribe tu nombre"
                required>

            <label for="mail">Correo electrónico</label>
            <input
                type="email"
                id="mail"
                name="mail"
                class="form-control"
                placeholder="ejemplo@correo.com"
                required>

            <label for="pass">Contraseña</label>
            <input
                type="password"
                id="pass"
                name="pass"
                class="form-control"
                placeholder="Elige una contraseña"
                required>

            <button type="submit" class="btn-login" style="margin-top: 15px;">
                Crear cuenta
            </button>

            <p class="login-footer-text">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
            </p>

        </form>

    </div>

</body>
</html>
