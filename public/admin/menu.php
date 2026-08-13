<?php
@session_start();

if (!isset($_SESSION['tip_usu']) || (int)$_SESSION['tip_usu'] !== 1) {
    session_destroy();
    header("Location: ../login.php?error=" . urlencode('Acceso no autorizado'));
    exit;
}
?>
<aside class="admin-sidebar">
    <div class="admin-brand">
        <img src="../assets/img/mindkind.jpeg" alt="MindKind">
        <span>MindKind Admin</span>
    </div>

    <ul class="admin-menu">
        <li>
            <a href="index.php"
               class="<?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                Home
            </a>
        </li>
        <li>
            <a href="catalogo_pictogramas.php"
               class="<?php echo basename($_SERVER['PHP_SELF']) === 'catalogo_pictogramas.php' ? 'active' : ''; ?>">
                Pictogramas
            </a>
        </li>
        <li>
            <a href="gestionar_catalogos.php"
               class="<?php echo basename($_SERVER['PHP_SELF']) === 'gestionar_catalogos.php' ? 'active' : ''; ?>">
                Gestionar Catálogos
            </a>
        </li>
        <li>
            <a href="ver_compras.php"
               class="<?php echo basename($_SERVER['PHP_SELF']) === 'ver_compras.php' ? 'active' : ''; ?>">
                Ver Compras
            </a>
        </li>
    </ul>

    <div class="admin-user">
        Usuario: <strong><?php echo htmlspecialchars($_SESSION['nom_usr']); ?></strong><br>
        <a href="../login.php" class="text-decoration-none" style="color:#f97373;">
            Cerrar sesión
        </a>
    </div>
</aside>