<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // tu contraseña si tienes
$dbname = 'neurodiv_eval';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die('Error de conexion: ' . mysqli_connect_error());
}
?>
