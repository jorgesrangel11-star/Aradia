<?php
@session_start();

if (!isset($_SESSION['tip_usu']) || (int)$_SESSION['tip_usu'] !== 2) {
    session_destroy();
    header("Location: ../login.php?error=" . urlencode('Acceso no autorizado'));
    exit;
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Usuario General</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>
                <!-- más adelante agregamos "Ver tarjetas" -->
            </ul>
            <span class="navbar-text">
                <?php echo $_SESSION['nom_usr']; ?>
            </span>
        </div>
    </div>
</nav>
