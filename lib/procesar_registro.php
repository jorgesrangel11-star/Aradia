<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/registro.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Limpiar datos
$nom_usr = trim(mysqli_real_escape_string($conn, $_POST['nom_usr']));
$mail    = trim(mysqli_real_escape_string($conn, $_POST['mail']));
$pass    = trim(mysqli_real_escape_string($conn, $_POST['pass']));

// Validar que no estén vacíos
if ($nom_usr === '' || $mail === '' || $pass === '') {
    header('Location: ../public/registro.php?error=' . urlencode('Completa todos los campos'));
    exit;
}

// Verificar si el correo ya existe
$sql_check = "SELECT id_usr FROM usuario WHERE mail = '$mail' LIMIT 1";
$res_check = mysqli_query($conn, $sql_check);

if ($res_check && mysqli_num_rows($res_check) > 0) {
    header('Location: ../public/registro.php?error=' . urlencode('El correo ya está registrado'));
    exit;
}

// Insertar usuario nuevo como tipo 2
$sql_insert = "INSERT INTO usuario (nom_usr, mail, pass, tip_usu)
               VALUES ('$nom_usr', '$mail', '$pass', 2)";

if (mysqli_query($conn, $sql_insert)) {

    // Redirigir con mensaje
    header('Location: ../public/login.php?msg=' . urlencode('Cuenta creada exitosamente'));
    exit;

} else {
    header('Location: ../public/registro.php?error=' . urlencode('Error al registrar usuario'));
    exit;
}
?>
