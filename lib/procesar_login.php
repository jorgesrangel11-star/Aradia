<?php
session_start();

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/login.php');
    exit;
}

$mail = trim(mysqli_real_escape_string($conn, $_POST['mail']));
$pass = trim(mysqli_real_escape_string($conn, $_POST['pass']));

// Buscar usuario
$sql = "SELECT * FROM usuario WHERE mail = '$mail' LIMIT 1";
$resultado = mysqli_query($conn, $sql);

if ($resultado && mysqli_num_rows($resultado) === 1) {

    $usuario = mysqli_fetch_assoc($resultado);

    // Validar contraseña
    if ($pass === $usuario['pass']) {

        // Crear sesión
        $_SESSION['id_usr']  = $usuario['id_usr'];
        $_SESSION['nom_usr'] = $usuario['nom_usr'];
        $_SESSION['mail']    = $usuario['mail'];
        $_SESSION['tip_usu'] = $usuario['tip_usu'];

     
        // ADMIN EXCLUSIVO
        if ($usuario['mail'] === "admin@gmail.com") {
            header("Location: ../public/admin/index.php");
            exit;
        }

        // TODOS LOS DEMÁS → usuario general
        header("Location: ../public/general/index.php");
        exit;

    } else {
        header('Location: ../public/login.php?error=Contraseña incorrecta');
        exit;
    }

} else {
    header('Location: ../public/login.php?error=Correo no registrado');
    exit;
}
?>
